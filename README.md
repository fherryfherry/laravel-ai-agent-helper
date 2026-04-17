# Laravel AI Agent Helper

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fherryfherry/laravel-ai-agent-helper.svg?style=flat-square)](https://packagist.org/packages/fherryfherry/laravel-ai-agent-helper)
[![Total Downloads](https://img.shields.io/packagist/dt/fherryfherry/laravel-ai-agent-helper.svg?style=flat-square)](https://packagist.org/packages/fherryfherry/laravel-ai-agent-helper)
[![License](https://img.shields.io/packagist/l/fherryfherry/laravel-ai-agent-helper.svg?style=flat-square)](https://packagist.org/packages/fherryfherry/laravel-ai-agent-helper)

Laravel AI Agent Helper provides seamless integration for AI Agents to interact with your Laravel application. It includes features for secure auto-login and real-time metadata collection via iframe communication.

## Features

- **Secure Auto-Login**: Generate signed URLs for AI Agents to automatically log in to specific user accounts (local/dev environments only).
- **Metadata Reporting**: Automatically send application state (URL, Title, Route, Form Inputs, Sidebar Menus, etc.) to a parent window via `postMessage`.
- **Theme Support**: Detects and reports Dark/Light mode status.
- **Easy Integration**: Simple Blade directive to inject the helper script.

## Installation

You can install the package via composer:

```bash
composer require fherryfherry/laravel-ai-agent-helper
```

The package will automatically register its service provider.

## Configuration

You can publish the config file with:

```bash
php artisan vendor:publish --tag="ai-agent-helper-config"
```

This is the contents of the published config file:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Agent Login Token
    |--------------------------------------------------------------------------
    |
    | Secret token used to sign auto-login requests.
    |
    */
    'login_token' => env('AGENT_LOGIN_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Allowed Environments
    |--------------------------------------------------------------------------
    |
    | Environments where the auto-login feature is allowed.
    |
    */
    'allowed_environments' => ['local', 'development'],

    /*
    |--------------------------------------------------------------------------
    | Default Target Path
    |--------------------------------------------------------------------------
    |
    | Default path to redirect after successful auto-login.
    |
    */
    'default_target_path' => '/admin/dashboard',
];
```

## Usage

### 1. Metadata Collection Script

To enable real-time metadata collection, include the following directive in your base layout file (e.g., `layout.blade.php`) before the closing `</body>` tag:

```blade
@include('ai-agent-helper::metadata-script')
```

This script will collect information about the current page, forms, and navigation, then send it to the parent window (if the app is running inside an iframe).

### 2. Auto-Login Route

The package automatically registers a route at `/agent/auto-login`.

To use this feature, ensure you have set `AGENT_LOGIN_TOKEN` in your `.env` file. The route requires a valid `signature` and `expires` timestamp generated using HMAC-SHA256 with your login token.

Example parameters for the auto-login URL:
- `signature`: HMAC-SHA256 of `target_path|expires`
- `expires`: Unix timestamp
- `target_path`: (Optional) The path to redirect after login

## Security

The auto-login feature is **strictly disabled** outside of the environments specified in `allowed_environments` (defaults to `local` and `development`).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
