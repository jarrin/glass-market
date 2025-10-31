# Payment Error Logging System

## Overview
Comprehensive error logging system for tracking and monitoring failed payment attempts in the Glass Market subscription system.

## What Was Fixed

### 1. Webhook URL Validation Error
**Problem**: Mollie API rejected payment creation because webhook URL (`http://localhost/...`) is unreachable from Mollie's servers.

**Solution**:
- Set `webhookUrl` to `null` for localhost testing
- Mollie accepts payments without webhooks in test mode
- Add comment for production setup with publicly accessible webhook URL

**File Modified**: [database/classes/mollie.php](database/classes/mollie.php:200-203)

### 2. Error Logging Infrastructure
**Created**:
- New database table: `payment_errors`
- Comprehensive error logging function in `create-payment.php`
- Admin interface to view and analyze errors

## Database Schema

### Table: `payment_errors`

```sql
CREATE TABLE `payment_errors` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `plan` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `error_message` text NOT NULL,
  `error_context` text DEFAULT NULL COMMENT 'JSON with request details',
  `payment_id` varchar(255) DEFAULT NULL COMMENT 'Mollie payment ID if created',
  `request_data` text DEFAULT NULL COMMENT 'JSON of the request that caused error',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `payment_errors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Features

### 1. Automatic Error Logging
Every payment error is automatically logged with:
- User information (ID, email, name)
- Plan details (trial/monthly/annual)
- Amount attempted
- Full error message from Mollie/system
- Context data (IP, user agent, session ID, timestamp)
- Request data (months, description, etc.)
- Payment ID (if Mollie payment was created before failure)

### 2. Admin Dashboard Integration
**Location**: [resources/views/admin/payment-errors.php](resources/views/admin/payment-errors.php)

**Features**:
- Statistics dashboard showing:
  - Total errors
  - Errors today
  - Errors in last 7 days
  - Number of affected users
- Detailed error table with:
  - Date/time of error
  - User information
  - Plan type badge
  - Amount
  - Error summary
  - Payment ID
- Expandable error details showing:
  - Full error message
  - JSON context data
  - JSON request data
- Pagination for large error lists
- User filtering capability
- Click-to-expand error details

### 3. User-Friendly Error Messages
When a payment fails:
1. User sees a clean, non-technical error message
2. Error is automatically logged in database
3. User is informed that the error has been logged
4. User can return to pricing page easily

## Error Flow

```
User attempts payment
    ↓
Payment creation fails (Mollie/validation/database error)
    ↓
logPaymentError() function called
    ↓
Error saved to payment_errors table with full context
    ↓
User sees friendly error message
    ↓
Admin can review error in payment-errors.php dashboard
```

## Files Modified/Created

### Modified:
1. **database/classes/mollie.php**
   - Line 203: Set webhookUrl to null for localhost testing
   - Added comments for production webhook setup

2. **resources/views/create-payment.php**
   - Added `logPaymentError()` function (lines 96-125)
   - Integrated error logging at every failure point
   - Improved error messages for users

3. **resources/views/admin/dashboard.php**
   - Added "Payment Errors" button in Quick Actions section
   - Styled with red warning colors for visibility

### Created:
1. **database/migrations/2025_10_31_create_payment_errors_table.sql**
   - SQL migration for payment_errors table

2. **resources/views/admin/payment-errors.php**
   - Full admin interface for viewing errors
   - Statistics, table view, detailed error display
   - Pagination and filtering

3. **PAYMENT_ERROR_LOGGING.md** (this file)
   - Documentation of the error logging system

## Usage

### For Admins:

1. **View Payment Errors**:
   ```
   Navigate to: Admin Dashboard → Payment Errors
   Or directly: /resources/views/admin/payment-errors.php
   ```

2. **Analyze Error**:
   - Click any error row to expand full details
   - Review error message, context, and request data
   - Identify patterns (same user, same plan, time-based)

3. **Filter by User**:
   ```
   URL: payment-errors.php?user_id=123
   ```

### For Developers:

1. **Log Custom Payment Error**:
   ```php
   logPaymentError(
       $pdo,              // PDO connection
       $user_id,          // User ID
       'monthly',         // Plan name
       9.99,              // Amount
       'Custom error',    // Error message
       ['key' => 'val'],  // Optional: request data
       'tr_xxxxx'         // Optional: payment ID
   );
   ```

