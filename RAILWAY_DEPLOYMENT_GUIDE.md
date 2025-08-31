# Railway Deployment Guide for RFID Security System

This guide will help you deploy your Housync RFID Security System to Railway, a modern cloud platform perfect for Laravel applications.

## Prerequisites

- Railway account (sign up at [railway.app](https://railway.app))
- GitHub account (for code deployment)
- Your Laravel project pushed to GitHub
- Railway CLI (optional but recommended)

## 1. Prepare Your Laravel Project for Railway

### Create Required Files

#### 1.1 Create `Procfile` in your project root:
```
web: php artisan serve --host=0.0.0.0 --port=$PORT
```

#### 1.2 Create `nixpacks.toml` for build configuration:
```toml
[phases.setup]
nixPkgs = ['...', 'php81', 'php81Packages.composer', 'nodejs-18_x']

[phases.install]
cmds = [
    'composer install --no-dev --optimize-autoloader',
    'php artisan config:cache',
    'php artisan route:cache',
    'php artisan view:cache'
]

[start]
cmd = 'php artisan serve --host=0.0.0.0 --port=$PORT'
```

#### 1.3 Update `composer.json` to specify PHP version:
Add this to your `composer.json`:
```json
{
    "require": {
        "php": "^8.1"
    },
    "scripts": {
        "post-install-cmd": [
            "php artisan clear-compiled",
            "php artisan optimize",
            "@php artisan package:discover --ansi"
        ],
        "post-update-cmd": [
            "php artisan clear-compiled",
            "php artisan optimize",
            "@php artisan package:discover --ansi"
        ],
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ]
    }
}
```

## 2. Deploy to Railway

### Option A: Deploy via Railway Dashboard

1. **Visit Railway Dashboard**
   - Go to [railway.app](https://railway.app)
   - Sign in with GitHub

2. **Create New Project**
   - Click "New Project"
   - Select "Deploy from GitHub repo"
   - Choose your Housync repository

3. **Configure Build Settings**
   - Railway will auto-detect Laravel
   - Ensure it uses the `nixpacks.toml` configuration

### Option B: Deploy via Railway CLI

```bash
# Install Railway CLI
npm install -g @railway/cli

# Login to Railway
railway login

# Initialize project
railway init

# Deploy
railway up
```

## 3. Set Up MySQL Database

### 3.1 Add MySQL Service
1. In your Railway project dashboard
2. Click "New Service"
3. Select "Database" → "MySQL"
4. Railway will create a MySQL instance

### 3.2 Get Database Credentials
Railway will provide these environment variables:
- `MYSQL_URL`
- `MYSQL_HOST`
- `MYSQL_PORT`
- `MYSQL_USER`
- `MYSQL_PASSWORD`
- `MYSQL_DATABASE`

## 4. Configure Environment Variables

In your Railway project dashboard, go to **Variables** tab and add:

### 4.1 Laravel Configuration
```bash
APP_NAME=Housync
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE  # Generate with: php artisan key:generate --show
APP_DEBUG=false
APP_URL=https://your-app-name.up.railway.app

# Database (Railway will auto-populate these)
DB_CONNECTION=mysql
DB_HOST=${{ MYSQL_HOST }}
DB_PORT=${{ MYSQL_PORT }}
DB_DATABASE=${{ MYSQL_DATABASE }}
DB_USERNAME=${{ MYSQL_USER }}
DB_PASSWORD=${{ MYSQL_PASSWORD }}

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database

# Security
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# RFID Configuration
RFID_READER_LOCATION=main_entrance
RFID_ACCESS_LOG_RETENTION_DAYS=365
RFID_DEFAULT_CARD_EXPIRY_DAYS=365
```

### 4.2 Generate APP_KEY
Run locally and copy the result:
```bash
php artisan key:generate --show
```

## 5. Database Migration & Setup

### 5.1 Run Migrations via Railway CLI
```bash
# Connect to your Railway project
railway link

# Run migrations
railway run php artisan migrate --force

# Seed database (optional)
railway run php artisan db:seed --force
```

### 5.2 Alternative: Add to Build Process
Add to `nixpacks.toml`:
```toml
[phases.setup]
nixPkgs = ['...', 'php81', 'php81Packages.composer', 'nodejs-18_x']

[phases.install]
cmds = [
    'composer install --no-dev --optimize-autoloader',
    'php artisan config:cache',
    'php artisan route:cache',
    'php artisan view:cache'
]

[phases.build]
cmds = ['php artisan migrate --force']

[start]
cmd = 'php artisan serve --host=0.0.0.0 --port=$PORT'
```

## 6. RFID Bridge Configuration for Production

Since Railway doesn't support direct USB/serial connections, you have two options for the ESP32 bridge:

### Option A: Local Bridge with API Integration
Keep the ESP32 bridge running locally and use the API endpoint:

1. **Update your ESP32 code** to use HTTP requests instead of serial:
```cpp
// Add to your ESP32 code
#include <WiFi.h>
#include <HTTPClient.h>

void sendRFIDData(String cardUID) {
    if(WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        http.begin("https://your-app.up.railway.app/api/rfid/verify");
        http.addHeader("Content-Type", "application/json");
        
        String jsonPayload = "{\"card_uid\":\"" + cardUID + "\",\"reader_location\":\"main_entrance\"}";
        
        int httpResponseCode = http.POST(jsonPayload);
        
        if(httpResponseCode > 0) {
            String response = http.getString();
            Serial.println("Server response: " + response);
        }
        
        http.end();
    }
}
```

### Option B: IoT Integration Service
Use Railway's ability to connect to IoT services like:
- **Railway + Supabase Edge Functions**
- **Railway + Vercel Functions**
- **Railway + AWS IoT Core**

## 7. File Storage Configuration

### 7.1 For File Uploads (Documents, etc.)
Add to environment variables:
```bash
FILESYSTEM_DISK=public
```

### 7.2 For Production File Storage
Consider using Railway's volume mounts or external storage:
```bash
# For external storage (recommended)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
```

## 8. Domain Configuration

### 8.1 Custom Domain (Optional)
1. In Railway dashboard, go to **Settings** → **Domains**
2. Add your custom domain
3. Update `APP_URL` in environment variables

### 8.2 SSL Certificate
Railway automatically provides SSL certificates for all deployments.

## 9. Monitoring & Logging

### 9.1 Application Logs
View logs in Railway dashboard:
- Go to **Deployments** tab
- Click on any deployment
- View real-time logs

### 9.2 Database Monitoring
Railway provides built-in MySQL monitoring:
- Connection count
- Query performance
- Storage usage

## 10. Production Optimizations

### 10.1 Update `config/database.php`
Add connection pooling and optimization:
```php
'mysql' => [
    'driver' => 'mysql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        PDO::ATTR_TIMEOUT => 30,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]) : [],
],
```

### 10.2 Add Health Check Route
Add to `routes/web.php`:
```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected'
    ]);
});
```

## 11. Deployment Checklist

### Pre-Deployment
- [ ] Code pushed to GitHub
- [ ] `Procfile` created
- [ ] `nixpacks.toml` configured
- [ ] Environment variables set
- [ ] APP_KEY generated

### Post-Deployment
- [ ] Database migrations run
- [ ] Application accessible
- [ ] Health check working
- [ ] RFID API endpoint responding
- [ ] File uploads working (if applicable)

## 12. Troubleshooting

### Common Issues

#### 12.1 Build Failures
```bash
# Check build logs in Railway dashboard
# Common fixes:
- Ensure composer.json has correct PHP version
- Check nixpacks.toml syntax
- Verify all dependencies are listed
```

#### 12.2 Database Connection Issues
```bash
# Test database connection
railway run php artisan tinker
# Then run: DB::connection()->getPdo();
```

#### 12.3 Migration Failures
```bash
# Run migrations manually
railway run php artisan migrate:status
railway run php artisan migrate --force
```

#### 12.4 Permission Issues
```bash
# Set proper permissions in nixpacks.toml
[phases.install]
cmds = [
    'composer install --no-dev --optimize-autoloader',
    'chmod -R 755 storage',
    'chmod -R 755 bootstrap/cache'
]
```

## 13. Cost Optimization

### Railway Pricing Tiers
- **Hobby Plan**: $5/month - Perfect for development
- **Pro Plan**: $20/month - Production ready
- **Team Plan**: $100/month - Multiple environments

### Resource Management
- Monitor CPU and memory usage
- Use database connection pooling
- Implement caching strategies
- Optimize database queries

## 14. Security Considerations

### 14.1 Production Security
```bash
# Environment variables
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=your-domain.com

# Add to .env
TRUSTED_PROXIES=*
ASSET_URL=https://your-app.up.railway.app
```

### 14.2 API Security
```php
// Add rate limiting to API routes
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('/api/rfid/verify', [RfidController::class, 'verifyAccess']);
});
```

## 15. Backup Strategy

### 15.1 Database Backups
Railway provides automatic backups, but you can also:
```bash
# Manual backup
railway run mysqldump $MYSQL_DATABASE > backup.sql

# Automated backup script
railway run php artisan backup:run
```

### 15.2 Code Backups
- Use GitHub for version control
- Tag releases for easy rollbacks
- Keep development/staging branches

## 16. Support & Resources

### Railway Resources
- [Railway Documentation](https://docs.railway.app)
- [Railway Discord Community](https://discord.gg/railway)
- [Railway Status Page](https://status.railway.app)

### Laravel on Railway
- [Laravel Deployment Guide](https://docs.railway.app/guides/laravel)
- [Environment Variables](https://docs.railway.app/develop/variables)
- [Custom Domains](https://docs.railway.app/deploy/custom-domains)

---

## 🚀 Quick Start Commands

```bash
# 1. Create Railway project
railway init

# 2. Deploy
railway up

# 3. Add MySQL database
# (Do this via Railway dashboard)

# 4. Run migrations
railway run php artisan migrate --force

# 5. View your app
railway open
```

Your RFID security system will be live at `https://your-app-name.up.railway.app`! 🎉
