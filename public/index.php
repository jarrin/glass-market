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
        LIMIT 10
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
    <title>Glass Market - Premium Glass Trading Platform</title>
    <style>
        :root {
            --home-bg: #f5f5f7;
            --home-text: #1d1d1f;
            --home-muted: #6e6e73;
            --home-card-bg: rgba(255, 255, 255, 0.9);
            --home-border: rgba(15, 23, 42, 0.08);
        }

        body.home-body {
            background: var(--home-bg);
            color: var(--home-text);
            font-family: "SF Pro Display", "SF Pro Text", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .home-hero {
            padding: 120px 0 80px;
            text-align: center;
            background: linear-gradient(180deg, rgba(255,255,255,0) 0%, var(--home-bg) 100%);
        }

        .home-shell {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0 32px;
        }

        .home-hero-kicker {
            display: inline-block;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--home-muted);
            margin-bottom: 20px;
        }

        .home-hero-title {
            font-size: clamp(42px, 7vw, 72px);
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 24px;
            color: var(--home-text);
        }

        .home-hero-subtitle {
            font-size: 22px;
            color: var(--home-muted);
            line-height: 1.6;
            max-width: 680px;
            margin: 0 auto 40px;
        }

        .home-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: center;
        }

        .home-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 16px 32px;
            border-radius: 999px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            background: var(--home-text);
            color: white;
            box-shadow: 0 18px 40px -20px rgba(0, 0, 0, 0.45);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .home-btn-primary:hover {
            transform: translateY(-2px);
            background: #111013;
            box-shadow: 0 24px 50px -20px rgba(0, 0, 0, 0.5);
        }

        .home-section {
            padding: 60px 0;
        }

        .home-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .home-section-title {
            font-size: 36px;
            font-weight: 700;
            color: var(--home-text);
        }

        .home-section-link {
            font-size: 16px;
            font-weight: 600;
            color: var(--home-muted);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .home-section-link:hover {
            color: var(--home-text);
        }

        .home-listings-grid {
            display: grid;
            gap: 24px;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            margin-bottom: 40px;
        }

        .home-listing-card {
            background: var(--home-card-bg);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--home-border);
            box-shadow: 0 20px 40px -30px rgba(15, 23, 42, 0.25);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .home-listing-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 28px 50px -30px rgba(15, 23, 42, 0.35);
        }

        .home-listing-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }

        .home-listing-body {
            padding: 20px;
        }

        .home-listing-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--home-text);
        }

        .home-listing-meta {
            font-size: 14px;
            color: var(--home-muted);
            margin-bottom: 12px;
        }

        .home-listing-company {
            font-size: 15px;
            font-weight: 500;
            color: var(--home-text);
        }

        .home-values {
            padding: 80px 0;
        }

        .home-values-grid {
            display: grid;
            gap: 28px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .home-value-card {
            background: var(--home-card-bg);
            border-radius: 28px;
            padding: 36px;
            border: 1px solid var(--home-border);
            position: relative;
            overflow: hidden;
            box-shadow: 0 24px 50px -40px rgba(15, 23, 42, 0.35);
        }

        .home-value-card::before {
            content: '';
            position: absolute;
            inset: -40% 50% 60% -40%;
            background: radial-gradient(circle at top right, rgba(47, 109, 245, 0.18), transparent 60%);
            opacity: 0.8;
        }

        .home-value-icon {
            font-size: 36px;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
            color: var(--home-text);
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .home-value-icon svg {
            width: 40px;
            height: 40px;
            stroke: var(--home-text);
        }

        .home-value-title {
            font-size: 22px;
            font-weight: 600;
            color: var(--home-text);
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
        }

        .home-value-text {
            font-size: 16px;
            color: var(--home-muted);
            line-height: 1.7;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 768px) {
            .home-shell {
                padding: 0 20px;
            }

            .home-hero {
                padding: 80px 0 56px;
            }

            .home-section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .home-listings-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="home-body">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <?php include __DIR__ . '/../includes/subscription-notification.php'; ?>

    <main style="padding-top: 70px;">
        <section class="home-hero">
            <div class="home-shell">
                <div class="home-hero-kicker">PREMIUM GLASS TRADING</div>
                <h1 class="home-hero-title">Glass Market</h1>
                <p class="home-hero-subtitle">The best place to buy and sell glass worldwide</p>
                <div class="home-hero-actions">
                    <a class="home-btn-primary" href="../resources/views/browse.php">
                        Explore Collection
                        <span>→</span>
                    </a>
                </div>
            </div>
        </section>

        <section class="home-section">
            <div class="home-shell">
                <div class="home-section-header">
                    <h2 class="home-section-title">Latest Glass Listings</h2>
                    <a class="home-section-link" href="../resources/views/browse.php">View All →</a>
                </div>
                <div class="home-listings-grid">
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
                            <article class="home-listing-card">
                                <a href="../resources/views/listings.php?id=<?php echo (int)$listing['id']; ?>" style="display:block;color:inherit;text-decoration:none">
                                    <img src="<?php echo htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" />
                                    <div class="home-listing-body">
                                        <h3 class="home-listing-title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h3>
                                        <div class="home-listing-meta"><?php echo htmlspecialchars($glassType, ENT_QUOTES, 'UTF-8'); ?> • <?php echo $tons; ?> tons</div>
                                        <div class="home-listing-company"><?php echo htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?></div>
                                    </div>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback if no listings -->
                        <article class="home-listing-card">
                            <img src="https://picsum.photos/seed/glass1/900/600" alt="Featured piece" />
                            <div class="home-listing-body">
                                <h3 class="home-listing-title">No listings yet</h3>
                                <div class="home-listing-meta">Be the first to add a listing!</div>
                                <div class="home-listing-company">Glass Market</div>
                            </div>
                        </article>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="home-values">
            <div class="home-shell">
                <div class="home-values-grid">
                    <div class="home-value-card">
                        <div class="home-value-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            </svg>
                        </div>
                        <h4 class="home-value-title">Curated Selection</h4>
                        <p class="home-value-text">Every piece is carefully vetted by our team of glass industry experts</p>
                    </div>
                    <div class="home-value-card">
                        <div class="home-value-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 8V12L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h4 class="home-value-title">Verified Sellers</h4>
                        <p class="home-value-text">Trade with confidence through our secure platform and verified seller network</p>
                    </div>
                    <div class="home-value-card">
                        <div class="home-value-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2 12H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 2C14.5013 4.73835 15.9228 8.29203 16 12C15.9228 15.708 14.5013 19.2616 12 22C9.49872 19.2616 8.07725 15.708 8 12C8.07725 8.29203 9.49872 4.73835 12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h4 class="home-value-title">Global Network</h4>
                        <p class="home-value-text">Connect with glass traders and recyclers across the world</p>
                    </div>
                </div>
            </div>
        </section>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </main>
</body>
</html>