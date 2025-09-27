# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

**LucrativaBet** is a complete online casino system built with Laravel 10.48.2 and Filament v3 admin panel. The system is 100% functional and ready for production deployment.

⚠️ **CRITICAL WARNING**: This system is complete and fully functional. DO NOT create new features or modify core casino functionality without explicit permission.

## Essential Commands

### Development Server
```bash
# ALWAYS use port 8080 - never use other ports
php artisan serve --port=8080

# Access URLs
# Main casino: http://127.0.0.1:8080  
# Admin panel: http://127.0.0.1:8080/admin
```

### Database Management
```bash
# Run migrations (70+ tables)
php artisan migrate

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Access tinker for database operations
php artisan tinker
```

### Cache Management
```bash
# Clear all caches (use frequently)
php artisan config:clear
php artisan cache:clear  
php artisan route:clear
php artisan view:clear

# Or use the built-in web route
# GET /clear - clears all caches via web interface
```

### Asset Management
```bash
# NEVER run npm run build - it will break casino assets
# Use the fix script instead if assets are corrupted
bash _scripts/fix-casino-files.sh

# Development mode (if needed)
npm run dev

# Install dependencies
composer install
npm install
```

### Testing
```bash
# Run PHPUnit tests
./vendor/bin/phpunit

# Test specific features
php artisan test --filter=SomeTest
```

## Architecture Overview

### Core Technology Stack
- **Backend**: Laravel 10.48.2 (PHP 8.2+)
- **Admin Panel**: Filament v3 with role-based permissions
- **Database**: MySQL 8.0 (production) / SQLite (fallback)
- **Frontend**: Pure JavaScript (not Vue/React) compiled with Vite
- **Styling**: Tailwind CSS + custom casino themes
- **Authentication**: JWT + Sanctum with 2FA support
- **Payment**: AureoLink gateway integration

### System Architecture
```
┌── app/
│   ├── Console/Commands/          # Artisan commands (backup, cache, migrations)  
│   ├── Events/                    # WebSocket events for real-time features
│   ├── Filament/                  # Admin panel resources and pages
│   └── Http/Controllers/Api/      # API endpoints for games and wallet
├── database/migrations/           # 70+ migration files
├── routes/
│   ├── web.php                   # Main web routes + cache clearing endpoints
│   ├── api.php                   # API routes for games/payments
│   └── 2fa.php                   # Two-factor authentication routes  
└── public/build/assets/          # CRITICAL: Compiled casino assets
    ├── app-CRDk2_8R.js          # Main casino JS (1.7MB) - NEVER MODIFY
    └── app-BiLvXd5_.css          # Casino CSS (242KB)
```

### Critical System Components

#### Casino Engine
- **Core File**: `public/build/assets/app-CRDk2_8R.js` (1.7MB)
- **CSS**: `public/build/assets/app-BiLvXd5_.css` (242KB)  
- **Backup Location**: `bet.sorte365.fun/public/build/assets/` (NEVER DELETE)
- Built-in games with RTP controls and provider integrations

#### User Management & Roles
- Admin system with Spatie permissions
- Affiliate program with referral tracking  
- VIP levels and mission systems
- 2FA authentication support

#### Payment Integration
- **Active Gateway**: AureoLink (webhooks configured)
- **Removed**: BSPay, Stripe, EzzePay, DigitoPay
- Wallet system with deposit/withdrawal tracking

#### Admin Panel Features
- Filament-based dashboard with custom widgets
- Real-time analytics and reporting
- Game management and RTP controls
- User management with role assignments
- Transaction monitoring and gateway settings

### Database Structure
The system uses 70+ tables including:
- **users**: Main user accounts with affiliate codes
- **games**: Casino games with RTP settings and providers  
- **categories**: Game categorization
- **transactions**: Deposit/withdrawal tracking
- **gateways**: Payment gateway configurations
- **missions**: Challenge/bonus system
- **vip_users**: VIP level management
- **settings**: System configuration

## Environment Configuration

### Required Environment Variables
```env
APP_URL=http://127.0.0.1:8080  # NEVER change this port
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=mysql
DB_DATABASE=lucrativabet

# JWT Configuration  
JWT_SECRET=71RdDYCHDZCUQIYGU9EP8PVEp93YAYLnvlh49a3smdAKJwNj9XG0iQHiDhyQbKSk
JWT_TTL=60
```

### Default Credentials
```
Admin Login:
Email: lucrativa@bet.com
Password: foco123@

Affiliate Test:
Email: afiliado@teste.com  
Password: password
```

## Development Rules & Restrictions

### NEVER Do These:
1. Run `npm run build` - it overwrites critical casino files
2. Delete `bet.sorte365.fun/` folder - it's the backup when build breaks
3. Use ports other than 8080 - breaks APP_URL references
4. Modify `app-CRDk2_8R.js` directly - it's obfuscated casino code
5. Change APP_URL to different port - breaks image loading

### ALWAYS Do These:
1. Use port 8080 for development server
2. Run cache clear commands after configuration changes
3. Use `fix-casino-files.sh` if casino assets break
4. Check file size of `app-CRDk2_8R.js` should be 1.7MB
5. Read `CRITICAL-INFO.json` before making any changes

## Common Issues & Solutions

### White Screen/Casino Not Loading
**Cause**: Missing or corrupted casino asset files
**Solution**: 
```bash
bash _scripts/fix-casino-files.sh
```

### Login Failed/Hash Issues  
**Cause**: Password hashing problems or double encryption
**Solution**:
```bash
php artisan tinker
# Reset user password in tinker console
```

### Images Not Showing
**Cause**: Incorrect APP_URL configuration
**Solution**: Ensure `APP_URL=http://127.0.0.1:8080` in `.env`

### Build Process Breaks Casino
**Cause**: Vite overwrites compiled casino files
**Solution**: NEVER use `npm run build`, use backup restoration instead

## Quick Verification Steps
1. `php artisan serve --port=8080` 
2. Open `http://127.0.0.1:8080` - verify casino loads
3. Open `http://127.0.0.1:8080/admin` - verify admin panel
4. Login with admin credentials
5. Check that games are functional

## Deployment Notes
- System is Docker-ready with included Dockerfile
- Uses supervisord for process management in production
- Database can be MySQL or SQLite fallback
- Includes Digital Ocean deployment configuration
- Asset files must be preserved during deployment

This system is production-ready and should be treated as a complete, functional casino platform. Focus on configuration, maintenance, and deployment rather than feature development.

## Support Files
- `CRITICAL-INFO.json`: Essential system information and warnings
- `DEPLOY-MANUAL.md`: Complete deployment instructions  
- `_scripts/`: Important maintenance and setup scripts