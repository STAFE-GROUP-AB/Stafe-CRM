<?php

use App\Services\TeamTheme;

if (! function_exists('team_theme')) {
    /**
     * Get the current team's theme instance
     */
    function team_theme(): TeamTheme
    {
        return TeamTheme::current();
    }
}
