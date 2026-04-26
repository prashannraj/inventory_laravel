# Post-Deployment Automation

## Overview
This document provides automation scripts and configurations for post-deployment tasks including monitoring, backups, and maintenance.

## 1. Health Check Script

### `health-check.sh` - Automated Health Monitoring
```bash
#!/bin/bash
# Health check script for Laravel application
# Run after deployment or via cron

set -e

APP_DIR="/home/username/public_html/subdomain"
LOG_FILE="$APP_DIR/storage/logs/health-check.log"
ALERT_EMAIL="admin@example.com"

# Log function
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log "Starting health check..."

# Check 1: Application URL is accessible
if curl -s -o /dev/null -w "%{http_code}" http://localhost/ | grep -q "200\|302\|301"; then
    log "✅ Application is accessible"
else
    log "❌ Application is not accessible"
    echo "Application down at $(date)" | mail -s "Application Health Alert" "$ALERT_EMAIL"
fi

# Check 2: Database connection
if php "$APP_DIR/artisan" db:monitor > /dev/null 2>&1; then
    log "✅ Database connection OK"
else
    log "❌ Database connection failed"
    echo "Database connection failed at $(date)" | mail -s "Database Alert" "$ALERT_EMAIL"
fi

# Check 3: Storage writable
if [ -w "$APP_DIR/storage" ]; then
    log "✅ Storage directory is writable"
else
    log "❌ Storage directory is not writable"
    chmod -R 755 "$APP_DIR/storage"
    log "  - Fixed permissions"
fi

# Check 4: Recent logs
if find "$APP_DIR/storage/logs" -name "laravel*.log" -mtime -1 | grep -q .; then
    log "✅ Log files are being updated"
else
    log "⚠️  No recent log updates"
fi

# Check 5: Disk space
DISK_USAGE=$(df -h "$APP_DIR" | awk 'NR==2 {print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -gt 90 ]; then
    log "❌ Disk usage critical: ${DISK_USAGE}%"
    echo "Disk usage at ${DISK_USAGE}%" | mail -s "Disk Space Alert" "$ALERT_EMAIL"
elif [ "$DISK_USAGE" -gt 80 ]; then
    log "⚠️  Disk usage high: ${DISK_USAGE}%"
else
    log "✅ Disk usage normal: ${DISK_USAGE}%"
fi

# Check 6: PHP version
PHP_VERSION=$(php -v | head -1 | awk '{print $2}')
log "ℹ️  PHP version: $PHP_VERSION"

# Check 7: Laravel version
if [ -f "$APP_DIR/artisan" ]; then
    LARAVEL_VERSION=$(php "$APP_DIR/artisan" --version | awk '{print $2}')
    log "ℹ️  Laravel version: $LARAVEL_VERSION"
fi

log "Health check completed at $(date)"
```

## 2. Automated Backup Script

### `backup.sh` - Database and File Backup
```bash
#!/bin/bash
# Automated backup script for Laravel application

set -e

APP_DIR="/home/username/public_html/subdomain"
BACKUP_DIR="/home/username/backups"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="inventory_backup_$DATE"

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Load database credentials from .env
DB_DATABASE=$(grep DB_DATABASE "$APP_DIR/.env" | cut -d '=' -f2 | tr -d '[:space:]' | tr -d '"' | tr -d "'")
DB_USERNAME=$(grep DB_USERNAME "$APP_DIR/.env" | cut -d '=' -f2 | tr -d '[:space:]' | tr -d '"' | tr -d "'")
DB_PASSWORD=$(grep DB_PASSWORD "$APP_DIR/.env" | cut -d '=' -f2 | tr -d '[:space:]' | tr -d '"' | tr -d "'")

echo "Starting backup: $BACKUP_NAME"

# Backup database
if [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then
    echo "Backing up database: $DB_DATABASE"
    mysqldump -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_DIR/${BACKUP_NAME}.sql"
    
    # Compress database backup
    gzip "$BACKUP_DIR/${BACKUP_NAME}.sql"
    echo "Database backup created: ${BACKUP_NAME}.sql.gz"
fi

# Backup critical files
echo "Backing up critical files..."
tar -czf "$BACKUP_DIR/${BACKUP_NAME}_files.tar.gz" \
    "$APP_DIR/.env" \
    "$APP_DIR/storage" \
    "$APP_DIR/public/uploads" \
    "$APP_DIR/config" \
    2>/dev/null || true

# Backup entire application (optional, less frequent)
if [ $(date +%d) -eq 01 ]; then  # First day of month
    echo "Creating full monthly backup..."
    tar -czf "$BACKUP_DIR/${BACKUP_NAME}_full.tar.gz" -C "$APP_DIR" .
fi

# Cleanup old backups (keep last 30 days)
find "$BACKUP_DIR" -name "inventory_backup_*" -mtime +30 -delete

echo "Backup completed: $BACKUP_NAME"
echo "Backup size:"
du -h "$BACKUP_DIR/${BACKUP_NAME}"* 2>/dev/null || true
```

