# React Starter Kit Plus

## Introduction

This is a fork of Laravel's [React Starter Kit](https://github.com/laravel/react-starter-kit/) that makes development more strict and opinionated, while maintaining changes from upstream. Commits made to this fork aim to minimise refactoring and are generally additive. Various minor features may also be added here.

## Summary of changes

- Setup [essentials](https://github.com/nunomaduro/essentials) and publish `config/essentials.php`
- Setup `pint.json` with defaults from [essentials](https://github.com/nunomaduro/essentials).
- Setup rector and configure based on [nunomaduro/laravel-starter-kit](https://github.com/nunomaduro/laravel-starter-kit/)
- Add hostname configuration support to `vite.config.ts`
- Apply cursor-pointer class to interactive components that were missing it upstream

Please note that pint and rector have **NOT** been run on this fork. You will need to do that yourself.

## License

This is open-sourced software licensed under the MIT license.
