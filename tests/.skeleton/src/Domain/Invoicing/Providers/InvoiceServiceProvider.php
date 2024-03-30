<?php

namespace Domain\Invoicing\Providers;

use Domain\Invoicing\Models\Invoice;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class InvoiceServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('invoicing', function (Application $app) {
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
    }
}
