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

**Note**: Most database credentials are hardcoded in various files. While `.env` exists, many components connect directly using hardcoded values (`root` user, no password, `glass_market` database).

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
  - `notify-new-listing.php` - Email notifications for new listings
  - `save-listing.php` - Save/bookmark listing functionality
  - `push-notification-checker.php` - Real-time notification polling

- **`database/classes/`** - Business logic classes
  - `mollie.php` - Mollie payment integration (loads `.env` manually)
  - `subscriptions.php` - Subscription management

- **`resources/views/handlers/`** - Form submission handlers
  - `profile-update-handler.php` - User profile updates
  - `company-handler.php` - Company profile management
  - `subscription-handler.php` - Subscription modifications
  - `notification-handler.php` - Notification preferences

- **`email-service/`** - Rust-based email microservice
  - Standalone Rust application for reliable Gmail SMTP delivery
  - Solves PHPMailer/OpenSSL compatibility issues on Windows/XAMPP

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

## Listings & Image Management

### Multi-Image System

Listings support **up to 20 images** with a designated **main display image**.

**Table: `listing_images`**
- `id` - Primary key
- `listing_id` - Foreign key to listings table
- `image_path` - Path to uploaded image
- `is_main` - Boolean (1 = main/featured image, 0 = additional)
- `display_order` - Order of display in carousel
- `created_at` - Timestamp

**Key Features:**
- First uploaded image automatically becomes main image
- Main image shown in browse/search results
- Image carousel on listing detail page with keyboard navigation
- AJAX-powered image management (set main, delete)
- Supports JPG, PNG, WebP (max 5MB per image)
- Storage: `public/uploads/listings/`

**Migration:** Run `php database/migrations/migrate_existing_images.php` to convert old single-image listings.

**Implementation Files:**
- [resources/views/create.php](resources/views/create.php) - Multi-upload during creation
- [resources/views/edit-listing-modern.php](resources/views/edit-listing-modern.php) - Image management UI
- [resources/views/listings.php](resources/views/listings.php) - Carousel display
- [MULTI-IMAGE-GUIDE.md](MULTI-IMAGE-GUIDE.md) - Detailed documentation

### Saved Listings

Users can save/bookmark listings for later viewing.

**Table: `saved_listings`**
- `id` - Primary key
- `user_id` - User who saved the listing
- `listing_id` - Saved listing reference
- `created_at` - When saved

**Handler:** [includes/save-listing.php](includes/save-listing.php) - AJAX endpoint for save/unsave

## Company & Seller Profiles

### Company System

Sellers can create company profiles with branding and product showcase.

**Features:**
- Company name, description, location
- Company logo and cover image
- Public company page: [resources/views/company/](resources/views/company/)
- Seller shop page: [resources/views/seller-shop.php](resources/views/seller-shop.php)
- Shows all listings from company/seller

**Handler:** [resources/views/handlers/company-handler.php](resources/views/handlers/company-handler.php)

## Email Service

### Rust-Based Email Microservice

Due to PHPMailer/OpenSSL compatibility issues on Windows/XAMPP, the project uses a custom **Rust email service**.

**Location:** `email-service/` directory

**Build:**
```bash
cd email-service
cargo build --release
```

**Binary:** `email-service/target/release/glass-market-mailer.exe`

**Usage from PHP:**
```php
use App\Services\RustMailer;

$mailer = new RustMailer();
$mailer->sendEmail(
    to: 'user@example.com',
    toName: 'John Doe',
    subject: 'Welcome!',
    body: '<h1>Welcome to Glass Market</h1>',
    isHtml: true
);
```

**Quick Mode (CLI):**
```bash
glass-market-mailer.exe --quick "user@example.com" "Subject" "Body text"
glass-market-mailer.exe --quick "user@example.com" "Subject" "<h1>HTML</h1>" html
```

**Environment Variables:**
- `GMAIL_FROM_EMAIL` - Sender email (hardcoded: musieatsbeha633@gmail.com)
- `GOOGLE_APP_SECRET` - Gmail app password (hardcoded: dfylmduqfpapcsqp)

**Note:** Credentials are typically hardcoded in the Rust service, not always read from `.env`.

## Notification System

### Push Notifications

Real-time notifications for users when new listings are posted.

**Database Tables:**
- `notifications` - User notifications (new listings, messages, etc.)
- `users.notify_new_listings` - User preference for email notifications

**Implementation:**
- [includes/notify-new-listing.php](includes/notify-new-listing.php) - Creates notifications when listing posted
- [includes/push-notification-checker.php](includes/push-notification-checker.php) - Client-side polling script
- [includes/get-push-notifications.php](includes/get-push-notifications.php) - AJAX endpoint for unread notifications
- [includes/mark-notification-read.php](includes/mark-notification-read.php) - Mark as read handler

