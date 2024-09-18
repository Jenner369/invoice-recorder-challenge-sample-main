<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\IXmlVoucherService::class,
            \App\Services\XmlVoucherService::class
        );

        $this->app->bind(
            \App\Contracts\ICurrencyService::class,
            \App\Services\CurrencyService::class
        );

        $this->app->bind(
            \App\Contracts\Vouchers\IGetVoucherService::class,
            \App\Services\VoucherService::class
        );

        $this->app->bind(
            \App\Contracts\Vouchers\IDeleteVoucherService::class,
            \App\Services\VoucherService::class
        );

        $this->app->bind(
            \App\Contracts\Vouchers\IStoreVoucherService::class,
            \App\Services\VoucherService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
