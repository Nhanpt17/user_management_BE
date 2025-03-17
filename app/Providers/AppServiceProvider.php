<?php

namespace App\Providers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //chinh sua de cho wweb lay anh
        Schema::defaultStringLength(191);

        if (!Storage::exists('public')) {
            Artisan::call('storage:link');
        }
    }
}
