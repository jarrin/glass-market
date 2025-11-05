# Multi-Image System - Quick Reference Guide

## Overview
The Glass Market platform now supports **up to 20 images per listing** with a designated **main display image**.

## Database Structure

### Table: `listing_images`
```sql
- id (BIGINT, Primary Key)
- listing_id (BIGINT, Foreign Key → listings.id)
- image_path (VARCHAR, path to uploaded image)
- is_main (TINYINT, 1 = main image, 0 = additional image)
- display_order (INT, order of display)
- created_at (TIMESTAMP)
```

## Features

### 1. **Main Image Concept**
- Every listing can have ONE main image (marked with ⭐)
- The main image appears first in carousels and search results
- Click "Set Main" on any image to make it the main display image
- Main images have a blue border for easy identification

### 2. **Image Management**
- **Upload**: Up to 20 images per listing
- **Delete**: Click the delete button on any image (with confirmation)
- **Set Main**: Click "Set Main" to change which image is the featured display
- **Supported Formats**: JPG, JPEG, PNG, WebP
- **Size Limit**: 5MB per image

### 3. **Where Images Are Used**

#### Create Listing Page (`create.php`)
- Upload multiple images during listing creation
- First uploaded image automatically becomes the main image
- Shows preview of all selected images before upload

#### Edit Listing Page (`edit-listing-modern.php`)
- View all current images in a grid
- Main image has blue border and "⭐ Main" badge
- Hover over images to see action buttons
- Add more images (up to 20 total)
- Delete unwanted images
- Change main image by clicking "Set Main"

#### Listing Detail Page (`listings.php`)
- Shows main image in large display
- Image carousel with arrow navigation (← →)
- Thumbnail strip below main image
- Click thumbnails to switch images
- Keyboard navigation (arrow keys work!)
- Image counter (e.g., "1 / 5")

## User Interface

### Visual Indicators
- **Blue Border**: Main image
- **⭐ Badge**: Marks the main image
- **Hover Effects**: Shows delete/set main buttons
- **Image Counter**: "X / 20 images" shows quota

### Action Buttons
- **⭐ Set Main**: Make this image the main display (only on non-main images)
- **🗑️ Delete**: Remove image from listing (with confirmation)

## Migration

### Automatic Migration
The system automatically migrates old images:
- When you edit a listing with an old single image
- It converts to the new multi-image format
- Sets as the main image automatically

### Manual Migration
Run this script to migrate all existing images:
```bash
php database/migrations/migrate_existing_images.php
```

## Technical Details

### Image Upload Flow
1. User selects files (up to 20)
2. Client-side validation (file type, size)
3. Server processes each file
4. Saves to `public/uploads/listings/`
5. Creates database entry in `listing_images`
6. First image marked as `is_main = 1`

### AJAX Operations
- **Delete Image**: Async deletion with toast notification
- **Set Main Image**: Async update with visual feedback
- **Loading States**: Buttons disable during operations
- **Error Handling**: Shows toast notifications for errors

## Best Practices

### For Users
1. **Always set a main image** - it's what buyers see first
2. **Upload quality images** - clear, well-lit photos
3. **Show different angles** - give buyers complete view
4. **Keep under 20 images** - focus on best shots

### For Developers
1. **Check image_path fallback** - for backwards compatibility
2. **Validate file uploads** - size and type
3. **Handle AJAX errors gracefully** - show user-friendly messages
4. **Use transactions** - when inserting multiple images

## Troubleshooting

### Images Not Showing?
1. Run migration script: `php database/migrations/migrate_existing_images.php`
2. Check file permissions on `public/uploads/listings/`
3. Verify `listing_images` table exists
4. Check browser console for AJAX errors

### Can't Set Main Image?
1. Ensure JavaScript is enabled
2. Check browser console for errors
3. Verify user owns the listing

### Upload Failing?
1. Check file size (max 5MB)
2. Verify file type (JPG, PNG, WebP only)
3. Check upload directory permissions
4. Verify 20 image limit not exceeded

## File Locations

```
resources/views/
├── create.php                    # Create listing with multi-upload
├── edit-listing-modern.php       # Modern edit page with image management
└── listings.php                  # Detail page with carousel

database/migrations/
├── create_listing_images_table.sql      # Table creation
└── migrate_existing_images.php          # Migration script

public/uploads/listings/          # Image storage directory
```

## Future Enhancements

Potential improvements:
- Drag-and-drop reordering
- Image cropping/editing
- Bulk upload via ZIP
- Image compression
- CDN integration
- Watermarking
