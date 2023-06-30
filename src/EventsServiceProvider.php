<?php

namespace Mpietrucha\Events;

use Illuminate\Support\ServiceProvider;

class EventsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Commands\TruncateCommand::class
        ]);
    }

    public function register(): void
    {
    }
}
