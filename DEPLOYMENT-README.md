# Laravel Inventory Application - Git Deployment to Shared Hosting

## 🚀 Quick Start

### 1. **Push Code to GitHub**
```bash
# Your code is already on GitHub at:
# https://github.com/prashannraj/inventory_laravel.git
```

### 2. **Set Up Shared Hosting**
1. SSH into your hosting or use cPanel Terminal
2. Navigate to your subdomain directory
3. Clone the repository:
   ```bash
   git clone https://github.com/prashannraj/inventory_laravel.git .
   ```

### 3. **Run Initial Deployment**
```bash
# Make the deployment script executable
chmod +x deploy-git.sh

# Run the deployment script
./deploy-git.sh
```

## 📋 Files Created for Deployment

### Core Deployment Scripts
- **`deploy-git.sh`** - Main deployment script for Linux/Mac hosting
- **`deploy-git.bat`** - Windows-compatible deployment script
- **`deploy-shared-hosting.sh`** - Alternative manual deployment script
- **`deploy-windows.bat`** - Windows manual deployment script

### Documentation
- **`git-deployment-guide.md`** - Complete Git deployment workflow
- **`shared-hosting-git-setup.md`** - Hosting configuration guide
- **`post-deployment-automation.md`** - Monitoring and backup scripts
- **`deploy-instructions.md`** - Detailed deployment instructions
- **`QUICK-DEPLOYMENT-CHECKLIST.md`** - Step-by-step checklist

## 🔧 Git Deployment Workflow

### Your New Workflow
```
Local Development → Commit → Push to GitHub → SSH to Hosting → Run deploy-git.sh
```

### Simple Deployment Command
```bash
# After making changes locally:
git add .
git commit -m "Your changes"
git push origin main

# On shared hosting:
cd /path/to/your/subdomain
./deploy-git.sh
```

## 🛠️ Setup Instructions

### Step 1: Initial Hosting Setup
1. **SSH into your hosting**
2. **Clone the repository**:
   ```bash
   cd /home/username/public_html/subdomain
   git clone https://github.com/prashannraj/inventory_laravel.git .
   ```
3. **Configure environment**:
   ```bash
   cp .env.example .env
   # Edit .env with your database credentials
   ```
4. **Set permissions**:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

### Step 2: First Deployment
```bash
./deploy-git.sh
```
The script will:
- Pull latest code from GitHub
- Install dependencies
- Generate application key
- Run database migrations
- Set up permissions
- Optimize for production

### Step 3: Regular Updates
When you make changes locally:
1. Commit and push to GitHub
2. SSH to hosting and run:
   ```bash
   cd /path/to/your/app
   ./deploy-git.sh
   ```

## 📁 File Structure After Deployment
```
your-subdomain/
├── .env                    # Your configuration (not in git)
├── deploy-git.sh          # Deployment script
├── storage/logs/deployments.log
├── public/                # Web root
├── app/                   # Application code
├── database/              # Migrations and seeders
└── vendor/                # Dependencies (created by composer)
```

## ⚙️ Configuration Options

### Automated Deployment Options
1. **Manual SSH Deployment** (Recommended):
   ```bash
   ssh username@yourdomain.com
   cd /path/to/app
   ./deploy-git.sh
   ```

2. **GitHub Webhooks** (Auto-deploy on push):
   - Configure webhook in GitHub repository settings
   - Use `webhook-deploy.php` from `post-deployment-automation.md`

3. **Cron Job** (Scheduled updates):
   ```bash
   # Run every hour
   0 * * * * cd /path/to/app && ./deploy-git.sh > /dev/null 2>&1
   ```

### Environment Variables
Edit `.env` file with:
```env
APP_URL=https://your-subdomain.your-domain.com
DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
APP_DEBUG=false
```

## 🔒 Security Notes

### Critical Steps
1. **Never commit `.env` to Git**
2. **Set proper permissions**:
   ```bash
   chmod 644 .env
   chmod -R 755 storage bootstrap/cache
   ```
3. **Use HTTPS** if available
4. **Regular backups** using provided backup script

### Protecting Deployment Scripts
```bash
# Restrict access to deployment scripts
chmod 700 deploy-git.sh
chmod 600 webhook-deploy.php  # If using webhooks
```

## 🚨 Troubleshooting

### Common Issues & Solutions

#### 1. "Git command not found"
- Request Git installation from hosting provider
- Use cPanel's Git Version Control feature
- Use manual deployment scripts instead

#### 2. Permission Denied Errors
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs
```

#### 3. Database Connection Errors
- Verify `.env` database credentials
- Check if MySQL is running
- Confirm database user has proper privileges

#### 4. Composer Not Available
- Upload `vendor` directory from local development
- Request Composer installation from hosting provider
- Use shared hosting with Composer support

#### 5. White Screen After Deployment
```bash
# Clear caches manually
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan optimize
```

## 📞 Support Resources

### Documentation
- **Git Deployment Guide**: `git-deployment-guide.md`
- **Shared Hosting Setup**: `shared-hosting-git-setup.md`
- **Automation Scripts**: `post-deployment-automation.md`

### Quick Commands Reference
```bash
# Pull updates only
git pull origin main

# Full deployment
./deploy-git.sh

# Check deployment status
tail -f storage/logs/deployments.log

# Manual cache clear
php artisan cache:clear && php artisan config:clear && php artisan view:clear

# Run migrations only
php artisan migrate --force
```

## 🎯 Next Steps

### Immediate Actions
1. [ ] SSH into your shared hosting
2. [ ] Clone the repository to your subdomain
3. [ ] Configure `.env` with your database credentials
4. [ ] Run `./deploy-git.sh`
5. [ ] Visit your subdomain to verify

### Ongoing Maintenance
- [ ] Set up cron job for automated backups
- [ ] Configure monitoring dashboard
- [ ] Regular security updates
- [ ] Database backups

### Monitoring
Check `storage/logs/deployments.log` for deployment history and `storage/logs/laravel.log` for application errors.

## 📊 Deployment Status
- ✅ Code pushed to GitHub: `https://github.com/prashannraj/inventory_laravel.git`
- ✅ Deployment scripts created and tested
- ✅ Documentation complete
- ✅ Ready for shared hosting deployment

Your Laravel inventory application is now fully configured for Git-based deployment to shared hosting with a subdomain!