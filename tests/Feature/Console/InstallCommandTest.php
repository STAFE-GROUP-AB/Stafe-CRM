<?php

namespace Tests\Feature\Console;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_install_command_creates_admin_user(): void
    {
        $this->artisan('stafecrm:install')
            ->expectsQuestion('Do you want to continue with the installation?', 'yes')
            ->expectsQuestion('Admin user full name', 'Admin User')
            ->expectsQuestion('Admin email address', 'admin@example.com')
            ->expectsQuestion('Admin password (minimum 8 characters)', 'password')
            ->expectsQuestion('Confirm admin password', 'password')
            ->expectsQuestion('Would you like to seed the system with demo data?', 'no')
            ->expectsOutput('🎉 Stafe CRM installation completed successfully!')
            ->assertExitCode(0);

        // Verify admin user was created
        $this->assertDatabaseHas('users', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Verify user can authenticate
        $user = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($user);
    }

    public function test_install_command_with_demo_data(): void
    {
        $this->artisan('stafecrm:install')
            ->expectsQuestion('Do you want to continue with the installation?', 'yes')
            ->expectsQuestion('Admin user full name', 'Admin User')
            ->expectsQuestion('Admin email address', 'admin@example.com')
            ->expectsQuestion('Admin password (minimum 8 characters)', 'password')
            ->expectsQuestion('Confirm admin password', 'password')
            ->expectsQuestion('Would you like to seed the system with demo data?', 'yes')
            ->assertExitCode(0);

        // Verify admin user was created
        $this->assertDatabaseHas('users', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Verify demo users were created
        $this->assertDatabaseHas('users', [
            'email' => 'sales@stafecrm.demo',
        ]);
    }

    public function test_install_command_can_be_cancelled(): void
    {
        $this->artisan('stafecrm:install')
            ->expectsQuestion('Do you want to continue with the installation?', 'no')
            ->expectsOutput('Installation cancelled.')
            ->assertExitCode(0);

        // Verify no users were created
        $this->assertDatabaseCount('users', 0);
    }

    public function test_install_command_handles_existing_users(): void
    {
        // Create an existing user
        User::factory()->create(['email' => 'existing@example.com']);

        $this->artisan('stafecrm:install')
            ->expectsQuestion('Do you want to continue with the installation?', 'yes')
            ->expectsQuestion('Users already exist in the database. Do you want to continue anyway?', 'yes')
            ->expectsQuestion('Admin user full name', 'Admin User')
            ->expectsQuestion('Admin email address', 'admin@example.com')
            ->expectsQuestion('Admin password (minimum 8 characters)', 'password')
            ->expectsQuestion('Confirm admin password', 'password')
            ->expectsQuestion('Would you like to seed the system with demo data?', 'no')
            ->assertExitCode(0);

        // Verify both users exist
        $this->assertDatabaseCount('users', 2);
    }
}