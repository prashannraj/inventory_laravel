# Quick Deployment Checklist for Shared Hosting

## Pre-Deployment
- [ ] Verify hosting meets requirements: PHP 8.2+, MySQL 5.7+
- [ ] Create subdomain in hosting control panel
- [ ] Create MySQL database and user
- [ ] Note database credentials: host, name, username, password

## File Preparation
- [ ] Use `deploy-windows.bat` (Windows) or `deploy-shared-hosting.sh` (Linux/Mac) to create deployment package
- [ ] Or manually copy files excluding: `.git`, `node_modules`, `vendor`, `.env`, `tests/`, development configs
- [ ] Ensure `storage/` and `bootstrap/cache/` directories exist and are writable

## Upload to Hosting
- [ ] Upload all files to subdomain directory via FTP/cPanel
- [ ] Set permissions: `storage/` → 755, `bootstrap/cache/` → 755

## Configuration
- [ ] Rename `.env.example` to `.env`
- [ ] Edit `.env` with your:
  - `APP_URL` (your subdomain URL)
  - Database credentials
  - `APP_DEBUG=false`
  - `APP_ENV=production`
- [ ] Generate application key: `php artisan key:generate` or use `generate_key.php`

## Database Setup
- [ ] Run migrations: `php artisan migrate --force` or use `run_migrations.php`
- [ ] Seed database: `php artisan db:seed --class=DatabaseSeeder`

## Dependencies
- [ ] Install Composer dependencies: `composer install --no-dev --optimize-autoloader`
- [ ] Or upload `vendor/` directory from local if Composer not available on hosting

## Web Server
- [ ] Ensure web server points to `public/` directory
- [ ] Verify `.htaccess` file is present and correct
- [ ] Test application at your subdomain URL

## Security
- [ ] Delete helper scripts (`generate_key.php`, `run_migrations.php`) after use
- [ ] Keep `APP_DEBUG=false` in production
- [ ] Use HTTPS if available

## Testing
- [ ] Visit subdomain to verify application loads
- [ ] Login with default/admin credentials
- [ ] Test key features: add product, manage inventory

## Troubleshooting
- Check `storage/logs/laravel.log` for errors
- Verify file permissions
- Confirm database connection
- Check PHP version compatibility (8.2+)

## Files Created for Deployment
1. `deploy-instructions.md` - Complete deployment guide
2. `deploy-shared-hosting.sh` - Linux/Mac deployment script
3. `deploy-windows.bat` - Windows deployment script
4. `QUICK-DEPLOYMENT-CHECKLIST.md` - This quick checklist

## Support
- Refer to `deploy-instructions.md` for detailed steps
- Check Laravel documentation for framework-specific issues
- Contact hosting provider for server-related problems