# Gmail Email Configuration - Setup Complete ✅

## What Was Done

### 1. Updated .env Configuration
- ✅ Changed `MAIL_HOST` from `mailpit` to `smtp.gmail.com`
- ✅ Changed `MAIL_PORT` from `1025` to `587` (Gmail TLS port)
- ✅ Set `MAIL_ENCRYPTION` to `tls`
- ✅ Added `GMAIL_FROM_EMAIL` variable
- ✅ Configured `MAIL_PASSWORD` to use `GOOGLE_APP_SECRET`

### 2. Enhanced EmailService.php
- ✅ Added Gmail SMTP configuration
- ✅ Implemented environment variable loader
- ✅ Created socket-based SMTP sender (works without PHPMailer)
- ✅ Added PHPMailer support (auto-detects if installed)
- ✅ Implemented proper Gmail authentication flow
- ✅ Added detailed error logging
- ✅ Supports TLS encryption for secure connections

### 3. Created Testing Tools
- ✅ `test-email.php` - Test script for verifying email configuration
- ✅ `GMAIL_SETUP_GUIDE.md` - Complete setup documentation

## How to Complete Setup

### Step 1: Update .env with Your Gmail

Open `.env` and replace these placeholders:

```env
MAIL_USERNAME=your-email@gmail.com  ← Change this
GMAIL_FROM_EMAIL="your-email@gmail.com"  ← Change this
```

With your actual Gmail address, for example:
```env
MAIL_USERNAME=glassmarket@gmail.com
GMAIL_FROM_EMAIL="glassmarket@gmail.com"
```

### Step 2: Verify Google App Password

Your `.env` already has:
```env
GOOGLE_APP_SECRET="eiiivxslqwwsvbxe"
```

**Important:** Make sure this is a valid 16-character Google App Password.

To generate a new one:
1. Go to https://myaccount.google.com/security
2. Click "App passwords" (you need 2FA enabled)
3. Generate new password for "Mail" app
4. Update `GOOGLE_APP_SECRET` if needed

### Step 3: Test Email Sending

Run the test script:

**Option A: Command Line**
```bash
cd c:\xampp\htdocs\glass-market
php test-email.php
```

**Option B: Browser**
```
http://localhost/glass-market/test-email.php
```

Before running, update the test email in `test-email.php`:
```php
$test_email = 'your-email@gmail.com';  // Change this to your email
```

### Step 4: Verify Emails Arrive

Check your inbox for:
1. ✉️ Welcome Email - "Welcome to Glass Market"
2. ✉️ New Listing Notification - "New Glass Product Available"

**Note:** Check spam folder if not in inbox.

## Email Service Features

### Automatic Email Sending

Emails are now automatically sent for:

1. **User Registration** (`register.php`)
   - Welcome email with account details
   - Tips for getting started

2. **New Listings** (`includes/notify-new-listing.php`)
   - Sent to users who enabled "New Listing" notifications
   - Includes product details and direct link

### Manual Email Testing

You can manually test emails:

```php
require_once 'app/Services/EmailService.php';

$emailService = new EmailService();

// Test welcome email
$emailService->sendWelcomeEmail(
    'test@example.com',
    'Test User'
);

// Test listing notification
$listing = [
    'title' => 'Test Product',
    'glass_type' => 'Float Glass',
    'quantity' => '500 kg',
    'location' => 'Amsterdam'
];
$emailService->sendNewListingNotification(
    'buyer@example.com',
    'Buyer Name',
    $listing
);
```

## Technical Implementation

### EmailService Methods

```php
class EmailService {
    // Send welcome email to new users
    public function sendWelcomeEmail($to_email, $to_name)
    
    // Send new listing notification
    public function sendNewListingNotification($to_email, $to_name, $listing)
    
    // Generic email sender
    public function sendEmail($to, $to_name, $subject, $message)
}
```

### SMTP Connection Flow

1. Connect to `smtp.gmail.com:587`
2. Initiate TLS encryption
3. Authenticate with Gmail credentials
4. Send email with HTML template
5. Log result to database
6. Close connection

### Fallback Options

The system tries multiple sending methods:

1. **PHPMailer** (if installed via Composer)
   - Most reliable
   - Full error reporting
   - Recommended for production

2. **Socket SMTP** (built-in fallback)
   - Custom implementation
   - No external dependencies
   - Works with basic PHP installation

3. **PHP mail()** (legacy fallback)
   - Simple but unreliable
   - May go to spam
   - Used if SMTP fails

## Optional: Install PHPMailer

For better reliability, install PHPMailer:

```bash
cd c:\xampp\htdocs\glass-market
composer require phpmailer/phpmailer
```

The system will automatically detect and use it.

## Troubleshooting

### "Authentication Failed" Error

- ✅ Check `MAIL_USERNAME` matches `GMAIL_FROM_EMAIL`
- ✅ Verify `GOOGLE_APP_SECRET` is correct 16-character password
- ✅ Ensure 2-Factor Authentication is enabled on Gmail
- ✅ Make sure you're using App Password, not account password

### "Connection Timeout" Error

- ✅ Check firewall allows port 587
- ✅ Disable antivirus email protection temporarily
- ✅ Try alternative port 465 with SSL encryption

### Emails Going to Spam

- ✅ Use your Gmail address in `MAIL_FROM_ADDRESS`
- ✅ Don't send too many emails quickly (Gmail limits)
- ✅ Ask recipients to mark as "Not Spam"

### Can't Find .env File

The file is located at:
```
c:\xampp\htdocs\glass-market\.env
```

Show hidden files in Windows Explorer to see it.

## Email Logging

All sent emails are logged in the database:

```sql
SELECT * FROM email_logs 
ORDER BY sent_at DESC 
LIMIT 10;
```

Check this table if emails aren't being received.

## Gmail Sending Limits

**Free Gmail Account:**
- 500 emails per day
- Rate limited to prevent spam

**Google Workspace:**
- 2,000 emails per day

If you need higher volume:
- Consider SendGrid (100/day free)
- Or Mailgun (5,000/month free)
- Or Amazon SES (pay as you go)

## Current Status

✅ EmailService class configured for Gmail SMTP  
✅ Environment variables set up  
✅ Test script created  
✅ Documentation complete  
⏳ Waiting for your Gmail credentials  
⏳ Ready to test email sending  

## Next Steps

1. **Update .env** with your Gmail address (see Step 1 above)
2. **Verify App Password** is correct (see Step 2 above)
3. **Run test-email.php** to verify setup (see Step 3 above)
4. **Check inbox** for test emails
5. **Register test user** to verify welcome emails work
6. **Create test listing** to verify notifications work

## Files Modified

- ✅ `.env` - Gmail SMTP configuration
- ✅ `app/Services/EmailService.php` - Enhanced with Gmail support
- ✅ `test-email.php` - New test script
- ✅ `GMAIL_SETUP_GUIDE.md` - Complete documentation

## Need Help?

Check the detailed guide:
```
c:\xampp\htdocs\glass-market\GMAIL_SETUP_GUIDE.md
```

Or review the error logs:
```
c:\xampp\htdocs\glass-market\storage\logs\laravel.log
```