## 3. Cron Job Configuration

### `cron-jobs.txt` - Scheduled Tasks
```bash
# Edit crontab: crontab -e

# Run health check every hour
0 * * * * /home/username/public_html/subdomain/health-check.sh > /dev/null 2>&1

# Run daily backup at 2 AM
0 2 * * * /home/username/public_html/subdomain/backup.sh > /dev/null 2>&1

# Clear Laravel cache daily at 3 AM
0 3 * * * cd /home/username/public_html/subdomain && php artisan cache:clear > /dev/null 2>&1

# Optimize Laravel weekly (Sunday at 4 AM)
0 4 * * 0 cd /home/username/public_html/subdomain && php artisan optimize > /dev/null 2>&1

# Check for Git updates every 5 minutes (optional)
*/5 * * * * cd /home/username/public_html/subdomain && git fetch origin > /dev/null 2>&1

# Log rotation for Laravel logs (weekly)
0 5 * * 0 find /home/username/public_html/subdomain/storage/logs -name "laravel-*.log" -mtime +7 -delete
```

## 4. Deployment Webhook Script

### `webhook-deploy.php` - GitHub Webhook Handler
```php
<?php
// webhook-deploy.php - Secure GitHub webhook handler
// Place this outside web root or protect with .htaccess

$config = [
    'secret' => 'your_github_webhook_secret',
    'branch' => 'main',
    'log_file' => __DIR__ . '/storage/logs/webhook.log',
    'deploy_script' => __DIR__ . '/deploy.sh',
];

// Get headers
$headers = getallheaders();
$signature = $headers['X-Hub-Signature-256'] ?? '';

// Get payload
$payload = file_get_contents('php://input');
$payloadData = json_decode($payload, true);

// Verify signature
$hash = 'sha256=' . hash_hmac('sha256', $payload, $config['secret']);

if (!hash_equals($hash, $signature)) {
    http_response_code(403);
    logMessage('Invalid signature');
    exit;
}

// Verify it's a push to the correct branch
$ref = $payloadData['ref'] ?? '';
if ($ref !== "refs/heads/{$config['branch']}") {
    http_response_code(200);
    logMessage("Ignoring push to branch: " . str_replace('refs/heads/', '', $ref));
    exit;
}

// Execute deployment
logMessage("Deployment triggered by: " . ($payloadData['pusher']['name'] ?? 'Unknown'));
$output = [];
$returnCode = 0;

exec("cd " . escapeshellarg(__DIR__) . " && bash " . escapeshellarg($config['deploy_script']) . " 2>&1", $output, $returnCode);

// Log results
logMessage("Deployment executed with exit code: $returnCode");
logMessage("Output: " . implode("\n", $output));

if ($returnCode === 0) {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Deployment completed']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Deployment failed']);
}

function logMessage($message) {
    global $config;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message\n";
    file_put_contents($config['log_file'], $logEntry, FILE_APPEND);
}
?>
```

### `.htaccess` protection for webhook
```apache
# Protect webhook-deploy.php
<Files "webhook-deploy.php">
    AuthType Basic
    AuthName "Restricted Access"
    AuthUserFile /home/username/.htpasswd
    Require valid-user
</Files>
```

## 5. Monitoring Dashboard

