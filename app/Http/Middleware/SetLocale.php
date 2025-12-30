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
        // Get locale from session or default to config value
        $locale = $this->getLocale($request);

        App::setLocale($locale);

        return $next($request);
    }

    /**
     * Get the locale for the current request.
     */
    private function getLocale(Request $request): string
    {
        $locale = session('locale');

        if ($locale && $this->isSupportedLocale($locale)) {
            return $locale;
        }

        return $this->getDefaultLocale();
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
        return array_key_exists($locale, config('locale.supported', []));
    }
}
