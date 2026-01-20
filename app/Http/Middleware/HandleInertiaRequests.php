<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Inertia\Middleware;
use Throwable;

final class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'currentLocale' => App::currentLocale(),
            'supportedLocales' => config('locale.supported', []),
            'translations' => fn () => $this->loadTranslations(App::currentLocale()),
        ];
    }

    /**
     * Load translation files for the given locale.
     *
     * @return array<string, mixed>
     */
    private function loadTranslations(string $locale): array
    {
        $translations = [];
        $jsonKeys = [];

        // Load JSON file for UI translations (if enabled)
        if (config('locale.json_enabled', true)) {
            $jsonPath = lang_path("{$locale}.json");
            if (file_exists($jsonPath)) {
                try {
                    $jsonContent = file_get_contents($jsonPath);
                    $jsonData = json_decode($jsonContent, true);

                    if (! is_array($jsonData)) {
                        logger()->warning("JSON translation file does not contain a valid array: {$jsonPath}");
                    } else {
                        $jsonKeys = array_keys($jsonData);
                        $translations = array_merge($translations, $jsonData);
                    }
                } catch (Throwable $e) {
                    logger()->warning("Failed to load JSON translation file: {$jsonPath}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Load PHP files for backend translations
        // These are loaded under a 'backend' namespace to strictly avoid conflicts with JSON UI translations
        $translationPath = lang_path($locale);
        $phpFiles = config('locale.php_files', []);

        if (! is_array($phpFiles)) {
            logger()->warning('locale.php_files config must be an array');
            $phpFiles = [];
        }

        if (is_dir($translationPath)) {
            foreach ($phpFiles as $file) {
                if (! is_string($file)) {
                    logger()->warning('Invalid PHP file name in locale.php_files config: '.var_export($file, true));

                    continue;
                }

                $filePath = "{$translationPath}/{$file}.php";

                if (file_exists($filePath)) {
                    try {
                        $phpData = require $filePath;

                        if (! is_array($phpData)) {
                            logger()->warning("PHP translation file does not return an array: {$filePath}");

                            continue;
                        }

                        // Strict: Check for conflicts with JSON top-level keys
                        if (in_array($file, $jsonKeys, true)) {
                            logger()->warning(
                                "PHP translation file '{$file}' conflicts with JSON key. ".
                                "PHP translations will be namespaced under 'backend.{$file}' to avoid conflicts.",
                                ['json_key' => $file, 'php_file' => $filePath]
                            );
                        }

                        // Store PHP translations under 'backend' namespace to strictly avoid conflicts
                        // e.g., backend.auth.failed instead of auth.failed
                        if (! isset($translations['backend']) || ! is_array($translations['backend'])) {
                            $translations['backend'] = [];
                        }
                        $translations['backend'][$file] = $phpData;
                    } catch (Throwable $e) {
                        logger()->warning("Failed to load PHP translation file: {$filePath}", [
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        return $translations;
    }
}
