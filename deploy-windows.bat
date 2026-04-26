@echo off
REM Deployment script for Laravel Inventory Application to Shared Hosting
REM Windows version

echo =========================================
echo Laravel Inventory App - Deployment Prep
echo =========================================

REM Create deployment directory
set timestamp=%date:~10,4%%date:~4,2%%date:~7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set DEPLOY_DIR=inventory-app-deploy-%timestamp%
echo Creating deployment directory: %DEPLOY_DIR%
mkdir "%DEPLOY_DIR%"

echo Copying application files...
REM Copy all files using xcopy
xcopy . "%DEPLOY_DIR%" /E /I /EXCLUDE:exclude-list.txt

REM Create exclude list file
echo .git > exclude-list.txt
echo .gitignore >> exclude-list.txt
echo .editorconfig >> exclude-list.txt
echo .env >> exclude-list.txt
echo .env.example >> exclude-list.txt
echo .env.backup >> exclude-list.txt
echo .env.production >> exclude-list.txt
echo .phpactor.json >> exclude-list.txt
echo .phpunit.result.cache >> exclude-list.txt
echo .fleet >> exclude-list.txt
echo .idea >> exclude-list.txt
echo .nova >> exclude-list.txt
echo .phpunit.cache >> exclude-list.txt
echo .vscode >> exclude-list.txt
echo .zed >> exclude-list.txt
echo auth.json >> exclude-list.txt
echo node_modules >> exclude-list.txt
echo public\build >> exclude-list.txt
echo public\hot >> exclude-list.txt
echo public\storage >> exclude-list.txt
echo storage\*.key >> exclude-list.txt
echo storage\pail >> exclude-list.txt
echo vendor >> exclude-list.txt
echo Homestead.json >> exclude-list.txt
echo Homestead.yaml >> exclude-list.txt
echo Thumbs.db >> exclude-list.txt
echo *.log >> exclude-list.txt
echo .DS_Store >> exclude-list.txt
echo deploy-shared-hosting.sh >> exclude-list.txt
echo deploy-windows.bat >> exclude-list.txt
echo deploy-instructions.md >> exclude-list.txt
echo composer.lock >> exclude-list.txt
echo package-lock.json >> exclude-list.txt
echo phpunit.xml >> exclude-list.txt
echo tests >> exclude-list.txt

REM Create necessary directories
echo Creating required directories...
mkdir "%DEPLOY_DIR%\storage\framework\cache" 2>nul
mkdir "%DEPLOY_DIR%\storage\framework\sessions" 2>nul
mkdir "%DEPLOY_DIR%\storage\framework\views" 2>nul
mkdir "%DEPLOY_DIR%\storage\logs" 2>nul
mkdir "%DEPLOY_DIR%\bootstrap\cache" 2>nul

REM Create .env file template
echo Creating .env template...
(
echo APP_NAME="Inventory Management"
echo APP_ENV=production
echo APP_KEY=
echo APP_DEBUG=false
echo APP_URL=http://your-subdomain.your-domain.com
echo.
echo DB_CONNECTION=mysql
echo DB_HOST=localhost
echo DB_PORT=3306
echo DB_DATABASE=your_database_name
echo DB_USERNAME=your_database_user
echo DB_PASSWORD=your_database_password
echo.
echo SESSION_DRIVER=database
echo SESSION_LIFETIME=120
echo.
echo CACHE_DRIVER=file
echo QUEUE_CONNECTION=sync
echo.
echo MAIL_MAILER=smtp
echo MAIL_HOST=mailhog
echo MAIL_PORT=1025
echo MAIL_USERNAME=null
echo MAIL_PASSWORD=null
echo MAIL_ENCRYPTION=null
echo MAIL_FROM_ADDRESS="hello@example.com"
echo MAIL_FROM_NAME="%%APP_NAME%%"
) > "%DEPLOY_DIR%\.env.example"

