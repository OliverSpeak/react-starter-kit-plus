<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocale($request);

        App::setLocale($locale);

        return $next($request);
    }

    /**
     * Get the locale for the current request.
     */
    private function getLocale(Request $request): string
    {
        // Priority: authenticated user's locale preference > cookie > browser > default
        $user = $request->user();
        if ($user && array_key_exists('locale', $user->getAttributes())) {
            $userLocale = $user->getAttributeValue('locale');
            if ($userLocale && $this->isSupportedLocale($userLocale)) {
                return $userLocale;
            }
        }

        // Check cookie for persisted preference (survives logout/login)
        $cookieLocale = $request->cookie('locale');
        if ($cookieLocale && $this->isSupportedLocale($cookieLocale)) {
            return $cookieLocale;
        }

        // Only use browser language if no cookie preference exists (first visit)
        $browserLocale = $this->getBrowserLocale($request);
        if ($browserLocale && $this->isSupportedLocale($browserLocale)) {
            return $browserLocale;
        }

        return $this->getDefaultLocale();
    }

    /**
     * Get the preferred locale from the browser's Accept-Language header.
     */
    private function getBrowserLocale(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        if (! $acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header and check each language in order
        // (e.g., "en-US,en;q=0.9,ja;q=0.8" -> check "en", then "ja")
        foreach (explode(',', $acceptLanguage) as $lang) {
            // Extract locale (remove quality value if present)
            $locale = mb_strtolower(mb_trim(explode(';', mb_trim($lang))[0]));

            // Extract base locale (e.g., "en-US" -> "en")
            $baseLocale = explode('-', $locale)[0];

            if ($baseLocale !== '' && $this->isSupportedLocale($baseLocale)) {
                return $baseLocale;
            }
        }

        return null;
    }

    /**
     * Get the default locale from configuration.
     */
    private function getDefaultLocale(): string
    {
        return config('locale.default', config('app.locale'));
    }

    /**
     * Check if the given locale is supported.
     */
    private function isSupportedLocale(string $locale): bool
    {
        $supported = config('locale.supported', []);

        return is_array($supported) && array_key_exists($locale, $supported);
    }
}
