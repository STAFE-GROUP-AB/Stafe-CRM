<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SLA Target Times (in minutes)
    |--------------------------------------------------------------------------
    |
    | Define SLA targets for first response and resolution times based on
    | ticket priority levels. Times are in minutes.
    |
    */
    'targets' => [
        'first_response' => [
            'urgent' => 60,      // 1 hour
            'high' => 240,       // 4 hours
            'medium' => 480,     // 8 hours
            'low' => 1440,       // 24 hours
        ],
        'resolution' => [
            'urgent' => 240,     // 4 hours
            'high' => 1440,      // 24 hours
            'medium' => 2880,    // 48 hours
            'low' => 10080,      // 7 days
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Hours Configuration
    |--------------------------------------------------------------------------
    |
    | Define business hours for SLA calculations. Set enabled to false to
    | calculate SLA based on 24/7 time.
    |
    */
    'business_hours' => [
        'enabled' => true,
        'timezone' => 'Europe/Stockholm',
        'schedule' => [
            'monday' => ['start' => '08:00', 'end' => '17:00'],
            'tuesday' => ['start' => '08:00', 'end' => '17:00'],
            'wednesday' => ['start' => '08:00', 'end' => '17:00'],
            'thursday' => ['start' => '08:00', 'end' => '17:00'],
            'friday' => ['start' => '08:00', 'end' => '17:00'],
            'saturday' => null,
            'sunday' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Holiday Exclusions
    |--------------------------------------------------------------------------
    |
    | Define dates to exclude from SLA calculations (holidays, etc.)
    | Format: 'YYYY-MM-DD' or 'MM-DD' for recurring annual holidays
    |
    */
    'holidays' => [
        // Swedish holidays (recurring)
        '01-01', // New Year's Day
        '01-06', // Epiphany
        '05-01', // Labor Day
        '06-06', // National Day
        '12-24', // Christmas Eve
        '12-25', // Christmas Day
        '12-26', // Boxing Day
        '12-31', // New Year's Eve
        // Add specific year holidays as needed
        // '2025-04-18', // Good Friday 2025
        // '2025-04-21', // Easter Monday 2025
    ],

    /*
    |--------------------------------------------------------------------------
    | SLA Warning Threshold
    |--------------------------------------------------------------------------
    |
    | Percentage of SLA time remaining when a warning should be shown.
    | For example, 0.25 means warn when 25% of SLA time remains.
    |
    */
    'warning_threshold' => 0.25,

    /*
    |--------------------------------------------------------------------------
    | SLA Status Labels (Swedish)
    |--------------------------------------------------------------------------
    */
    'status_labels' => [
        'on_track' => 'Inom SLA',
        'warning' => 'SLA-varning',
        'breached' => 'SLA-brott',
        'paused' => 'SLA pausad',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pause SLA for Certain Statuses
    |--------------------------------------------------------------------------
    |
    | Define ticket statuses where SLA timer should be paused.
    |
    */
    'pause_statuses' => [
        'waiting_customer',
    ],
];
