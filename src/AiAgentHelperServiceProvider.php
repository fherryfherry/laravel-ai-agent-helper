<?php

namespace CRUDBooster\AiAgentHelper;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AiAgentHelperServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ai-agent-helper.php', 'ai-agent-helper');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ai-agent-helper.php' => config_path('ai-agent-helper.php'),
            ], 'ai-agent-helper-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/ai-agent-helper'),
            ], 'ai-agent-helper-views');
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ai-agent-helper');

        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        Route::middleware(['web'])
            ->group(function () {
                Route::get('/agent/auto-login', [\CRUDBooster\AiAgentHelper\Http\Controllers\AiAgentController::class, 'autoLogin'])
                    ->name('agent.auto-login');
            });
    }
}
