<?php

namespace App\Providers;

use App\Services\TeamTheme;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TeamTheme::class, function ($app) {
            return TeamTheme::current();
        });
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('theme', team_theme());
        });
    }
}
