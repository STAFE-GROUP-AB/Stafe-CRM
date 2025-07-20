# Installation & Setup Guide

This comprehensive guide covers the installation and initial configuration of Stafe CRM for development, staging, and production environments.

## System Requirements

### Minimum Requirements
- **PHP**: 8.2 or higher
- **Memory**: 512MB RAM minimum, 2GB recommended
- **Storage**: 1GB free disk space minimum
- **Database**: MySQL 8.0+, PostgreSQL 13+, or SQLite 3.35+
- **Node.js**: 16.x or higher
- **NPM/Yarn**: Latest stable version

### Recommended Requirements
- **PHP**: 8.3 with OPcache enabled
- **Memory**: 4GB RAM or higher
- **Storage**: 10GB+ free disk space for production
- **Database**: MySQL 8.0+ or PostgreSQL 15+
- **Web Server**: Nginx 1.20+ or Apache 2.4+
- **Redis**: For queue processing and caching

### PHP Extensions
Ensure the following PHP extensions are installed:
```bash
# Required extensions
php-mbstring
php-xml
php-curl
php-zip
php-gd
php-pdo
php-json
php-tokenizer
php-fileinfo
php-openssl

# Database-specific extensions
php-mysql      # For MySQL
php-pgsql      # For PostgreSQL
php-sqlite3    # For SQLite

# Recommended extensions
php-redis      # For Redis caching/queues
php-imagick    # For advanced image processing
php-intl       # For internationalization
```

## Installation Methods

### Method 1: Git Clone (Recommended for Development)

```bash
# Clone the repository
git clone https://github.com/STAFE-GROUP-AB/Stafe-CRM.git
cd Stafe-CRM

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create storage symlink
php artisan storage:link
```

### Method 2: Composer Create-Project

```bash
# Create new project via Composer
composer create-project stafe/crm stafe-crm

# Navigate to project directory
cd stafe-crm

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Method 3: Docker (Recommended for Production)

```bash
# Clone repository
git clone https://github.com/STAFE-GROUP-AB/Stafe-CRM.git
cd Stafe-CRM

# Start with Docker Compose
docker-compose up -d

# Run initial setup
docker-compose exec app php artisan migrate --seed
```

## Environment Configuration

### Database Configuration

#### SQLite (Default for Development)
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

#### MySQL
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stafe_crm
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### PostgreSQL
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=stafe_crm
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Email Configuration

#### SMTP (Recommended)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=crm@your-company.com
MAIL_FROM_NAME="Your Company CRM"
```

#### SendGrid
```env
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your-sendgrid-api-key
MAIL_FROM_ADDRESS=crm@your-company.com
MAIL_FROM_NAME="Your Company CRM"
```

#### Postmark
```env
MAIL_MAILER=postmark
POSTMARK_TOKEN=your-postmark-token
MAIL_FROM_ADDRESS=crm@your-company.com
MAIL_FROM_NAME="Your Company CRM"
```

### Queue Configuration

#### Database Queues (Default)
```env
QUEUE_CONNECTION=database
```

#### Redis Queues (Recommended for Production)
```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
```

### Cache Configuration

#### File Cache (Default)
```env
CACHE_DRIVER=file
```

#### Redis Cache (Recommended for Production)
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=1
```

### Security Configuration

```env
# Application Security
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-generated-key

# Session Security
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

# Additional Security Headers
FORCE_HTTPS=true
HSTS_MAX_AGE=31536000
CSP_ENABLED=true
```

## Database Setup

### Run Migrations

```bash
# Run database migrations
php artisan migrate

# Run with sample data (recommended for development)
php artisan migrate --seed

# Run specific seeder
php artisan db:seed --class=DemoDataSeeder
```

### Available Seeders

- **`DatabaseSeeder`** - Core system data (roles, permissions, pipeline stages)
- **`DemoDataSeeder`** - Sample companies, contacts, and deals for testing
- **`UserSeeder`** - Default admin user and sample team members
- **`WorkflowSeeder`** - Example automation workflows and templates

### Custom Database Configuration

#### Create Custom Database
```sql
-- MySQL
CREATE DATABASE stafe_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'stafe_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON stafe_crm.* TO 'stafe_user'@'localhost';
FLUSH PRIVILEGES;

