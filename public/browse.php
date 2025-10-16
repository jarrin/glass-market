<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Browse Collection</title>
    <style>
        body{font-family: Arial, Helvetica, sans-serif; background:#f6f0eb; color:#111; margin:0}
        .glass-navbar { position: relative; width:100%; display:flex; align-items:center; gap:16px; padding:12px 20px; background:rgba(255,255,255,0.35); backdrop-filter: blur(8px); border-bottom:1px solid rgba(0,0,0,0.04)}
        .brand{font-family:Georgia, serif;font-weight:700}
        .container{max-width:1200px;margin:40px auto;padding:0 20px}
        .page-title{font-family: Georgia, 'Times New Roman', serif; font-size:48px; margin-bottom:6px}
        .subtitle{color:#6b6460;margin-bottom:18px}
        .layout{display:flex;gap:30px}
        .sidebar{width:260px}
        .panel{background:transparent;padding:12px 0}
        .panel h4{margin:0 0 12px 0;font-size:16px}
        .filter-list{list-style:none;padding:0;margin:0}
        .filter-list li{display:flex;justify-content:space-between;padding:8px 6px;color:#6b6460}
        .divider{height:1px;background:#e3dad3;margin:18px 0}
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
    <header class="glass-navbar">
        <div class="brand">|| GLASS MARKET</div>
        <nav style="margin-left:16px;">
            <a href="#">Browse</a> &nbsp; <a href="#">Categories</a> &nbsp; <a href="#">Sellers</a>
        </nav>
        <div style="margin-left:auto;"> <input type="search" placeholder="Search glass art, crystals..." style="padding:8px;border-radius:6px;border:1px solid rgba(0,0,0,0.08)"></div>
    </header>

    <main class="container">
        <h1 class="page-title">Browse Collection</h1>
        <p class="subtitle">Discover unique glass art from artisans worldwide</p>

        <div class="layout">
            <aside class="sidebar">
                <div class="panel">
                    <h4>Categories</h4>
                    <ul class="filter-list" id="categories-list"></ul>
                </div>
                <div class="divider"></div>
                <div class="panel">
                    <h4>Price Range</h4>
                    <div style="padding:6px 0">
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
                    </ul>
                </div>
            </aside>

            <section class="content">
                <div class="toolbar">
                    <div class="items-count">0 items</div>
                    <div>
                        <select aria-label="Sort">
                            <option>Featured</option>
                            <option>Newest</option>
                        </select>
                    </div>
                </div>

                <div class="grid" id="productGrid"></div>
            </section>
        </div>
    </main>

    <script>
        // static data and client-side rendering
        const products = [];
        for(let i=1;i<=48;i++){
            const cats = ['Vases & Vessels','Sculptures','Tableware','Lighting','Decorative','Jewelry'];
            products.push({
                id:i,
                title:`Glass Item #${i}`,
                category:cats[i%cats.length],
                price: Math.floor(Math.random()*900)+50,
                image:`https://picsum.photos/seed/glass${i}/800/800`
            });
        }

        const catList = Array.from(new Set(products.map(p=>p.category)));
        const categoriesListEl = document.getElementById('categories-list');
        catList.forEach(cat=>{
            const li = document.createElement('li');
            li.innerHTML = `<label><input type='checkbox' class='cat-filter' value='${cat}'> <span>${cat}</span></label> <span class='count'>0</span>`;
            categoriesListEl.appendChild(li);
        });

        const grid = document.getElementById('productGrid');
        function renderProducts(items){
            grid.innerHTML = '';
            items.forEach(p=>{
                const a = document.createElement('article');
                a.className = 'card';
                a.dataset.category = p.category;
                a.dataset.price = p.price;
                a.innerHTML = `<div class='media'><img src='${p.image}' alt='${p.title}' style='width:100%;height:100%;object-fit:cover;display:block'></div><div class='meta'><div class='cat'>${p.category.toUpperCase()}</div><div class='title'>${p.title}</div><div class='price' style='margin-top:6px;color:#6b6460'>$${p.price}</div></div>`;
                grid.appendChild(a);
            });
        }

        const checkboxes = ()=>Array.from(document.querySelectorAll('.cat-filter'));
        const minRange = document.getElementById('minRange');
        const maxRange = document.getElementById('maxRange');
        const minVal = document.getElementById('minVal');
        const maxVal = document.getElementById('maxVal');
        const itemsCount = document.querySelector('.items-count');

        function applyFilters(){
            const activeCats = checkboxes().filter(cb=>cb.checked).map(cb=>cb.value);
            const min = parseInt(minRange.value,10);
            const max = parseInt(maxRange.value,10);
            const cards = Array.from(document.querySelectorAll('.grid .card'));
            let shown = 0;
            const counts = {};
            cards.forEach(c=>{
                const cat = c.dataset.category;
                const price = parseInt(c.dataset.price,10);
                if(!(cat in counts)) counts[cat]=0;
                if(price>=min && price<=max) counts[cat]++;
            });
            cards.forEach(c=>{
                const cat = c.dataset.category;
                const price = parseInt(c.dataset.price,10);
                const catMatch = activeCats.length ? activeCats.indexOf(cat)!==-1 : true;
                const priceMatch = price>=min && price<=max;
                if(catMatch && priceMatch){ c.style.display=''; shown++; } else c.style.display='none';
            });
            itemsCount.textContent = shown + ' items';
            // update counts
            document.querySelectorAll('#categories-list .count').forEach((el, idx)=>{
                const cat = catList[idx];
                el.textContent = counts[cat]||0;
            });
        }

        // wire events
        document.addEventListener('change', e=>{ if(e.target.classList && e.target.classList.contains('cat-filter')) applyFilters(); });
        minRange.addEventListener('input', ()=>{ if(parseInt(minRange.value)>parseInt(maxRange.value)) maxRange.value = minRange.value; minVal.textContent = '$'+minRange.value; applyFilters(); });
        maxRange.addEventListener('input', ()=>{ if(parseInt(maxRange.value)<parseInt(minRange.value)) minRange.value = maxRange.value; maxVal.textContent = '$'+maxRange.value; applyFilters(); });

        // initial render
        renderProducts(products);
        minVal.textContent = '$'+minRange.value; maxVal.textContent = '$'+maxRange.value;
        applyFilters();
    </script>
</body>
</html>
