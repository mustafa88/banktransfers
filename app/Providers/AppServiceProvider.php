<?php

namespace App\Providers;

use App\Models\bank\Banks;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        //

        $share_listbanks = Banks::with(['enterprise','projects'])->get();
        //echo $share_listbanks;
        View::share('share_listbank', $share_listbanks);

    }
}
