# Stafe CRM Installation Guide

## Quick Installation

The easiest way to get Stafe CRM up and running is using the installation command:

```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create database (SQLite)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Install Stafe CRM (creates admin user + optional demo data)
php artisan stafecrm:install
```

## Installation Command Features

The `php artisan stafecrm:install` command will:

1. **Create Admin User**: Prompts for admin user details
   - Full name
   - Email address (with validation)
   - Password (with confirmation and strength requirements)

2. **Optional Demo Data**: Asks if you want to seed demo data including:
   - Demo users (sales@stafecrm.demo, marketing@stafecrm.demo, success@stafecrm.demo)
   - Demo companies (Tech Solutions Inc., Global Marketing Agency, Enterprise Corp)
   - Demo contacts linked to companies
   - Demo deals with various stages and values
   - Demo tasks with different priorities and statuses

3. **System Setup**: Ensures the system is ready to use

## Authentication Features

- **Standard Laravel Auth**: Uses Laravel's built-in authentication system
- **Login/Registration**: Proper validation and error handling
- **Password Security**: Follows Laravel password defaults
- **Session Management**: Secure session handling with regeneration
- **Remember Me**: Optional persistent login
- **Error Handling**: User-friendly error messages and validation

## Manual Setup (Alternative)

If you prefer manual setup:

1. Create your admin user directly in the database
2. Run specific seeders: `php artisan db:seed --class=DemoDataSeeder`
3. Access the application at your configured URL

## Next Steps After Installation

1. Start the development server: `php artisan serve`
2. Visit your application in the browser
3. Login with your admin credentials
4. Explore the CRM features with demo data (if seeded)

## Demo Credentials

If you seed demo data, you can also login with:
- Email: `sales@stafecrm.demo` | Password: `password`
- Email: `marketing@stafecrm.demo` | Password: `password`  
- Email: `success@stafecrm.demo` | Password: `password`