<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use App\Services\Payments\Contracts\PaymentGateway;
 use App\Services\Payments\PaymobGateway;
use App\Models\Invoice;
use App\Policies\InvoicePolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    $this->app->bind(PaymentGateway::class, PaymobGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    Model::preventLazyLoading(! app()->isProduction());
    Gate::policy(Invoice::class, InvoicePolicy::class);
    }

}

