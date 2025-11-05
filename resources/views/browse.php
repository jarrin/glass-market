<?php session_start(); ?>
<?php require_once __DIR__ . '/../../config.php'; ?>
<?php require_once __DIR__ . '/../../includes/subscription-check.php'; ?>
<!-- Browse collection page - static mockup -->
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Browse Collection</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <style>
        body{font-family: Arial, Helvetica, sans-serif; background:#f5f5f7; color:#111; margin:0}
        .container{max-width:1200px;margin:40px auto;padding:0 20px}
        .page-title{font-family: Georgia, 'Times New Roman', serif; font-size:48px; margin-bottom:6px}
        .subtitle{color:#6b6460;margin-bottom:18px}
        .layout{display:flex;gap:30px}
        /* Sidebar */
        .sidebar{width:260px;position:sticky;top:20px;height:fit-content}
        .panel{background:transparent;padding:12px 0}
        .panel h4{margin:0 0 12px 0;font-size:16px}
        .filter-list{list-style:none;padding:0;margin:0}
        .filter-list li{display:flex;justify-content:space-between;padding:8px 6px;color:#6b6460}
        .divider{height:1px;background:#e3dad3;margin:18px 0}

        /* Enhanced Price Range */
        .price-range-container{margin:12px 0}
        .price-range-display{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;font-weight:600;color:#2a2623}
        .price-inputs{display:flex;gap:12px;align-items:center}
        .price-input-group{display:flex;align-items:center}
        .price-input{ width:80px;padding:8px;border:1px solid #d4c5b3;border-radius:6px;text-align:center;font-size:14px;background:#fff;color:#2a2623}
        .price-input:focus{outline:none;border-color:#8c8278}
        .price-slider-container{margin:16px 0}
        .price-slider{background:linear-gradient(to right, #e3dad3 0%, #e3dad3 100%);height:6px;border-radius:3px;position:relative}
        .price-slider-fill{background:#8c8278;height:100%;border-radius:3px;position:absolute;left:0;right:0}
        .price-slider-thumb{position:absolute;top:50%;transform:translateY(-50%);width:20px;height:20px;background:#fff;border:2px solid #8c8278;border-radius:50%;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,0.1)}
        .price-slider-thumb:hover{box-shadow:0 4px 12px rgba(0,0,0,0.15)}

        /* Grid */
        .content{flex:1}
        .toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
        .items-count{color:#6b6460}
        .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
        .card{background:#fff;border-radius:6px;overflow:hidden;border:1px solid rgba(0,0,0,0.06)}
        .card .media{height:320px;background:#ddd;background-size:cover;background-position:center}
        .card .meta{padding:12px}
        .card .meta .cat{font-size:12px;color:#8b8683}
        .card .meta .title{font-weight:700;margin-top:6px}

        /* Styled Select Dropdown */
        .toolbar select {
            padding: 8px 12px;
            border: 1px solid #d4c5b3;
            border-radius: 6px;
            background: #fff;
            color: #2a2623;
            font-size: 14px;
            cursor: pointer;
            outline: none;
            transition: border-color 0.2s ease;
            min-width: 180px;
        }
        .toolbar select:focus {
            border-color: #8c8278;
        }
        .toolbar select:hover {
            border-color: #8c8278;
        }

        /* Pagination */
        .pagination{display:flex;justify-content:center;align-items:center;gap:8px;margin-top:40px;padding:20px 0}
        .pagination button{padding:8px 14px;border:1px solid #d4c5b3;background:#fff;color:#2a2623;border-radius:6px;cursor:pointer;font-size:14px;transition:all 0.2s ease}
        .pagination button:hover:not(:disabled){background:#f6f0eb;border-color:#8c8278}
        .pagination button:disabled{opacity:0.4;cursor:not-allowed}
        .pagination button.active{background:#2a2623;color:#fff;border-color:#2a2623}
        .pagination .page-number{padding:8px 14px;border:1px solid #d4c5b3;background:#fff;color:#2a2623;border-radius:6px;cursor:pointer;font-size:14px;transition:all 0.2s ease;min-width:40px;text-align:center}
        .pagination .page-number:hover{background:#f6f0eb;border-color:#8c8278}
        .pagination .page-number.active{background:#2a2623;color:#fff;border-color:#2a2623;font-weight:600}
        .pagination .page-ellipsis{padding:8px;color:#6b6460}

        /* Filter toggle button for mobile */
        .filter-toggle-btn{display:none;width:100%;padding:12px 16px;margin-bottom:16px;background:#2a2623;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:14px;font-weight:600;box-shadow:0 2px 8px rgba(0,0,0,0.1)}
        .filter-toggle-btn:hover{background:#1a1614}
        .filter-toggle-btn:active{transform:scale(0.98)}
        
        @media (max-width:980px){.grid{grid-template-columns:repeat(2,1fr)}}
        @media (max-width:640px){
            .layout{flex-direction:column}
            .sidebar{width:100%;display:none;margin-bottom:20px;position:relative;z-index:10;background:#f6f0eb;padding:16px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1)}
            .sidebar.show{display:block}
            .grid{grid-template-columns:1fr}
            .filter-toggle-btn{display:block}
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>
    <?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>

    <!-- Toast Notification Container -->
    <div id="toast-container" style="position: fixed; top: 80px; right: 20px; z-index: 99999;"></div>

    <?php if (isset($_SESSION['browse_error'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('<?php echo addslashes($_SESSION['browse_error']); ?>', 'error');
            });
        </script>
        <?php unset($_SESSION['browse_error']); ?>
    <?php endif; ?>

<main class="container" style="padding-top: 70px;">
    <h1 class="page-title">Browse Collection</h1>

    <?php
        // Fetch products from database
        require_once __DIR__ . '/../../includes/db_connect.php';
        
        $products = [];
        
        try {
            // Fetch all published listings from the database
            $stmt = $pdo->query("
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
                    c.name as company_name
                FROM listings l
                LEFT JOIN companies c ON l.company_id = c.id
                WHERE l.published = 1
                ORDER BY l.created_at DESC
            ");
            
            $listings = $stmt->fetchAll();
            
            // Map database listings to product format expected by browse page
            $categoryMapping = [
                'Clear Cullet' => 'Vases',
                'Brown Cullet' => 'Tableware',
                'Mixed Cullet' => 'Decorative',
                'Green Cullet' => 'Vases & Vessels',
                'Flat Glass' => 'Tableware',
                'Container Glass' => 'Tableware'
            ];
            
            foreach ($listings as $index => $listing) {
                // Extract numeric price from price_text (e.g., "€120/ton CIF" -> 120)
                $price = 0;
                if (preg_match('/[\d,]+/', $listing['price_text'], $matches)) {
                    $price = (int)str_replace(',', '', $matches[0]);
                }
                
                // Map glass type to category
                $glassType = $listing['glass_type_other'] ?: $listing['glass_type'];
                $category = $categoryMapping[$listing['glass_type']] ?? 'Decorative';
                
                // Determine style based on recycled/tested status
                $style = 'Contemporary';
                if ($listing['recycled'] === 'recycled' && $listing['tested'] === 'tested') {
                    $style = 'Art Deco';
                } elseif ($listing['recycled'] === 'recycled') {
                    $style = 'Murano';
                } elseif ($listing['tested'] === 'tested') {
                    $style = 'Contemporary';
                } else {
                    $style = 'Vintage';
                }
                
                // Determine condition
                $condition = 'New';
                if ($listing['tested'] === 'tested') {
                    $condition = 'New';
                } elseif ($listing['tested'] === 'untested') {
                    $condition = 'Vintage';
                } else {
                    $condition = 'Like New';
                }
                
                // Determine image URL - use uploaded image if available, otherwise placeholder
                $imageUrl = "https://picsum.photos/seed/glass{$listing['id']}/800/800";
                if (!empty($listing['image_path'])) {
                    $imageUrl = PUBLIC_URL . '/' . $listing['image_path'];
                }
                
                $products[] = [
                    'id' => $listing['id'],
                    'title' => $glassType,
                    'listing_title' => $listing['quantity_note'] ?: $glassType,
                    'tons' => $listing['quantity_tons'],
                    'category' => $category,
                    'image' => $imageUrl,
                    'price' => $price,
                    'style' => $style,
                    'condition' => $condition,
                    'description' => $listing['quality_notes'] ?: 'Quality glass material',
                    'location' => $listing['storage_location']
                ];
            }
            
            // If no products in database, add some demo products
            if (empty($products)) {
                $styles = ['Contemporary', 'Vintage', 'Art Deco', 'Murano', 'Bohemian'];
                $conditions = ['New', 'Like New', 'Vintage'];
                for($i=1;$i<=24;$i++){
                    $price = rand(25, 950);
                    $products[] = [
                        'id'=>$i,
                        'title'=>"Glass Item #$i",
                        'category'=>($i%3==0)?'Tableware':(($i%3==1)?'Vases':'Sculptures'),
                        'image'=>"https://picsum.photos/seed/glass{$i}/800/800",
                        'price'=>$price,
                        'style'=>$styles[array_rand($styles)],
                        'condition'=>$conditions[array_rand($conditions)]
                    ];
                }
            }
            
        } catch (PDOException $e) {
            // If database query fails, use demo products
            error_log("Database error in browse.php: " . $e->getMessage());
            $styles = ['Contemporary', 'Vintage', 'Art Deco', 'Murano', 'Bohemian'];
            $conditions = ['New', 'Like New', 'Vintage'];
            for($i=1;$i<=24;$i++){
                $price = rand(25, 950);
                $products[] = [
                    'id'=>$i,
                    'title'=>"Glass Item #$i",
                    'category'=>($i%3==0)?'Tableware':(($i%3==1)?'Vases':'Sculptures'),
                    'image'=>"https://picsum.photos/seed/glass{$i}/800/800",
                    'price'=>$price,
                    'style'=>$styles[array_rand($styles)],
                    'condition'=>$conditions[array_rand($conditions)]
                ];
            }
        }
    ?>

    <!-- Filter toggle button (visible only on mobile) -->
    <button class="filter-toggle-btn" id="filterToggle">🔍 Filters tonen/verbergen</button>
    
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
                <h4>Recycled</h4>
                <ul class="filter-list" id="recycled-filter">
                    <?php
                        $recycledOptions = ['Recycled', 'Not Recycled'];
                        // Initialize counts
                        $recycledCounts = [
                            'Recycled' => 0,
                            'Not Recycled' => 0
                        ];
                        
                        // Calculate counts from products
                        if(isset($products) && is_array($products)){
                            foreach($products as $pp){
                                if(isset($pp['is_recycled']) && $pp['is_recycled'] == 1) {
                                    $recycledCounts['Recycled']++;
                                } else {
                                    $recycledCounts['Not Recycled']++;
                                }
                            }
                        }
                        
                        // Output the checkboxes
                        foreach($recycledOptions as $option) {
                            $count = $recycledCounts[$option] ?? 0;
                            $id = 'recycled_' . preg_replace('/[^a-z0-9]+/i', '_', strtolower($option));
                            echo "<li><label><input type=\"checkbox\" class=\"recycled-filter\" id=\"$id\" value=\"".htmlspecialchars($option, ENT_QUOTES, 'UTF-8')."\"> <span>".htmlspecialchars($option, ENT_QUOTES, 'UTF-8')."</span></label> <span class=\"count\">$count</span></li>";
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
                <div class="items-count">24 items</div>
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
                    <article class="card" data-glass-type="<?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>" data-tons="<?php echo isset($p['tons']) ? (float)$p['tons'] : 0; ?>" data-condition="<?php echo htmlspecialchars($p['condition'], ENT_QUOTES, 'UTF-8'); ?>">
                        <a href="listings.php?id=<?php echo (int)$p['id']; ?>" style="display:block;color:inherit;text-decoration:none">
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
                        </a>
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
            
            const glassTypeCheckboxes = Array.from(document.querySelectorAll('.glass-type-filter'));
            const recycledCheckboxes = Array.from(document.querySelectorAll('.recycled-filter'));
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
            let activeRecycled = [];

            function applyFilters(){
                console.log('=== APPLY FILTERS CALLED ===');

                const activeGlassTypes = glassTypeCheckboxes.filter(cb=>cb.checked).map(cb=>cb.value);
                activeRecycled = recycledCheckboxes.filter(cb=>cb.checked).map(cb=>cb.value);

                // FIX: Use minPrice/maxPrice instead of minRange/maxRange
                const min = parseFloat(minPrice.value) || 0;
                const max = parseFloat(maxPrice.value) || maxTonsValue;

                console.log('Active Glass Types:', activeGlassTypes);
                console.log('Active Recycled:', activeRecycled);
                console.log('Tons range:', min, 'to', max);
                console.log('Total cards:', cards.length);

                // compute counts per glass type and condition for the current filters
                const glassTypeCounts = {};
                const conditionCounts = {};
                visibleCards = [];
                
                cards.forEach(c=>{
                    const glassType = c.getAttribute('data-glass-type');
                    const isRecycled = c.getAttribute('data-recycled') === '1';
                    const tons = parseFloat(c.getAttribute('data-tons') || 0);
                    const tonsMatch = tons >= min && tons <= max;
                    const glassTypeMatch = activeGlassTypes.length === 0 || activeGlassTypes.includes(glassType);
                    let matchesRecycled = true;
                    
                    if (activeRecycled.length > 0) {
                        matchesRecycled = activeRecycled.some(option => {
                            if (option === 'Recycled') return isRecycled;
                            if (option === 'Not Recycled') return !isRecycled;
                            return false;
                        });
                    }
                    
                    if(tonsMatch && glassTypeMatch && matchesRecycled){
                        if(!(glassType in glassTypeCounts)) glassTypeCounts[glassType]=0;
                        glassTypeCounts[glassType]++;
                    }
                    if(tonsMatch && glassTypeMatch){
                        if(!(condition in conditionCounts)) conditionCounts[condition]=0;
                        conditionCounts[condition]++;
                    }
                });

                cards.forEach((c, index) => {
                    const glassType = c.getAttribute('data-glass-type');
                    const isRecycled = c.getAttribute('data-recycled') === '1';
                    const tons = parseFloat(c.getAttribute('data-tons') || 0);
                    const glassTypeMatch = activeGlassTypes.length === 0 || activeGlassTypes.includes(glassType);
                    let matchesRecycled = true;
                    
                    if (activeRecycled.length > 0) {
                        matchesRecycled = activeRecycled.some(option => {
                            if (option === 'Recycled') return isRecycled;
                            if (option === 'Not Recycled') return !isRecycled;
                            return false;
                        });
                    }
                    
                    const tonsMatch = tons >= min && tons <= max;

                    // Log first 3 cards for debugging
                    if(index < 3) {
                        console.log(`Card ${index}:`, {
                            glassType: `"${glassType}"`,
                            activeGlassTypes,
                            indexOfResult: activeGlassTypes.indexOf(glassType),
                            glassTypeMatch,
                            isRecycled,
                            matchesRecycled,
                            tons,
                            tonsMatch,
                            willShow: glassTypeMatch && matchesRecycled && tonsMatch
                        });
                    }

                    if(glassTypeMatch && matchesRecycled && tonsMatch){
                        visibleCards.push(c);
                    }
                });
                
                console.log('Visible cards after filter:', visibleCards.length);
                console.log('Glass type counts:', glassTypeCounts);
                console.log('Condition counts:', conditionCounts);

                itemsCount.textContent = visibleCards.length + ' items';

                // update count labels in sidebar
                document.querySelectorAll('#glass-types-list .count').forEach(el=>{
                    const li = el.closest('li');
                    const input = li.querySelector('.glass-type-filter');
                    const glassType = input.value;
                    el.textContent = glassTypeCounts[glassType] !== undefined ? glassTypeCounts[glassType] : 0;
                });
                document.querySelectorAll('#recycled-filter .count').forEach(el=>{
                    const li = el.closest('li');
                    const input = li.querySelector('.recycled-filter');
                    const recycled = input.value;
                    el.textContent = activeRecycled.includes(recycled) ? visibleCards.length : 0;
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
                recycledCheckboxes.forEach(cb=>cb.checked = false);
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

            glassTypeCheckboxes.forEach(checkbox => checkbox.addEventListener('change', applyFilters));
            recycledCheckboxes.forEach(checkbox => checkbox.addEventListener('change', applyFilters));
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

        // Toast Notification System
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const icons = {
                success: '✓',
                error: '✕',
                info: 'ℹ'
            };
            
            const colors = {
                success: '#10b981',
                error: '#ef4444',
                info: '#3b82f6'
            };
            
            toast.style.cssText = `
                background: white;
                border-left: 4px solid ${colors[type] || colors.info};
                padding: 16px 20px;
                margin-bottom: 10px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 300px;
                max-width: 400px;
                animation: slideIn 0.3s ease-out;
            `;
            
            toast.innerHTML = `
                <span style="
                    background: ${colors[type] || colors.info};
                    color: white;
                    width: 24px;
                    height: 24px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    flex-shrink: 0;
                ">${icons[type] || icons.info}</span>
                <span style="flex: 1; color: #1f2937; font-size: 14px;">${message}</span>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
        
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>

</main>
    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
