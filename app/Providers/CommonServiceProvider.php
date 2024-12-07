<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CommonService\ResourceControllerService;

class CommonServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ResourceControllerService::class, function ($app) {
            return new ResourceControllerService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