**Flow:**
1. Seller creates new listing
2. System calls `notify-new-listing.php`
3. Creates notification records for subscribed users
4. Sends email via Rust mailer (if user opted in)
5. Client polls for unread notifications
6. Shows notification badge in navbar

## Payment & Subscription System

### Overview

Glass Market uses a **subscription-based access model** with Mollie payment integration. All users require an active subscription to access platform features, except admins who bypass all checks.

### Subscription Plans

| Plan | Duration | Price | Type |
|------|----------|-------|------|
| **Free Trial** | 3 months | €0.00 | Auto-activated on registration |
| **Monthly** | 1 month | €9.99 | Paid via Mollie |
| **Annual** | 12 months | €99.00 | Paid via Mollie |

### Mollie Integration

Payment processing uses the Mollie API (test mode):

- **API Key**: `MOLLIE_TEST_API_KEY` in `.env`
- **Profile ID**: `PROFILE_ID` in `.env`
- **Class**: [database/classes/mollie.php](database/classes/mollie.php) (manually parses `.env`, NOT via Laravel)
- **Library**: `mollie/mollie-api-php` via Composer

### Database Schema

**`user_subscriptions`** - Core subscription records
- `id`, `user_id` (FK to users)
- `start_date`, `end_date` - Subscription period
- `is_trial` - Boolean (1 = free trial, 0 = paid)
- `is_active` - Boolean (1 = active, 0 = cancelled)
- `created_at`, `updated_at`

**`mollie_payments`** - Payment transaction log
- `payment_id` - Mollie transaction ID (e.g., `tr_xxxxx`)
- `user_id`, `amount`, `months`
- `status` - `open`, `paid`, `failed`, `canceled`, `expired`
- `created_at`, `updated_at`, `paid_at`

**`payment_errors`** - Comprehensive error logging
- `user_id`, `plan`, `amount`, `payment_id`
- `error_message` - Full error text
- `error_context` - JSON: IP, user agent, session
- `request_data` - JSON: Request parameters
- `created_at`

### Complete Subscription Flow

