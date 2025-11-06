# Company Cover Image System Implementation

## Overview
Implemented a company cover image upload system to replace hardcoded placeholder images with real uploaded images or a fallback image.

---

## Changes Made

### 1. **Database Migration**
**File:** `database/migrations/add_cover_image_to_companies.sql`

Added `cover_image` column to the `companies` table:
```sql
ALTER TABLE `companies` 
ADD COLUMN `cover_image` VARCHAR(500) NULL DEFAULT NULL AFTER `logo`;
```

**⚠️ Run this migration:**
```bash
# Open phpMyAdmin and run the SQL file, or execute via command line:
mysql -u root glass_market < database/migrations/add_cover_image_to_companies.sql
```

---

### 2. **Registration Cleanup**
**File:** `resources/views/register.php`

**✅ Removed company fields from registration form:**
- Removed `company_name` field
- Removed `address` field  
- Removed `city` field
- Removed `country` field
- Removed backend logic that creates company during registration

**Result:** Registration is now focused only on user accounts. Company creation is done through the profile.

---

### 3. **Cover Image Upload Feature**
**File:** `resources/views/company/edit-company.php`

**✅ Added cover image management section:**
- Current cover image preview (600x200px)
- File upload form with validation
- Supports: JPG, PNG, GIF, WebP (max 5MB)
- Recommended size: 1200x400px
- Upload button with progress feedback

**Backend Features:**
- Validates file type and size
- Creates `/public/uploads/companies/` directory if needed
- Generates unique filenames: `company_{id}_cover_{timestamp}.{ext}`
- Deletes old cover images automatically
- Uses fallback if no image uploaded: `uploads/default/fallback_company.png`

**Upload Path:**
```
public/uploads/companies/company_123_cover_1699999999.jpg
```

---

### 4. **Removed Hardcoded Images**
**Files Updated:**
- `resources/views/sellers.php`
- `resources/views/seller-shop.php`

**Before:**
```php
$avatarUrl = "https://picsum.photos/seed/seller{$seller['id']}/600/600";
```

**After:**
```php
$avatarUrl = !empty($seller['cover_image']) 
    ? PUBLIC_URL . '/' . $seller['cover_image']
    : PUBLIC_URL . '/uploads/default/fallback_company.png';
```

**SQL Queries Updated:**
- Added `c.cover_image` to SELECT statements
- Added `c.cover_image` to GROUP BY clauses

---

## Fallback Image Setup

**Required File:** `public/uploads/default/fallback_company.png`

This image will be used when:
1. Company has no cover image uploaded
2. Cover image file is missing or deleted
3. New companies are created

**✅ Place the provided `fallback_company.png` file at:**
```
public/uploads/default/fallback_company.png
```

**Create directory if needed:**
```bash
mkdir -p public/uploads/default
# Then copy fallback_company.png to this folder
```

---

## File Structure

```
glass-market/
├── database/
│   └── migrations/
│       └── add_cover_image_to_companies.sql  [NEW]
├── public/
│   └── uploads/
│       ├── companies/                         [AUTO-CREATED]
│       │   └── company_*_cover_*.jpg          [UPLOADED FILES]
│       └── default/
│           └── fallback_company.png           [REQUIRED]
└── resources/
    └── views/
        ├── register.php                       [MODIFIED - Removed company fields]
        ├── sellers.php                        [MODIFIED - Uses cover_image]
        ├── seller-shop.php                    [MODIFIED - Uses cover_image]
        └── company/
            └── edit-company.php               [MODIFIED - Added upload UI]
```

---

## Usage Guide

### For Users:
1. **Navigate to Profile** → Company tab
2. Click **"Edit Company"**
3. Scroll to **"Company Cover Image"** section
4. See current cover image (or fallback if none)
5. Click **"Choose File"** and select an image
6. Click **"📤 Upload Cover Image"**
7. Image is instantly saved and displayed

### For Developers:
```php
// Get company cover image in any view:
$coverImage = !empty($company['cover_image']) 
    ? PUBLIC_URL . '/' . $company['cover_image']
    : PUBLIC_URL . '/uploads/default/fallback_company.png';
```

---

## Validation Rules

✅ **Allowed Types:** JPG, JPEG, PNG, GIF, WebP  
✅ **Max Size:** 5MB  
✅ **Recommended Dimensions:** 1200x400px (16:9 ratio)  
❌ **Rejects:** SVG, PDF, other file types  
❌ **Rejects:** Files over 5MB

---

## Security Features

1. ✅ File type validation (MIME type check)
2. ✅ File size validation (5MB limit)
3. ✅ Unique filename generation (prevents conflicts)
4. ✅ Ownership validation (only owner can upload)
5. ✅ Automatic old file cleanup
6. ✅ Safe file paths (no directory traversal)

---

## Testing Checklist

- [ ] Run SQL migration to add `cover_image` column
- [ ] Place `fallback_company.png` in `public/uploads/default/`
- [ ] Register a new user (verify no company fields shown)
- [ ] Create a company through profile
- [ ] Visit Edit Company page
- [ ] Verify fallback image is shown
- [ ] Upload a cover image (JPG)
- [ ] Verify image appears in preview
- [ ] Visit sellers page - verify cover image shows
- [ ] Visit seller shop page - verify cover image shows
- [ ] Upload a new cover image
- [ ] Verify old image is deleted from server

---

## Troubleshooting

### Image Not Showing?
1. Check if `cover_image` column exists in `companies` table
2. Verify `fallback_company.png` exists in `public/uploads/default/`
3. Check file permissions on `public/uploads/` directory (755)
4. Verify `PUBLIC_URL` constant is correctly set in `config.php`

### Upload Fails?
1. Check `public/uploads/companies/` directory exists
2. Verify directory has write permissions (755)
3. Check PHP `upload_max_filesize` and `post_max_size` settings
4. Review error logs for specific error messages

### Old Images Not Deleted?
- Non-fallback images are automatically deleted when new image is uploaded
- Fallback image is never deleted (it's shared across all companies)

---

## Summary

✅ **Removed hardcoded picsum images**  
✅ **Added cover_image column to database**  
✅ **Implemented cover image upload feature**  
✅ **Removed company fields from registration**  
✅ **Added fallback image support**  
✅ **Updated sellers pages to use real images**  

**All company pages now use real uploaded images or the fallback image instead of random placeholder images!**
