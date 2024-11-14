<?php

namespace Application\Providers;

use Infrastructure\Models\AppSession;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('application-layer', function (Application $app) {
            return 'application-layer-singleton';
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        AppSession::setSecret('application-secret');
    }
}
