<?php session_start(); ?>
<?php require_once __DIR__ . '/../../config.php'; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Seller Shop - Glass Market</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-top: 80px;
        }
        
        /* Seller Header Section */
        .seller-header {
            padding: 40px 20px 60px;
        }
        
        .seller-header-content {
            max-width: 1280px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.5);
            display: flex;
            gap: 40px;
            align-items: flex-start;
        }
        
        .seller-avatar-large {
            width: 200px;
            height: 200px;
            border-radius: 24px;
            background-size: cover;
            background-position: center;
            flex-shrink: 0;
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        }
        
        .seller-header-info {
            flex: 1;
        }
        
        .seller-name-large {
            font-size: 48px;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0 0 16px 0;
            letter-spacing: -1px;
        }
        
        .seller-specialty-large {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 24px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .seller-description {
            font-size: 17px;
            color: #4b5563;
            line-height: 1.7;
            margin: 0 0 24px 0;
            max-width: 700px;
        }
        
        .seller-location-large {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 28px;
            font-weight: 500;
        }
        
        .seller-location-large svg {
            width: 22px;
            height: 22px;
            color: #667eea;
        }
        
        .seller-stats-large {
            display: flex;
            gap: 48px;
            flex-wrap: wrap;
        }
        
        .seller-stat-large {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        
        .seller-stat-value-large {
            font-size: 40px;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .seller-stat-value-large svg {
            width: 32px;
            height: 32px;
            color: #667eea;
        }
        
        .seller-stat-label-large {
            font-size: 13px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        /* Contact Section */
        .seller-contact {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            padding: 28px;
            margin-top: 32px;
            max-width: 400px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        
        .seller-contact h3 {
            margin: 0 0 20px 0;
            font-size: 20px;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #4b5563;
            font-size: 14px;
            font-weight: 500;
        }
        
        .contact-item svg {
            width: 20px;
            height: 20px;
            color: #667eea;
            flex-shrink: 0;
        }
        
        .contact-item a {
            color: #1f2937;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .contact-item a:hover {
            color: #667eea;
        }
        
        .contact-btn {
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        
        .contact-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(102, 126, 234, 0.4);
        }
        
        /* Products Section */
        .products-section {
            max-width: 1280px;
            margin: 0 auto 60px;
            padding: 0 20px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            padding: 28px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .section-title {
            font-size: 32px;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }
        
        .product-count {
            font-size: 16px;
            color: #6b7280;
            font-weight: 600;
        }
        
        /* Filter Tabs */
        .filter-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 28px;
            overflow-x: auto;
            padding: 16px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }
        
        .filter-tab {
            padding: 12px 24px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .filter-tab:hover {
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-2px);
        }
        
        .filter-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        /* Product Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 60px;
        }
        
        .product-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .product-image {
            width: 100%;
            height: 280px;
            background: #e5e7eb;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .product-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            color: #1f2937;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .product-badge.wts {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
        }
        
        .product-badge.wtb {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
        }
        
        .product-info {
            padding: 24px;
        }
        
        .product-title {
            font-size: 18px;
            font-weight: 800;
            color: #1f2937;
            margin: 0 0 8px 0;
            line-height: 1.3;
        }
        
        .product-type {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .product-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 16px;
        }
        
        .product-detail {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #4b5563;
        }
        
        .product-detail svg {
            width: 18px;
            height: 18px;
            color: #667eea;
            flex-shrink: 0;
        }
        
        .product-detail strong {
            color: #1f2937;
            font-weight: 700;
        }
        
        .product-price {
            font-size: 20px;
            font-weight: 800;
            color: #10b981;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px solid #f3f4f6;
        }
        
        .product-location {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #6b7280;
            margin-top: 10px;
            font-weight: 500;
        }
        
        .product-location svg {
            width: 16px;
            height: 16px;
            color: #667eea;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        
        .empty-state svg {
            width: 80px;
            height: 80px;
            color: #d1d5db;
            margin-bottom: 24px;
        }
        
        .empty-state h3 {
            font-size: 24px;
            color: #1f2937;
            margin: 0 0 12px 0;
            font-weight: 800;
        }
        
        .empty-state p {
            font-size: 16px;
            margin: 0;
            color: #6b7280;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding-top: 60px;
            }

            .seller-header {
                padding: 20px 20px 40px;
            }
            
            .seller-header-content {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 32px 24px;
            }
            
            .seller-avatar-large {
                width: 150px;
                height: 150px;
            }
            
            .seller-name-large {
                font-size: 32px;
            }
            
            .seller-description {
                max-width: 100%;
            }
            
            .seller-location-large {
                justify-content: center;
            }
            
            .seller-stats-large {
                justify-content: center;
                gap: 32px;
            }
            
            .seller-contact {
                max-width: 100%;
            }
            
            .section-title {
                font-size: 24px;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .filter-tabs {
                gap: 8px;
                padding: 12px;
            }
        }
    </style>
                font-size: 24px;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .filter-tabs {
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>
    <?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>
    
    <?php
        require_once __DIR__ . '/../../includes/db_connect.php';
        
        // Get seller ID from URL parameter
        $sellerId = isset($_GET['seller']) ? (int)$_GET['seller'] : 0;
        
        if ($sellerId <= 0) {
            echo '<div style="padding: 100px 20px; text-align: center;">';
            echo '<h1>Seller Not Found</h1>';
            echo '<p>The seller you are looking for does not exist.</p>';
            echo '<a href="' . VIEWS_URL . '/sellers.php" style="color: #2a2623;">← Back to Sellers</a>';
            echo '</div>';
            include __DIR__ . '/../../includes/footer.php';
            exit;
        }
        
        // Fetch seller information
        try {
            $stmt = $pdo->prepare("
                SELECT 
                    c.id,
                    c.name,
                    c.company_type,
                    c.description,
                    c.phone,
                    c.website,
                    c.owner_user_id,
                    u.email as seller_email,
                    COUNT(l.id) as listing_count
                FROM companies c
                LEFT JOIN users u ON c.owner_user_id = u.id
                LEFT JOIN listings l ON c.id = l.company_id AND l.published = 1
                WHERE c.id = ?
                GROUP BY c.id
            ");
            $stmt->execute([$sellerId]);
            $seller = $stmt->fetch();
            
            if (!$seller) {
                echo '<div style="padding: 100px 20px; text-align: center;">';
                echo '<h1>Seller Not Found</h1>';
                echo '<p>The seller you are looking for does not exist.</p>';
                echo '<a href="' . VIEWS_URL . '/sellers.php" style="color: #2a2623;">← Back to Sellers</a>';
                echo '</div>';
                include __DIR__ . '/../../includes/footer.php';
                exit;
            }
            
            // Fetch seller's listings
            $stmt = $pdo->prepare("
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
                    l.created_at,
                    l.image_path
                FROM listings l
                WHERE l.company_id = ? AND l.published = 1
                ORDER BY l.created_at DESC
            ");
            $stmt->execute([$sellerId]);
            $listings = $stmt->fetchAll();
            
            // Map company type to specialty
            $specialtyMap = [
                'Glass Recycle Plant' => 'Glass Recycling',
                'Glass Factory' => 'Glass Manufacturing',
                'Collection Company' => 'Glass Collection',
                'Trader' => 'Glass Trading',
                'Other' => 'Glass Industry'
            ];
            $specialty = $specialtyMap[$seller['company_type']] ?? 'Glass Products';
            
            // Generate avatar
            $avatarUrl = "https://picsum.photos/seed/seller{$seller['id']}/600/600";
            
            // Extract unique location from listings
            $locations = [];
            foreach ($listings as $listing) {
                if (!empty($listing['storage_location'])) {
                    $locations[] = $listing['storage_location'];
                }
            }
            $location = !empty($locations) ? $locations[0] : 'Global';
            
        } catch (PDOException $e) {
            error_log("Error fetching seller: " . $e->getMessage());
            echo '<div style="padding: 100px 20px; text-align: center;">';
            echo '<h1>Error</h1>';
            echo '<p>An error occurred while loading the seller information.</p>';
            echo '<p style="color: #ef4444; font-family: monospace; font-size: 12px;">' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<a href="' . VIEWS_URL . '/sellers.php" style="color: #2a2623;">← Back to Sellers</a>';
            echo '</div>';
            include __DIR__ . '/../../includes/footer.php';
            exit;
        }
    ?>
    
    <!-- Seller Header -->
    <section class="seller-header">
        <div class="seller-header-content">
            <div class="seller-avatar-large" style="background-image: url('<?php echo htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8'); ?>');"></div>
            
            <div class="seller-header-info">
                <h1 class="seller-name-large"><?php echo htmlspecialchars($seller['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                
                <span class="seller-specialty-large"><?php echo htmlspecialchars($specialty, ENT_QUOTES, 'UTF-8'); ?></span>
                
                <?php if (!empty($seller['description'])): ?>
                <p class="seller-description">
                    <?php echo nl2br(htmlspecialchars($seller['description'], ENT_QUOTES, 'UTF-8')); ?>
                </p>
                <?php endif; ?>
                
                <div class="seller-location-large">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span><?php echo htmlspecialchars($location, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                
                <div class="seller-stats-large">
                    <div class="seller-stat-large">
                        <div class="seller-stat-value-large">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <?php echo number_format($seller['listing_count']); ?>
                        </div>
                        <div class="seller-stat-label-large">Active Listings</div>
                    </div>
                    
                    <div class="seller-stat-large">
                        <div class="seller-stat-value-large">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Fast
                        </div>
                        <div class="seller-stat-label-large">Response Time</div>
                    </div>
                </div>
                
                <div class="seller-contact">
                    <h3>Contact Information</h3>
                    <div class="contact-info">
                        <?php if (!empty($seller['phone'])): ?>
                        <div class="contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <a href="tel:<?php echo htmlspecialchars($seller['phone'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($seller['phone'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($seller['website'])): ?>
                        <div class="contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                            <a href="<?php echo htmlspecialchars($seller['website'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
                                Visit Website
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <div class="contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>Message Seller</span>
                        </div>
                    </div>
                    
                    <button class="contact-btn" onclick="contactSeller()">
                        Send Message
                    </button>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Products Section -->
    <section class="products-section">
        <div class="section-header">
            <h2 class="section-title">Available Products</h2>
            <span class="product-count"><?php echo count($listings); ?> listing<?php echo count($listings) != 1 ? 's' : ''; ?></span>
        </div>
        
        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <button class="filter-tab active" data-filter="all">All Products</button>
            <button class="filter-tab" data-filter="WTB">Wanted to Buy</button>
            <button class="filter-tab" data-filter="recycled">Recycled</button>
            <button class="filter-tab" data-filter="tested">Tested</button>
        </div>
        
        <?php if (empty($listings)): ?>
        <!-- Empty State -->
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h3>No Products Available</h3>
            <p>This seller doesn't have any active listings at the moment.</p>
        </div>
        <?php else: ?>
        <!-- Product Grid -->
        <div class="products-grid">
            <?php foreach ($listings as $listing): 
                $glassType = $listing['glass_type_other'] ?: $listing['glass_type'];
                $title = $listing['quantity_note'] ?: $glassType;
                // Use actual uploaded image if available, otherwise use placeholder
                if (!empty($listing['image_path'])) {
                    $imageUrl = PUBLIC_URL . '/' . $listing['image_path'];
                } else {
                    $imageUrl = "https://picsum.photos/seed/glass{$listing['id']}/800/800";
                }
            ?>
            <article class="product-card" 
                     data-side="<?php echo htmlspecialchars($listing['side'], ENT_QUOTES, 'UTF-8'); ?>"
                     data-recycled="<?php echo htmlspecialchars($listing['recycled'], ENT_QUOTES, 'UTF-8'); ?>"
                     data-tested="<?php echo htmlspecialchars($listing['tested'], ENT_QUOTES, 'UTF-8'); ?>"
                     onclick="window.location.href='<?php echo VIEWS_URL; ?>/listings.php?id=<?php echo $listing['id']; ?>'">
                <div class="product-image" style="background-image: url('<?php echo htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>');">
                    <span class="product-badge <?php echo strtolower($listing['side']); ?>">
                        <?php echo $listing['side'] === 'WTS' ? 'For Sale' : 'Wanted'; ?>
                    </span>
                </div>
                
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h3>
                    <div class="product-type"><?php echo htmlspecialchars($glassType, ENT_QUOTES, 'UTF-8'); ?></div>
                    
                    <div class="product-details">
                        <?php if ($listing['quantity_tons']): ?>
                        <div class="product-detail">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                            </svg>
                            <span><strong><?php echo number_format($listing['quantity_tons'], 2); ?> tons</strong></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($listing['recycled'] !== 'unknown'): ?>
                        <div class="product-detail">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span><?php echo ucfirst($listing['recycled']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($listing['tested'] !== 'unknown'): ?>
                        <div class="product-detail">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span><?php echo ucfirst($listing['tested']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($listing['price_text'])): ?>
                    <div class="product-price">
                        <?php echo htmlspecialchars($listing['price_text'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($listing['storage_location'])): ?>
                    <div class="product-location">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span><?php echo htmlspecialchars($listing['storage_location'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>
    
    <?php include __DIR__ . '/../../includes/footer.php'; ?>
    
    <script>
        // Filter functionality
        const filterTabs = document.querySelectorAll('.filter-tab');
        const productCards = document.querySelectorAll('.product-card');
        
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Update active tab
                filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const filter = this.getAttribute('data-filter');
                
                // Filter products
                productCards.forEach(card => {
                    if (filter === 'all') {
                        card.style.display = '';
                    } else if (filter === 'WTS' || filter === 'WTB') {
                        card.style.display = card.getAttribute('data-side') === filter ? '' : 'none';
                    } else if (filter === 'recycled') {
                        card.style.display = card.getAttribute('data-recycled') === 'recycled' ? '' : 'none';
                    } else if (filter === 'tested') {
                        card.style.display = card.getAttribute('data-tested') === 'tested' ? '' : 'none';
                    }
                });
                
                // Update product count
                const visibleCards = Array.from(productCards).filter(card => card.style.display !== 'none');
                const productCount = document.querySelector('.product-count');
                productCount.textContent = visibleCards.length + ' listing' + (visibleCards.length !== 1 ? 's' : '');
            });
        });
        
        // Contact seller functionality
        function contactSeller() {
            <?php if (!empty($seller['seller_email'])): ?>
                const sellerName = <?php echo json_encode($seller['name']); ?>;
                const subject = encodeURIComponent('Inquiry about: ' + sellerName);
                const body = encodeURIComponent('Hello,\n\nI am interested in your company listings on Glass Market.\n\nCompany: ' + sellerName + '\n\nShop URL: ' + window.location.href + '\n\nThank you.');
                
                window.location.href = 'mailto:<?= htmlspecialchars($seller['seller_email']) ?>?subject=' + subject + '&body=' + body;
            <?php else: ?>
                alert('Contact information for this seller is not available.');
            <?php endif; ?>
        }
    </script>
</body>
</html>
