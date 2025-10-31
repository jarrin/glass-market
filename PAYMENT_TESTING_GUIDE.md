# Payment Testing Guide - Complete Flow

## 🎯 What We Fixed

1. **Webhook URL Issue** - Set to `null` for localhost (Mollie can't reach localhost)
2. **Payment Lookup** - mollie-return.php now finds payments by user_id instead of relying on URL param
3. **Subscription Activation** - Properly creates/updates subscriptions after payment
4. **Error Logging** - All errors logged to `payment_errors` table
5. **Debugging Tools** - Added check-subscription.php for status checking

---

## 🧪 Step-by-Step Testing

### Test 1: Check Current Subscription Status

**Before making any payment**, check your current status:

```
URL: http://localhost/glass-market/resources/views/admin/check-subscription.php
```

This shows:
- Current subscription (if any)
- Days remaining
- Recent payments
- Raw database data

---

### Test 2: Make a Monthly Payment (€9.99)

1. **Login** as regular user
   ```
   URL: http://localhost/glass-market/resources/views/login.php
   ```

2. **Go to Pricing Page**
   ```
   URL: http://localhost/glass-market/resources/views/pricing.php
   ```

3. **Click "Subscribe Now"** on Monthly plan

4. **You should see**:
   - Redirect to `create-payment.php?plan=monthly`
   - Then redirect to Mollie checkout page
   - Mollie payment form with €9.99

5. **On Mollie page**, enter test details:
   ```
   Card Number: 4111 1111 1111 1111
   Expiry: 12/26 (any future date)
   CVV: 123
   Name: Test User
   ```

6. **Click "Pay"**

7. **You should be redirected to**:
   ```
   mollie-return.php?user_id=YOUR_ID
   ```

8. **Check the page**:
   - Should show "Payment Successful!"
   - Should say "Your subscription has been activated"
   - Button: "Go to Homepage"

9. **Verify subscription activated**:
   ```
   URL: http://localhost/glass-market/resources/views/admin/check-subscription.php
   ```

   Should show:
   - Status: ACTIVE
   - End Date: 1 month from today
   - Is Active: ✅ Yes
   - Payment status: PAID

---

### Test 3: Check Database Directly

```sql
-- Check mollie_payments table
SELECT * FROM mollie_payments WHERE user_id = YOUR_USER_ID ORDER BY created_at DESC;

-- Check user_subscriptions table
SELECT * FROM user_subscriptions WHERE user_id = YOUR_USER_ID;

-- Check payment_errors (should be empty if successful)
SELECT * FROM payment_errors WHERE user_id = YOUR_USER_ID;
```

**Expected Results**:

**mollie_payments**:
- `payment_id`: Starts with `tr_`
- `status`: `paid`
- `amount`: `9.99`
- `months`: `1`
- `paid_at`: Current timestamp

**user_subscriptions**:
- `is_active`: `1`
- `is_trial`: `0`
- `start_date`: Today's date
- `end_date`: 1 month from today

---

## 🐛 Troubleshooting

### Issue: Stuck on "Processing Payment..."

**Possible Causes**:
1. Mollie payment not found in database
2. Payment status still "open"
3. Database error

**How to Debug**:

1. **Check Apache error log**:
   ```
   C:\xampp\apache\logs\error.log
   ```

   Look for lines like:
   ```
   Mollie Return - GET params: Array...
   Found payment ID: tr_xxxxx for user: 123
   Payment tr_xxxxx successfully processed for user 123
   ```

2. **Check database**:
   ```sql
   SELECT * FROM mollie_payments WHERE user_id = YOUR_ID ORDER BY created_at DESC LIMIT 1;
   ```

   - If `status` = 'open': Payment not yet completed by Mollie
   - If no record: Payment creation failed

3. **Manual check via Mollie**:
   - Go to Mollie dashboard
   - Check payment status
   - If paid in Mollie but not in database, run the return URL manually

4. **Manually trigger return processing**:
   ```
   URL: http://localhost/glass-market/resources/views/admin/mollie-return.php?user_id=YOUR_USER_ID
   ```

---

### Issue: Subscription Not Activated

**Check**:

1. **Verify payment is paid**:
   ```sql
   SELECT status FROM mollie_payments WHERE user_id = YOUR_ID ORDER BY created_at DESC LIMIT 1;
   ```

   Should be `paid`, not `open`

2. **Check user_subscriptions table**:
   ```sql
   SELECT * FROM user_subscriptions WHERE user_id = YOUR_ID;
   ```

   - If no record: mollie-return.php didn't create it
   - If `is_active = 0`: Something went wrong

3. **Check error log**:
   ```sql
   SELECT * FROM payment_errors WHERE user_id = YOUR_ID ORDER BY created_at DESC;
   ```

4. **Manually activate** (if needed):
   ```sql
   -- Delete old subscription
   DELETE FROM user_subscriptions WHERE user_id = YOUR_ID;

   -- Create new active subscription
   INSERT INTO user_subscriptions (user_id, start_date, end_date, is_trial, is_active)
   VALUES (YOUR_ID, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 MONTH), 0, 1);
   ```

---

### Issue: Payment Created But No Redirect

**Check**:

1. **JavaScript console** (F12 in browser)
   - Look for errors

2. **Network tab** (F12 → Network)
   - Check if redirect happened
   - Look for 302/301 redirects

3. **Check create-payment.php output**:
   - Should redirect, not show content
   - If seeing HTML: There's an error before redirect

---

### Issue: "No Payment Found"

**Possible Causes**:
1. Payment not saved to database
2. Different user_id in URL vs session
3. Database connection failed

**Debug**:

1. **Check mollie_payments table**:
   ```sql
   SELECT * FROM mollie_payments ORDER BY created_at DESC LIMIT 5;
   ```

2. **Check user_id** in URL matches your session:
   ```sql
   SELECT id, email FROM users WHERE id = YOUR_ID;
   ```

3. **Check Apache error log** for SQL errors

---

## 📊 Monitoring Payment Flow

### Check Payment Creation

**Location**: `create-payment.php`

**What it does**:
1. Gets plan from URL (`?plan=monthly`)
2. Calculates amount and months
3. Creates Mollie payment
4. Saves to `mollie_payments` table
5. Redirects to Mollie checkout

**Logs to check**:
```bash
# In Apache error.log, look for:
"Create Subscription Payment Error"
"Mollie Payment Error"
```

---

### Check Payment Return

**Location**: `mollie-return.php`

**What it does**:
1. Gets `user_id` from URL
2. Finds most recent payment for user
3. Checks Mollie payment status
4. If paid:
   - Updates `mollie_payments` status to 'paid'
   - Creates/updates `user_subscriptions`
5. Shows result page

**Logs to check**:
```bash
# In Apache error.log, look for:
"Mollie Return - GET params"
"Found payment ID: tr_xxxxx"
"Created new subscription for user"
"Payment successfully processed"
```

---

## 🔍 Advanced Debugging

### Enable Detailed Logging

Already enabled in `mollie-return.php`:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
```

### Check Mollie API Response

Add to `database/classes/mollie.php` in `getPayment()`:

```php
public function getPayment($paymentId)
{
    try {
        if (!$this->mollie) {
            return false;
        }

        $payment = $this->mollie->payments->get($paymentId);

        // Debug log
        error_log("Mollie Payment Details: " . json_encode([
            'id' => $payment->id,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'isPaid' => $payment->isPaid()
        ]));

        return $payment;
    } catch (Exception $e) {
        error_log('Mollie Get Payment Error: ' . $e->getMessage());
        return false;
    }
}
```

---

## ✅ Success Checklist

After successful payment, verify:

- [ ] `mollie_payments` has record with `status = 'paid'`
- [ ] `mollie_payments.paid_at` is set
- [ ] `user_subscriptions` has record with `is_active = 1`
- [ ] `user_subscriptions.end_date` is 1 month from start_date
- [ ] `user_subscriptions.is_trial = 0` (not trial)
- [ ] No records in `payment_errors` for this payment
- [ ] User can see subscription on check-subscription.php
- [ ] User has access to premium features

---

## 🔄 Testing Different Scenarios

### Scenario 1: New User, First Payment

**Expected**:
- No existing subscription
- New record in `user_subscriptions`
- `start_date` = today
- `end_date` = 1 month from today
- `is_trial = 0`, `is_active = 1`

---

### Scenario 2: User with Expired Trial

**Setup**:
```sql
-- Create expired trial
INSERT INTO user_subscriptions (user_id, start_date, end_date, is_trial, is_active)
VALUES (YOUR_ID, '2025-08-01', '2025-10-30', 1, 1);
```

**After Payment**:
- Old trial record deleted
- New subscription created
- `is_trial = 0`
- New dates set

---

### Scenario 3: User with Active Subscription (Renewal)

**Setup**:
```sql
-- Create active subscription ending in 5 days
INSERT INTO user_subscriptions (user_id, start_date, end_date, is_trial, is_active)
VALUES (YOUR_ID, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 0, 1);
```

**After Payment**:
- Existing record updated (not deleted)
- `end_date` extended by 1 month from current end_date
- Still `is_active = 1`

---

## 📝 Notes

### Test Mode Behavior

In Mollie test mode:
- Payments are instantly "paid" after you click pay
- No real money charged
- Can use test card numbers
- Webhook won't be called (we set it to null)

### Production Behavior

In production:
- Real payment processing
- Webhook will be called
- Need publicly accessible webhook URL
- Delays possible between payment and webhook

---

## 🚨 Common Errors & Fixes

### Error: "Mollie is not configured"

**Fix**:
```bash
# Check .env
cat .env | grep MOLLIE

# Should have:
MOLLIE_TEST_API_KEY="test_DPnkq9mH3BgmWfJQJVwBpF9MjySf5F"

# If missing, add it and restart Apache
```

---

### Error: "No user ID provided"

**Fix**:
- Ensure URL has `?user_id=123`
- Check session has `$_SESSION['user_id']`
- Verify user is logged in

---

### Error: "Could not retrieve payment information"

**Fix**:
1. Check Mollie API key is valid
2. Check internet connection
3. Check payment_id exists in Mollie dashboard
4. Run composer install to ensure Mollie SDK is present

---

## 🎓 Quick Reference

### URLs

```
Pricing Page:
http://localhost/glass-market/resources/views/pricing.php

Check Subscription:
http://localhost/glass-market/resources/views/admin/check-subscription.php

Payment Return (manual trigger):
http://localhost/glass-market/resources/views/admin/mollie-return.php?user_id=YOUR_ID

Payment Errors (admin):
http://localhost/glass-market/resources/views/admin/payment-errors.php
```

### SQL Queries

```sql
-- Check subscription
SELECT * FROM user_subscriptions WHERE user_id = YOUR_ID;

-- Check payments
SELECT * FROM mollie_payments WHERE user_id = YOUR_ID ORDER BY created_at DESC;

-- Check errors
SELECT * FROM payment_errors WHERE user_id = YOUR_ID ORDER BY created_at DESC;

-- Reset subscription
DELETE FROM user_subscriptions WHERE user_id = YOUR_ID;
DELETE FROM mollie_payments WHERE user_id = YOUR_ID;
```

---

## 📞 Still Having Issues?

1. Check Apache error log: `C:\xampp\apache\logs\error.log`
2. Check payment_errors table via admin dashboard
3. Use check-subscription.php to see current state
4. Check database tables directly
5. Review this guide from the beginning

---

**Last Updated**: October 31, 2025
**Status**: ✅ Payment system fully functional with debugging tools
