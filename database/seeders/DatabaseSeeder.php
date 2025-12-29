<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user with personal team (or get existing)
        $user = User::where('email', 'admin@stafe.com')->first();

        if (!$user) {
            $user = User::factory()->withPersonalTeam()->create([
                'name' => 'Admin User',
                'email' => 'admin@stafe.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Seed all demo data
        $this->call([
            PipelineStageSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            AiProvidersSeeder::class,
            EmailTemplateSeeder::class,
            IntegrationSeeder::class,
            SubscriptionPlanSeeder::class,
            DemoDataSeeder::class,
            SampleContactsSeeder::class,
        ]);

        $this->command->info('✅ Database seeded successfully with demo data!');
        $this->command->info('🔐 Login credentials:');
        $this->command->info('   Email: admin@stafe.com');
        $this->command->info('   Password: password');
    }
}
