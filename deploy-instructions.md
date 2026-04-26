# Laravel Inventory Application - Shared Hosting Deployment Guide

## Overview
This guide explains how to deploy the Laravel Inventory Management application to a shared hosting environment with a subdomain.

## Prerequisites

### 1. Hosting Requirements
- **PHP 8.2 or higher** (required by Laravel 12)
- **MySQL 5.7+ or MariaDB 10.3+**
- **Apache with mod_rewrite enabled** (most shared hosting has this)
- **SSH access** (optional but recommended)
- **Composer support** (optional, but helpful)

### 2. Before You Begin
1. **Subdomain Setup**: Create a subdomain via your hosting control panel (e.g., `inventory.yourdomain.com`)
2. **Database Creation**: Create a MySQL database and user via cPanel/phpMyAdmin
3. **FTP/Credentials**: Have your FTP or file manager credentials ready

## Deployment Methods

### Method 1: Using the Deployment Script (Recommended)

#### Step 1: Run the Deployment Script
```bash
# Make the script executable
chmod +x deploy-shared-hosting.sh

# Run the script
./deploy-shared-hosting.sh
```

This will create a folder named `inventory-app-deploy-YYYYMMDD_HHMMSS` containing:
- All application files (excluding development files)
- `.env.example` template
- `DEPLOYMENT-README.md` with instructions
- Helper PHP scripts for key generation and migrations

#### Step 2: Upload to Hosting
1. **Zip the folder** (optional but faster):
   ```bash
   zip -r inventory-app-deploy.zip inventory-app-deploy-*/
   ```

2. **Upload via FTP**:
   - Connect to your hosting via FTP (FileZilla, WinSCP, etc.)
   - Navigate to your subdomain directory (usually `public_html/subdomain` or `subdomain.yourdomain.com`)
   - Upload the entire folder or zip file
   - If uploading zip, extract it via cPanel File Manager

3. **Set Correct Directory Structure**:
   Ensure files are in the subdomain's root directory, not in a subfolder.

### Method 2: Manual Deployment

