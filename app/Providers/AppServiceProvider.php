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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.tabler', function ($view) {
            $view->with('global_account_types', \App\Models\AccountType::where('is_active', true)->orderBy('name')->get());
        });
    }
}