### `monitor-dashboard.php` - Simple Monitoring Page
```php
<?php
// monitor-dashboard.php - Simple monitoring dashboard
// Protect with .htaccess or basic auth

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Application Monitor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .status-ok { color: green; }
        .status-warning { color: orange; }
        .status-error { color: red; }
        .card { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        pre { background: #f5f5f5; padding: 10px; overflow: auto; }
    </style>
</head>
<body>
    <h1>Application Monitoring Dashboard</h1>
    
    <?php
    function checkStatus($name, $checkFn) {
        try {
            $result = $checkFn();
            $class = $result['status'] === 'ok' ? 'status-ok' : 
                    ($result['status'] === 'warning' ? 'status-warning' : 'status-error');
            echo "<div class='card'>";
            echo "<h3 class='$class'>$name: {$result['message']}</h3>";
            if (!empty($result['details'])) {
                echo "<pre>{$result['details']}</pre>";
            }
            echo "</div>";
        } catch (Exception $e) {
            echo "<div class='card'><h3 class='status-error'>$name: Error - {$e->getMessage()}</h3></div>";
        }
    }
    
    // Check 1: Application accessible
    checkStatus('Application Accessibility', function() {
        $url = 'http://' . $_SERVER['HTTP_HOST'];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 400) {
            return ['status' => 'ok', 'message' => "HTTP $httpCode"];
        } else {
            return ['status' => 'error', 'message' => "HTTP $httpCode"];
        }
    });
    
    // Check 2: Database connection
    checkStatus('Database Connection', function() {
        require __DIR__ . '/vendor/autoload.php';
        $app = require_once __DIR__ . '/bootstrap/app.php';
        
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Connected'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    });
    
    // Check 3: Storage writable
    checkStatus('Storage Permissions', function() {
        $storagePath = __DIR__ . '/storage';
        $cachePath = __DIR__ . '/bootstrap/cache';
        
        $issues = [];
        if (!is_writable($storagePath)) $issues[] = 'Storage not writable';
        if (!is_writable($cachePath)) $issues[] = 'Cache not writable';
        
        if (empty($issues)) {
            return ['status' => 'ok', 'message' => 'All directories writable'];
        } else {
            return ['status' => 'warning', 'message' => implode(', ', $issues)];
        }
    });
    
    // Check 4: Disk space
    checkStatus('Disk Space', function() {
        $free = disk_free_space(__DIR__);
        $total = disk_total_space(__DIR__);
        $usedPercent = round(100 - ($free / $total * 100), 2);
        
        if ($usedPercent > 90) {
            return ['status' => 'error', 'message' => "$usedPercent% used"];
        } elseif ($usedPercent > 80) {
            return ['status' => 'warning', 'message' => "$usedPercent% used"];
        } else {
            return ['status' => 'ok', 'message' => "$usedPercent% used"];
        }
    });
    
    // Check 5: Recent deployments
    checkStatus('Recent Deployments', function() {
        $logFile = __DIR__ . '/storage/logs/deployments.log';
        if (!file_exists($logFile)) {
            return ['status' => 'warning', 'message' => 'No deployment log found'];
        }
        
        $lines = array_slice(file($logFile), -10); // Last 10 lines
        $recent = implode('', $lines);
        
        if (strpos($recent, 'Deployment completed successfully') !== false) {
            return ['status' => 'ok', 'message' => 'Recent deployment successful', 'details' => $recent];
        } else {
            return ['status' => 'warning', 'message' => 'No recent successful deployments', 'details' => $recent];
        }
    });
    ?>
    
    <div class="card">
        <h3>Quick Actions</h3>
        <form method="post" action="deploy-actions.php">
            <button type="submit" name="action" value="clear_cache">Clear Cache</button>
            <button type="submit" name="action" value="run_migrations">Run Migrations</button>
            <button type="submit" name="action" value="pull_updates">Pull Updates</button>
        </form>
    </div>
    
    <div class="card">
        <h3>System Information</h3>
        <pre>
PHP Version: <?php echo phpversion(); ?>

Server: <?php echo $_SERVER['SERVER_SOFTWARE']; ?>

Laravel: <?php 
    try {
        $app = require_once __DIR__ . '/bootstrap/app.php';
        echo \Illuminate\Foundation\Application::VERSION;
    } catch (Exception $e) {
        echo 'Unknown';
    }
?>

Uptime: <?php 
    if (file_exists('/proc/uptime')) {
        $uptime = file_get_contents('/proc/uptime');
        $uptime = explode(' ', $uptime)[0];
        echo gmdate("H:i:s", $uptime) . ' (HH:MM:SS)';
    } else {
        echo 'N/A';
    }
?>
        </pre>
    </div>
</body>
</html>
```

## 6. Deployment Actions Handler

### `deploy-actions.php` - Web-based Deployment Controls
```php
<?php
// deploy-actions.php - Handle deployment actions from web interface
// SECURITY: Protect this file with authentication!

session_start();

// Simple authentication
$valid_password = 'your_secure_password_here'; // CHANGE THIS!

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] !== $valid_password) {
        die('Invalid password');
    }
    
    $action = $_POST['action'] ?? '';
    $output = [];
    $returnCode = 0;
    
    switch ($action) {
        case 'clear_cache':
            exec('cd ' . escapeshellarg(__DIR__) . ' && php artisan cache:clear 2>&1', $output, $returnCode);
            $message = "Cache cleared";
            break;
            
        case 'run_migrations':
            exec('cd ' . escapeshellarg(__DIR__) . ' && php artisan migrate --force 2>&1', $output, $returnCode);
            $message = "Migrations run";
            break;
            
        case 'pull_updates':
            exec('cd ' . escapeshellarg(__DIR__) . ' && git pull origin main 2>&1', $output, $returnCode);
            $message = "Git pull completed";
            break;
            
        case 'run_deploy':
            exec('cd ' . escapeshellarg(__DIR__) . ' && ./deploy.sh 2>&1', $output, $returnCode);
            $message = "Full deployment completed";
            break;
            
        default:
            $message = "Unknown action";
            $output = [];