#### Step 1: Prepare Files Locally
1. Copy all files except:
   - `.git`, `.gitignore`, `.editorconfig`
   - `.env` (but keep `.env.example`)
   - `node_modules/`, `vendor/` (unless you'll upload them)
   - `storage/*.key`, `storage/pail/`
   - `tests/`, `*.log` files
   - Development configuration files

2. Create necessary directories if missing:
   ```bash
   mkdir -p storage/framework/{cache,sessions,views}
   mkdir -p storage/logs
   mkdir -p bootstrap/cache
   ```

#### Step 2: Upload Files
Upload all prepared files to your subdomain directory via FTP or cPanel File Manager.

## Configuration Steps

### 1. Environment Configuration
1. In your hosting file manager, locate the uploaded files
2. Rename `.env.example` to `.env`
3. Edit `.env` with your configuration:

```env
APP_NAME="Inventory Management"
APP_ENV=production
APP_KEY=  # Will be generated in next step
APP_DEBUG=false
APP_URL=https://inventory.yourdomain.com  # Your actual subdomain URL

DB_CONNECTION=mysql
DB_HOST=localhost  # Usually localhost for shared hosting
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# Mail configuration (optional)
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. Generate Application Key

#### Option A: Via SSH (if available)
```bash
php artisan key:generate
```

#### Option B: Via PHP Script
Use the provided `generate_key.php` script:
1. Upload `generate_key.php` to your subdomain root
2. Visit `https://inventory.yourdomain.com/generate_key.php`
3. Delete the file immediately after use

#### Option C: Via cPanel Terminal
If your hosting provides a web terminal, run:
```bash
cd /home/username/public_html/subdomain
php artisan key:generate
```

### 3. Set File Permissions
Set the following permissions via cPanel File Manager or FTP:
- `storage/` → **755** (recursive)
- `bootstrap/cache/` → **755** (recursive)
- All files in `storage/` and `bootstrap/cache/` should be writable by web server

### 4. Install Dependencies

#### Option A: If Composer is available on hosting
```bash
composer install --no-dev --optimize-autoloader
```

#### Option B: Upload vendor directory from local
1. On your local machine, run:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
2. Upload the entire `vendor/` directory to your hosting

#### Option C: Use pre-built vendor (contact developer)
If you don't have composer locally, you may need to get the vendor directory from the developer.

### 5. Run Database Migrations

#### Option A: Via SSH
```bash
php artisan migrate --force
```

#### Option B: Via PHP Script
Use the provided `run_migrations.php` script:
1. Upload `run_migrations.php` to your subdomain root
2. Visit `https://inventory.yourdomain.com/run_migrations.php`
3. Delete the file immediately after use

#### Option C: Via cPanel Terminal
```bash
cd /home/username/public_html/subdomain
php artisan migrate --force
```

### 6. Seed Database (Create Admin User)
```bash
php artisan db:seed --class=DatabaseSeeder
```

## Web Server Configuration

### Apache Configuration (Most Shared Hosting)
Ensure your `.htaccess` file in the **root directory** contains:

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

### If Application is in Subdirectory
If you can't point the domain to the `public/` folder, create an `.htaccess` in the root:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

## Testing Your Deployment

### 1. Verify Installation
Visit your subdomain: `https://inventory.yourdomain.com`

You should see:
- Login page or dashboard (if seeded)
- No PHP errors displayed (white screen indicates issues)

### 2. Check Logs for Errors
If you encounter issues, check:
- `storage/logs/laravel.log` (via file manager)
- PHP error logs in cPanel
- Browser console for JavaScript errors

### 3. Test Key Features
1. Login with default admin credentials (if seeded)
2. Create a test product
3. Test inventory management functions

## Troubleshooting Common Issues

### 1. White Screen/Blank Page
**Causes**:
- Missing or incorrect `.env` file
- Wrong file permissions
- PHP version incompatible
- Missing vendor dependencies

**Solutions**:
1. Check `storage/logs/laravel.log` for errors
2. Verify `.env` file exists and has correct database credentials
3. Ensure `storage/` and `bootstrap/cache/` are writable (755)
4. Check PHP version via cPanel (must be 8.2+)

### 2. Database Connection Error
**Solutions**:
1. Verify database credentials in `.env`
2. Check if database host is `localhost` (usually for shared hosting)
3. Ensure database user has proper privileges
4. Confirm database exists via phpMyAdmin

### 3. 500 Internal Server Error
**Solutions**:
1. Check `.htaccess` file syntax
2. Verify PHP memory limit (increase to at least 256M)
3. Check for syntax errors in PHP files
4. Look at hosting error logs in cPanel

### 4. "No application encryption key has been specified"
**Solution**:
Run: `php artisan key:generate` or use the `generate_key.php` script

### 5. File Upload/Storage Issues
**Solution**:
1. Set `storage/` permissions to 755 recursively
2. Check `storage/app/public/` exists and is writable
3. Verify `public/storage` symlink exists (or create it)

## Security Considerations

### 1. Production Security
- Set `APP_DEBUG=false` in `.env`
- Keep `APP_ENV=production`
- Use HTTPS if available
- Regularly update dependencies

### 2. File Security
- Delete `generate_key.php` and `run_migrations.php` after use
- Never commit `.env` to version control
- Restrict access to sensitive directories via `.htaccess`:

```apache
# Deny access to .env
<Files ".env">
    Order Allow,Deny
    Deny from all
</Files>

# Deny access to storage (except public)
<IfModule mod_rewrite.c>
    RewriteRule ^storage/.*$ - [F,L]
</IfModule>
```

### 3. Database Security
- Use strong database passwords
- Regular database backups
- Limit database user privileges

## Maintenance

### 1. Regular Updates
```bash
composer update
php artisan migrate
```

### 2. Backup Strategy
1. **Database**: Export via phpMyAdmin regularly
2. **Files**: Backup entire application directory
3. **.env**: Keep a secure copy of your configuration

### 3. Monitoring
- Check `storage/logs/laravel.log` regularly
- Monitor disk space usage
- Set up error notifications if possible

## Support Resources

### 1. Laravel Documentation
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Configuration](https://laravel.com/docs/configuration)

### 2. Hosting Support
- Contact your hosting provider for PHP/MySQL issues
- Check hosting knowledge base for Laravel-specific guides

### 3. Application Documentation
- Check `GUIDELINE.md` for application-specific guidance
- Review database schema in `database/migrations/`

## Quick Reference Commands

```bash
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --class=DatabaseSeeder

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

**Note**: After successful deployment, delete any helper scripts (`generate_key.php`, `run_migrations.php`) and the deployment script itself from your hosting for security.

For additional help, consult the Laravel community or your hosting provider's support team.