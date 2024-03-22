<?php

namespace App\Providers;

use App\Contracts\WeatherServiceInterface;
use App\Services\WeatherApiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            WeatherServiceInterface::class,
            WeatherApiService::class
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
