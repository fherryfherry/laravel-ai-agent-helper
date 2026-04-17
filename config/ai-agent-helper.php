<?php

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
