# Subscription Access Rules

## Pages Accessible WITHOUT Active Subscription

✅ **Public Pages** (Always accessible - No login required)
- `resources/views/about.php` - About Us
- `resources/views/support.php` - Support/Help
- `resources/views/contact.php` - Contact
- `resources/views/login.php` - Login
- `resources/views/register.php` - Registration
- `resources/views/pricing.php` - Pricing/Plans
- `resources/views/subscription.php` - Subscription selection

✅ **Profile Pages** (Logged-in users only, but always accessible)
- `resources/views/profile.php?tab=subscription` - Subscription management tab
  - View current subscription status
  - Activate free trial
  - Upgrade plans
  - Cancel subscription

## Pages Requiring Active Subscription

🔒 **Homepage & Marketplace** (Requires active subscription)
- `public/index.php` - Homepage (shows featured listings)
- `resources/views/browse.php` - Browse all listings
- `resources/views/listings.php?id=X` - View listing details
- `resources/views/sellers.php` - Browse sellers directory
- `resources/views/seller-shop.php?id=X` - View seller shop
- `resources/views/create.php` - Create new listing (redirects to pricing if no access)
- `resources/views/edit-listing-modern.php` - Edit listings

🔒 **Profile Tabs** (Show restriction message when no active subscription)
- `resources/views/profile.php?tab=overview` - Account overview
- `resources/views/profile.php?tab=listings` - My listings
- `resources/views/profile.php?tab=saved` - Saved listings
- `resources/views/profile.php?tab=company` - Company management
- `resources/views/profile.php?tab=edit` - Edit profile
- `resources/views/profile.php?tab=notifications` - Notification preferences

🔒 **Company Features**
- `resources/views/company/create-company.php` - Create company
- `resources/views/company/edit-company.php` - Edit company
- `resources/views/seller-shop.php` - Seller shop page

## Admin Bypass

👑 **Admin users bypass ALL subscription checks**
- Full access to all features regardless of subscription status
- Admins are detected via: `$_SESSION['is_admin'] == 1`

## Automatic Actions When Subscription Expires

📦 **Listing Deactivation** (Automatic on page load)
- All user's personal listings set to `status = 'inactive'`
- All company listings (owned by user) set to `status = 'inactive'`
- Happens automatically when:
  - Subscription expires (`end_date < today`)
  - Subscription cancelled (`is_active = 0`)
  - No subscription found
- Logged in `error_log` with count
- Listings must be manually reactivated after renewal

## Implementation Files

**Core Files:**
- `includes/subscription-check.php` - Main access control logic
- `includes/subscription-notification.php` - Modal overlay notification
- `resources/views/components/subscription-required-message.php` - Restriction message component

**Database Tables:**
- `user_subscriptions` - Subscription records
- `mollie_payments` - Payment transactions
- `listings` - Listings with `status` column

## Testing Checklist

### Without Active Subscription:
- [ ] Can access homepage
- [ ] Can access about/support/contact pages
- [ ] Can view pricing page
- [ ] Can access profile subscription tab
- [ ] CANNOT access browse page (shows notification)
- [ ] CANNOT access create listing (redirects to pricing)
- [ ] CANNOT access listing details (shows notification)
- [ ] Profile tabs show "Subscription Required" message
- [ ] All user listings are marked inactive

### With Active Subscription:
- [ ] Full access to all marketplace pages
- [ ] Can create/edit listings
- [ ] Can view all profile tabs
- [ ] Listings remain active
- [ ] See expiration warning 7 days before end

### As Admin:
- [ ] Full access regardless of subscription
- [ ] No notifications shown
- [ ] Listings not auto-deactivated
