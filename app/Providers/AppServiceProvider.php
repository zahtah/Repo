<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\HeadingRowFormatter;
use Maatwebsite\Excel\Imports\HeadingRowFormatter as ImportsHeadingRowFormatter;

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
         ImportsHeadingRowFormatter::default('none'); // هدرها تغییر نکنند
    }
}
