<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/app.css">
    <title>Glass Market</title>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <main>
        <section class="hero">
            <div class="container">
                <h1 class="hero-title">Glass Market<br>The best place<br>to buy and sell glass</h1>
                <p class="hero-subtitle">Find the perfect glass piece or start selling your own</p>
                <div class="hero-cta">
                    <a class="btn btn-primary" href="#">Explore Collection</a>
                    <a class="btn btn-secondary" href="#">Start Selling</a>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container section-head">
                <h2>browse the glass market</h2>
                <a class="link" href="#">View All →</a>
            </div>
            <div class="container grid grid-4">
                <article class="card">
                    <img src="https://picsum.photos/seed/glass1/900/600" alt="Featured piece" />
                    <div class="card-body">
                        <h3 class="card-title">Venetian Crystal Vase</h3>
                        <div class="card-meta">Artisan Glass Co.</div>
                        <div class="card-price">$245.00</div>
                    </div>
                </article>
                <article class="card">
                    <img src="https://picsum.photos/seed/glass2/900/600" alt="Featured piece" />
                    <div class="card-body">
                        <h3 class="card-title">Hand-Blown Glass Bowl</h3>
                        <div class="card-meta">Studio Lumina</div>
                        <div class="card-price">$189.00</div>
                    </div>
                </article>
                <article class="card">
                    <img src="https://picsum.photos/seed/glass3/900/600" alt="Featured piece" />
                    <div class="card-body">
                        <h3 class="card-title">Murano Glass Sculpture</h3>
                        <div class="card-meta">Venice Artworks</div>
                        <div class="card-price">$520.00</div>
                    </div>
                </article>
                <article class="card">
                    <img src="https://picsum.photos/seed/glass4/900/600" alt="Featured piece" />
                    <div class="card-body">
                        <h3 class="card-title">Crystal Wine Glasses Set</h3>
                        <div class="card-meta">Crystal Craft</div>
                        <div class="card-price">$156.00</div>
                    </div>
                </article>
            </div>
        </section>

        <section class="section values">
            <div class="container values-grid">
                <div class="value">
                    <div class="value-icon">✦</div>
                    <h4>Curated Selection</h4>
                    <p>Every piece is carefully vetted by our team of glass art experts</p>
                </div>
                <div class="value">
                    <div class="value-icon">🛡</div>
                    <h4>Buyer Protection</h4>
                    <p>Shop with confidence with our secure payment and authenticity guarantee</p>
                </div>
                <div class="value">
                    <div class="value-icon">🚚</div>
                    <h4>Worldwide Shipping</h4>
                    <p>Professional packaging and insured delivery to your doorstep</p>
                </div>
            </div>
        </section>
        <section class="cta">
            <div class="container center">
                <h2>Join Our Community<br/>of Glass Artisans</h2>
                <p class="muted">Start selling your glass creations to collectors and enthusiasts worldwide</p>
                <a class="btn btn-primary" href="#">Become a Seller</a>
            </div>
        </section>
        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </main>
</body>
</html>