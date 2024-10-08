<?php

namespace App\Providers;

use App\Services\Acquirings\Kassa24;
use App\Services\Contracts\Acquiring;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);

        $this->app->singleton(Acquiring::class, Kassa24::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || config('app.env') === 'development') {
            URL::forceScheme('https');
        }
    }
}
