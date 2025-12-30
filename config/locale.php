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
    | Translation Files to Load
    |--------------------------------------------------------------------------
    |
    | This array specifies which translation files should be loaded and shared
    | with the frontend. Files are loaded in the order specified. If a file
    | doesn't exist for a locale, it will be silently skipped.
    |
    | You can add or remove files as needed. The key is the namespace that
    | will be used in the frontend (e.g., 'auth' becomes 'translations.auth').
    |
    */

    'files' => [
        'auth',
        'pagination',
        'passwords',
        'ui',
        'validation',
    ],

];
