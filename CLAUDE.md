# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Glass Market is a marketplace for buying and selling glass art. The project uses a **hybrid architecture** combining Laravel 9 (backend framework) with traditional PHP views (not Blade templates). It runs on XAMPP locally with MySQL database and uses Mollie for payment processing.

## Development Environment

### Required Setup
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Configure environment
copy .env.example .env  # Windows
# Edit .env with database credentials

# Generate Laravel application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Build frontend assets
npm run dev
```

### Database Configuration
- **Database name**: `glass_market` (note: uses underscore, not hyphen)
- **Default connection**: MySQL via PDO at `127.0.0.1:3306`
- **Credentials**: Configured in both `.env` (Laravel) and `includes/db_connect.php` (direct PDO)

## Architecture

### Dual Database Connection Pattern
This project uses **two database connection methods**:

1. **Laravel's Eloquent ORM** (configured via `.env`)
   - Used by: Laravel migrations, models in `app/Models/`
   - Configuration: `DB_*` variables in `.env`

2. **Direct PDO connections** (configured in `includes/db_connect.php`)
   - Used by: Most PHP views and business logic
   - Hardcoded connection: `$pdo` object with `glass_market` database

**Important**: When changing database credentials, update BOTH locations.

### Directory Structure

- **`public/`** - Web-accessible entry point
  - `index.php` - Homepage (uses traditional PHP, not Laravel routing)
  - Compiled CSS/JS assets served from here

- **`resources/views/`** - PHP view files (NOT Blade templates)
  - Regular user pages: `browse.php`, `categories.php`, `pricing.php`, etc.
  - `admin/` - Admin-only pages (dashboard, user management, Mollie integration)

- **`includes/`** - Shared PHP components
  - `navbar.php`, `footer.php` - Layout components
  - `subscription-check.php` - Validates user subscription status
  - `subscription-notification.php` - Displays subscription alerts
  - `admin-guard.php` - Admin authentication middleware
  - `db_connect.php` - PDO database connection

- **`database/classes/`** - Business logic classes
  - `mollie.php` - Mollie payment integration (loads `.env` manually)
  - `subscriptions.php` - Subscription management

- **`config.php`** - Dynamic URL configuration (auto-detects project folder)

### URL Configuration System

The project uses **dynamic URL detection** (in `config.php`) that works regardless of project folder name:

```php
// Available constants
BASE_URL      // e.g., /glass-market
PUBLIC_URL    // /glass-market/public
VIEWS_URL     // /glass-market/resources/views
CSS_URL       // /glass-market/public/css
JS_URL        // /glass-market/public/js
```

**Critical**: Always use these constants for links and assets. NEVER hardcode paths like `/glass-market/...`.

### Session Management

- Sessions started in most view files via `session_start()`
- Session variables:
  - `$_SESSION['user_logged_in']` - Boolean login status
  - `$_SESSION['user_id']` - Logged-in user ID
  - `$_SESSION['is_admin']` - Admin flag (boolean or int 1/0)
  - `$_SESSION['notification_dismissed_at']` - Timestamp for dismissed notifications

### Authentication Flow

1. **Regular users**: Login via `resources/views/login.php`
2. **Admins**: Separate login at `resources/views/admin/login.php`
3. **Admin protection**: Use `includes/admin-guard.php` at top of admin pages
4. **Logout**: Both user types have separate logout handlers

## Payment & Subscription System

### Mollie Integration

Payment processing uses the Mollie API (test mode):

- **API Key**: Configured in `.env` as `MOLLIE_TEST_API_KEY`
- **Class**: `database/classes/mollie.php` (loads `.env` manually, NOT via Laravel)
- **Library**: `mollie/mollie-api-php` via Composer

### Payment Flow

```
User selects plan on pricing.php
    ↓
resources/views/create-payment.php (creates Mollie payment)
    ↓
Redirects to Mollie checkout (external)
    ↓
User completes payment on Mollie
    ↓
Returns to resources/views/admin/mollie-return.php
    ↓
Subscription activated in user_subscriptions table
```

### Subscription Database Schema

**Table: `user_subscriptions`**
- `user_id` - Foreign key to users table
- `start_date` - Subscription start date
- `end_date` - Expiration date
- `is_trial` - Boolean (1 = free trial, 0 = paid)
- `is_active` - Boolean (1 = active, 0 = inactive)

**Table: `mollie_payments`**
- `payment_id` - Mollie payment ID (e.g., `tr_xxxxx`)
- `user_id` - User who made payment
- `amount` - Payment amount in EUR
- `status` - Payment status (open, paid, failed, canceled, expired)
- `months` - Subscription duration
- `created_at`, `paid_at` - Timestamps

### Subscription Plans

- **Free Trial**: 3 months, no payment
- **Monthly**: €9.99/month
- **Annual**: €99/year (12 months)

### Subscription Access Control

Use `includes/subscription-check.php` to validate access:

```php
<?php require_once __DIR__ . '/includes/subscription-check.php'; ?>
<!-- Now $subscription_status array is available -->

<?php if (!$subscription_status['has_access']): ?>
    <!-- Show upgrade prompt -->
