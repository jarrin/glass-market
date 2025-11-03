# Laravel Routing Setup Guide

## Overview
This application now uses Laravel's routing system instead of direct PHP file access.

## How It Works

### 1. Routes Definition
All routes are defined in `routes/web.php`:

```php
Route::get('/browse', function () {
    return view('browse');
})->name('browse');
```

### 2. Route Helper Function
The `route()` helper function (in `includes/route-helpers.php`) generates URLs:

```php
<a href="<?php echo route('browse'); ?>">Browse</a>
```

This generates: `http://localhost/browse`

### 3. URL Rewriting
The `.htaccess` file in the `public` directory routes all requests through `index.php`:

```apache
RewriteRule ^ index.php [L]
```

## Apache Configuration for XAMPP

### Step 1: Update httpd.conf
Edit `C:\xampp\apache\conf\httpd.conf`:

1. Find and uncomment (remove `#`):
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

2. Find all instances of `AllowOverride None` and change to:
```apache
AllowOverride All
```

### Step 2: Set Document Root to Public Folder
In `httpd.conf` or `httpd-vhosts.conf`, set:

```apache
DocumentRoot "C:/xampp/htdocs/glass-market/public"
<Directory "C:/xampp/htdocs/glass-market/public">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

### Step 3: Optional - Create Virtual Host
Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName glassmarket.local
    DocumentRoot "C:/xampp/htdocs/glass-market/public"
    <Directory "C:/xampp/htdocs/glass-market/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Then add to `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 glassmarket.local
```

### Step 4: Restart Apache
Restart Apache from XAMPP Control Panel.

## Access URLs

### Without Virtual Host:
- Home: `http://localhost/`
- Browse: `http://localhost/browse`
- About: `http://localhost/about`

### With Virtual Host:
- Home: `http://glassmarket.local/`
- Browse: `http://glassmarket.local/browse`
- About: `http://glassmarket.local/about`

## Available Routes

All routes are named and can be accessed using `route('name')`:

### Public Routes:
- `home` → `/`
- `browse` → `/browse`
- `about` → `/about`
- `login` → `/login`
- `register` → `/register`
- `sellers` → `/sellers`

### User Routes:
- `profile` → `/profile`
- `logout` → `/logout`
- `my-listings` → `/my-listings`

### Content Pages:
- `help` → `/help`
- `contact` → `/contact`
- `shipping` → `/shipping`
- `returns` → `/returns`
- `seller-guidelines` → `/seller-guidelines`
- `privacy` → `/privacy`
- `terms` → `/terms`

### Listing Management:
- `listings` → `/listings`
- `edit-listing` → `/edit-listing`
- `pricing` → `/pricing`

## Usage in Views

### Blade Templates (*.blade.php):
```blade
<a href="{{ route('browse') }}">Browse</a>
```

### Plain PHP Files:
```php
<?php require_once __DIR__ . '/includes/route-helpers.php'; ?>
<a href="<?php echo route('browse'); ?>">Browse</a>
```

### Included Files (navbar, footer):
Route helpers are automatically loaded, just use:
```php
<a href="<?php echo route('browse'); ?>">Browse</a>
```

## Troubleshooting

### Issue: 404 Not Found
**Solution:**
1. Check `.htaccess` file exists in `public/` directory
2. Verify `mod_rewrite` is enabled in Apache
3. Ensure `AllowOverride All` is set in httpd.conf
4. Restart Apache

### Issue: Routes don't work on XAMPP
**Solution:**
1. Make sure document root points to `public/` folder
2. Check that `.htaccess` file is being read
3. Enable rewrite module in Apache config

### Issue: CSS/JS not loading
**Solution:**
1. Use `asset()` helper for static files
2. Ensure assets are in `public/` directory
3. Check file permissions

## Benefits of Using Laravel Routing

1. **Clean URLs**: `/browse` instead of `/browse.php`
2. **Named Routes**: Change URLs without updating all links
3. **Middleware Support**: Easy to add authentication, logging, etc.
4. **Route Parameters**: `/product/{id}` for dynamic routes
5. **Route Grouping**: Apply middleware to multiple routes
6. **Better SEO**: Clean, readable URLs

## Next Steps

1. Move all view files from `public/` to `resources/views/`
2. Convert plain PHP files to Blade templates
3. Add controllers for complex logic
4. Implement middleware for authentication
5. Use route groups for admin sections

## File Structure

```
glass-market/
├── public/
│   ├── index.php          # Laravel entry point
│   ├── .htaccess          # URL rewriting rules
│   ├── css/               # Compiled CSS
│   ├── js/                # Compiled JS
│   └── uploads/           # User uploads
├── routes/
│   └── web.php            # Route definitions
├── resources/
│   └── views/             # View templates
├── includes/
│   ├── navbar.php         # Navigation (uses routes)
│   ├── footer.php         # Footer (uses routes)
│   └── route-helpers.php  # Route helper functions
└── app/
    └── Http/
        └── Controllers/   # Future controllers
```

## Important Notes

- Always use `route()` helper for internal links
- Don't hardcode URLs
- Keep view files in `resources/views/`
- Keep public assets in `public/` directory
- Use Blade syntax for new templates
