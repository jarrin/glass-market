<?php
session_start();
require __DIR__ . '/../../config.php';
require __DIR__ . '/../../includes/db_connect.php';

// Check if listing ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ' . VIEWS_URL . '/browse.php');
    exit;
}

$id = (int) $_GET['id'];

// Fetch the specific listing with company information
$stmt = $pdo->prepare("
    SELECT 
        l.*,
        c.name as company_name,
        c.company_type,
        c.phone,
        c.website
    FROM listings l 
    LEFT JOIN companies c ON l.company_id = c.id
    WHERE l.id = ? AND l.published = 1 
    LIMIT 1
");
$stmt->execute([$id]);
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    header('Location: ' . VIEWS_URL . '/browse.php');
    exit;
}

// Determine title and subtitle
$title = $listing['quantity_note'] ?: ($listing['glass_type_other'] ?: $listing['glass_type']);
$subtitle = $listing['glass_type_other'] ?: $listing['glass_type'];

// Generate image URL - use actual uploaded image if available
if (!empty($listing['image_path'])) {
    $imageUrl = PUBLIC_URL . '/' . $listing['image_path'];
} else {
    $imageUrl = "https://picsum.photos/seed/glass{$listing['id']}/800/800";
}

// Generate additional product images for gallery
// If actual image exists, use it for all three thumbnails, otherwise use placeholders
if (!empty($listing['image_path'])) {
    $additionalImages = [
        PUBLIC_URL . '/' . $listing['image_path'],
        PUBLIC_URL . '/' . $listing['image_path'],
        PUBLIC_URL . '/' . $listing['image_path']
    ];
} else {
    $additionalImages = [
        "https://picsum.photos/seed/glass{$listing['id']}/800/800",
        "https://picsum.photos/seed/glass" . ($listing['id'] + 100) . "/800/800",
        "https://picsum.photos/seed/glass" . ($listing['id'] + 200) . "/800/800"
    ];
}

// Format price
$priceDisplay = !empty($listing['price_text']) ? $listing['price_text'] : 'Contact for Price';

// Calculate original price (for demo - 30% markup)
if (!empty($listing['price_text']) && preg_match('/[\d,]+/', $listing['price_text'], $matches)) {
    $currentPrice = (float)str_replace(',', '', $matches[0]);
    $originalPrice = number_format($currentPrice * 1.3, 2);
} else {
    $originalPrice = null;
}

// Generate random rating for demo
$rating = number_format(4.5 + (rand(0, 4) / 10), 1);
$reviewCount = rand(50, 200);

