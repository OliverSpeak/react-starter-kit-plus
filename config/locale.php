<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | This array contains all locales that your application supports. Each
    | locale must have corresponding translation files in the lang/ directory.
    |
    | The 'name' key is the English name of the locale, and 'native' is the
    | native name (useful for displaying in UI).
    |
    */

    'supported' => [
        'en' => [
            'name' => 'English',
            'native' => 'English',
        ],
        'ja' => [
            'name' => 'Japanese',
            'native' => '日本語',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | This is the default locale that will be used when no locale is specified
    | or when the requested locale is not supported. This should match one of
    | the keys in the 'supported' array above.
    |
    */

    'default' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Locale
    |--------------------------------------------------------------------------
    |
    | This is the locale that will be used when a translation key is not found
    | in the current locale. This should match one of the keys in the
    | 'supported' array above.
    |
    */

    'fallback' => env('APP_FALLBACK_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | PHP Translation Files to Load
    |--------------------------------------------------------------------------
    |
    | These are backend-related translation files (validation, auth, etc.)
    | that are loaded from lang/{locale}/{file}.php. These are used by Laravel
    | for backend validation messages and other server-side translations.
    |
    */

    'php_files' => [
        'auth',
        'pagination',
        'passwords',
        'validation',
    ],

    /*
    |--------------------------------------------------------------------------
    | JSON Translation Files
    |--------------------------------------------------------------------------
    |
    | UI-related translations are stored as JSON files in lang/{locale}.json.
    | These are loaded and shared with the frontend for UI elements.
    |
    */

    'json_enabled' => true,

];
