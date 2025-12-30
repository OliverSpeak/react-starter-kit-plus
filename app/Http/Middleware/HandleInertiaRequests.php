<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
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
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
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
        $translationPath = lang_path($locale);
        $translationFiles = config('locale.files', []);

        if (! is_dir($translationPath)) {
            return $translations;
        }

        foreach ($translationFiles as $file) {
            $filePath = "{$translationPath}/{$file}.php";

            if (file_exists($filePath)) {
                try {
                    $translations[$file] = require $filePath;
                } catch (Throwable $e) {
                    // Log error but continue loading other files
                    logger()->warning("Failed to load translation file: {$filePath}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $translations;
    }
}