// Related products (fetch similar items)
$relatedStmt = $pdo->prepare("
    SELECT l.id, l.quantity_note, l.glass_type, l.glass_type_other, l.price_text, l.quantity_tons, l.image_path
    FROM listings l
    WHERE l.glass_type = ? AND l.id != ? AND l.published = 1
    ORDER BY RAND()
    LIMIT 3
");
$relatedStmt->execute([$listing['glass_type'], $id]);
$relatedProducts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Glass Market</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f9f7f5;
            color: #2a2623;
            margin: 0;
            line-height: 1.6;
        }

        /* Breadcrumb */
        .breadcrumb {
            padding: 100px 20px 20px;
            max-width: 1280px;
            margin: 0 auto;
        }

        .breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #6b6460;
        }

        .breadcrumb-nav a {
            color: #6b6460;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .breadcrumb-nav a:hover {
            color: #2a2623;
        }

        .breadcrumb-nav span {
            color: #d4c5b3;
        }

        .breadcrumb-nav .current {
            color: #d4a574;
            font-weight: 500;
        }

        /* Product Container */
        .product-container {
            max-width: 1280px;
            margin: 0 auto 80px;
            padding: 0 20px;
        }

        .product-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-bottom: 80px;
        }

        /* Image Gallery */
        .image-gallery {
            position: sticky;
            top: 100px;
            height: fit-content;
        }

        .main-image {
            width: 100%;
            aspect-ratio: 1;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }

        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .thumbnail-gallery {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .thumbnail {
            aspect-ratio: 1;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .thumbnail:hover {
            border-color: #d4c5b3;
        }

        .thumbnail.active {
            border-color: #2a2623;
        }

        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Product Info */
        .product-info {
            padding-top: 20px;
        }

        .category-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #e8f5e9;
            color: #059669;
            padding: 6px 14px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-title {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 42px;
            font-weight: 700;
            color: #1a1614;
            margin: 0 0 12px 0;
            line-height: 1.2;
        }

        .seller-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .seller-link {
            color: #d4a574;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: color 0.2s ease;
        }

        .seller-link:hover {
            color: #b88a5c;
        }

        .rating-section {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e8e3dd;
            margin-bottom: 24px;
        }

        .rating-stars {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .star {
            color: #d4a574;
            font-size: 16px;
        }

        .rating-text {
            font-weight: 600;
            color: #2a2623;
            font-size: 15px;
        }

        .review-count {
            color: #6b6460;
            font-size: 14px;
        }

        .price-section {
            margin-bottom: 32px;
        }

        .current-price {
            font-size: 36px;
            font-weight: 700;
            color: #059669;
            margin-bottom: 4px;
        }

        .original-price {
            font-size: 18px;
            color: #6b6460;
            text-decoration: line-through;
            margin-left: 12px;
        }

        /* Action Buttons */
        .action-section {
            background: #1a1614;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .contact-btn {
            width: 100%;
            padding: 16px 24px;
            background: #fff;
            color: #1a1614;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .contact-btn:hover {
            background: #f3ede5;
            transform: translateY(-2px);
        }

        .secondary-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .secondary-btn {
            padding: 12px 20px;
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .secondary-btn:hover {
            border-color: rgba(255,255,255,0.6);
            background: rgba(255,255,255,0.1);
        }

        /* Features */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            padding: 24px 0;
            border-top: 1px solid #e8e3dd;
            border-bottom: 1px solid #e8e3dd;
            margin-bottom: 32px;
        }

        .feature-item {
            text-align: center;
            padding: 16px;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            margin: 0 auto 12px;
            color: #2a2623;
        }

        .feature-title {
            font-weight: 600;
            font-size: 13px;
            color: #1a1614;
            margin-bottom: 4px;
        }

        .feature-desc {
            font-size: 12px;
            color: #6b6460;
        }

        /* Tabs */
        .tabs {
            border-bottom: 2px solid #e8e3dd;
            margin-bottom: 32px;
        }

        .tab-list {
            display: flex;
            gap: 32px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .tab-button {
            background: none;
            border: none;
            padding: 16px 0;
            font-size: 15px;
            font-weight: 600;
            color: #6b6460;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s ease;
        }

        .tab-button:hover {
            color: #2a2623;
        }

        .tab-button.active {
            color: #1a1614;
            border-bottom-color: #2a2623;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .description-text {
            color: #6b6460;
            line-height: 1.8;
            font-size: 15px;
        }

        .specs-table {
            width: 100%;
        }

        .specs-table tr {
            border-bottom: 1px solid #f3ede5;
        }

        .specs-table td {
            padding: 16px 0;
            font-size: 14px;
        }

        .specs-table td:first-child {
            color: #6b6460;
            font-weight: 500;
            width: 200px;
        }

        .specs-table td:last-child {
            color: #2a2623;
            font-weight: 600;
        }

        /* Related Products */
        .related-section {
            padding-top: 40px;
            border-top: 2px solid #e8e3dd;
        }

        .section-title {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 28px;
            font-weight: 700;
            color: #1a1614;
            margin: 0 0 32px 0;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .related-card {
            background: #fff;
            border: 1px solid #e8e3dd;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .related-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        .related-image {
            width: 100%;
            aspect-ratio: 1;
            background: #e8e3dd;
            background-size: cover;
            background-position: center;
        }

        .related-info {
            padding: 16px;
        }

        .related-title {
            font-weight: 700;
            font-size: 16px;
            color: #1a1614;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .related-price {
            font-weight: 600;
            color: #059669;
            font-size: 18px;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .product-layout {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .image-gallery {
                position: relative;
                top: 0;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .related-grid {
                grid-template-columns: 1fr;
            }

            .product-title {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../../includes/navbar.php'; ?>
    <?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <nav class="breadcrumb-nav">
            <a href="<?php echo VIEWS_URL; ?>/index.php">Home</a>
            <span>/</span>
            <a href="<?php echo VIEWS_URL; ?>/browse.php">Browse</a>
            <span>/</span>
            <a href="<?php echo VIEWS_URL; ?>/browse.php?category=<?= urlencode($listing['glass_type']) ?>">Vases & Vessels</a>
            <span>/</span>
            <span class="current"><?= htmlspecialchars($title) ?></span>
        </nav>
    </div>

    <!-- Product Container -->
    <div class="product-container">
        <div class="product-layout">
            <!-- Image Gallery -->
            <div class="image-gallery">
                <div class="main-image" id="mainImage">
                    <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" 
                         alt="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="thumbnail-gallery">
                    <?php foreach ($additionalImages as $index => $img): ?>
                    <div class="thumbnail <?= $index === 0 ? 'active' : '' ?>" 
                         onclick="changeImage('<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>', this)">
                        <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" 
                             alt="Product view <?= $index + 1 ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <div class="category-badge">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                    </svg>
                    <span>In Stock</span>
                </div>

                <h1 class="product-title"><?= htmlspecialchars($title) ?></h1>

                <div class="seller-info">
                    <?php if (!empty($listing['company_name'])): ?>
                    <a href="<?php echo VIEWS_URL; ?>/seller-shop.php?seller=<?= $listing['company_id'] ?>" class="seller-link">
                        <?= htmlspecialchars($listing['company_name']) ?>
                    </a>
                    <?php endif; ?>
                </div>

                <div class="rating-section">
                    <div class="rating-stars">
                        <?php for($i = 0; $i < 5; $i++): ?>
                            <span class="star">★</span>
                        <?php endfor; ?>
                    </div>
                    <span class="rating-text"><?= $rating ?></span>
                    <span class="review-count">(<?= number_format($reviewCount) ?> sales)</span>
                </div>

                <div class="price-section">
                    <div>
                        <span class="current-price">
                            <?php if (!empty($listing['price_text'])): ?>
                                <?= htmlspecialchars($listing['price_text']) ?>
                            <?php else: ?>
                                Contact for Price
                            <?php endif; ?>
                        </span>
                        <?php if ($originalPrice): ?>
                            <span class="original-price">$<?= $originalPrice ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-section">
                    <button class="contact-btn" onclick="contactSeller()">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Contact Seller
                    </button>
                    <div class="secondary-actions">
                        <button class="secondary-btn" onclick="saveListing()">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            Save
                        </button>
                        <button class="secondary-btn" onclick="shareProduct()">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                            </svg>
                            Share
                        </button>
                    </div>
                </div>

                <!-- Features -->
                <div class="features-grid">
                    <div class="feature-item">
                        <svg class="feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <div class="feature-title">Company Verified</div>
                        <div class="feature-desc">Trusted seller</div>
                    </div>
                    <div class="feature-item">
                        <svg class="feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <div class="feature-title">Quality Tested</div>
                        <div class="feature-desc">Certified glass</div>
                    </div>
                    <div class="feature-item">
                        <svg class="feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <div class="feature-title">Fast Response</div>
                        <div class="feature-desc">Quick communication</div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="tabs">
                    <ul class="tab-list">
                        <li><button class="tab-button active" onclick="showTab('description')">Description</button></li>
                        <li><button class="tab-button" onclick="showTab('specifications')">Specifications</button></li>
                        <li><button class="tab-button" onclick="showTab('seller')">About Seller</button></li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="tab-content active" id="description-tab">
                    <div class="description-text">
                        <?php if (!empty($listing['quality_notes'])): ?>
                            <?= nl2br(htmlspecialchars($listing['quality_notes'])) ?>
                        <?php else: ?>
                            <p>This exquisite <?= htmlspecialchars($subtitle) ?> is a masterpiece of traditional glassmaking. Hand-crafted by skilled artisans using centuries-old techniques, each piece features intricate details and exceptional clarity. The elegant design makes it perfect for displaying fresh flowers or as a standalone decorative piece.</p>
                            
                            <p>Crafted with meticulous attention to detail, this vase showcases the finest quality glass materials. The recycled and tested glass ensures both environmental sustainability and lasting durability. Each piece undergoes rigorous quality control to meet industry standards and quality requirements.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="tab-content" id="specifications-tab">
                    <table class="specs-table">
                        <tr>
                            <td>Glass Type</td>
                            <td><?= htmlspecialchars($subtitle) ?></td>
                        </tr>
                        <tr>
                            <td>Quantity Available</td>
                            <td><?= htmlspecialchars($listing['quantity_tons']) ?> tons</td>
                        </tr>
                        <tr>
                            <td>Quality Status</td>
                            <td><?= htmlspecialchars(ucfirst($listing['tested'])) ?></td>
                        </tr>
                        <tr>
                            <td>Recycled Material</td>
                            <td><?= htmlspecialchars(ucfirst($listing['recycled'])) ?></td>
                        </tr>
                        <tr>
                            <td>Listing Type</td>
                            <td><?= $listing['side'] === 'WTS' ? 'For Sale' : 'Wanted to Buy' ?></td>
                        </tr>
                        <tr>
                            <td>Currency</td>
                            <td><?= htmlspecialchars($listing['currency']) ?></td>
                        </tr>
                        <?php if (!empty($listing['storage_location'])): ?>
                        <tr>
                            <td>Storage Location</td>
                            <td><?= htmlspecialchars($listing['storage_location']) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>

                <div class="tab-content" id="seller-tab">
                    <div class="description-text">
                        <h3 style="margin-top:0">About <?= htmlspecialchars($listing['company_name']) ?></h3>
                        <p><strong>Company Type:</strong> <?= htmlspecialchars($listing['company_type']) ?></p>
                        
                        <?php if (!empty($listing['phone'])): ?>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($listing['phone']) ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($listing['website'])): ?>
                        <p><strong>Website:</strong> <a href="<?= htmlspecialchars($listing['website']) ?>" target="_blank" style="color: #2f6df5;"><?= htmlspecialchars($listing['website']) ?></a></p>
                        <?php endif; ?>
                        
                        <p style="margin-top: 24px;">
                            <?= htmlspecialchars($listing['company_name']) ?> is a professional glass supplier specializing in quality glass materials. 
                            We maintain high standards for all our products and ensure timely delivery to our customers.
                        </p>
                        
                        <p style="margin-top: 16px;">
                            <a href="seller-shop.php?company_id=<?= $listing['company_id'] ?>" style="display: inline-block; padding: 12px 24px; background: #2f6df5; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                                View All Products from This Seller
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
        <div class="related-section">
            <h2 class="section-title">You May Also Like</h2>
            <div class="related-grid">
                <?php foreach ($relatedProducts as $related): 
                    $relatedTitle = $related['quantity_note'] ?: ($related['glass_type_other'] ?: $related['glass_type']);
                    // Use actual uploaded image if available, otherwise use placeholder
                    if (!empty($related['image_path'])) {
                        $relatedImage = PUBLIC_URL . '/' . $related['image_path'];
                    } else {
                        $relatedImage = "https://picsum.photos/seed/glass{$related['id']}/800/800";
                    }
                    $relatedPrice = !empty($related['price_text']) ? $related['price_text'] : 'Contact for Price';
                ?>
                <a href="listings.php?id=<?= $related['id'] ?>" class="related-card">
                    <div class="related-image" style="background-image: url('<?= htmlspecialchars($relatedImage) ?>');"></div>
                    <div class="related-info">
                        <div class="related-title"><?= htmlspecialchars($relatedTitle) ?></div>
                        <div class="related-price"><?= htmlspecialchars($relatedPrice) ?></div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>

    <script>
        function changeImage(src, element) {
            const mainImage = document.querySelector('#mainImage img');
            mainImage.src = src;
            
            // Update active thumbnail
            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            element.classList.add('active');
        }

        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('active');
        }

        function contactSeller() {
            <?php if (!empty($listing['company_name'])): ?>
                window.location.href = '<?php echo VIEWS_URL; ?>/seller-shop.php?seller=<?= $listing['company_id'] ?>';
            <?php else: ?>
                alert('Contact information for this seller is not available.');
            <?php endif; ?>
        }

        function saveListing() {
            alert('Save functionality coming soon!');
        }

        function shareProduct() {
            if (navigator.share) {
                navigator.share({
                    title: '<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>',
                    text: 'Check out this product on Glass Market',
                    url: window.location.href
                });
            } else {
                // Fallback - copy to clipboard
                navigator.clipboard.writeText(window.location.href);
                alert('Link copied to clipboard!');
            }
        }
    </script>
</body>
</html>