2. **Query Recent Errors**:
   ```sql
   SELECT * FROM payment_errors
   ORDER BY created_at DESC
   LIMIT 10;
   ```

3. **Find User-Specific Errors**:
   ```sql
   SELECT * FROM payment_errors
   WHERE user_id = 123
   ORDER BY created_at DESC;
   ```

## Testing

### Test Payment Flow:

1. **Attempt Paid Subscription**:
   ```
   1. Login as regular user
   2. Go to pricing page
   3. Click "Subscribe Now" on Monthly/Annual plan
   4. Should redirect to Mollie checkout
   5. Complete test payment
   ```

2. **Verify Error Logging Works**:
   ```
   1. Temporarily break Mollie API key in .env
   2. Attempt payment
   3. Check payment_errors table has new entry
   4. View error in admin dashboard
   ```

### Verify Webhook Fix:

1. **Before Fix**:
   - Error: "webhook URL is invalid because it is unreachable from Mollie's point of view"
   - Payment creation failed

2. **After Fix**:
   - Webhook URL set to null
   - Payment created successfully
   - Redirects to Mollie checkout
   - Can complete test payment

## Production Deployment

### Before Going Live:

1. **Update Webhook URL**:
   ```php
   // In database/classes/mollie.php, line 203
   // Change from:
   $webhookUrl = null;

   // To:
   $webhookUrl = 'https://yourdomain.com/glass-market/resources/views/admin/mollie-webhook.php';
   ```

2. **Verify Webhook Accessibility**:
   ```bash
   curl https://yourdomain.com/glass-market/resources/views/admin/mollie-webhook.php
   # Should return 200 OK (not 404/403)
   ```

3. **Switch to Live API Keys**:
   ```env
   # In .env
   MOLLIE_LIVE_API_KEY="live_xxxxxxxxxxxxx"
   ```

4. **Update Mollie Class**:
   ```php
   // Use live key instead of test key
   $this->apiKey = getenv('MOLLIE_LIVE_API_KEY');
   ```

## Monitoring

### Key Metrics to Watch:

1. **Error Rate**:
   ```sql
   SELECT DATE(created_at) as date, COUNT(*) as errors
   FROM payment_errors
   GROUP BY DATE(created_at)
   ORDER BY date DESC;
   ```

2. **Most Common Errors**:
   ```sql
   SELECT error_message, COUNT(*) as count
   FROM payment_errors
   GROUP BY error_message
   ORDER BY count DESC
   LIMIT 10;
   ```

3. **Affected Users**:
   ```sql
   SELECT u.email, u.name, COUNT(pe.id) as error_count
   FROM payment_errors pe
   JOIN users u ON pe.user_id = u.id
   GROUP BY pe.user_id
   ORDER BY error_count DESC;
   ```

## Troubleshooting

### Common Issues:

1. **"Mollie is not configured"**:
   - Check .env file exists
   - Verify MOLLIE_TEST_API_KEY is set
   - Restart Apache (env vars cached)

2. **"Mollie API client not initialized"**:
   - Run `composer install`
   - Verify vendor/mollie directory exists

3. **Errors not appearing in admin dashboard**:
   - Check payment_errors table exists
   - Verify foreign key constraint on user_id
   - Check admin user has proper permissions

4. **Payment succeeds but no subscription activated**:
   - Check mollie-return.php processing
   - Verify user_subscriptions table
   - Check Mollie payment status

## Security Considerations

1. **Admin-Only Access**:
   - Payment errors page protected by admin-guard.php
   - Only admins can view error details

2. **No Sensitive Data**:
   - Payment card details never logged
   - Only Mollie payment IDs stored
   - User context sanitized

3. **Error Message Sanitization**:
   - All error output uses `htmlspecialchars()`
   - Prevents XSS attacks

## Future Enhancements

Potential improvements:

1. **Email Notifications**:
   - Alert admin when error rate spikes
   - Notify user of payment failure with retry link

2. **Error Categorization**:
   - Classify errors (network, validation, API, etc.)
   - Color-code by severity

3. **Automatic Retry**:
   - Queue failed payments for automatic retry
   - Exponential backoff strategy

4. **Export Functionality**:
   - CSV export of errors
   - Date range filtering

5. **Resolution Tracking**:
   - Mark errors as "resolved"
   - Add admin notes

## Support

For issues or questions:
1. Check error logs in payment_errors table
2. Review Mollie dashboard for payment status
3. Check Apache error logs for PHP errors
4. Contact Mollie support for API issues
