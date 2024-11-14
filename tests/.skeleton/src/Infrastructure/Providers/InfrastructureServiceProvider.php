<?php

namespace Infrastructure\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Support\Clipboard;

class InfrastructureServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('infrastructure-layer', function (Application $app) {
            return 'infrastructure-layer-singleton';
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Clipboard::set('secret', 'infrastructure-secret');
    }
}
