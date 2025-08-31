# Housync RFID Security System - Railway Deployment

A complete Laravel-based apartment management system with integrated RFID access control, deployed on Railway.

## 🚀 Quick Deploy to Railway

### Prerequisites
- Railway account ([sign up here](https://railway.app))
- GitHub repository with your code
- Railway CLI installed: `npm install -g @railway/cli`

### One-Click Deployment
```bash
# Run the automated deployment script
deploy-to-railway.bat
```

Or follow the manual steps below.

## 📋 Manual Deployment Steps

### 1. Prepare Your Project
Ensure these files are in your project root:
- ✅ `Procfile` - Railway process configuration
- ✅ `nixpacks.toml` - Build configuration  
- ✅ `composer.json` - Updated with Railway scripts
- ✅ `railway-env-variables.txt` - Environment variables template

### 2. Deploy to Railway
```bash
# Login to Railway
railway login

# Initialize project
railway init

# Deploy
railway up
```

### 3. Add MySQL Database
1. Go to Railway dashboard
2. Click "New Service" → "Database" → "MySQL"
3. Railway will auto-configure database connection

### 4. Configure Environment Variables
Copy variables from `railway-env-variables.txt` to Railway dashboard:

**Required Variables:**
```bash
APP_NAME=Housync
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://your-app.up.railway.app
DB_CONNECTION=mysql
# Railway auto-populates DB_* variables
```

### 5. Run Migrations
```bash
railway run php artisan migrate --force
railway run php artisan db:seed --force  # Optional
```

### 6. Test Deployment
```bash
railway open
```

## 🔧 ESP32 Configuration for Production

### WiFi-Enabled ESP32 Code
Use the provided `esp32_wifi_rfid_code.ino` with these updates:

```cpp
// Update these in the Arduino code:
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD"; 
const char* serverURL = "https://your-app.up.railway.app/api/rfid/verify";
```

### Hardware Setup
```
RFID RC522    ESP32
VCC       ->  3.3V
RST       ->  GPIO 0
GND       ->  GND
MISO      ->  GPIO 19
MOSI      ->  GPIO 23
SCK       ->  GPIO 18
SDA(SS)   ->  GPIO 5
```

### Libraries Required
- MFRC522 library
- ArduinoJson library
- WiFi library (built-in)
- HTTPClient library (built-in)

## 🎯 System Features

### For Landlords
- **RFID Card Management**: Assign cards to tenants
- **Access Monitoring**: Real-time access logs
- **Security Dashboard**: Statistics and alerts
- **Tenant Integration**: Cards linked to lease agreements

### For Tenants  
- **Seamless Access**: Tap card for building entry
- **Access History**: View your entry logs
- **Multiple Cards**: Support for backup cards

### For System Admins
- **Complete Audit Trail**: All access attempts logged
- **Real-time Monitoring**: Live access dashboard
- **Security Alerts**: Unauthorized access attempts
- **API Integration**: RESTful API for integrations

## 📊 API Endpoints

### RFID Verification
```http
POST /api/rfid/verify
Content-Type: application/json

{
    "card_uid": "A1B2C3D4",
    "reader_location": "main_entrance"
}
```

**Response:**
```json
{
    "card_uid": "A1B2C3D4",
    "access_granted": true,
    "tenant_name": "John Doe",
    "denial_reason": null,
    "timestamp": "2025-01-01T12:00:00Z"
}
```

### Health Check
```http
GET /health
```

**Response:**
```json
{
    "status": "healthy",
    "timestamp": "2025-01-01T12:00:00Z",
    "database": "connected",
    "version": "12.0.0"
}
```

## 🔐 Security Features

### Production Security
- ✅ **Rate Limiting**: 60 requests per minute per IP
- ✅ **HTTPS**: Automatic SSL certificates
- ✅ **Database Encryption**: Secure MySQL connections  
- ✅ **Session Security**: Secure cookies and CSRF protection
- ✅ **Access Logging**: Complete audit trail
- ✅ **Card Validation**: Multi-level access verification

### Access Control Logic
1. **Card Validation**: Check if card exists and is active
2. **Tenant Verification**: Verify tenant lease status
3. **Expiration Check**: Validate card expiration date
4. **Security Status**: Check for lost/stolen cards
5. **Time Restrictions**: Optional access hour limits
6. **Location Verification**: Multi-reader support

## 📈 Monitoring & Maintenance

### Railway Dashboard
- **Real-time Logs**: Application and database logs
- **Performance Metrics**: CPU, memory, response times
- **Database Monitoring**: Connection count, query performance
- **Deployment History**: Rollback capability

### Application Monitoring
```bash
# View logs
railway logs

# Check application status
railway status

# Run artisan commands
railway run php artisan [command]
```

### Database Maintenance
```bash
# Database backup
railway run mysqldump $MYSQL_DATABASE > backup.sql

# Clear old access logs (optional)
railway run php artisan rfid:cleanup-logs --days=365
```

## 🎛️ Environment Configuration

### Development vs Production

**Development (.env.local):**
```bash
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=mysql
# Local database settings
```

**Production (Railway):**
```bash
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
# Railway auto-configured database
SESSION_SECURE_COOKIE=true
TRUSTED_PROXIES=*
```

## 🔧 Troubleshooting

### Common Issues

#### Deployment Failures
```bash
# Check build logs
railway logs --deployment

# Verify nixpacks.toml syntax
# Ensure all dependencies are in composer.json
```

#### Database Connection Issues
```bash
# Test database connection
railway run php artisan tinker
# Run: DB::connection()->getPdo();
```

#### ESP32 Connection Issues
```bash
# Check ESP32 serial output
# Verify WiFi credentials
# Test API endpoint manually:
curl -X POST https://your-app.up.railway.app/api/rfid/verify \
  -H "Content-Type: application/json" \
  -d '{"card_uid":"TEST123","reader_location":"main_entrance"}'
```

#### Migration Failures
```bash
# Check migration status
railway run php artisan migrate:status

# Run specific migration
railway run php artisan migrate --path=database/migrations/specific_migration.php
```

### Performance Optimization

#### Database Optimization
```bash
# Add database indexes
railway run php artisan migrate

# Optimize database tables
railway run php artisan db:optimize
```

#### Application Optimization
```bash
# Clear and cache configurations
railway run php artisan config:cache
railway run php artisan route:cache
railway run php artisan view:cache
```

## 📞 Support

### Resources
- [Railway Documentation](https://docs.railway.app)
- [Laravel Documentation](https://laravel.com/docs)
- [MFRC522 Library](https://github.com/miguelbalboa/rfid)

### Getting Help
1. **Railway Issues**: Check Railway status page
2. **Application Issues**: Check Laravel logs
3. **Hardware Issues**: Verify ESP32 connections
4. **API Issues**: Test endpoints with curl/Postman

### Contact
- Railway Support: [Discord](https://discord.gg/railway)
- Project Issues: GitHub Issues
- Documentation: This README

---

## 🎉 Success!

Your RFID Security System is now running on Railway with:
- ✅ **Scalable Infrastructure**: Auto-scaling Laravel application
- ✅ **Managed Database**: Railway MySQL with backups
- ✅ **SSL Security**: Automatic HTTPS certificates
- ✅ **Real-time Monitoring**: Comprehensive logging and metrics
- ✅ **WiFi-Connected ESP32**: Direct API integration
- ✅ **Production Ready**: Optimized for performance and security

**Access your system at:** `https://your-app.up.railway.app`

Happy secure access management! 🔐🏠
