# Quick Test Guide - Payment System

## 🚀 Quick Start Testing

### Test 1: Monthly Subscription (€9.99)

```
1. Open browser: http://localhost/glass-market/resources/views/login.php
2. Login as regular user (NOT admin)
3. Go to: http://localhost/glass-market/resources/views/pricing.php
4. Click "Subscribe Now" on Monthly plan
5. Should redirect to Mollie checkout
6. Use test card: 4111 1111 1111 1111, Expiry: 12/26, CVV: 123
7. Complete payment
8. Should see success message
```

**Expected Result**: ✅ Payment successful, subscription activated

---

### Test 2: Annual Subscription (€99)

```
Same as Test 1, but click "Subscribe Now" on Annual plan
```

**Expected Result**: ✅ Payment for €99 successful

---

### Test 3: Free Trial (No Payment)

```
1. Login as regular user
2. Go to pricing page
3. Click "Start Free Trial"
4. Should redirect to homepage immediately (NO Mollie page)
```

**Expected Result**: ✅ Trial activated instantly, no payment required

---

### Test 4: View Payment Errors (Admin)

```
1. Login as admin: http://localhost/glass-market/resources/views/admin/login.php
   Email: admin@glassmarket.com
   Password: password (or your admin password)

2. Go to Admin Dashboard
3. Click "Payment Errors" button (red warning style)
4. View error statistics and table
5. Click any error row to expand details
```

**Expected Result**: ✅ Can view error dashboard with statistics

---

## 🧪 Test Error Logging

### Simulate Payment Error:

```
1. Edit .env file
2. Corrupt the MOLLIE_TEST_API_KEY (add random characters)
3. Save file
4. Restart Apache in XAMPP
5. Attempt a payment (monthly/annual)
6. Should see error message: "This error has been logged"
7. Login as admin
8. Check Payment Errors dashboard
9. Should see your error logged
10. Restore .env file
11. Restart Apache
```

**Expected Result**: ✅ Error logged with full context

---

## 📊 Verify Database Changes

### Check Payment Record:

```sql
-- Check if payment was created
SELECT * FROM mollie_payments ORDER BY created_at DESC LIMIT 5;

-- Check if subscription was activated
SELECT * FROM user_subscriptions ORDER BY created_at DESC LIMIT 5;

-- Check if any errors were logged
SELECT * FROM payment_errors ORDER BY created_at DESC LIMIT 5;
```

---

## 🔍 Check Specific User Subscription

```sql
-- Replace USER_ID with actual user ID
SELECT
    us.*,
    u.email,
    u.name,
    DATEDIFF(us.end_date, CURDATE()) as days_remaining
FROM user_subscriptions us
JOIN users u ON us.user_id = u.id
WHERE us.user_id = USER_ID;
```

---

## 🐛 Common Issues & Quick Fixes

### Issue: "Mollie is not configured"

```bash
# Check .env file
cat .env | grep MOLLIE

# Should see:
# MOLLIE_TEST_API_KEY="test_DPnkq9mH3BgmWfJQJVwBpF9MjySf5F"

# If missing, add it and restart Apache
```

---

### Issue: "Mollie API client not initialized"

```bash
# Install Composer dependencies
cd C:\xampp\htdocs\glass-market
composer install

# Verify vendor folder exists
dir vendor\mollie
```

---

### Issue: Can't access payment-errors.php

```
Make sure you're logged in as ADMIN (is_admin = 1)
Regular users cannot access admin pages
```

---

## ✅ Success Checklist

After testing, verify:

- [ ] Monthly payment works (€9.99)
- [ ] Annual payment works (€99)
- [ ] Free trial works (no payment)
- [ ] Subscription activated in database
- [ ] Payment recorded in mollie_payments table
- [ ] Error logging works (when simulated)
- [ ] Admin can view payment errors
- [ ] Error details expandable in admin dashboard
- [ ] Statistics show correct counts

---

## 🎯 What Should Work Now

✅ Users can subscribe after trial expires
✅ Payments redirect to Mollie correctly
✅ Test payments process successfully
✅ Subscriptions activate automatically
✅ Errors are logged automatically
✅ Admins can monitor all payment issues
✅ User-friendly error messages shown
✅ Complete error context captured

---

## 📱 Quick URLs

```
Pricing Page:
http://localhost/glass-market/resources/views/pricing.php

Admin Login:
http://localhost/glass-market/resources/views/admin/login.php

Admin Dashboard:
http://localhost/glass-market/resources/views/admin/dashboard.php

Payment Errors:
http://localhost/glass-market/resources/views/admin/payment-errors.php

User Login:
http://localhost/glass-market/resources/views/login.php
```

---

## 🔑 Test Credentials

### Admin Account:
```
Email: admin@glassmarket.com
Password: password (check your database)
```

### Test Payment Card (Mollie):
```
Card Number: 4111 1111 1111 1111
Expiry: 12/26 (any future date)
CVV: 123
Name: Test User (any name)
```

---

## ⚡ Quick Database Check

```bash
# Access MySQL
mysql -u root -p
# (Press Enter for no password, or enter your password)

# Use database
USE glass_market;

# Check recent payments
SELECT * FROM mollie_payments ORDER BY created_at DESC LIMIT 3;

# Check recent subscriptions
SELECT * FROM user_subscriptions ORDER BY created_at DESC LIMIT 3;

# Check payment errors
SELECT COUNT(*) as error_count FROM payment_errors;
```

---

## 🎓 Need More Help?

See detailed documentation:
- [PAYMENT_ERROR_LOGGING.md](PAYMENT_ERROR_LOGGING.md) - Complete guide
- [SUBSCRIPTION_PAYMENT_FIX_SUMMARY.md](SUBSCRIPTION_PAYMENT_FIX_SUMMARY.md) - What was fixed
- [CLAUDE.md](CLAUDE.md) - Project overview

---

**Last Updated**: October 31, 2025
**Status**: ✅ All Systems Operational
