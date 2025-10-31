<?php 
session_start();
require_once __DIR__ . '/../config.php'; 

// Fetch latest listings from database
require_once __DIR__ . '/../includes/db_connect.php';

$featuredListings = [];
try {
    $stmt = $pdo->query("
        SELECT 
            l.id,
            l.glass_type,
            l.glass_type_other,
            l.quantity_tons,
            l.quantity_note,
            l.image_path,
            c.name as company_name
        FROM listings l
        LEFT JOIN companies c ON l.company_id = c.id
        WHERE l.published = 1
        ORDER BY l.created_at DESC
        LIMIT 4
    ");
    $featuredListings = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Database error in index.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Load compiled CSS from public/css -->
    <link rel="stylesheet" href="css/app.css">
    <title>Glass Market</title>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <?php include __DIR__ . '/../includes/subscription-notification.php'; ?>

    <main style="padding-top: 70px;">
        <section class="hero">
            <div class="container">
                <h1 class="hero-title">Glass Market<br>The best place<br>to buy and sell glass</h1>
                <p class="hero-subtitle">Find the perfect glass piece or start selling your own</p>
                <div class="hero-cta">
                    <a class="btn btn-primary" href="../resources/views/browse.php">Explore Collection</a>
                    <a class="btn btn-secondary" href="../resources/views/selling-page.php">Start Selling</a>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container section-head">
                <h2>browse the glass market</h2>
                <a class="link" href="../resources/views/browse.php">View All →</a>
            </div>
            <div class="container grid grid-4">
                <?php if (!empty($featuredListings)): ?>
                    <?php foreach ($featuredListings as $listing): ?>
                        <?php
                            $glassType = $listing['glass_type_other'] ?: $listing['glass_type'];
                            $title = $listing['quantity_note'] ?: $glassType;
                            $companyName = $listing['company_name'] ?: 'Glass Market';
                            $tons = number_format($listing['quantity_tons'], 2);
                            
                            // Determine image URL
                            $imageUrl = "https://picsum.photos/seed/glass{$listing['id']}/900/600";
                            if (!empty($listing['image_path'])) {
                                $imageUrl = PUBLIC_URL . '/' . $listing['image_path'];
                            }
                        ?>
                        <article class="card">
                            <img src="<?php echo htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" />
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h3>
                                <div class="card-meta"><?php echo htmlspecialchars($glassType, ENT_QUOTES, 'UTF-8'); ?> • <?php echo $tons; ?> tons</div>
                                <div class="card-price"><?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback if no listings -->
                    <article class="card">
                        <img src="https://picsum.photos/seed/glass1/900/600" alt="Featured piece" />
                        <div class="card-body">
                            <h3 class="card-title">No listings yet</h3>
                            <div class="card-meta">Be the first to add a listing!</div>
                            <div class="card-price">Glass Market</div>
                        </div>
                    </article>
                <?php endif; ?>
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
                <a class="btn btn-primary" href="../resources/views/selling-page.php">Start Selling</a>
            </div>
        </section>
        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </main>
</body>
</html>