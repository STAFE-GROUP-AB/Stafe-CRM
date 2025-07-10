<?php

namespace App\Console\Commands;

use App\Models\DataRetentionPolicy;
use App\Models\FieldEncryptionSetting;
use App\Services\Security\DataRetentionService;
use Illuminate\Console\Command;

class InitializeSecurityFeatures extends Command
{
    protected $signature = 'security:initialize 
                            {--force : Force initialization even if features are already configured}';

    protected $description = 'Initialize enterprise security and compliance features';

    public function handle(): int
    {
        $this->info('🔐 Initializing Enterprise Security & Compliance Features');
        $this->newLine();

        // Check if features are already initialized
        if (!$this->option('force') && $this->isAlreadyInitialized()) {
            $this->warn('Security features appear to be already initialized.');
            $this->warn('Use --force option to reinitialize.');
            return self::SUCCESS;
        }

        $this->initializeEncryptionSettings();
        $this->initializeDataRetentionPolicies();
        $this->displaySecurityStatus();

        $this->newLine();
        $this->info('✅ Security features initialized successfully!');
        $this->newLine();
        $this->line('Next steps:');
        $this->line('1. Review and update config/security.php settings');
        $this->line('2. Configure SSO providers via the admin interface');
        $this->line('3. Set up IP whitelist rules if needed');
        $this->line('4. Review GDPR compliance settings');

        return self::SUCCESS;
    }

    protected function isAlreadyInitialized(): bool
    {
        return FieldEncryptionSetting::exists() || DataRetentionPolicy::exists();
    }

    protected function initializeEncryptionSettings(): void
    {
        $this->task('Setting up field encryption defaults', function () {
            $sensitiveFields = [
                'App\Models\Contact' => [
                    ['field' => 'ssn', 'level' => 'critical'],
                    ['field' => 'tax_id', 'level' => 'high'],
                    ['field' => 'phone', 'level' => 'medium'],
                ],
                'App\Models\Company' => [
                    ['field' => 'tax_id', 'level' => 'high'],
                    ['field' => 'bank_account', 'level' => 'critical'],
                ],
                'App\Models\User' => [
                    ['field' => 'ssn', 'level' => 'critical'],
                    ['field' => 'phone', 'level' => 'medium'],
                ],
            ];

            foreach ($sensitiveFields as $model => $fields) {
                foreach ($fields as $fieldConfig) {
                    FieldEncryptionSetting::updateOrCreate(
                        [
                            'tenant_id' => 1, // Default tenant
                            'model_type' => $model,
                            'field_name' => $fieldConfig['field'],
                        ],
                        [
                            'is_encrypted' => false, // Start disabled for safety
                            'sensitivity_level' => $fieldConfig['level'],
                            'description' => "Auto-configured for {$fieldConfig['field']} field",
                            'is_active' => true,
                        ]
                    );
                }
            }

            return true;
        });
    }

    protected function initializeDataRetentionPolicies(): void
    {
        $this->task('Creating default data retention policies', function () {
            $defaultPolicies = [
                [
                    'name' => 'Inactive Contacts Retention',
                    'model_type' => 'App\Models\Contact',
                    'retention_days' => 2555, // 7 years
                    'action_after_retention' => 'anonymize',
                    'description' => 'Anonymize inactive contacts after 7 years',
                    'conditions' => ['status' => 'inactive'],
                    'warning_days' => 30,
                ],
                [
                    'name' => 'Lost Deals Archive',
                    'model_type' => 'App\Models\Deal',
                    'retention_days' => 2190, // 6 years
                    'action_after_retention' => 'archive',
                    'description' => 'Archive lost deals after 6 years',
                    'conditions' => ['status' => 'lost'],
                    'warning_days' => 30,
                ],
                [
                    'name' => 'Old Audit Logs Cleanup',
                    'model_type' => 'App\Models\SecurityAuditLog',
                    'retention_days' => 2555, // 7 years
                    'action_after_retention' => 'delete',
                    'description' => 'Delete old audit logs after 7 years (except high-risk events)',
                    'conditions' => ['risk_level' => 'low'],
                    'warning_days' => 60,
                ],
            ];

            foreach ($defaultPolicies as $policyData) {
                DataRetentionPolicy::updateOrCreate(
                    [
                        'tenant_id' => 1,
                        'name' => $policyData['name'],
                    ],
                    array_merge($policyData, [
                        'tenant_id' => 1,
                        'is_active' => false, // Start disabled for safety
                    ])
                );
            }

            return true;
        });
    }

    protected function displaySecurityStatus(): void
    {
        $this->newLine();
        $this->info('📊 Security Features Status:');
        
        $this->table(
            ['Feature', 'Status', 'Configuration'],
            [
                ['GDPR Compliance', '✅ Available', 'Configure in admin panel'],
                ['Audit Trails', '✅ Available', 'Auto-enabled with requests'],
                ['Field Encryption', '⚠️ Configured', 'Enable per field in admin'],
                ['SSO Integration', '✅ Available', 'Add providers in admin'],
                ['Data Retention', '⚠️ Configured', 'Enable policies in admin'],
                ['IP Whitelisting', '✅ Available', 'Configure rules as needed'],
            ]
        );

        $this->newLine();
        $this->info('📈 Current Counts:');
        $encryptionSettings = FieldEncryptionSetting::count();
        $retentionPolicies = DataRetentionPolicy::count();
        
        $this->line("• Encryption settings: {$encryptionSettings}");
        $this->line("• Retention policies: {$retentionPolicies}");
    }
}