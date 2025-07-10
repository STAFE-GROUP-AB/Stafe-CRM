<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the enterprise security
    | and compliance features of Stafe CRM.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | GDPR Compliance
    |--------------------------------------------------------------------------
    |
    | Configuration for GDPR compliance features including consent management
    | and data protection.
    |
    */
    'gdpr' => [
        'enabled' => env('GDPR_ENABLED', true),
        
        'consent' => [
            'default_expiry_days' => env('GDPR_CONSENT_EXPIRY_DAYS', 365),
            'purposes' => [
                'marketing' => 'Marketing communications and promotions',
                'analytics' => 'Usage analytics and performance monitoring',
                'essential' => 'Essential service functionality',
                'preferences' => 'User preferences and personalization',
            ],
            'legal_bases' => [
                'consent' => 'User consent',
                'contract' => 'Contract performance',
                'legal_obligation' => 'Legal obligation',
                'vital_interests' => 'Vital interests',
                'public_task' => 'Public task',
                'legitimate_interests' => 'Legitimate interests',
            ],
        ],

        'data_requests' => [
            'default_deadline_days' => env('GDPR_REQUEST_DEADLINE_DAYS', 30),
            'auto_approve_portability' => env('GDPR_AUTO_APPROVE_PORTABILITY', false),
            'notification_email' => env('GDPR_NOTIFICATION_EMAIL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Trail Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for comprehensive audit logging and compliance monitoring.
    |
    */
    'audit' => [
        'enabled' => env('AUDIT_ENABLED', true),
        'retention_days' => env('AUDIT_RETENTION_DAYS', 2555), // 7 years default
        'high_risk_retention_days' => env('AUDIT_HIGH_RISK_RETENTION_DAYS', 3653), // 10 years
        
        'log_levels' => [
            'authentication' => env('AUDIT_LOG_AUTH', true),
            'data_access' => env('AUDIT_LOG_DATA_ACCESS', true),
            'data_modification' => env('AUDIT_LOG_DATA_MODIFICATION', true),
            'system_events' => env('AUDIT_LOG_SYSTEM_EVENTS', true),
            'security_events' => env('AUDIT_LOG_SECURITY_EVENTS', true),
        ],

        'sensitive_models' => [
            'App\Models\Contact',
            'App\Models\Company',
            'App\Models\Deal',
            'App\Models\User',
            'App\Models\GdprConsent',
            'App\Models\GdprDataRequest',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Field-Level Encryption
    |--------------------------------------------------------------------------
    |
    | Configuration for granular field encryption settings.
    |
    */
    'encryption' => [
        'enabled' => env('FIELD_ENCRYPTION_ENABLED', true),
        'algorithm' => env('ENCRYPTION_ALGORITHM', 'AES-256-GCM'),
        
        'auto_encrypt_fields' => [
            'ssn',
            'tax_id',
            'bank_account',
            'credit_card',
            'passport_number',
        ],

        'sensitivity_levels' => [
            'low' => 'General business information',
            'medium' => 'Personal identifiable information',
            'high' => 'Sensitive personal data',
            'critical' => 'Highly sensitive financial/medical data',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Single Sign-On (SSO)
    |--------------------------------------------------------------------------
    |
    | Configuration for enterprise SSO integration.
    |
    */
    'sso' => [
        'enabled' => env('SSO_ENABLED', true),
        'force_sso' => env('SSO_FORCE', false), // Disable local login when true
        
        'supported_providers' => [
            'google' => [
                'name' => 'Google',
                'type' => 'oauth',
                'scopes' => ['openid', 'profile', 'email'],
            ],
            'microsoft' => [
                'name' => 'Microsoft',
                'type' => 'oauth',
                'scopes' => ['openid', 'profile', 'email'],
            ],
            'okta' => [
                'name' => 'Okta',
                'type' => 'saml',
            ],
            'azure_ad' => [
                'name' => 'Azure AD',
                'type' => 'oidc',
            ],
        ],

        'default_mapping' => [
            'email' => 'email',
            'name' => 'name',
            'first_name' => 'given_name',
            'last_name' => 'family_name',
        ],

        'session_timeout' => env('SSO_SESSION_TIMEOUT', 480), // 8 hours in minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Retention Policies
    |--------------------------------------------------------------------------
    |
    | Configuration for automated data lifecycle management.
    |
    */
    'data_retention' => [
        'enabled' => env('DATA_RETENTION_ENABLED', true),
        'dry_run' => env('DATA_RETENTION_DRY_RUN', true),
        
        'default_policies' => [
            'inactive_contacts' => [
                'model' => 'App\Models\Contact',
                'retention_days' => 2555, // 7 years
                'action' => 'anonymize',
                'conditions' => ['status' => 'inactive'],
            ],
            'closed_deals' => [
                'model' => 'App\Models\Deal',
                'retention_days' => 2190, // 6 years
                'action' => 'archive',
                'conditions' => ['status' => 'lost'],
            ],
        ],
        
        'warning_days' => env('DATA_RETENTION_WARNING_DAYS', 30),
        'execution_schedule' => env('DATA_RETENTION_SCHEDULE', 'daily'),
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Whitelisting
    |--------------------------------------------------------------------------
    |
    | Configuration for network-level security controls.
    |
    */
    'ip_whitelist' => [
        'enabled' => env('IP_WHITELIST_ENABLED', false),
        'default_action' => env('IP_WHITELIST_DEFAULT_ACTION', 'allow'), // allow or deny
        
        'bypass_routes' => [
            'health-check',
            'status',
            'public/*',
        ],

        'trusted_proxies' => env('TRUSTED_PROXIES', ''),
        'cache_ttl' => env('IP_WHITELIST_CACHE_TTL', 300), // 5 minutes
        
        'default_rules' => [
            // Add default IP rules here if needed
            // [
            //     'ip_address' => '127.0.0.1',
            //     'rule_type' => 'allow',
            //     'description' => 'Localhost access',
            // ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Configuration for security headers and HTTPS enforcement.
    |
    */
    'headers' => [
        'force_https' => env('FORCE_HTTPS', false),
        'hsts_max_age' => env('HSTS_MAX_AGE', 31536000), // 1 year
        'content_security_policy' => env('CSP_ENABLED', true),
        'x_frame_options' => env('X_FRAME_OPTIONS', 'DENY'),
        'x_content_type_options' => env('X_CONTENT_TYPE_OPTIONS', 'nosniff'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compliance Reporting
    |--------------------------------------------------------------------------
    |
    | Settings for compliance reporting and monitoring.
    |
    */
    'reporting' => [
        'enabled' => env('COMPLIANCE_REPORTING_ENABLED', true),
        'schedule' => env('COMPLIANCE_REPORT_SCHEDULE', 'monthly'),
        'recipients' => explode(',', env('COMPLIANCE_REPORT_RECIPIENTS', '')),
        'include_metrics' => [
            'gdpr_requests',
            'audit_events',
            'security_incidents',
            'data_retention_actions',
        ],
    ],
];