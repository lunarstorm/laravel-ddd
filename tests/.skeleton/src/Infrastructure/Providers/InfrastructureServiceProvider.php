<?php

namespace Infrastructure\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Support\Clipboard;

class InfrastructureServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('infrastructure-singleton', function (Application $app) {
            return 'infrastructure-singleton';
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Clipboard::set('infrastructure-secret', 'infrastructure-secret');
    }
}
