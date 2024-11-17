<?php

namespace Domain\Invoicing\Providers;

use Domain\Invoicing\Models\Invoice;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Support\Clipboard;

class InvoiceServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('invoicing-singleton', function (Application $app) {
            return 'invoicing-singleton';
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Invoice::setSecret('invoice-secret');
        Clipboard::set('invoicing-secret', 'invoicing-secret');
    }
}
