<?php

return [

    'backup' => [

        /*
         * The name of this application.
         * IMPORTANT: Use empty string to avoid folder creation issues with Google Drive.
         * The folder structure is pre-created manually in Google Drive.
         */
        'name' => '',

        'source' => [

            'files' => [

                'include' => [
                    base_path(),
                ],

                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                    storage_path('app/backups'),
                    storage_path('logs'),
                    storage_path('framework/cache'),
                    storage_path('framework/sessions'),
                    storage_path('framework/views'),
                    base_path('.git'),
                ],

                'follow_links' => false,

                'ignore_unreadable_directories' => true,

                'relative_path' => null,
            ],

            'databases' => [
                'mysql',
            ],
        ],

        /*
         * Database dump compression for smaller backup files.
         */
        'database_dump_compressor' => \Spatie\DbDumper\Compressors\GzipCompressor::class,

        'database_dump_file_extension' => '',

        'destination' => [

            'filename_prefix' => '',

            'disks' => [
                'backups',  // Local secure storage
                'google',   // Google Drive cloud storage
            ],
        ],

        'temporary_directory' => storage_path('app/backup-temp'),

        'password' => env('BACKUP_ARCHIVE_PASSWORD'),

        'encryption' => 'default',
    ],

    'notifications' => [

        'notifications' => [
            \Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class => ['mail'],
        ],

        'notifiable' => \Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
            'to' => env('BACKUP_NOTIFICATION_EMAIL', 'admin@example.com'),

            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Backup System'),
            ],
        ],

        'slack' => [
            'webhook_url' => '',
            'channel' => null,
            'username' => null,
            'icon' => null,
        ],

        'discord' => [
            'webhook_url' => '',
            'username' => null,
            'avatar_url' => null,
        ],
    ],

    'monitor_backups' => [
        [
            'name' => '',
            'disks' => ['backups', 'google'],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
    ],

    'cleanup' => [

        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'default_strategy' => [

            'keep_all_backups_for_days' => 7,

            'keep_daily_backups_for_days' => 14,

            'keep_weekly_backups_for_weeks' => 0,
            'keep_monthly_backups_for_months' => 0,
            'keep_yearly_backups_for_years' => 0,

            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],

        'tries' => 1,

        'retry_delay' => 0,
    ],

];
