<?php

namespace Bohumer\AiStandards;

use Illuminate\Support\ServiceProvider;
use Bohumer\AiStandards\Console\Commands\InstallCommand;

class AiStandardsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
