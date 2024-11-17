<?php

namespace Application\Providers;

use Infrastructure\Models\AppSession;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Support\Clipboard;

class ApplicationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('application-singleton', function (Application $app) {
            return 'application-singleton';
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
        Clipboard::set('application-secret', 'application-secret');
    }
}
