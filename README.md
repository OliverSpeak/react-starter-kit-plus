# React Starter Kit Plus

## Introduction

This is a fork of the official React Starter Kit branch with extra boilerplate specific to my own needs.

## Main "Plus" Features

- The user `name` is replaced with `username` and has character constraints for safety.
    - The username must be between 3 - 20 characters long and only contain letters, numbers, underscores, and hyphens.
    - Users can log in via email or username.
- Conditional password rules.
    - When the environment is not `production`, the app will relax all password rules you may define, such as the 8 character minimum that's default in the original React Starter Kit.
    - Rules can be specified in `AppServiceProvider`.
- [Husky](https://github.com/typicode/husky) is setup and configured to run lint/style checks in the pre-commit hook.
    - The template hook uses ASCII art to clearly define the start and end of the pre-commit process.
    - The pre-commit hook will use [Laravel Sail](https://github.com/laravel/sail) commands if available and fallback to using regular commands. This can be customised.

## Minor changes

- Concise wording for UI labels and messages.
- Add `cursor-pointer` to shadcn/ui components missing this basic style.
- Disabled shadcn/ui sidebar shortcut.

## More Information

See the original [React Starter Kit](https://github.com/laravel/react-starter-kit) repository.

Documentation for official Laravel starter kits can be found on the [Laravel website](https://laravel.com/docs/starter-kits).

## License

Just like the original React starter kit, this is open-sourced software licensed under the MIT license.