-- PostgreSQL
CREATE DATABASE stafe_crm;
CREATE USER stafe_user WITH PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE stafe_crm TO stafe_user;
```

## Web Server Configuration

### Nginx Configuration

Create `/etc/nginx/sites-available/stafe-crm`:

```nginx
server {
    listen 80;
    listen [::]:80;
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
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/stafe-crm /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Apache Configuration

Create `.htaccess` in the public directory:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## Production Deployment

### 1. Optimize for Production

```bash
# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Compile assets
npm run production
```

### 2. Set Proper Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/stafe-crm

# Set directory permissions
sudo find /var/www/stafe-crm -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/stafe-crm -type f -exec chmod 644 {} \;

# Set storage and cache permissions
sudo chmod -R 775 /var/www/stafe-crm/storage
sudo chmod -R 775 /var/www/stafe-crm/bootstrap/cache
```

### 3. Configure Queue Workers

Create systemd service `/etc/systemd/system/stafe-crm-worker.service`:

```ini
[Unit]
Description=Stafe CRM Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/stafe-crm
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

Enable and start the service:
```bash
sudo systemctl enable stafe-crm-worker
sudo systemctl start stafe-crm-worker
```

### 4. Configure Cron Jobs

Add to crontab:
```bash
* * * * * cd /var/www/stafe-crm && php artisan schedule:run >> /dev/null 2>&1
```

### 5. SSL Configuration

Using Certbot for Let's Encrypt:
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

## Advanced Configuration

### Multi-Tenancy Setup

```env
# Enable multi-tenancy
MULTI_TENANCY_ENABLED=true
TENANT_MODEL=App\Models\Tenant
DEFAULT_TENANT_DOMAIN=app.your-domain.com
```

### AI & Machine Learning

```env
# OpenAI Integration
OPENAI_API_KEY=your-openai-api-key
OPENAI_ORGANIZATION=your-org-id

# AI Features
AI_LEAD_SCORING_ENABLED=true
AI_CONVERSATION_ANALYSIS_ENABLED=true
AI_PREDICTIVE_FORECASTING_ENABLED=true
```

### Communication Integrations

```env
# Twilio (SMS/Voice)
TWILIO_SID=your-account-sid
TWILIO_TOKEN=your-auth-token
TWILIO_FROM=+1234567890

# Slack Integration
SLACK_BOT_TOKEN=xoxb-your-bot-token
SLACK_SIGNING_SECRET=your-signing-secret
```

### Analytics & Monitoring

```env
# Application Monitoring
SENTRY_DSN=your-sentry-dsn

# Google Analytics
GOOGLE_ANALYTICS_ID=GA-XXXXXXXXX

# Performance Monitoring
PERFORMANCE_MONITORING_ENABLED=true
SLOW_QUERY_THRESHOLD=1000
```

## Troubleshooting

### Common Installation Issues

#### Permission Errors
```bash
# Fix storage permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

#### Database Connection Issues
```bash
# Test database connection
php artisan migrate:status

# Reset database
php artisan migrate:fresh --seed
```

#### Asset Compilation Issues
```bash
# Clear Node modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Clear Laravel caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### Queue Issues
```bash
# Restart queue workers
php artisan queue:restart

# Clear failed jobs
php artisan queue:clear
```

### Debugging Tools

#### Enable Debug Mode
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

#### Laravel Telescope (Development)
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

#### Log Analysis
```bash
# View latest logs
tail -f storage/logs/laravel.log

# Search for errors
grep "ERROR" storage/logs/laravel.log
```

## Performance Optimization

### Database Optimization

```bash
# Index optimization
php artisan db:optimize

# Query optimization
php artisan cache:queries
```

### Application Optimization

```bash
# Enable OPcache
sudo nano /etc/php/8.3/fpm/php.ini
# opcache.enable=1
# opcache.memory_consumption=256
# opcache.max_accelerated_files=10000

# Configure PHP-FPM
sudo nano /etc/php/8.3/fpm/pool.d/www.conf
# pm = dynamic
# pm.max_children = 50
# pm.start_servers = 5
```

### Monitoring

```bash
# Monitor queue status
php artisan queue:monitor

# Check system status
php artisan system:status

# Performance metrics
php artisan performance:report
```

## Backup & Recovery

### Database Backup

```bash
# MySQL backup
mysqldump -u username -p stafe_crm > backup.sql

# PostgreSQL backup
pg_dump stafe_crm > backup.sql

# Automated backup script
php artisan backup:create
```

### File Backup

```bash
# Backup storage files
tar -czf storage-backup.tar.gz storage/

# Backup entire application
tar -czf stafe-crm-backup.tar.gz /var/www/stafe-crm/
```

---

This installation guide provides comprehensive coverage for deploying Stafe CRM in various environments. For additional help, consult the [troubleshooting documentation](../troubleshooting.md) or contact our support team.