<a href="https://nunomaduro.com/">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="art/header-dark.png">
    <img alt="Logo for essentials" src="art/header-light.png">
  </picture>
</a>

# Essentials

<p>
    <a href="https://github.com/nunomaduro/essentials/actions"><img src="https://github.com/nunomaduro/essentials/actions/workflows/tests.yml/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/nunomaduro/essentials"><img src="https://img.shields.io/packagist/dt/nunomaduro/essentials" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/nunomaduro/essentials"><img src="https://img.shields.io/packagist/v/nunomaduro/essentials" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/nunomaduro/essentials"><img src="https://img.shields.io/packagist/l/nunomaduro/essentials" alt="License"></a>
</p>

Essentials provides **better defaults** for your Laravel applications including: strict models, automatically eager loaded relationships, immutable dates, and more! 

> **Requires [PHP 8.3+](https://php.net/releases/)**, **[Laravel 11+](https://laravel.com/docs/11.x/)**.

> **Note:** This package is a **work in progress (don't use it yet)**, and it modifies the default behavior of Laravel. It is recommended to use it in new projects or when you are comfortable with the changes it introduces.

## Installation

⚡️ Get started by requiring the package using [Composer](https://getcomposer.org):

```bash
composer require nunomaduro/essentials:@dev
```

## Features

All features are optional and configurable in `config/essentials.php`.

### ✅ Strict Models

Improves how Eloquent handles undefined attributes, lazy loading, and invalid assignments.

- Accessing a missing attribute throws an error.
- Lazy loading is blocked unless explicitly allowed.
- Setting undefined attributes throws instead of failing silently.

**Why:** Avoids subtle bugs and makes model behavior easier to reason about.

---

### ⚡️ Auto Eager Loading

Automatically eager loads relationships defined in the model's `$with` property.

**Why:** Reduces N+1 query issues and improves performance without needing `with()` everywhere.

---

### 🔓 Optional Unguarded Models

Disables Laravel's mass assignment protection globally (opt-in).

**Why:** Useful in trusted or local environments where you want to skip defining `$fillable`.

---

### 🕒 Immutable Dates

Uses `CarbonImmutable` instead of mutable date objects across your app.

**Why:** Prevents unexpected date mutations and improves predictability.

---

### 🔒 Force HTTPS

Forces all generated URLs to use `https://`.

**Why:** Ensures all traffic uses secure connections by default.

---

### 🛑 Safe Console

Blocks potentially destructive Artisan commands in production (e.g., `migrate:fresh`).

**Why:** Prevents accidental data loss and adds a safety net in sensitive environments.

---

### 🚀 Asset Prefetching

Configures Laravel Vite to preload assets more aggressively.

**Why:** Improves front-end load times and user experience.

---

### 🔄 Prevent Stray Requests

Configures Laravel Http Facade to prevent stray requests.

**Why:** Ensure every HTTP calls during tests have been explicitly faked.

---

### 😴 Fake Sleep

Configures Laravel Sleep Facade to be faked.

**Why:** Avoid unexpected sleep during testing cases.

### 🏗️ Artisan Commands

#### `make:action`

Quickly generates action classes in your Laravel application:

```bash
php artisan make:action CreateUserAction
```

This creates a clean action class at `app/Actions/CreateUserAction.php`:

```php
<?php

declare(strict_types=1);

namespace App\Actions;

final readonly class CreateUserAction
{
    /**
     * Execute the action.
     */
    public function handle(): void
    {
        DB::transaction(function (): void {
            //
        });
    }
}
```

Actions help organize business logic in dedicated classes, promoting single responsibility and cleaner controllers.

#### `essentials:pint`

Laravel Pint is included by default in every Laravel project and is a great tool to keep your code clean and consistent. But it is configured very minimally by default. This command will publish a configuration file for Pint that includes the following:

- "declare_strict_types" - Enforces strict types in all files.
- "final_class" - Enforces final classes by default.
- "ordered_class_elements" - Orders class elements by visibility and type.
- "strict_comparison" - Enforces strict comparison in all files.
- and more...

```bash
php artisan essentials:pint {--force} {--backup}
```

*Options:*
- `--force` - Overwrites the existing configuration file without asking for confirmation.
- `--backup` - Creates a backup of the existing configuration file.


## Configuration

All features are configurable through the `essentials.php` config file. By default, most features are enabled, but you can disable any feature by setting its configuration value to `false`:

```php
// config/essentials.php
return [
    NunoMaduro\Essentials\Configurables\ShouldBeStrict::class => true,
    NunoMaduro\Essentials\Configurables\Unguard::class => false,
    // other configurables...
];
```

#### `essentials:composer`

Add the following scripts to your `composer.json` file:

```json
{
    "refactor": "rector",
    "lint": "pint",
    "test:refactor": "rector --dry-run",
    "test:lint": "pint --test",
    "test:types": "phpstan analyse --ansi",
    "test:unit": "pest --colors=always --coverage --parallel",
    "test": [
        "@test:refactor",
        "@test:lint",
        "@test:types",
        "@test:unit"
    ]
}
```

```bash
php artisan essentials:composer {--force} {--backup}
```

*Options:*
- `--force` - Overwrites the existing configuration file without asking for confirmation.
- `--backup` - Creates a backup of the existing configuration file.

You may also publish the stubs used by this package:

```bash
php artisan vendor:publish --tag=essentials-stubs
```

## Roadmap

- Better defaults before each test case
- Better Pint configuration by default
- General cleanup of the skeleton
- Additional configurables for common Laravel patterns

## License

**Essentials** was created by **[Nuno Maduro](https://twitter.com/enunomaduro)** under the **[MIT license](https://opensource.org/licenses/MIT)**.
