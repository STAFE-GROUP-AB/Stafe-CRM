<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class InstallStafeCrmCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stafecrm:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Stafe CRM and create the initial admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Welcome to Stafe CRM Installation!');
        $this->info('This command will set up your CRM system and create an admin user.');
        $this->newLine();

        if (!$this->confirm('Do you want to continue with the installation?')) {
            $this->info('Installation cancelled.');
            return;
        }

        // Check if users already exist
        if (User::count() > 0) {
            if (!$this->confirm('Users already exist in the database. Do you want to continue anyway?')) {
                $this->info('Installation cancelled.');
                return;
            }
        }

        $this->newLine();
        $this->info('Creating admin user...');

        // Get admin user details
        $name = $this->ask('Admin user full name', 'Admin User');
        $email = $this->getValidEmail();
        $password = $this->getValidPassword();

        // Create admin user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $this->info("✓ Admin user created successfully!");
            $this->info("  Name: {$user->name}");
            $this->info("  Email: {$user->email}");
        } catch (\Exception $e) {
            $this->error('Failed to create admin user: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Ask about demo data
        if ($this->confirm('Would you like to seed the system with demo data?')) {
            $this->info('Seeding demo data...');
            
            try {
                $this->call('db:seed', ['--class' => 'DemoDataSeeder']);
                $this->info('✓ Demo data seeded successfully!');
            } catch (\Exception $e) {
                $this->warn('Demo data seeding failed: ' . $e->getMessage());
                $this->warn('You can run "php artisan db:seed --class=DemoDataSeeder" later to seed demo data.');
            }
        }

        $this->newLine();
        $this->info('🎉 Stafe CRM installation completed successfully!');
        $this->info('You can now login with the admin credentials you provided.');
        $this->newLine();
        $this->info('Next steps:');
        $this->info('1. Start the development server: php artisan serve');
        $this->info('2. Visit your application in the browser');
        $this->info('3. Login with your admin credentials');
        $this->newLine();

        return 0;
    }

    /**
     * Get a valid email address from user input.
     */
    private function getValidEmail(): string
    {
        do {
            $email = $this->ask('Admin email address');
            
            $validator = Validator::make(['email' => $email], [
                'email' => ['required', 'email', 'unique:users,email']
            ]);

            if ($validator->fails()) {
                $this->error('Invalid email: ' . $validator->errors()->first('email'));
                $email = null;
            }
        } while (!$email);

        return $email;
    }

    /**
     * Get a valid password from user input.
     */
    private function getValidPassword(): string
    {
        do {
            $password = $this->secret('Admin password (minimum 8 characters)');
            $passwordConfirmation = $this->secret('Confirm admin password');

            $validator = Validator::make([
                'password' => $password,
                'password_confirmation' => $passwordConfirmation
            ], [
                'password' => ['required', 'confirmed', Rules\Password::defaults()]
            ]);

            if ($validator->fails()) {
                $this->error('Invalid password: ' . $validator->errors()->first('password'));
                $password = null;
            }
        } while (!$password);

        return $password;
    }
}