<?php endif; ?>
```

**Admin bypass**: Admins always have `has_access = true` regardless of subscription.

## Common Commands

### Development
```bash
# Start development server (if using Laravel's server)
php artisan serve

# Watch and compile assets during development
npm run dev

# Build assets for production
npm run build

# Run code style fixer
./vendor/bin/pint
```

### Database
```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh migration (drops all tables)
php artisan migrate:fresh

# Access database via Tinker REPL
php artisan tinker
```

### Testing
```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test file
./vendor/bin/phpunit tests/Unit/ExampleTest.php

# Run tests with coverage
./vendor/bin/phpunit --coverage-html coverage
```

### Debugging
```bash
# Clear Laravel caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# View routes (note: most routes are PHP files, not Laravel routes)
php artisan route:list
```

## Key Implementation Details

### Environment Variables

The `database/classes/mollie.php` class **manually parses `.env`** (does not use Laravel's `env()` helper). When modifying Mollie configuration:

1. Edit `.env` file
2. Restart Apache in XAMPP (PHP-FPM may cache environment)
3. Check `resources/views/admin/diagnose-env.php` for debugging

### View File Patterns

Most PHP views follow this pattern:

```php
<?php
session_start();
require_once __DIR__ . '/../../config.php';  // Adjust path as needed
require_once __DIR__ . '/../../includes/subscription-check.php';
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
    <title>Page Title</title>
</head>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>
    <?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>

    <!-- Page content -->

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
```

### Admin Page Pattern

```php
<?php
session_start();
require_once __DIR__ . '/../../../includes/admin-guard.php';  // Verify admin access
require_once __DIR__ . '/../../../config.php';
?>
<!-- Admin content here -->
```

### Creating Payment Links

```php
// Example: Link to create a monthly subscription payment
<a href="<?php echo VIEWS_URL; ?>/create-payment.php?plan=monthly">
    Subscribe Monthly
</a>
```

The `create-payment.php` handler:
1. Loads `database/classes/mollie.php`
2. Creates payment via `createSubscriptionPayment()`
3. Redirects to Mollie checkout URL

## Special Files & Debugging

- **`resources/views/admin/test-mollie.php`** - Test Mollie API connection
- **`resources/views/admin/diagnose-env.php`** - Debug environment variables
- **`resources/views/admin/sandbox.php`** - Payment testing sandbox

## Frontend Assets

Assets are compiled via Vite:

- **Source**: `resources/css/app.css`, `resources/js/app.js`
- **Output**: `public/css/`, `public/js/`
- **Build tool**: Vite (configured in `vite.config.js`)

## Important Notes

### Laravel Framework Usage
While Laravel 9 is installed, this project uses it **minimally**:
- ✅ Migrations and database tools
- ✅ Composer autoloading
- ✅ Some helper functions
- ❌ **Not using**: Blade templates, Eloquent ORM (mostly), route files, controllers (mostly)

Most functionality is in traditional PHP files with direct PDO database access.

### Path Resolution
When creating new files, calculate relative paths carefully:
- From `public/index.php`: `../includes/navbar.php`
- From `resources/views/browse.php`: `../../includes/navbar.php`
- From `resources/views/admin/dashboard.php`: `../../../includes/navbar.php`

Always verify the correct `require_once` depth when including files.

### Database Name Discrepancy
- `.env` may say `DB_DATABASE=glass-market` (with hyphen)
- Actual database name: `glass_market` (with underscore)
- `includes/db_connect.php` uses: `glass_market`

**When creating migrations**, ensure they target the correct database name.

## Mollie Test Mode

For testing payments without real money:

- Use test API key starting with `test_`
- Test card: `4111 1111 1111 1111`, any future expiry, CVV: `123`
- Test mode payments auto-approve immediately
- Switch to live mode: Replace `MOLLIE_TEST_API_KEY` with `MOLLIE_LIVE_API_KEY` in `.env`

## Working with Subscriptions

### Activating Free Trial
```php
// In PHP view, after user selects free trial
$stmt = $pdo->prepare("
    INSERT INTO user_subscriptions (user_id, start_date, end_date, is_trial, is_active)
    VALUES (:user_id, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), 1, 1)
");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
```

### Checking Subscription Status
```php
<?php require_once __DIR__ . '/includes/subscription-check.php'; ?>

<?php if ($subscription_status['has_access']): ?>
    <!-- Full access content -->
<?php else: ?>
    <!-- Upgrade prompt -->
<?php endif; ?>

<!-- Days remaining: <?php echo $subscription_status['days_remaining']; ?> -->
```

### Expiration Notifications
Handled automatically by `includes/subscription-notification.php`:
- Shows warning when < 7 days remaining
- Shows error when expired
- Shows prompt when no subscription
- Users can dismiss for 24 hours

## Deployment Considerations

The project is designed for flexible deployment:

1. Clone to any directory under `htdocs/` (or any web server document root)
2. Rename folder as desired - `config.php` auto-detects base path
3. Update `.env` database credentials
4. Run `composer install` and `npm install`
5. Run migrations: `php artisan migrate`
6. Ensure proper file permissions for `storage/` and `bootstrap/cache/`

No URL hardcoding means the project works regardless of folder name.
