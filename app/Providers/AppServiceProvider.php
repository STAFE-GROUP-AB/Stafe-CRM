<?php

namespace App\Providers;

use App\Events\CommunicationLogged;
use App\Events\DealStatusChanged;
use App\Events\SurveyResponseSubmitted;
use App\Events\TicketCreated;
use App\Events\TicketResolved;
use App\Listeners\UpdateCustomerHealthScore;
use App\Services\TeamTheme;
use Illuminate\Support\Facades\Event;
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

        $this->registerHealthScoreEvents();
    }

    /**
     * Register customer health score event listeners.
     */
    protected function registerHealthScoreEvents(): void
    {
        Event::listen(
            TicketCreated::class,
            [UpdateCustomerHealthScore::class, 'handleTicketCreated']
        );

        Event::listen(
            TicketResolved::class,
            [UpdateCustomerHealthScore::class, 'handleTicketResolved']
        );

        Event::listen(
            DealStatusChanged::class,
            [UpdateCustomerHealthScore::class, 'handleDealStatusChanged']
        );

        Event::listen(
            SurveyResponseSubmitted::class,
            [UpdateCustomerHealthScore::class, 'handleSurveyResponseSubmitted']
        );

        Event::listen(
            CommunicationLogged::class,
            [UpdateCustomerHealthScore::class, 'handleCommunicationLogged']
        );
    }
}
