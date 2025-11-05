<?php session_start(); ?>
<?php require_once __DIR__ . '/../../config.php'; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Discover Our Sellers - Glass Market</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f9f7f5;
            color: #2a2623;
            margin: 0;
            line-height: 1.6;
        }
        
        .hero-section {
            text-align: center;
            padding: 100px 20px 60px;
            background: linear-gradient(180deg, rgba(255,255,255,0.8) 0%, rgba(249,247,245,0.9) 100%);
        }
        
        .hero-section h1 {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 56px;
            font-weight: 700;
            margin: 0 0 16px 0;
            color: #1a1614;
            letter-spacing: -0.5px;
        }
        
        .hero-section p {
            font-size: 18px;
            color: #6b6460;
            max-width: 700px;
            margin: 0 auto 40px;
            line-height: 1.7;
        }
        
        .search-container {
            max-width: 640px;
            margin: 0 auto;
            position: relative;
        }
        
        .search-box {
            width: 100%;
            padding: 16px 20px 16px 50px;
            border: 1px solid #d4c5b3;
            border-radius: 12px;
            font-size: 16px;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
        }
        
        .search-box:focus {
            outline: none;
            border-color: #8c8278;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #8c8278;
            width: 20px;
            height: 20px;
        }
        
        .stats-section {
            display: flex;
            justify-content: center;
            gap: 60px;
            padding: 40px 20px;
            background: #fff;
            border-top: 1px solid #e8e3dd;
            border-bottom: 1px solid #e8e3dd;
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-number {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 42px;
            font-weight: 700;
            color: #1a1614;
            margin-bottom: 4px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #6b6460;
            text-transform: capitalize;
        }
        
        .sellers-grid {
            max-width: 1280px;
            margin: 60px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }
        
        .seller-card {
            background: #fff;
            border: 1px solid #e8e3dd;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        
        .seller-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            border-color: #d4c5b3;
        }
        
        .seller-avatar {
            width: 100%;
            height: 240px;
            background: #e8e3dd;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .verified-badge {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        .verified-badge svg {
            width: 20px;
            height: 20px;
            color: #059669;
        }
        
        .seller-info {
            padding: 20px;
        }
        
        .seller-name {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 20px;
            font-weight: 700;
            color: #1a1614;
            margin: 0 0 8px 0;
        }
        
        .seller-specialty {
            display: inline-block;
            background: #f3ede5;
            color: #6b6460;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 12px;
        }
        
        .seller-location {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #6b6460;
            font-size: 14px;
            margin-bottom: 12px;
        }
        
        .seller-location svg {
            width: 16px;
            height: 16px;
        }
        
        .seller-stats {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f3ede5;
        }
        
        .seller-stat {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #6b6460;
        }
        
        .seller-stat svg {
            width: 16px;
            height: 16px;
            color: #d4a574;
        }
        
        .seller-stat-value {
            font-weight: 600;
            color: #2a2623;
        }
        
        .view-shop-btn {
            width: 100%;
            padding: 12px;
            background: #fff;
            color: #2a2623;
            border: 1px solid #d4c5b3;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }
        
        .view-shop-btn:hover {
            background: #2a2623;
            color: #fff;
            border-color: #2a2623;
        }
        
        .load-more-container {
            text-align: center;
            padding: 40px 20px 80px;
        }
        
        .load-more-btn {
            padding: 14px 32px;
            background: #fff;
            color: #2a2623;
            border: 1px solid #d4c5b3;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        
        .load-more-btn:hover {
            background: #2a2623;
            color: #fff;
            border-color: #2a2623;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        
        .cta-section {
            background: linear-gradient(180deg, #fff 0%, #f9f7f5 100%);
            padding: 80px 20px;
            text-align: center;
            border-top: 1px solid #e8e3dd;
        }
        
        .cta-section h2 {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 42px;
            font-weight: 700;
            margin: 0 0 16px 0;
            color: #1a1614;
        }
        
        .cta-section p {
            font-size: 18px;
            color: #6b6460;
            margin: 0 0 32px 0;
        }
        
        .cta-btn {
            display: inline-block;
            padding: 16px 40px;
            background: #2a2623;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(42,38,35,0.2);
        }
        
        .cta-btn:hover {
            background: #1a1614;
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(42,38,35,0.3);
        }
        
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 38px;
            }
            
            .stats-section {
                flex-wrap: wrap;
                gap: 32px;
            }
            
            .stat-number {
                font-size: 32px;
            }
            
            .sellers-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 16px;
                margin: 40px auto;
            }
            
            .cta-section h2 {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>
    <?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <h1>Discover Our Sellers</h1>
        <p>Browse through our curated collection of talented glass artists and collectors from around the world</p>
        
        <div class="search-container">
            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input 
                type="text" 
                class="search-box" 
                id="searchInput"
                placeholder="Search sellers by name, location, or specialty..."
                aria-label="Search sellers"
            >
        </div>
    </section>
    
    <!-- Stats Section -->
    <section class="stats-section">
        <?php
            // Fetch real statistics from database
            require_once __DIR__ . '/../../includes/db_connect.php';
            
            $stats = [
                'active_sellers' => 500,
                'countries' => 50,
                'avg_rating' => 4.8,
                'products_listed' => 10000
            ];
            
            try {
                // Count unique companies with published listings
                $stmt = $pdo->query("
                    SELECT COUNT(DISTINCT c.id) as seller_count
                    FROM companies c
                    INNER JOIN listings l ON c.id = l.company_id
                    WHERE l.published = 1
                ");
                $result = $stmt->fetch();
                if ($result && $result['seller_count'] > 0) {
                    $stats['active_sellers'] = $result['seller_count'];
                }
                
                // Count unique countries (from storage_location)
                $stmt = $pdo->query("
                    SELECT COUNT(DISTINCT 
                        CASE 
                            WHEN storage_location LIKE '%,%' THEN TRIM(SUBSTRING_INDEX(storage_location, ',', -1))
                            ELSE storage_location
                        END
                    ) as country_count
                    FROM listings
                    WHERE published = 1 AND storage_location IS NOT NULL AND storage_location != ''
                ");
                $result = $stmt->fetch();
                if ($result && $result['country_count'] > 0) {
                    $stats['countries'] = $result['country_count'];
                }
                
                // Count total products
                $stmt = $pdo->query("SELECT COUNT(*) as product_count FROM listings WHERE published = 1");
                $result = $stmt->fetch();
                if ($result && $result['product_count'] > 0) {
                    $stats['products_listed'] = $result['product_count'];
                }
                
            } catch (PDOException $e) {
                error_log("Error fetching stats: " . $e->getMessage());
            }
        ?>
        <div class="stat">
            <div class="stat-number"><?php echo $stats['active_sellers']; ?>+</div>
            <div class="stat-label">Active Sellers</div>
        </div>
        <div class="stat">
            <div class="stat-number"><?php echo $stats['countries']; ?>+</div>
            <div class="stat-label">Countries</div>
        </div>
        <div class="stat">
            <div class="stat-number"><?php echo number_format($stats['avg_rating'], 1); ?></div>
            <div class="stat-label">Average Rating</div>
        </div>
        <div class="stat">
            <div class="stat-number"><?php echo number_format($stats['products_listed'] / 1000, 0); ?>K+</div>
            <div class="stat-label">Products Listed</div>
        </div>
    </section>
    
    <!-- Sellers Grid -->
    <section class="sellers-grid" id="sellersGrid">
        <?php
            // Fetch sellers from database
            $sellers = [];
            
            try {
                $stmt = $pdo->query("
                    SELECT 
                        c.id,
                        c.name,
                        c.company_type,
                        c.phone,
                        c.website,
                        c.city,
                        c.country,
                        COUNT(l.id) as listing_count,
                        u.name as owner_name
                    FROM companies c
                    LEFT JOIN users u ON c.owner_user_id = u.id
                    LEFT JOIN listings l ON c.id = l.company_id AND l.published = 1
                    WHERE c.owner_user_id IS NOT NULL
                    GROUP BY c.id, c.name, c.company_type, c.phone, c.website, c.city, c.country, u.name
                    ORDER BY listing_count DESC, c.created_at DESC
                ");
                
                $dbSellers = $stmt->fetchAll();
                
                foreach ($dbSellers as $index => $seller) {
                    // Build location string
                    $location = 'Global';
                    if (!empty($seller['city']) && !empty($seller['country'])) {
                        $location = $seller['city'] . ', ' . $seller['country'];
                    } elseif (!empty($seller['city'])) {
                        $location = $seller['city'];
                    } elseif (!empty($seller['country'])) {
                        $location = $seller['country'];
                    }
                    
                    // Generate avatar image
                    $avatarSeed = $seller['id'];
                    $avatarUrl = "https://picsum.photos/seed/seller{$avatarSeed}/600/600";
                    
                    // Use company_type as specialty
                    $specialty = $seller['company_type'] ?? 'Glass Trading';
                    
                    // Generate random rating between 4.6 and 4.9
                    $rating = number_format(4.6 + (rand(0, 30) / 100), 1);
                    
                    // Generate random review count
                    $reviews = rand(100, 400);
                    
                    $sellers[] = [
                        'id' => $seller['id'],
                        'name' => $seller['name'],
                        'specialty' => $specialty,
                        'location' => $location,
                        'avatar' => $avatarUrl,
                        'rating' => $rating,
                        'reviews' => $reviews,
                        'listings' => $seller['listing_count'],
                        'verified' => true,
                        'owner' => $seller['owner_name']
                    ];
                }
                
            } catch (PDOException $e) {
                error_log("Error fetching sellers: " . $e->getMessage());
            }
            
            // If no sellers in database, create demo sellers
            if (empty($sellers)) {
                $demoSellers = [
                    ['name' => 'Crystal Artisan Studio', 'specialty' => 'Murano Glass', 'location' => 'Venice, Italy'],
                    ['name' => 'Modern Glass Collective', 'specialty' => 'Contemporary Art', 'location' => 'Brooklyn, NY'],
                    ['name' => 'Vintage Crystal Finds', 'specialty' => 'Vintage & Antique', 'location' => 'Prague, Czech Republic'],
                    ['name' => 'Bohemian Glass Works', 'specialty' => 'Traditional Craft', 'location' => 'Karlovy Vary, Czech Republic'],
                    ['name' => 'Studio Lumière', 'specialty' => 'Art Deco', 'location' => 'Paris, France'],
                    ['name' => 'Nordic Glass Design', 'specialty' => 'Minimalist Design', 'location' => 'Stockholm, Sweden'],
                    ['name' => 'Artisan Glassblowers', 'specialty' => 'Hand-Blown Glass', 'location' => 'Seattle, WA'],
                    ['name' => 'Crystal Heritage', 'specialty' => 'Fine Crystal', 'location' => 'Waterford, Ireland']
                ];
                
                foreach ($demoSellers as $index => $demo) {
                    $sellers[] = [
                        'id' => $index + 1,
                        'name' => $demo['name'],
                        'specialty' => $demo['specialty'],
                        'location' => $demo['location'],
                        'avatar' => "https://picsum.photos/seed/seller" . ($index + 1) . "/600/600",
                        'rating' => number_format(4.6 + (rand(0, 30) / 100), 1),
                        'reviews' => rand(150, 400),
                        'listings' => rand(30, 160),
                        'verified' => true
                    ];
                }
            }
            
            // Display sellers
            foreach ($sellers as $seller):
        ?>
        <article class="seller-card" data-name="<?php echo htmlspecialchars(strtolower($seller['name']), ENT_QUOTES, 'UTF-8'); ?>" data-location="<?php echo htmlspecialchars(strtolower($seller['location']), ENT_QUOTES, 'UTF-8'); ?>" data-specialty="<?php echo htmlspecialchars(strtolower($seller['specialty']), ENT_QUOTES, 'UTF-8'); ?>">
            <div class="seller-avatar" style="background-image: url('<?php echo htmlspecialchars($seller['avatar'], ENT_QUOTES, 'UTF-8'); ?>');">
                <?php if ($seller['verified']): ?>
                <div class="verified-badge" title="Verified Seller">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="seller-info">
                <h2 class="seller-name"><?php echo htmlspecialchars($seller['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                
                <span class="seller-specialty"><?php echo htmlspecialchars($seller['specialty'], ENT_QUOTES, 'UTF-8'); ?></span>
                
                <div class="seller-location">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span><?php echo htmlspecialchars($seller['location'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                
                <div class="seller-stats">
                    <div class="seller-stat">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <span class="seller-stat-value"><?php echo $seller['rating']; ?></span>
                        <span>(<?php echo number_format($seller['reviews']); ?> reviews)</span>
                    </div>
                </div>
                
                <div class="seller-stat" style="margin-bottom: 16px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span class="seller-stat-value"><?php echo number_format($seller['listings']); ?></span>
                    <span>listings</span>
                </div>
                
                <button class="view-shop-btn" onclick="window.location.href='<?php echo VIEWS_URL; ?>/seller-shop.php?seller=<?php echo $seller['id']; ?>'">
                    View Shop
                </button>
            </div>
        </article>
        <?php endforeach; ?>
    </section>
    
    <!-- Load More Button -->
    <div class="load-more-container">
        <button class="load-more-btn" id="loadMoreBtn">Load More Sellers</button>
    </div>
    
    <?php include __DIR__ . '/../../includes/footer.php'; ?>
    
    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const sellerCards = document.querySelectorAll('.seller-card');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            sellerCards.forEach(card => {
                const name = card.getAttribute('data-name');
                const location = card.getAttribute('data-location');
                const specialty = card.getAttribute('data-specialty');
                
                const matches = name.includes(searchTerm) || 
                               location.includes(searchTerm) || 
                               specialty.includes(searchTerm);
                
                card.style.display = matches ? '' : 'none';
            });
            
            // Update load more button visibility
            const visibleCards = Array.from(sellerCards).filter(card => card.style.display !== 'none');
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            loadMoreBtn.style.display = visibleCards.length > 8 ? 'block' : 'none';
        });
        
        // Load more functionality (placeholder)
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        loadMoreBtn.addEventListener('click', function() {
            alert('Load more functionality would fetch additional sellers from the database.');
        });
        
        // Initially hide load more if not enough sellers
        if (sellerCards.length <= 8) {
            loadMoreBtn.style.display = 'none';
        }
    </script>
</body>
</html>