#### 1. New User Registration → Free Trial
```
User registers at register.php
    ↓
Account created in users table
    ↓
Subscription::createTrialSubscription() called
    ↓
user_subscriptions record (is_trial=1, 3 months)
    ↓
Email sent via Rust mailer
```
**Implementation:** [database/classes/subscriptions.php:27](database/classes/subscriptions.php#L27)

#### 2. User Selects Paid Plan
```
User visits pricing.php or subscription.php
    ↓
Clicks plan button
    ↓
Redirects to create-payment.php?plan=monthly
```
**Pages:** [resources/views/pricing.php](resources/views/pricing.php), [resources/views/subscription.php](resources/views/subscription.php)

#### 3. Payment Creation
```
create-payment.php validates user & plan
    ↓
If free trial: Creates subscription directly, redirects home
    ↓
If paid: Checks for existing active paid subscription
    ↓
Loads MolliePayment class
    ↓
Calls createSubscriptionPayment($user_id, $months, $pdo)
    ↓
Creates mollie_payments record (status='open')
    ↓
Gets Mollie checkout URL
    ↓
Redirects user to Mollie payment page
```
**Implementation:** [resources/views/create-payment.php](resources/views/create-payment.php)

**Error Handling:** All errors logged via `logPaymentError()` to `payment_errors` table

#### 4. Payment Processing on Mollie
```
User completes payment on Mollie.com
    ↓
Mollie processes payment
    ↓
Redirects to mollie-return.php?user_id=X
```

#### 5. Payment Return & Activation
```
mollie-return.php receives user_id
    ↓
Queries mollie_payments for most recent 'open' payment
    ↓
Fetches payment status from Mollie API
    ↓
If PAID:
  - Update mollie_payments: status='paid', paid_at=NOW()
  - Check if active subscription exists
  - Extend or create user_subscriptions record
  - Set is_trial=0, is_active=1
  - Show success page
    ↓
If FAILED/CANCELED/EXPIRED:
  - Show error page
  - User can retry payment
```
**Implementation:** [resources/views/admin/mollie-return.php](resources/views/admin/mollie-return.php)

#### 6. Access Control (Every Page Load)
```
Page includes subscription-check.php
    ↓
Checks: Admin? Logged in? Active subscription?
    ↓
Returns $subscription_status array
    ↓
If show_notification=true:
  Display modal blocking access
```
**Files:** [includes/subscription-check.php](includes/subscription-check.php), [includes/subscription-notification.php](includes/subscription-notification.php)

### Subscription Access Control

**Standard Pattern:**
```php
<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/subscription-check.php';
// $subscription_status array now available
?>

<?php if (!$subscription_status['has_access']): ?>
    <div>Please subscribe to access this feature.</div>
<?php else: ?>
    <!-- Protected content -->
<?php endif; ?>

<?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>
```

**$subscription_status Array:**
```php
[
    'has_access' => true/false,        // Can access content?
    'is_expired' => true/false,        // Subscription expired?
    'is_trial' => true/false,          // Trial subscription?
    'days_remaining' => 30,            // Days until expiration
    'end_date' => '2025-12-31',        // Expiration date
    'show_notification' => true/false, // Show modal?
    'notification_type' => 'expired'   // Modal type
]
```

**Notification Types:**
- `expired` - Subscription has ended
- `expiring_soon` - ≤7 days remaining (dismissible for 24hr)
- `no_subscription` - No subscription found
- `not_logged_in` - User not authenticated

**Admin Bypass:** Admins always have `has_access = true`, never see notifications.

### Subscription Management

**Cancel Subscription:**
- Form submits to [resources/views/handlers/subscription-handler.php](resources/views/handlers/subscription-handler.php)
- Sets `is_active = 0`
- Sends cancellation email via Rust mailer
- User retains access until `end_date`

**Upgrade from Trial:**
- `create-payment.php?plan=monthly&upgrade_from_trial=1`
- Cancels trial subscription (is_active=0, end_date=NOW())
- Creates new paid subscription

**Renew/Extend:**
- User purchases more months
- System extends `end_date` by adding months
- No duplicate subscription records

### Payment Error Logging

All payment failures automatically logged with full context.

**Admin Dashboard:** [resources/views/admin/payment-errors.php](resources/views/admin/payment-errors.php)

**Features:**
- Statistics (total, today, last 7 days, affected users)
- Detailed error table with expandable JSON
- User filtering, search, pagination
- Full request/response debugging data

### Testing & Debugging

**Test Pages:**
- [resources/views/admin/test-mollie.php](resources/views/admin/test-mollie.php) - API connection test
- [resources/views/admin/sandbox.php](resources/views/admin/sandbox.php) - Payment sandbox
- [resources/views/admin/diagnose-env.php](resources/views/admin/diagnose-env.php) - Environment debug
- [resources/views/admin/check-subscription.php](resources/views/admin/check-subscription.php) - User subscription status

**Mollie Test Mode:**
- Test card: `4111 1111 1111 1111`, any future expiry, CVV: `123`
- Payments auto-approve in test mode
- No real money charged

**Production Deployment:**
1. Replace `MOLLIE_TEST_API_KEY` with `MOLLIE_LIVE_API_KEY`
2. Update webhook URL (currently `null` for localhost)
3. Test with real transaction
4. Monitor `payment_errors` table

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

**Important**: While `.env` exists, many components use **hardcoded values** instead of reading from environment variables. This is a known pattern in the codebase.

**Key `.env` Variables:**
- `DB_*` - Database connection (often ignored in favor of hardcoded values)
- `MOLLIE_TEST_API_KEY` - Mollie payment API key
- `PROFILE_ID` - Mollie profile ID
- `MAIL_*` - Mail server settings (often unused, Rust mailer used instead)
- `GMAIL_FROM_EMAIL` - Sender email for Rust mailer
- `GOOGLE_APP_SECRET` - Gmail app password for Rust mailer

**Mollie Configuration:**
The `database/classes/mollie.php` class **manually parses `.env`** (does not use Laravel's `env()` helper). When modifying Mollie configuration:

1. Edit `.env` file
2. Restart Apache in XAMPP (PHP-FPM may cache environment)
3. Check `resources/views/admin/diagnose-env.php` for debugging

**Database Connections:**
Most files use hardcoded credentials:
```php
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';
```

**Email Service:**
Rust mailer typically uses hardcoded Gmail credentials, not environment variables.

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

### Form Handler Pattern

Form submissions follow a handler pattern in `resources/views/handlers/`:

```php
// Example: Profile update form
<form action="<?php echo VIEWS_URL; ?>/handlers/profile-update-handler.php" method="POST">
    <!-- Form fields -->
</form>
```

**Available Handlers:**
- `profile-update-handler.php` - User profile updates (name, email, bio)
- `company-handler.php` - Company profile management
- `subscription-handler.php` - Subscription cancellation/reactivation
- `notification-handler.php` - Notification preference updates

Each handler:
1. Validates POST data
2. Updates database
3. Sets session messages
4. Redirects back to referring page

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