REM Create deployment instructions file
echo Creating deployment instructions...
(
echo # Laravel Inventory Application - Deployment Instructions
echo.
echo ## Prerequisites
echo 1. Shared hosting with PHP 8.2+ support
echo 2. MySQL database (5.7+ or MariaDB 10.3+)
echo 3. SSH/FTP access to your hosting account
echo 4. Subdomain or directory ready for deployment
echo.
echo ## Deployment Steps
echo.
echo ### 1. Upload Files
echo Upload ALL files from this directory to your hosting:
echo - Via FTP: Upload to your subdomain directory (e.g., public_html/subdomain)
echo - Via cPanel File Manager: Upload and extract zip file
echo.
echo ### 2. Configure Database
echo 1. Create a MySQL database via cPanel
echo 2. Create a database user with full privileges
echo 3. Note down: database name, username, password, and host (usually localhost)
echo.
echo ### 3. Configure Environment
echo 1. Rename `.env.example` to `.env`
echo 2. Edit `.env` file with your configuration:
echo    - Update `APP_URL` to your actual subdomain URL
echo    - Update database credentials (DB_* settings)
echo    - Set `APP_DEBUG=false` for production
echo.
echo ### 4. Generate Application Key
echo Via SSH (if available):
echo ```bash
echo php artisan key:generate
echo ```
echo.
echo Via cPanel Terminal (if available) or run this PHP script:
echo Create a file `generate_key.php` in your web root with:
echo ```php
echo ^<?php
echo require 'vendor/autoload.php';
echo $app = require_once 'bootstrap/app.php';
echo $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo $kernel->call('key:generate');
echo echo "Application key generated!";
echo ?^>
echo ```
echo Then visit this file in browser, then delete it.
echo.
echo ### 5. Set Directory Permissions
echo Set the following permissions via cPanel File Manager or FTP:
echo - `storage/` → 755 (recursive)
echo - `bootstrap/cache/` → 755 (recursive)
echo.
echo ### 6. Run Database Migrations
echo Via SSH:
echo ```bash
echo php artisan migrate --force
echo ```
echo.
echo Or create a migration script `run_migrations.php`:
echo ```php
echo ^<?php
echo require 'vendor/autoload.php';
echo $app = require_once 'bootstrap/app.php';
echo $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo $kernel->call('migrate', ['--force' => true]);
echo echo "Migrations completed!";
echo ?^>
echo ```
echo.
echo ### 7. Install Dependencies
echo If your hosting has Composer installed:
echo ```bash
echo composer install --no-dev --optimize-autoloader
echo ```
echo.
echo If not, you need to upload the vendor directory from your local development.
echo.
echo ### 8. Configure Web Server
echo Ensure your web server points to the `public/` directory.
echo.
echo For Apache (typical shared hosting), create or modify `.htaccess` in the root:
echo ```
echo <IfModule mod_rewrite.c^>
echo     RewriteEngine On
echo     RewriteRule ^(.*)$ public/$1 [L]
echo </IfModule^>
echo ```
echo.
echo ### 9. Test the Application
echo Visit your subdomain URL to verify the application loads.
echo.
echo ### 10. Create Admin User
echo Via SSH:
echo ```bash
echo php artisan db:seed --class=DatabaseSeeder
echo ```
echo.
echo Or run the seeder via a PHP script.
) > "%DEPLOY_DIR%\DEPLOYMENT-README.md"

REM Create helper PHP scripts
echo Creating helper PHP scripts...

(
echo ^<?php
echo // Script to generate application key
echo echo "<h3>Laravel Application Key Generator</h3>";
echo.
echo if (!file_exists('.env')) {
echo     echo "Error: .env file not found. Please rename .env.example to .env first.";
echo     exit;
echo }
echo.
echo require 'vendor/autoload.php';
echo $app = require_once 'bootstrap/app.php';
echo.
echo $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo.
echo try {
echo     $status = $kernel->call('key:generate');
echo     echo "Application key generated successfully!<br>";
echo     echo "Please delete this file immediately after use.";
echo } catch (Exception $e) {
echo     echo "Error: " . $e->getMessage();
echo }
echo ?^>
) > "%DEPLOY_DIR%\generate_key.php"

(
echo ^<?php
echo // Script to run database migrations
echo echo "<h3>Laravel Database Migrations</h3>";
echo.
echo if (!file_exists('.env')) {
echo     echo "Error: .env file not found. Please configure .env file first.";
echo     exit;
echo }
echo.
echo require 'vendor/autoload.php';
echo $app = require_once 'bootstrap/app.php';
echo.
echo $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo.
echo try {
echo     $status = $kernel->call('migrate', ['--force' => true]);
echo     echo "Database migrations completed successfully!<br>";
echo     echo "Please delete this file immediately after use.";
echo } catch (Exception $e) {
echo     echo "Error: " . $e->getMessage() . "<br>";
echo     echo "Check your database configuration in .env file.";
echo }
echo ?^>
) > "%DEPLOY_DIR%\run_migrations.php"

REM Create zip file for easy upload
echo Creating zip archive...
powershell Compress-Archive -Path "%DEPLOY_DIR%" -DestinationPath "%DEPLOY_DIR%.zip" -Force

del exclude-list.txt

echo =========================================
echo Deployment preparation complete!
echo =========================================
echo.
echo Next steps:
echo 1. Upload the '%DEPLOY_DIR%' folder or '%DEPLOY_DIR%.zip' to your hosting
echo 2. Follow instructions in '%DEPLOY_DIR%\DEPLOYMENT-README.md'
echo 3. Or read the detailed deployment guide in 'deploy-instructions.md'
echo.
echo For detailed instructions, see: deploy-instructions.md
pause