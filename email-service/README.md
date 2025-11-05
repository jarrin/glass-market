# Glass Market Email Service

A lightweight Rust-based email service for reliable Gmail SMTP delivery.

## Why Rust?

PHP/PHPMailer has issues with Gmail SMTP on Windows/XAMPP due to OpenSSL compatibility problems. This Rust microservice provides a reliable alternative.

## Build

```bash
cd email-service
cargo build --release
```

The compiled binary will be at: `target/release/glass-market-mailer.exe`

## Usage

### Quick Mode (Simple Emails)
```bash
glass-market-mailer.exe --quick "user@example.com" "Subject" "Body text"
glass-market-mailer.exe --quick "user@example.com" "Subject" "<h1>HTML</h1>" html
```

### JSON Mode (Complex Emails)
```bash
glass-market-mailer.exe email-request.json
```

**email-request.json:**
```json
{
  "to": "user@example.com",
  "to_name": "John Doe",
  "subject": "Welcome to Glass Market",
  "body": "<h1>Welcome!</h1><p>Thanks for joining.</p>",
  "is_html": true
}
```

## PHP Integration

```php
<?php
// Send simple email
$mailer = __DIR__ . '/email-service/target/release/glass-market-mailer.exe';
$to = escapeshellarg('user@example.com');
$subject = escapeshellarg('Test Email');
$body = escapeshellarg('Hello from PHP!');

exec("$mailer --quick $to $subject $body", $output, $returnCode);

if ($returnCode === 0) {
    echo "Email sent!";
}
?>
```

## Environment Variables

- `GMAIL_USER`: Gmail address (default: musieatsbeha633@gmail.com)
- `GMAIL_PASS`: Google App Password (default: dfylmduqfpapcsqp)

Set these for security instead of hardcoding credentials.
