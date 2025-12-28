# Stafe CRM Installation Guide

## Requirements

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ and NPM
- Database (SQLite, MySQL, or PostgreSQL)
- Web server (Apache, Nginx, or PHP built-in server)

## Quick Installation

The fastest way to get Stafe CRM up and running:

```bash
# Clone the repository
git clone https://github.com/STAFE-GROUP-AB/Stafe-CRM.git
cd Stafe-CRM

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create database (SQLite - easiest for testing)
touch database/database.sqlite

# Run migrations to create database structure
php artisan migrate

# Seed the database with demo data (recommended for testing)
php artisan db:seed

# Build frontend assets
npm run build

# Start the development server
php artisan serve
```

Visit http://localhost:8000 and login with:
- **Email**: admin@stafe.com
- **Password**: password

## Jetstream Features

Stafe CRM now includes Laravel Jetstream with these powerful features:

### 🔐 Two-Factor Authentication
Enable 2FA from your profile settings for enhanced security. Compatible with any TOTP authenticator app (Google Authenticator, Authy, etc.).

### 👥 Team Management
- Create multiple teams for different projects or departments
- Invite members via email with automatic notifications
- Assign roles: owner, admin, or member
- Switch between teams seamlessly
- Each user gets a personal team automatically

### 🎨 Profile Management
- Upload and manage profile photos
- Update personal information
- Change password securely
- View and logout other browser sessions
- Download your data or delete account (GDPR compliant)

### 🔑 API Token Management
Create personal access tokens for API authentication from the API Tokens section. Assign specific permissions to each token.

## Production Setup

For production deployment:

### 1. Environment Configuration

Update your `.env` file:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database (MySQL/PostgreSQL recommended for production)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stafe_crm
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="Stafe CRM"

# Cache & Session (Redis recommended for production)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 2. Optimize for Production

```bash
# Install production dependencies only
composer install --optimize-autoloader --no-dev

# Build optimized frontend assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
php artisan migrate --force

# Start queue worker (for background jobs)
php artisan queue:work --daemon
```

### 3. Web Server Configuration

#### Nginx Example

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/stafe-crm/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 4. SSL Certificate (Required for Production)

```bash
# Using Let's Encrypt (free)
sudo apt-get install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### 5. Set Proper Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/stafe-crm

# Set permissions
sudo find /var/www/stafe-crm -type f -exec chmod 644 {} \;
sudo find /var/www/stafe-crm -type d -exec chmod 755 {} \;

# Storage and cache writable
sudo chmod -R 775 /var/www/stafe-crm/storage
sudo chmod -R 775 /var/www/stafe-crm/bootstrap/cache
```

### 6. Setup Supervisor (For Queue Workers)

Create `/etc/supervisor/conf.d/stafe-crm.conf`:

```ini
[program:stafe-crm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/stafe-crm/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/stafe-crm/storage/logs/worker.log
stopwaitsecs=3600
```

Then start it:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start stafe-crm-worker:*
```

## Database Options

### SQLite (Development Only)

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### MySQL (Recommended for Production)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stafe_crm
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### PostgreSQL (Also Recommended)

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=stafe_crm
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Troubleshooting

### Permission Issues

If you encounter permission errors:

```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Mix Manifest Not Found

If you see "Mix manifest not found" error:

```bash
npm install
npm run build
```

### Database Connection Failed

Check your database credentials in `.env` and ensure the database exists:

```bash
# For MySQL
mysql -u root -p
CREATE DATABASE stafe_crm;
```

### 500 Server Error

Enable debug mode temporarily to see the error:

```env
APP_DEBUG=true
```

Then check `storage/logs/laravel.log` for details.

## Security Best Practices

1. **Never commit `.env` file** - It contains sensitive credentials
2. **Use strong passwords** - Especially for database and admin accounts
3. **Enable 2FA** - For all admin users
4. **Keep updated** - Regularly update dependencies with `composer update`
5. **Use HTTPS** - Always in production
6. **Regular backups** - Backup database and storage regularly
7. **Monitor logs** - Check `storage/logs` for suspicious activity

## Updating Stafe CRM

To update to the latest version:

```bash
# Backup first!
php artisan backup:run  # If backup package installed

# Pull latest changes
git pull origin main

# Update dependencies
composer install
npm install

# Run migrations
php artisan migrate --force

# Rebuild assets
npm run build

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Support

- **Email**: andreas@stafegroup.com
- **GitHub Issues**: https://github.com/STAFE-GROUP-AB/Stafe-CRM/issues
- **Documentation**: Check README.md for feature documentation

## License

Stafe CRM is open-source software licensed under the MIT license.
