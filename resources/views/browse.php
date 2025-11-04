<?php session_start(); ?>
<?php require_once __DIR__ . '/../../config.php'; ?>
<!-- Browse collection page - static mockup -->
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Browse Collection - Glass Market</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <style>
        :root {
            --browse-bg: #f5f5f7;
            --browse-text: #1d1d1f;
            --browse-muted: #6e6e73;
            --browse-card-bg: rgba(255, 255, 255, 0.9);
            --browse-border: rgba(15, 23, 42, 0.08);
            --browse-accent: #2f6df5;
        }

        body {
            font-family: "SF Pro Display", "SF Pro Text", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--browse-bg);
            color: var(--browse-text);
            margin: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 40px 32px;
        }

        .page-header {
            text-align: center;
            padding: 60px 0 40px;
            background: linear-gradient(180deg, rgba(255,255,255,0.5) 0%, transparent 100%);
        }

        .page-title {
            font-size: clamp(38px, 6vw, 56px);
            font-weight: 700;
            margin-bottom: 16px;
            color: var(--browse-text);
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: 18px;
            color: var(--browse-muted);
            margin-bottom: 32px;
            font-weight: 400;
        }

        .layout {
            display: flex;
            gap: 32px;
            align-items: flex-start;
        }

        /* Sidebar Filters */
        .sidebar {
            width: 280px;
            position: sticky;
            top: 100px;
            background: var(--browse-card-bg);
            border: 1px solid var(--browse-border);
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 8px 24px -12px rgba(15, 23, 42, 0.12);
            backdrop-filter: blur(10px);
        }

        .panel {
            padding: 0 0 24px 0;
        }

        .panel h4 {
            margin: 0 0 18px 0;
            font-size: 17px;
            font-weight: 600;
            color: var(--browse-text);
            letter-spacing: -0.01em;
        }

        .filter-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .filter-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            color: var(--browse-text);
            border-bottom: 1px solid rgba(0,0,0,0.04);
            transition: background-color 0.2s ease;
        }

        .filter-list li:last-child {
            border-bottom: none;
        }

        .filter-list li:hover {
            background-color: rgba(47, 109, 245, 0.02);
            margin: 0 -8px;
            padding-left: 8px;
            padding-right: 8px;
            border-radius: 8px;
        }

        .filter-list label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            flex: 1;
            font-size: 15px;
            font-weight: 400;
        }

        .filter-list input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--browse-accent);
        }

        .filter-list .count {
            font-size: 13px;
            color: var(--browse-muted);
            font-weight: 500;
            background: rgba(15, 23, 42, 0.05);
            padding: 2px 8px;
            border-radius: 12px;
        }

        .divider {
            height: 1px;
            background: var(--browse-border);
            margin: 20px 0;
        }

        /* Price Range */
        .price-range-container {
            margin: 16px 0;
        }

        .price-range-display {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            font-weight: 600;
            font-size: 14px;
            color: var(--browse-text);
        }

        .price-inputs {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 16px;
        }

        .price-input-group {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .price-input-group label {
            font-size: 12px;
            color: var(--browse-muted);
            font-weight: 500;
        }

        .price-input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--browse-border);
            border-radius: 10px;
            text-align: center;
            font-size: 14px;
            background: #fff;
            color: var(--browse-text);
            font-weight: 500;
        }

        .price-input:focus {
            outline: none;
            border-color: var(--browse-accent);
        }

        .price-slider-container {
            margin: 16px 0;
        }

        .price-slider {
            background: #e3e3e8;
            height: 6px;
            border-radius: 3px;
            position: relative;
        }

        .price-slider-fill {
            background: var(--browse-accent);
            height: 100%;
            border-radius: 3px;
            position: absolute;
        }

        .price-slider-thumb {
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            background: #fff;
            border: 2px solid var(--browse-accent);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: box-shadow 0.2s ease;
        }

        .price-slider-thumb:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        /* Content Area */
        .content {
            flex: 1;
            min-width: 0;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            padding: 24px 0 16px;
            border-bottom: 1px solid var(--browse-border);
        }

        .items-count {
            font-size: 15px;
            color: var(--browse-muted);
            font-weight: 500;
        }

        .items-count strong {
            color: var(--browse-text);
            font-weight: 600;
        }

        .toolbar select {
            padding: 11px 18px;
            border: 1px solid var(--browse-border);
            border-radius: 12px;
            background: var(--browse-card-bg);
            color: var(--browse-text);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            min-width: 200px;
        }

        .toolbar select:focus {
            border-color: var(--browse-accent);
            box-shadow: 0 0 0 3px rgba(47, 109, 245, 0.08);
        }

        .toolbar select:hover {
            border-color: var(--browse-accent);
        }

        /* Product Grid */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 28px;
        }

        .card {
            background: var(--browse-card-bg);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--browse-border);
            box-shadow: 0 4px 16px -8px rgba(15, 23, 42, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px -16px rgba(15, 23, 42, 0.2);
            border-color: rgba(47, 109, 245, 0.2);
        }

        .card .media {
            height: 280px;
            background: #e8e9ed;
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }

        .card .media::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.05) 100%);
        }

        .card .meta {
            padding: 20px;
        }

        .card .meta .cat {
            font-size: 12px;
            color: var(--browse-accent);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }

        .card .meta .title {
            font-weight: 600;
            font-size: 18px;
            margin-top: 0;
            color: var(--browse-text);
            line-height: 1.4;
            letter-spacing: -0.01em;
        }

        /* Filter Toggle Button */
        .filter-toggle-btn {
            display: none;
            width: 100%;
            padding: 16px 20px;
            margin-bottom: 24px;
            background: var(--browse-text);
            color: #fff;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            letter-spacing: -0.01em;
        }

        .filter-toggle-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .filter-toggle-btn:active {
            transform: scale(0.98);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 56px;
            padding: 32px 0;
        }

        .pagination button,
        .pagination .page-number {
            padding: 10px 16px;
            border: 1px solid var(--browse-border);
            background: var(--browse-card-bg);
            color: var(--browse-text);
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            min-width: 44px;
            text-align: center;
        }

        .pagination button:hover:not(:disabled),
        .pagination .page-number:hover {
            background: var(--browse-accent);
            color: #fff;
            border-color: var(--browse-accent);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(47, 109, 245, 0.2);
        }

        .pagination button:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .pagination button.active,
        .pagination .page-number.active {
            background: var(--browse-accent);
            color: #fff;
            border-color: var(--browse-accent);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(47, 109, 245, 0.2);
        }

        .pagination .page-ellipsis {
            padding: 10px;
            color: var(--browse-muted);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px 20px;
            }

            .page-header {
                padding: 60px 0 32px;
            }

            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                display: none;
                position: relative;
                top: 0;
                margin-bottom: 24px;
            }

            .sidebar.show {
                display: block;
            }

            .filter-toggle-btn {
                display: block;
            }

            .grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .toolbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .toolbar select {
                width: 100%;
            }

            .pagination {
                flex-wrap: wrap;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>
    <?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>
<main style="padding-top: 70px;">
    <div class="page-header">
        <h1 class="page-title">Browse Collection</h1>
        <p class="subtitle">Discover premium glass from verified sellers worldwide</p>
    </div>

    <?php
            $seller = null;
            $sellerId = isset($_GET['seller']) ? (int)$_GET['seller'] : null;

        // Fetch products from database
        require_once __DIR__ . '/../../includes/db_connect.php';
        
        $products = [];
        
        try {
            // Fetch all published listings from the database
            $stmt = $pdo->prepare("
                SELECT 
                    l.id,
                    l.company_id,
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
                    c.name as company_name
                FROM listings l
                LEFT JOIN companies c ON l.company_id = c.id
                WHERE l.published = 1
                ORDER BY l.created_at DESC
            ");
            $stmt->execute();
            $listings = $stmt->fetchAll();

            foreach ($listings as $listing) {
                $glassType = $listing['glass_type_other'] ?: $listing['glass_type'];
                $title = $listing['quantity_note'] ?: $glassType;
                $companyName = $listing['company_name'] ?: 'Glass Market';
                $tons = $listing['quantity_tons'];
                // Determine image URL - uploaded or fallback
                $imageUrl = "https://picsum.photos/seed/glass{$listing['id']}/800/800";
                if (!empty($listing['image_path'])) {
                    $imageUrl = PUBLIC_URL . '/' . $listing['image_path'];
                }
                // Map for card
                $products[] = [
                    'id' => $listing['id'],
                    'title' => $glassType,
                    'listing_title' => $title,
                    'tons' => $tons,
                    'category' => $glassType,
                    'image' => $imageUrl,
                    'price' => $listing['price_text'],
                    'company' => $companyName,
                    'recycled' => $listing['recycled'],
                    'tested' => $listing['tested'],
                    'storage_location' => $listing['storage_location'],
                    'currency' => $listing['currency'],
                    'side' => $listing['side'],
                    'description' => $listing['quality_notes'],
                ];
            }
        } catch (PDOException $e) {
            error_log("Database error in browse.php: " . $e->getMessage());
            // Laat $products leeg bij fout (geen dummy invullen)
        }
    ?>

        <?php if ($seller): ?>
            <section style="background:transparent;padding:18px 0 8px;margin-bottom:8px">
                <div style="max-width:1200px;margin:0 auto;padding:0 20px;display:flex;gap:20px;align-items:center;justify-content:space-between;flex-wrap:wrap">
                    <div style="display:flex;gap:18px;align-items:center;flex:1;min-width:260px">
                        <div style="width:120px;height:120px;border-radius:999px;overflow:hidden;background:#e8e3dd;flex-shrink:0">
                            <img src="<?php echo htmlspecialchars('https://picsum.photos/seed/seller' . $seller['id'] . '/600/600', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($seller['name'], ENT_QUOTES, 'UTF-8'); ?>" style="width:100%;height:100%;object-fit:cover;display:block">
                        </div>
                        <div>
                            <h2 style="font-family: Georgia, 'Times New Roman', serif;font-size:28px;margin:0"><?php echo htmlspecialchars($seller['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                            <div style="color:#6b6460;margin-top:6px;font-size:14px"><?php echo htmlspecialchars($seller['website'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                            <p style="color:#6b6460;margin:8px 0 0;max-width:760px"><?php echo htmlspecialchars($seller['company_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </div>

                    <div style="display:flex;gap:12px;align-items:stretch">
                        <?php
                            $activeListings = 0;
                            $totalSales = 0;
                            $avgRating = number_format(4.6 + (rand(0, 35) / 100), 1);
                            try {
                                $s = $pdo->prepare('SELECT COUNT(*) as cnt, COALESCE(SUM(quantity_tons),0) as total_tons FROM listings WHERE published = 1 AND company_id = ?');
                                $s->execute([$sellerId]);
                                $r = $s->fetch();
                                $activeListings = $r['cnt'] ?? 0;
                                $totalTons = $r['total_tons'] ?? 0;
                                $totalSales = $activeListings * 20; // approximate
                            } catch (Exception $e) {}
                        ?>
                        <div style="background:#fff;border:1px solid #e8e3dd;padding:14px;border-radius:8px;text-align:center;min-width:110px">
                            <div style="font-family: Georgia, 'Times New Roman', serif;font-size:24px;font-weight:700"><?php echo number_format($totalSales); ?></div>
                            <div style="color:#6b6460;font-size:12px">Total Sales</div>
                        </div>
                        <div style="background:#fff;border:1px solid #e8e3dd;padding:14px;border-radius:8px;text-align:center;min-width:110px">
                            <div style="font-family: Georgia, 'Times New Roman', serif;font-size:24px;font-weight:700"><?php echo number_format($activeListings); ?></div>
                            <div style="color:#6b6460;font-size:12px">Active Listings</div>
                        </div>
                        <div style="background:#fff;border:1px solid #e8e3dd;padding:14px;border-radius:8px;text-align:center;min-width:110px">
                            <div style="font-family: Georgia, 'Times New Roman', serif;font-size:24px;font-weight:700"><?php echo $avgRating; ?></div>
                            <div style="color:#6b6460;font-size:12px">Average Rating</div>
                        </div>
                        <div style="background:#fff;border:1px solid #e8e3dd;padding:14px;border-radius:8px;text-align:center;min-width:140px">
                            <div style="font-family: Georgia, 'Times New Roman', serif;font-size:14px;font-weight:700">Within 24 hours</div>
                            <div style="color:#6b6460;font-size:12px">Response Time</div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

    <div class="container">
        <!-- Filter toggle button (visible only on mobile) -->
        <button class="filter-toggle-btn" id="filterToggle">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                <path d="M22 3H2L10 12.46V19L14 21V12.46L22 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Show Filters
        </button>
        
        <div class="layout">
        <aside class="sidebar" id="filterSidebar">
            <div class="panel">
                <h4>Glass Type</h4>
                <ul class="filter-list" id="glass-types-list">
                    <?php
                        $allGlassTypes = ['Green Glass', 'White Glass', 'Brown Glass', 'Clear Glass', 'Mixed Glass'];
                        // compute counts from products
                        $glassTypeCounts = array_fill_keys($allGlassTypes, 0);
                        if(isset($products) && is_array($products)){
                            foreach($products as $pp){
                                if(isset($pp['title']) && isset($glassTypeCounts[$pp['title']])){
                                    $glassTypeCounts[$pp['title']]++;
                                }
                            }
                        }
                        foreach($allGlassTypes as $glassType){
                            $count = isset($glassTypeCounts[$glassType]) ? $glassTypeCounts[$glassType] : 0;
                            // sanitize id for input
                            $id = 'glass_' . preg_replace('/[^a-z0-9]+/i','_', strtolower($glassType));
                            echo "<li><label><input type=\"checkbox\" class=\"glass-filter\" id=\"$id\" value=\"".htmlspecialchars($glassType,ENT_QUOTES,'UTF-8')."\"> <span>".htmlspecialchars($glassType,ENT_QUOTES,'UTF-8')."</span></label> <span class=\"count\">$count</span></li>";
                        }
                    ?>
                </ul>
            </div>

            <div class="divider"></div>

            <div class="panel">
                <h4>Tonnage Range</h4>
                <div class="price-range-container">
                    <?php
                        // determine tons bounds from products
                        $minTons = 0; $maxTons = 1000;
                        if(isset($products) && count($products)){
                            $tonsArray = array_column($products,'tons');
                            if(!empty($tonsArray)){
                                $minTons = (int)min($tonsArray);
                                $maxTons = (int)max($tonsArray);
                                if($maxTons == $minTons) $maxTons = $minTons + 100;
                            }
                        }
                    ?>
                    <div class="price-range-display">
                        <span id="minPriceLabel">0 tons</span>
                        <span id="maxPriceLabel"><?php echo $maxTons; ?> tons</span>
                    </div>

                    <div class="price-inputs">
                        <div class="price-input-group">
                            <label for="minPrice" style="font-size:12px;color:#6b6460;margin-right:4px">Min</label>
                            <input id="minPrice" type="number" class="price-input" min="0" max="<?php echo $maxTons; ?>" value="0" step="0.1">
                        </div>
                        <span style="color:#6b6460">-</span>
                        <div class="price-input-group">
                            <label for="maxPrice" style="font-size:12px;color:#6b6460;margin-right:4px">Max</label>
                            <input id="maxPrice" type="number" class="price-input" min="0" max="<?php echo $maxTons; ?>" value="<?php echo $maxTons; ?>" step="0.1">
                        </div>
                    </div>

                    <div class="price-slider-container">
                        <div class="price-slider">
                            <div class="price-slider-fill" id="priceFill"></div>
                            <div class="price-slider-thumb" id="minThumb" style="left:0%"></div>
                            <div class="price-slider-thumb" id="maxThumb" style="left:100%"></div>
                        </div>
                    </div>

                    <!-- Hidden range inputs for compatibility -->
                    <input id="minRange" type="range" min="0" max="<?php echo $maxTons; ?>" value="0" step="0.1" style="display:none">
                    <input id="maxRange" type="range" min="0" max="<?php echo $maxTons; ?>" value="<?php echo $maxTons; ?>" step="0.1" style="display:none">
                </div>
            </div>

            <div class="divider"></div>

            <div class="panel">
                <h4>Condition</h4>
                <ul class="filter-list" id="conditions-list">
                    <?php
                        $allConditions = ['New', 'Like New', 'Vintage'];
                        // compute counts from products
                        $conditionCounts = array_fill_keys($allConditions, 0);
                        if(isset($products) && is_array($products)){
                            foreach($products as $pp){
                                if(isset($pp['condition']) && isset($conditionCounts[$pp['condition']])){
                                    $conditionCounts[$pp['condition']]++;
                                }
                            }
                        }
                        foreach($allConditions as $condition){
                            $count = isset($conditionCounts[$condition]) ? $conditionCounts[$condition] : 0;
                            // sanitize id for input
                            $id = 'condition_' . preg_replace('/[^a-z0-9]+/i','_', strtolower($condition));
                            echo "<li><label><input type=\"checkbox\" class=\"condition-filter\" id=\"$id\" value=\"".htmlspecialchars($condition,ENT_QUOTES,'UTF-8')."\"> <span>".htmlspecialchars($condition,ENT_QUOTES,'UTF-8')."</span></label> <span class=\"count\">$count</span></li>";
                        }
                    ?>
                </ul>
            </div>

            <div class="divider"></div>

            <div class="panel">
                <button id="clear-filters" style="width:100%;padding:10px;border:1px solid #6b6460;background:#fff;color:#6b6460;border-radius:4px;cursor:pointer;">Clear All Filters</button>
            </div>
        </aside>

        <section class="content">
            <div class="toolbar">
                <div class="items-count"><strong id="itemCountNum">0</strong> listings found</div>
                <div>
                    <select aria-label="Sort" id="sortSelect">
                        <option value="featured">No filters</option>
                        <option value="tons-low">Tons: Low to High</option>
                        <option value="tons-high">Tons: High to Low</option>
                    </select>
                </div>
            </div>

            <!-- products are defined above so category counts can be computed before rendering the sidebar -->

            <div class="grid">
                <?php foreach($products as $p): ?>
                    <article class="card" data-glass-type="<?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>" data-tons="<?php echo isset($p['tons']) ? (float)$p['tons'] : 0; ?>" onclick="window.location.href='listings.php?id=<?php echo urlencode($p['id']); ?>'" style="cursor:pointer">
                        <div class="media" role="img" aria-label="<?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>">
                            <img src="<?php echo htmlspecialchars($p['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>" style="width:100%;height:100%;object-fit:cover;display:block">
                        </div>
                        <div class="meta">
                            <div class="listing-title" style="font-weight:700;font-size:16px;color:#2a2623;margin-bottom:6px">
                                <?php echo htmlspecialchars($p['listing_title'] ?? $p['title'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            <?php if(isset($p['tons'])): ?>
                            <div class="tons" style="color:#6b6460;font-size:14px;margin-bottom:4px">
                                <strong>Tonnage:</strong> <?php echo number_format($p['tons'], 2); ?> tons
                            </div>
                            <?php endif; ?>
                            <div class="glass-type" style="color:#6b6460;font-size:14px">
                                <strong>Type:</strong> <?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="pagination" id="pagination">
                <!-- Pagination will be generated by JavaScript -->
            </div>
        </section>
    </div>

    <script>
        (function(){
            // Mobile filter toggle
            const filterToggleBtn = document.getElementById('filterToggle');
            const filterSidebar = document.getElementById('filterSidebar');
            
            if (filterToggleBtn && filterSidebar) {
                filterToggleBtn.addEventListener('click', function() {
                    filterSidebar.classList.toggle('show');
                });
            }
            
            const glassTypeCheckboxes = Array.from(document.querySelectorAll('.glass-filter'));
            const conditionCheckboxes = Array.from(document.querySelectorAll('.condition-filter'));
            const minRange = document.getElementById('minRange');
            const maxRange = document.getElementById('maxRange');
            const minPrice = document.getElementById('minPrice');
            const maxPrice = document.getElementById('maxPrice');
            const minPriceLabel = document.getElementById('minPriceLabel');
            const maxPriceLabel = document.getElementById('maxPriceLabel');
            const priceFill = document.getElementById('priceFill');
            const minThumb = document.getElementById('minThumb');
            const maxThumb = document.getElementById('maxThumb');
            const cards = Array.from(document.querySelectorAll('.grid .card'));
            const itemsCount = document.querySelector('.items-count');
            const paginationContainer = document.getElementById('pagination');
            
            const ITEMS_PER_PAGE = 9;
            let currentPage = 1;
            let visibleCards = [];
            let maxTonsValue = <?php echo $maxTons; ?>;

            function applyFilters(){
                console.log('=== APPLY FILTERS CALLED ===');

                const activeGlassTypes = glassTypeCheckboxes.filter(cb=>cb.checked).map(cb=>cb.value);
                const activeConditions = conditionCheckboxes.filter(cb=>cb.checked).map(cb=>cb.value);

                // FIX: Use minPrice/maxPrice instead of minRange/maxRange
                const min = parseFloat(minPrice.value) || 0;
                const max = parseFloat(maxPrice.value) || maxTonsValue;

                console.log('Active Glass Types:', activeGlassTypes);
                console.log('Active Conditions:', activeConditions);
                console.log('Tons range:', min, 'to', max);
                console.log('Total cards:', cards.length);

                // compute counts per glass type and condition for the current filters
                const glassTypeCounts = {};
                const conditionCounts = {};
                visibleCards = [];
                
                cards.forEach(c=>{
                    const glassType = (c.getAttribute('data-glass-type') || '').trim();
                    const condition = (c.getAttribute('data-condition') || '').trim();
                    const tons = parseFloat(c.getAttribute('data-tons')) || 0;
                    const tonsMatch = tons >= min && tons <= max;
                    const glassTypeMatch = activeGlassTypes.length ? activeGlassTypes.indexOf(glassType) !== -1 : true;
                    const conditionMatch = activeConditions.length ? activeConditions.indexOf(condition) !== -1 : true;
                    
                    if(tonsMatch && conditionMatch){
                        if(!(glassType in glassTypeCounts)) glassTypeCounts[glassType]=0;
                        glassTypeCounts[glassType]++;
                    }
                    if(tonsMatch && glassTypeMatch){
                        if(!(condition in conditionCounts)) conditionCounts[condition]=0;
                        conditionCounts[condition]++;
                    }
                });

                cards.forEach((c, index) => {
                    const glassType = (c.getAttribute('data-glass-type') || '').trim();
                    const condition = (c.getAttribute('data-condition') || '').trim();
                    const tons = parseFloat(c.getAttribute('data-tons')) || 0;
                    const glassTypeMatch = activeGlassTypes.length ? activeGlassTypes.indexOf(glassType) !== -1 : true;
                    const conditionMatch = activeConditions.length ? activeConditions.indexOf(condition) !== -1 : true;
                    const tonsMatch = tons >= min && tons <= max;

                    // Log first 3 cards for debugging
                    if(index < 3) {
                        console.log(`Card ${index}:`, {
                            glassType: `"${glassType}"`,
                            activeGlassTypes,
                            indexOfResult: activeGlassTypes.indexOf(glassType),
                            glassTypeMatch,
                            condition: `"${condition}"`,
                            conditionMatch,
                            tons,
                            tonsMatch,
                            willShow: glassTypeMatch && conditionMatch && tonsMatch
                        });
                    }

                    if(glassTypeMatch && conditionMatch && tonsMatch){
                        visibleCards.push(c);
                    }
                });
                
                console.log('Visible cards after filter:', visibleCards.length);
                console.log('Glass type counts:', glassTypeCounts);
                console.log('Condition counts:', conditionCounts);

                // Update count
                const countNum = document.getElementById('itemCountNum');
                if (countNum) {
                    countNum.textContent = visibleCards.length;
                }

                // update count labels in sidebar
                document.querySelectorAll('#glass-types-list .count').forEach(el=>{
                    const li = el.closest('li');
                    const input = li.querySelector('.glass-filter');
                    const glassType = input.value;
                    el.textContent = glassTypeCounts[glassType] !== undefined ? glassTypeCounts[glassType] : 0;
                });
                document.querySelectorAll('#conditions-list .count').forEach(el=>{
                    const li = el.closest('li');
                    const input = li.querySelector('.condition-filter');
                    const condition = input.value;
                    el.textContent = conditionCounts[condition] !== undefined ? conditionCounts[condition] : 0;
                });

                // Reset to page 1 when filters change
                currentPage = 1;
                updatePagination();
                console.log('=== FILTER COMPLETE ===\n');
            }
            
            function updatePagination(){
                console.log('=== UPDATE PAGINATION CALLED ===');
                console.log('Total cards:', cards.length);
                console.log('Visible cards:', visibleCards.length);
                console.log('Current page:', currentPage);
                
                const grid = document.querySelector('.grid');
                
                // Hide all cards first
                cards.forEach(c => c.style.display = 'none');
                
                const totalPages = Math.ceil(visibleCards.length / ITEMS_PER_PAGE);
                const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
                const endIndex = startIndex + ITEMS_PER_PAGE;
                
                console.log('Start index:', startIndex, 'End index:', endIndex);
                console.log('Cards to show:', visibleCards.slice(startIndex, endIndex).length);
                
                // Show cards for current page in the correct order
                const cardsToShow = visibleCards.slice(startIndex, endIndex);
                cardsToShow.forEach((c, idx) => {
                    console.log(`Showing card ${idx}:`, c.getAttribute('data-glass-type'));
                    c.style.display = '';
                    // Reorder in DOM to match sorted order
                    grid.appendChild(c);
                });
                
                // Render pagination controls
                renderPaginationControls(totalPages);
                console.log('=== PAGINATION UPDATE COMPLETE ===\n');
            }
            
            function renderPaginationControls(totalPages){
                if(totalPages <= 1){
                    paginationContainer.innerHTML = '';
                    return;
                }
                
                let html = '';
                
                // Previous button
                html += `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>&larr;</button>`;
                
                // Page numbers
                for(let i = 1; i <= totalPages; i++){
                    if(i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)){
                        html += `<span class="page-number ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</span>`;
                    } else if(i === currentPage - 2 || i === currentPage + 2){
                        html += `<span class="page-ellipsis">...</span>`;
                    }
                }
                
                // Next button
                html += `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>&rarr;</button>`;
                
                paginationContainer.innerHTML = html;
            }
            
            window.changePage = function(page){
                const totalPages = Math.ceil(visibleCards.length / ITEMS_PER_PAGE);
                if(page < 1 || page > totalPages) return;
                currentPage = page;
                updatePagination();
                window.scrollTo({top: 0, behavior: 'smooth'});
            }

            // sync number inputs with range inputs and update visual slider
            function syncRanges(e){
                let min = parseFloat(minPrice.value);
                let max = parseFloat(maxPrice.value);
                
                // Handle NaN or empty values
                if(isNaN(min)) min = 0;
                if(isNaN(max)) max = maxTonsValue;

                // enforce boundaries for min tons
                if(min < 0){
                    min = 0;
                    minPrice.value = 0;
                }
                if(min > maxTonsValue){
                    min = maxTonsValue;
                    minPrice.value = maxTonsValue;
                }

                // enforce boundaries for max tons
                if(max < 0){
                    max = 0;
                    maxPrice.value = 0;
                }
                if(max > maxTonsValue){
                    max = maxTonsValue;
                    maxPrice.value = maxTonsValue;
                }

                // ensure min <= max
                if(min > max){
                    if(e && e.target === minPrice) {
                        max = min;
                        maxPrice.value = min;
                    } else {
                        min = max;
                        minPrice.value = max;
                    }
                }

                // update hidden range inputs for compatibility
                minRange.value = min;
                maxRange.value = max;

                // update display labels
                minPriceLabel.textContent = min.toFixed(1) + ' tons';
                maxPriceLabel.textContent = max.toFixed(1) + ' tons';

                // update visual slider
                updateVisualSlider(min, max);

                applyFilters();
            }

            // update visual slider appearance
            function updateVisualSlider(min, max) {
                const minPercent = (min / maxTonsValue) * 100;
                const maxPercent = (max / maxTonsValue) * 100;

                minThumb.style.left = minPercent + '%';
                maxThumb.style.left = maxPercent + '%';

                // update fill between thumbs
                priceFill.style.left = minPercent + '%';
                priceFill.style.right = (100 - maxPercent) + '%';
            }

            function clearAllFilters(){
                glassTypeCheckboxes.forEach(cb=>cb.checked = false);
                conditionCheckboxes.forEach(cb=>cb.checked = false);
                minPrice.value = 0;
                maxPrice.value = maxTonsValue;
                minRange.value = 0;
                maxRange.value = maxTonsValue;
                minPriceLabel.textContent = '0 tons';
                maxPriceLabel.textContent = maxTonsValue + ' tons';
                updateVisualSlider(0, maxTonsValue);
                currentPage = 1;
                applyFilters();
            }

            glassTypeCheckboxes.forEach(cb=>cb.addEventListener('change', applyFilters));
            conditionCheckboxes.forEach(cb=>cb.addEventListener('change', applyFilters));
            minPrice.addEventListener('input', syncRanges);
            maxPrice.addEventListener('input', syncRanges);
            minRange.addEventListener('input', syncRanges);
            maxRange.addEventListener('input', syncRanges);
            document.getElementById('clear-filters').addEventListener('click', clearAllFilters);

            // Sorting functionality
            const sortSelect = document.getElementById('sortSelect');
            sortSelect.addEventListener('change', function() {
                const sortValue = this.value;

                if(sortValue === 'featured') {
                    // No filters: restore original order (reset to cards array order)
                    visibleCards.sort((a, b) => {
                        return cards.indexOf(a) - cards.indexOf(b);
                    });
                } else if(sortValue === 'tons-low') {
                    // Tons: Low to High
                    visibleCards.sort((a, b) => {
                        const tonsA = parseFloat(a.getAttribute('data-tons')) || 0;
                        const tonsB = parseFloat(b.getAttribute('data-tons')) || 0;
                        return tonsA - tonsB;
                    });
                } else if(sortValue === 'tons-high') {
                    // Tons: High to Low
                    visibleCards.sort((a, b) => {
                        const tonsA = parseFloat(a.getAttribute('data-tons')) || 0;
                        const tonsB = parseFloat(b.getAttribute('data-tons')) || 0;
                        return tonsB - tonsA;
                    });
                }

                // Reset to first page after sorting
                currentPage = 1;
                updatePagination();
            });

            // Drag functionality for slider thumbs
            let isDragging = false;
            let activeThumb = null;

            function startDrag(e, thumb) {
                isDragging = true;
                activeThumb = thumb;
                e.preventDefault();
            }

            function handleDrag(e) {
                if (!isDragging || !activeThumb) return;
                
                const slider = document.querySelector('.price-slider');
                const rect = slider.getBoundingClientRect();
                const x = (e.type.includes('mouse') ? e.clientX : e.touches[0].clientX) - rect.left;
                const percent = Math.max(0, Math.min(100, (x / rect.width) * 100));
                const value = (percent / 100) * maxTonsValue;

                if (activeThumb === minThumb) {
                    minPrice.value = value.toFixed(1);
                } else if (activeThumb === maxThumb) {
                    maxPrice.value = value.toFixed(1);
                }
                
                syncRanges();
            }

            function stopDrag() {
                isDragging = false;
                activeThumb = null;
            }

            minThumb.addEventListener('mousedown', (e) => startDrag(e, minThumb));
            maxThumb.addEventListener('mousedown', (e) => startDrag(e, maxThumb));
            minThumb.addEventListener('touchstart', (e) => startDrag(e, minThumb));
            maxThumb.addEventListener('touchstart', (e) => startDrag(e, maxThumb));
            
            document.addEventListener('mousemove', handleDrag);
            document.addEventListener('touchmove', handleDrag);
            document.addEventListener('mouseup', stopDrag);
            document.addEventListener('touchend', stopDrag);

            // initialize values
            syncRanges();
            applyFilters();
        })();
    </script>

    </div> <!-- .container -->
</main>
    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
