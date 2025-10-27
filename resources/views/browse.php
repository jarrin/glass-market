<?php require_once __DIR__ . '/../../config.php'; ?>
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
        body{font-family: Arial, Helvetica, sans-serif; background:#f6f0eb; color:#111; margin:0}
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

        @media (max-width:980px){.grid{grid-template-columns:repeat(2,1fr)}}
        @media (max-width:640px){.layout{flex-direction:column}.sidebar{width:100%}.grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>
<main class="container" style="padding-top: 70px;">
    <h1 class="page-title">Browse Collection</h1>
    <p class="subtitle">Discover unique glass art from artisans worldwide</p>

    <?php
        // static products array (more items than the original mock)
        $products = [];
        $styles = ['Contemporary', 'Vintage', 'Art Deco', 'Murano', 'Bohemian'];
        $conditions = ['New', 'Like New', 'Vintage'];
        for($i=1;$i<=24;$i++){
            // simple price spread for demo
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
    ?>

    <div class="layout">
        <aside class="sidebar">
            <div class="panel">
                <h4>Categories</h4>
                <ul class="filter-list" id="categories-list">
                    <?php
                        $allCategories = ['Vases & Vessels','Sculptures','Tableware','Lighting','Decorative','Jewelry'];
                        // compute counts from products (if products not defined yet we'll set to 0 temporarily)
                        $categoryCounts = array_fill_keys($allCategories, 0);
                        if(isset($products) && is_array($products)){
                            foreach($products as $pp){
                                if(isset($pp['category']) && isset($categoryCounts[$pp['category']])){
                                    $categoryCounts[$pp['category']]++;
                                }
                            }
                        }
                        foreach($allCategories as $cat){
                            $count = isset($categoryCounts[$cat]) ? $categoryCounts[$cat] : 0;
                            // sanitize id for input
                            $id = 'cat_' . preg_replace('/[^a-z0-9]+/i','_', strtolower($cat));
                            echo "<li><label><input type=\"checkbox\" class=\"cat-filter\" id=\"$id\" value=\"".htmlspecialchars($cat,ENT_QUOTES,'UTF-8')."\"> <span>".htmlspecialchars($cat,ENT_QUOTES,'UTF-8')."</span></label> <span class=\"count\">$count</span></li>";
                        }
                    ?>
                </ul>
            </div>

            <div class="divider"></div>

            <div class="panel">
                <h4>Price Range</h4>
                <div class="price-range-container">
                    <?php
                        // determine price bounds from products
                        $minPrice = 0; $maxPrice = 1000;
                        if(isset($products) && count($products)){
                            $prices = array_column($products,'price');
                            $minPrice = (int)min($prices);
                            $maxPrice = (int)max($prices);
                        }
                    ?>
                    <div class="price-range-display">
                        <span id="minPriceLabel">$0</span>
                        <span id="maxPriceLabel">$1000</span>
                    </div>

                    <div class="price-inputs">
                        <div class="price-input-group">
                            <label for="minPrice" style="font-size:12px;color:#6b6460;margin-right:4px">Min</label>
                            <input id="minPrice" type="number" class="price-input" min="0" max="1000" value="0">
                        </div>
                        <span style="color:#6b6460">-</span>
                        <div class="price-input-group">
                            <label for="maxPrice" style="font-size:12px;color:#6b6460;margin-right:4px">Max</label>
                            <input id="maxPrice" type="number" class="price-input" min="0" max="1000" value="1000">
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
                    <input id="minRange" type="range" min="0" max="1000" value="0" style="display:none">
                    <input id="maxRange" type="range" min="0" max="1000" value="1000" style="display:none">
                </div>
            </div>

            <div class="divider"></div>

            <div class="panel">
                <h4>Style</h4>
                <ul class="filter-list" id="styles-list">
                    <?php
                        $allStyles = ['Contemporary', 'Vintage', 'Art Deco', 'Murano', 'Bohemian'];
                        // compute counts from products
                        $styleCounts = array_fill_keys($allStyles, 0);
                        if(isset($products) && is_array($products)){
                            foreach($products as $pp){
                                if(isset($pp['style']) && isset($styleCounts[$pp['style']])){
                                    $styleCounts[$pp['style']]++;
                                }
                            }
                        }
                        foreach($allStyles as $style){
                            $count = isset($styleCounts[$style]) ? $styleCounts[$style] : 0;
                            // sanitize id for input
                            $id = 'style_' . preg_replace('/[^a-z0-9]+/i','_', strtolower($style));
                            echo "<li><label><input type=\"checkbox\" class=\"style-filter\" id=\"$id\" value=\"".htmlspecialchars($style,ENT_QUOTES,'UTF-8')."\"> <span>".htmlspecialchars($style,ENT_QUOTES,'UTF-8')."</span></label> <span class=\"count\">$count</span></li>";
                        }
                    ?>
                </ul>
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
                <div class="items-count">24 items</div>
                <div>
                    <select aria-label="Sort">
                        <option>Featured</option>
                        <option>Newest</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                    </select>
                </div>
            </div>

            <!-- products are defined above so category counts can be computed before rendering the sidebar -->

            <div class="grid">
                <?php foreach($products as $p): ?>
                    <article class="card" data-category="<?php echo htmlspecialchars($p['category'], ENT_QUOTES, 'UTF-8'); ?>" data-price="<?php echo (int)$p['price']; ?>" data-style="<?php echo htmlspecialchars($p['style'], ENT_QUOTES, 'UTF-8'); ?>" data-condition="<?php echo htmlspecialchars($p['condition'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="media" role="img" aria-label="<?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>">
                            <img src="<?php echo htmlspecialchars($p['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>" style="width:100%;height:100%;object-fit:cover;display:block">
                        </div>
                        <div class="meta">
                            <div class="cat"><?php echo htmlspecialchars(strtoupper($p['category']), ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="title"><?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="price" style="margin-top:6px;color:#6b6460">$<?php echo number_format($p['price'],0); ?></div>
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
            const catCheckboxes = Array.from(document.querySelectorAll('.cat-filter'));
            const styleCheckboxes = Array.from(document.querySelectorAll('.style-filter'));
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

            function applyFilters(){
                const activeCats = catCheckboxes.filter(cb=>cb.checked).map(cb=>cb.value);
                const activeStyles = styleCheckboxes.filter(cb=>cb.checked).map(cb=>cb.value);
                const activeConditions = conditionCheckboxes.filter(cb=>cb.checked).map(cb=>cb.value);
                const min = parseInt(minRange.value,10);
                const max = parseInt(maxRange.value,10);

                // compute counts per category, style, condition for the current filters
                const catCounts = {};
                const styleCounts = {};
                const conditionCounts = {};
                visibleCards = [];
                
                cards.forEach(c=>{
                    const cat = c.getAttribute('data-category');
                    const style = c.getAttribute('data-style');
                    const condition = c.getAttribute('data-condition');
                    const price = parseInt(c.getAttribute('data-price'),10);
                    const priceMatch = price >= min && price <= max;
                    const catMatch = activeCats.length ? activeCats.indexOf(cat) !== -1 : true;
                    const styleMatch = activeStyles.length ? activeStyles.indexOf(style) !== -1 : true;
                    const conditionMatch = activeConditions.length ? activeConditions.indexOf(condition) !== -1 : true;
                    if(priceMatch && styleMatch && conditionMatch){
                        if(!(cat in catCounts)) catCounts[cat]=0;
                        catCounts[cat]++;
                    }
                    if(priceMatch && catMatch && conditionMatch){
                        if(!(style in styleCounts)) styleCounts[style]=0;
                        styleCounts[style]++;
                    }
                    if(priceMatch && catMatch && styleMatch){
                        if(!(condition in conditionCounts)) conditionCounts[condition]=0;
                        conditionCounts[condition]++;
                    }
                });

                cards.forEach(c=>{
                    const cat = c.getAttribute('data-category');
                    const style = c.getAttribute('data-style');
                    const condition = c.getAttribute('data-condition');
                    const price = parseInt(c.getAttribute('data-price'),10);
                    const catMatch = activeCats.length ? activeCats.indexOf(cat) !== -1 : true;
                    const styleMatch = activeStyles.length ? activeStyles.indexOf(style) !== -1 : true;
                    const conditionMatch = activeConditions.length ? activeConditions.indexOf(condition) !== -1 : true;
                    const priceMatch = price >= min && price <= max;
                    if(catMatch && styleMatch && conditionMatch && priceMatch){
                        visibleCards.push(c);
                    }
                });
                
                itemsCount.textContent = visibleCards.length + ' items';

                // update count labels in sidebar
                document.querySelectorAll('#categories-list .count').forEach(el=>{
                    const li = el.closest('li');
                    const input = li.querySelector('.cat-filter');
                    const cat = input.value;
                    el.textContent = catCounts[cat] !== undefined ? catCounts[cat] : 0;
                });
                document.querySelectorAll('#styles-list .count').forEach(el=>{
                    const li = el.closest('li');
                    const input = li.querySelector('.style-filter');
                    const style = input.value;
                    el.textContent = styleCounts[style] !== undefined ? styleCounts[style] : 0;
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
            }
            
            function updatePagination(){
                // Hide all cards first
                cards.forEach(c => c.style.display = 'none');
                
                const totalPages = Math.ceil(visibleCards.length / ITEMS_PER_PAGE);
                const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
                const endIndex = startIndex + ITEMS_PER_PAGE;
                
                // Show only cards for current page
                visibleCards.slice(startIndex, endIndex).forEach(c => c.style.display = '');
                
                // Render pagination controls
                renderPaginationControls(totalPages);
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
                let min = parseInt(minPrice.value,10) || 0;
                let max = parseInt(maxPrice.value,10) || 1000;

                // enforce boundaries for min price (0-1000)
                if(min < 0){
                    min = 0;
                    minPrice.value = 0;
                }
                if(min > 1000){
                    min = 1000;
                    minPrice.value = 1000;
                }

                // enforce boundaries for max price (0-1000)
                if(max < 0){
                    max = 0;
                    maxPrice.value = 0;
                }
                if(max > 1000){
                    max = 1000;
                    maxPrice.value = 1000;
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
                minPriceLabel.textContent = '$' + min;
                maxPriceLabel.textContent = '$' + max;

                // update visual slider
                updateVisualSlider(min, max);

                applyFilters();
            }

            // update visual slider appearance
            function updateVisualSlider(min, max) {
                const minPercent = (min / 1000) * 100;
                const maxPercent = (max / 1000) * 100;

                minThumb.style.left = minPercent + '%';
                maxThumb.style.left = maxPercent + '%';

                // update fill between thumbs
                priceFill.style.left = minPercent + '%';
                priceFill.style.right = (100 - maxPercent) + '%';
            }

            function clearAllFilters(){
                catCheckboxes.forEach(cb=>cb.checked = false);
                styleCheckboxes.forEach(cb=>cb.checked = false);
                conditionCheckboxes.forEach(cb=>cb.checked = false);
                minPrice.value = 0;
                maxPrice.value = 1000;
                minRange.value = 0;
                maxRange.value = 1000;
                minPriceLabel.textContent = '$0';
                maxPriceLabel.textContent = '$1000';
                updateVisualSlider(0, 1000);
                currentPage = 1;
                applyFilters();
            }

            catCheckboxes.forEach(cb=>cb.addEventListener('change', applyFilters));
            styleCheckboxes.forEach(cb=>cb.addEventListener('change', applyFilters));
            conditionCheckboxes.forEach(cb=>cb.addEventListener('change', applyFilters));
            minPrice.addEventListener('input', syncRanges);
            maxPrice.addEventListener('input', syncRanges);
            minRange.addEventListener('input', syncRanges);
            maxRange.addEventListener('input', syncRanges);
            document.getElementById('clear-filters').addEventListener('click', clearAllFilters);

            // initialize values
            syncRanges();
            applyFilters();
        })();
    </script>

</main>
    <?php include __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>
