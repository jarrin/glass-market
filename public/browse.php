<!-- Browse collection page - static mockup -->
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Browse Collection</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <style>
        body{font-family: Arial, Helvetica, sans-serif; background:#f6f0eb; color:#111; margin:0}
        .container{max-width:1200px;margin:40px auto;padding:0 20px}
        .page-title{font-family: Georgia, 'Times New Roman', serif; font-size:48px; margin-bottom:6px}
        .subtitle{color:#6b6460;margin-bottom:18px}
        .layout{display:flex;gap:30px}
        /* Sidebar */
        .sidebar{width:260px}
        .panel{background:transparent;padding:12px 0}
        .panel h4{margin:0 0 12px 0;font-size:16px}
        .filter-list{list-style:none;padding:0;margin:0}
        .filter-list li{display:flex;justify-content:space-between;padding:8px 6px;color:#6b6460}
        .divider{height:1px;background:#e3dad3;margin:18px 0}

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

        @media (max-width:980px){.grid{grid-template-columns:repeat(2,1fr)}}
        @media (max-width:640px){.layout{flex-direction:column}.sidebar{width:100%}.grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
<main class="container">
    <h1 class="page-title">Browse Collection</h1>
    <p class="subtitle">Discover unique glass art from artisans worldwide</p>

    <?php
        // static products array (more items than the original mock)
        $products = [];
        for($i=1;$i<=24;$i++){
            // simple price spread for demo
            $price = rand(25, 950);
            $products[] = [
                'id'=>$i,
                'title'=>"Glass Item #$i",
                'category'=>($i%3==0)?'Tableware':(($i%3==1)?'Vases':'Sculptures'),
                'image'=>"https://picsum.photos/seed/glass{$i}/800/800",
                'price'=>$price
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
                <div style="padding:6px 0">
                    <?php
                        // determine price bounds from products
                        $minPrice = 0; $maxPrice = 1000;
                        if(isset($products) && count($products)){
                            $prices = array_column($products,'price');
                            $minPrice = (int)min($prices);
                            $maxPrice = (int)max($prices);
                        }
                    ?>
                    <div style="display:flex;gap:8px;align-items:center">
                        <input id="minRange" type="range" min="0" max="1000" value="0">
                        <input id="maxRange" type="range" min="0" max="1000" value="1000">
                    </div>
                    <div style="display:flex;justify-content:space-between;color:#6b6460;margin-top:8px"><span id="minVal">$0</span><span id="maxVal">$1000</span></div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="panel">
                <h4>Style</h4>
                <ul class="filter-list">
                    <li><span>Contemporary</span><span>345</span></li>
                    <li><span>Vintage</span><span>234</span></li>
                    <li><span>Art Deco</span><span>156</span></li>
                    <li><span>Murano</span><span>189</span></li>
                    <li><span>Bohemian</span><span>123</span></li>
                </ul>
            </div>

            <div class="divider"></div>

            <div class="panel">
                <h4>Condition</h4>
                <ul class="filter-list">
                    <li><span>New</span></li>
                    <li><span>Like New</span></li>
                    <li><span>Vintage</span></li>
                </ul>
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
                    <article class="card" data-category="<?php echo htmlspecialchars($p['category'], ENT_QUOTES, 'UTF-8'); ?>" data-price="<?php echo (int)$p['price']; ?>">
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
        </section>
    </div>

    <script>
        (function(){
            const checkboxes = Array.from(document.querySelectorAll('.cat-filter'));
            const minRange = document.getElementById('minRange');
            const maxRange = document.getElementById('maxRange');
            const minVal = document.getElementById('minVal');
            const maxVal = document.getElementById('maxVal');
            const cards = Array.from(document.querySelectorAll('.grid .card'));
            const itemsCount = document.querySelector('.items-count');

            function applyFilters(){
                const activeCats = checkboxes.filter(cb=>cb.checked).map(cb=>cb.value);
                const min = parseInt(minRange.value,10);
                const max = parseInt(maxRange.value,10);
                let shown = 0;

                // compute counts per category for the current price range
                const counts = {};
                cards.forEach(c=>{
                    const cat = c.getAttribute('data-category');
                    const price = parseInt(c.getAttribute('data-price'),10);
                    if(!(cat in counts)) counts[cat]=0;
                    if(price >= min && price <= max) counts[cat]++;
                });

                cards.forEach(c=>{
                    const cat = c.getAttribute('data-category');
                    const price = parseInt(c.getAttribute('data-price'),10);
                    const catMatch = activeCats.length ? activeCats.indexOf(cat) !== -1 : true;
                    const priceMatch = price >= min && price <= max;
                    if(catMatch && priceMatch){
                        c.style.display = '';
                        shown++;
                    } else {
                        c.style.display = 'none';
                    }
                });
                itemsCount.textContent = shown + ' items';

                // update count labels in sidebar
                document.querySelectorAll('#categories-list .count').forEach(el=>{
                    const li = el.closest('li');
                    const input = li.querySelector('.cat-filter');
                    const cat = input.value;
                    el.textContent = counts[cat] !== undefined ? counts[cat] : 0;
                });
            }

            // keep min <= max
            function syncRanges(e){
                let a = parseInt(minRange.value,10);
                let b = parseInt(maxRange.value,10);
                if(a > b){
                    if(e.target === minRange) maxRange.value = a;
                    else minRange.value = b;
                }
                minVal.textContent = '$' + minRange.value;
                maxVal.textContent = '$' + maxRange.value;
                applyFilters();
            }

            checkboxes.forEach(cb=>cb.addEventListener('change', applyFilters));
            minRange.addEventListener('input', syncRanges);
            maxRange.addEventListener('input', syncRanges);

            // initialize values
            minVal.textContent = '$' + minRange.value;
            maxVal.textContent = '$' + maxRange.value;
            applyFilters();
        })();
    </script>

</main>
</body>
</html>
