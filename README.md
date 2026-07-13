# React Starter Kit Plus

## Introduction

This is a fork of Laravel's [React Starter Kit](https://github.com/laravel/react-starter-kit/) that makes opinionated changes considered generally unfit for upstream. It also builds branches to facilitate pulling upstream changes.

## Getting started

Four branches are maintained:

| Branch              | Purpose                                                                       |
| ------------------- | ----------------------------------------------------------------------------- |
| **main**            | Source branch for Plus changes without locale support                         |
| **main-i18n**       | Source branch for Plus changes with locale support (downstream from **main**) |
| **main-build**      | Generated from **main** — Chisel (all features), Rector, and Pint applied     |
| **main-i18n-build** | Generated from **main-i18n** when pushed and the workflow completes           |

Pushing to **main** or **main-i18n** regenerates **main-build** or **main-i18n-build** via Actions.

For your own projects, fork either **main-build** or **main-i18n-build**, depending on whether you want locale support (see below). These branches have post-install scripts run (see [Laravel Chisel](https://github.com/laravel/chisel)) and modifications from Rector and Pint already applied.

## Summary of features

- Locale support (**main-i18n** branch exclusive)
- Set up [essentials](https://github.com/nunomaduro/essentials) and publish `config/essentials.php`
- Set up Rector and configure based on [nunomaduro/laravel-starter-kit](https://github.com/nunomaduro/laravel-starter-kit/); applied on build branches
- Set up `pint.json` with defaults from [essentials](https://github.com/nunomaduro/essentials); applied on build branches
- Add hostname configuration support to `vite.config.ts`
- Apply `cursor-pointer` class to interactive components that are missing it upstream
- Apply `bg-background` class to component variants that are missing it upstream

## Locale (i18n) support

Since the [React Starter Kit](https://github.com/laravel/react-starter-kit/) was not built with localisation in mind, this feature required a highly invasive refactor of the frontend, and is by no means a "minor" feature. Therefore, it has been given its own branch **main-i18n** that inherits changes from **main**.

This implementation uses a **hybrid** approach: the backend follows Laravel's [conventions](https://laravel.com/docs/12.x/localization) and uses PHP language files, while the frontend aims to follow [react-i18next](https://react.i18next.com/) conventions and uses JSON language files. Since the `HandleInertiaRequests.php` middleware can handle translations for the frontend, a new hook, `use-translations.ts`, has been created to facilitate rendering of language strings, and serves as a **lightweight** alternative to react-i18next while maintaining basic conventions.

The `lang` directory has been scaffolded using Laravel's `lang:publish` Artisan command, and is where both PHP and JSON language files are stored. For demonstration purposes, a Japanese `ja` directory has been created alongside the English `en` directory with files for both the frontend and backend. The Japanese translations are **not** audited for accuracy.

A `config/locale.php` file has been created to centralise locale settings, such as supported languages and the default language. Supported languages **implicitly** use the [IETF language tag](https://en.wikipedia.org/wiki/IETF_language_tag) format.

A new component `language-switcher.tsx` has been created to provide a user-facing interface to view and switch languages, accessible from the welcome and dashboard pages.

Preferred locale persistence works using the priority chain: **authenticated user (database) > cookie > browser `Accept-Language` header > app default**. When an authenticated user changes their locale, this is updated in both the database and the new `locale` cookie.

Finally, every user-facing label has been refactored to use `useTranslation()` with the convention `t('lang.key')`, like react-i18next.

## License

This is open-sourced software licensed under the MIT license.
