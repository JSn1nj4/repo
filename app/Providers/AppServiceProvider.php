<?php

namespace App\Providers;

use App\Actions\CheckRemoteSourceExists;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            CheckRemoteSourceExists::class,
            fn() => new CheckRemoteSourceExists()
        );
    }
}
