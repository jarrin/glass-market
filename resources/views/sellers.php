<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/app.css">
    <title>Sellers</title>
</head>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="page">
        <section class="hero hero-muted">
            <div class="container text-center">
                <h1 class="page-title">Discover Our Sellers</h1>
                <p class="page-subtitle">Browse through our curated collection of talented glass artists and collectors from around the world</p>
                <div class="search-large">
                    <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="currentColor" d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 1 0-.71.71l.27.28v.79l5 4.99L20.49 19zm-6 0a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
                    </svg>
                    <input type="text" placeholder="Search sellers by name, location, or specialty..." />
                </div>
            </div>
        </section>

        <section class="stats">
            <div class="container stats-row">
                <div class="stat"><div class="num">500+</div><div class="label">Active Sellers</div></div>
                <div class="stat"><div class="num">50+</div><div class="label">Countries</div></div>
                <div class="stat"><div class="num">4.8</div><div class="label">Average Rating</div></div>
                <div class="stat"><div class="num">10K+</div><div class="label">Products Listed</div></div>
            </div>
        </section>

        <section class="cards-grid container">
            <?php for ($i = 0; $i < 8; $i++): ?>
                <article class="card seller-card">
                    <div class="avatar"></div>
                    <h3 class="seller-name">Sample Seller <?= $i + 1 ?></h3>
                    <div class="seller-meta">Amsterdam, NL • 4.8 (200 reviews)</div>
                    <div class="seller-meta">73 listings</div>
                    <a href="#" class="btn btn-secondary">View Shop</a>
                </article>
            <?php endfor; ?>
        </section>

        <div class="container text-center">
            <button class="btn btn-outline" type="button">Load More Sellers</button>
        </div>

        <section class="cta-band">
            <div class="container text-center">
                <h2>Become a Seller</h2>
                <p>Join our community of talented glass artists and reach collectors worldwide</p>
                <a class="btn btn-primary" href="#">Start Selling Today</a>
            </div>
        </section>
    </main>

</body>
</html>