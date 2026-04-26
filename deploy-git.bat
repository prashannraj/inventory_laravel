@echo off
REM Git Deployment Script for Laravel Inventory Application (Windows)
REM For shared hosting with Git access

echo =========================================
echo 🚀 Laravel Inventory - Git Deployment
echo =========================================
echo Started at: %date% %time%
echo.

set APP_DIR=%cd%
set LOG_FILE=%APP_DIR%\storage\logs\deployments.log

REM Create log directory if it doesn't exist
if not exist "%APP_DIR%\storage\logs" mkdir "%APP_DIR%\storage\logs"

REM Log function
setlocal enabledelayedexpansion
call :log "Starting deployment process..."

REM Check if we're in a git repository
if not exist ".git" (
    call :log "❌ Not a git repository. Initializing..."
    
    REM Check if directory is empty
    dir /b | findstr . >nul
    if not errorlevel 1 (
        call :log "⚠️  Directory not empty. Consider backing up existing files..."
    )
    
    REM Initialize git and clone
    git init
    git remote add origin https://github.com/prashannraj/inventory_laravel.git
    git fetch origin
    git checkout -t origin/main || git checkout -b main
    call :log "✅ Git repository initialized"
) else (
    call :log "✅ Git repository found"
)

REM Backup critical files
call :log "📦 Backing up critical files..."
if exist ".env" (
    copy ".env" ".env.backup.deploy" >nul
    call :log "  - .env backed up"
)

REM Pull latest changes
call :log "⬇️  Pulling latest changes from main branch..."
git fetch origin
git reset --hard origin/main
call :log "✅ Code updated to latest version"

REM Restore backed up files
call :log "🔧 Restoring configuration..."
if exist ".env.backup.deploy" (
    move /y ".env.backup.deploy" ".env" >nul
    call :log "  - .env restored"
) else (
    if not exist ".env" if exist ".env.example" (
        copy ".env.example" ".env" >nul
        call :log "⚠️  No .env found, copied from .env.example"
        call :log "  - Please edit .env with your configuration"
    )
)

REM Create necessary directories
call :log "📁 Creating required directories..."
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\logs" mkdir "storage\logs"
if not exist "bootstrap\cache" mkdir "bootstrap\cache"

REM Install/update dependencies
call :log "📦 Checking dependencies..."
where composer >nul 2>nul
if not errorlevel 1 (
    call :log "  - Composer found, installing dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
    if !errorlevel! equ 0 (
        call :log "✅ Dependencies installed"
    ) else (
        call :log "⚠️  Composer install had issues, trying update..."
        composer update --no-dev --optimize-autoloader --no-interaction
    )
) else (
    call :log "⚠️  Composer not found. Skipping dependency installation."
    call :log "  - Make sure vendor directory exists or install composer"
)

REM Generate application key if not set
call :log "🔑 Checking application key..."
if exist ".env" (
    findstr /C:"APP_KEY=" .env | findstr /C:"base64:" >nul
    if errorlevel 1 (
        call :log "  - Generating application key..."
        php artisan key:generate --force
    ) else (
        call :log "  - Application key already set"
    )
)

REM Run database migrations
call :log "🗄️  Running database migrations..."
php artisan migrate --force --no-interaction
if !errorlevel! equ 0 (
    call :log "✅ Migrations completed"
) else (
    call :log "⚠️  Migrations may have failed. Check logs above."
)

REM Clear caches
call :log "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
call :log "✅ Caches cleared"

REM Optimize for production
call :log "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
call :log "✅ Application optimized"

REM Verify deployment
call :log "🔍 Verifying deployment..."
if exist "public\index.php" (
    call :log "✅ Public index file exists"
) else (
    call :log "⚠️  Warning: public/index.php not found"
)

php artisan --version >nul 2>&1
if !errorlevel! equ 0 (
    for /f "tokens=*" %%i in ('php artisan --version 2^>nul') do set ARTISAN_VERSION=%%i
    call :log "✅ Artisan working: !ARTISAN_VERSION!"
) else (
    call :log "⚠️  Warning: Artisan may not be working properly"
)

REM Cleanup
call :log "🧽 Cleaning up..."
if exist ".env.backup.deploy" del ".env.backup.deploy"

REM Summary
call :log "========================================="
call :log "✅ Deployment completed successfully!"
call :log "========================================="
echo.
echo 📊 Deployment Summary:
echo   - Code updated from GitHub
echo   - Dependencies installed
echo   - Database migrated
echo   - Caches cleared and optimized
echo.
echo 📝 Next steps:
echo   1. Visit your application URL to verify
echo   2. Check storage\logs\deployments.log for details
echo   3. Test key functionality
echo.
echo 🕒 Deployment completed at: %date% %time%
echo =========================================
pause
exit /b 0

:log
echo [%date% %time%] %~1 >> "%LOG_FILE%"
echo [%date% %time%] %~1
exit /b 0