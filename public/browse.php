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

    <div class="layout">
        <aside class="sidebar">
            <div class="panel">
                <h4>Categories</h4>
                <ul class="filter-list">
                    <li><span>Vases &amp; Vessels</span><span>234</span></li>
                    <li><span>Sculptures</span><span>156</span></li>
                    <li><span>Tableware</span><span>412</span></li>
                    <li><span>Lighting</span><span>89</span></li>
                    <li><span>Decorative</span><span>178</span></li>
                    <li><span>Jewelry</span><span>203</span></li>
                </ul>
            </div>

            <div class="divider"></div>

            <div class="panel">
                <h4>Price Range</h4>
                <div style="padding:6px 0">
                    <div style="height:8px;background:#f0ece9;border-radius:8px;position:relative">
                        <div style="position:absolute;left:4px;top:-6px;width:12px;height:12px;border-radius:50%;background:#fff;border:2px solid #333"></div>
                        <div style="position:absolute;right:4px;top:-6px;width:12px;height:12px;border-radius:50%;background:#fff;border:2px solid #333"></div>
                        <div style="position:absolute;left:0;right:0;top:1px;height:6px;background:#2b241f;opacity:0.9;border-radius:6px;margin:0 12px"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;color:#6b6460;margin-top:8px"><span>$0</span><span>$1000</span></div>
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

            <?php
                // static products array (more items than the original mock)
                $products = [];
                for($i=1;$i<=24;$i++){
                    $products[] = [
                        'id'=>$i,
                        'title'=>"Glass Item #$i",
                        'category'=>($i%3==0)?'Tableware':(($i%3==1)?'Vases':'Sculptures'),
                        'image'=>"https://picsum.photos/seed/glass{$i}/800/800"
                    ];
                }
            ?>

            <div class="grid">
                <?php foreach($products as $p): ?>
                    <article class="card">
                        <div class="media" role="img" aria-label="<?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>">
                            <img src="<?php echo htmlspecialchars($p['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?>" style="width:100%;height:100%;object-fit:cover;display:block">
                        </div>
                        <div class="meta">
                            <div class="cat"><?php echo htmlspecialchars(strtoupper($p['category']), ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="title"><?php echo htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

</main>
</body>
</html>
