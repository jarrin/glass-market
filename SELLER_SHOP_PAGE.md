# Seller Shop Page

## Overview
The Seller Shop page (`seller-shop.php`) is a dedicated page that displays individual seller profiles along with all their products. This is different from the browse page, which shows all products from all sellers.

## Features

### Seller Profile Header
- **Large Avatar**: 200x200px seller profile image
- **Verified Badge**: Shows verified seller status
- **Seller Name**: Large, prominent display of the company name
- **Specialty Badge**: Shows the seller's business type (Glass Recycling, Manufacturing, etc.)
- **Description**: Welcome message and seller introduction
- **Location**: Primary business location
- **Statistics Display**:
  - Star rating and review count
  - Number of active listings
  - Response time indicator

### Contact Information Section
- Phone number (if available)
- Website link (if available)
- Email/messaging option
- "Send Message" button for direct contact

### Products Section
- **Section Header**: Shows total number of listings
- **Filter Tabs**: Filter products by:
  - All Products
  - For Sale (WTS)
  - Wanted to Buy (WTB)
  - Recycled
  - Tested
- **Product Grid**: Responsive grid layout displaying all seller's products
- **Empty State**: Shown when seller has no active listings

### Product Cards
Each product card displays:
- Product image
- Badge indicating WTS (For Sale) or WTB (Wanted)
- Product title
- Glass type
- Quantity in tons
- Recycled/Tested status
- Price (if available)
- Storage location

## URL Structure
```
/resources/views/seller-shop.php?seller={seller_id}
```

Example:
```
http://localhost/glass-market/resources/views/seller-shop.php?seller=1
```

## Database Queries

### Seller Information
```sql
SELECT 
    c.id,
    c.name,
    c.company_type,
    c.phone,
    c.website,
    COUNT(l.id) as listing_count
FROM companies c
LEFT JOIN listings l ON c.id = l.company_id AND l.published = 1
WHERE c.id = ?
GROUP BY c.id, c.name, c.company_type, c.phone, c.website
```

### Seller's Listings
```sql
SELECT 
    l.id,
    l.glass_type,
    l.glass_type_other,
    l.price_text,
    l.currency,
    l.quantity_tons,
    l.quantity_note,
    l.side,
    l.recycled,
    l.tested,
    l.storage_location,
    l.quality_notes,
    l.image_path,
    l.created_at
FROM listings l
WHERE l.company_id = ? AND l.published = 1
ORDER BY l.created_at DESC
```

## Integration

### From Sellers Page
The sellers page (`sellers.php`) has been updated to link to the new seller shop page:

```php
onclick="window.location.href='<?php echo VIEWS_URL; ?>/seller-shop.php?seller=<?php echo $seller['id']; ?>'"
```

### Navigation
- Users can browse all sellers on `/resources/views/sellers.php`
- Clicking "View Shop" on any seller card navigates to that seller's dedicated shop page
- From the shop page, users can browse all products from that specific seller

## Responsive Design
The page is fully responsive with breakpoints at:
- Desktop: Full layout with side-by-side seller info
- Tablet: Adjusted grid columns
- Mobile (768px and below):
  - Stacked seller header layout
  - Centered content
  - Single column product grid
  - Smaller seller avatar

## Error Handling
- Invalid seller ID: Shows "Seller Not Found" message
- Non-existent seller: Shows error message with link back to sellers page
- Database errors: Logged and user-friendly error message displayed
- No listings: Shows empty state with appropriate message

## Future Enhancements
- Contact form integration
- Customer reviews section
- Product detail modals
- Wishlist/favorite functionality
- Advanced product filtering options
- Seller performance metrics
- Chat/messaging system
