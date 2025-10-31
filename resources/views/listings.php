<?php session_start(); ?>
<?php require_once __DIR__ . '/../../config.php'; ?>

<?php
require __DIR__ . '/../../includes/db_connect.php';

// Haal alle listings op
$stmt = $pdo->query("SELECT id, glass_type, glass_type_other, price_text, currency, storage_location, recycled FROM listings WHERE published = 1 ORDER BY id DESC");
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Listings - Glass Market</title>
    <link rel="stylesheet" href="../css/app.css">
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>
<!-- Hero -->
<section class="hero hero-muted text-center">
  <div class="container">
    <h1 class="page-title">Ontdek onze beschikbare glass listings</h1>
    <p class="page-subtitle">Bekijk het actuele aanbod van gerecycled glas, gesorteerd per type en locatie.</p>
  </div>
</section>

<!-- Listings grid -->
<section class="section">
  <div class="container">
    <div class="section-head">
      <h2>Alle listings</h2>
    </div>

    <?php if (count($listings) > 0): ?>
      <div class="grid grid-4">
        <?php foreach ($listings as $listing): ?>
          <a href="listing.php?id=<?= $listing['id'] ?>" class="card">
            <img src="/glass-market/assets/images/default-glass.jpg" alt="Glass" />
            <div class="card-body">
              <h3 class="card-title"><?= htmlspecialchars($listing['glass_type']) ?></h3>
              <?php if ($listing['glass_type_other']): ?>
                <p class="card-meta"><?= htmlspecialchars($listing['glass_type_other']) ?></p>
              <?php endif; ?>
              <p class="card-price">
                <?= $listing['price_text'] ? htmlspecialchars($listing['price_text']) . ' ' . htmlspecialchars($listing['currency']) : 'Prijs op aanvraag' ?>
              </p>
              <p class="card-meta"><?= htmlspecialchars($listing['storage_location'] ?? 'Onbekende locatie') ?></p>
              <p class="card-meta">Recycled: <?= htmlspecialchars($listing['recycled']) ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>Er zijn momenteel geen listings beschikbaar.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="container footer-top">
    <div class="footer-brand">
      <a href="#" class="brand">
        <span class="brand-mark"></span> Glass Market
      </a>
      <p class="muted">Een platform voor duurzaam hergebruik van glasmaterialen.</p>
    </div>
    <div class="footer-cols">
      <div class="footer-col">
        <h5>Over ons</h5>
        <a href="#">Missie</a>
        <a href="#">Team</a>
      </div>
      <div class="footer-col">
        <h5>Contact</h5>
        <a href="#">Support</a>
        <a href="#">Partners</a>
      </div>
      <div class="footer-col">
        <h5>Volg ons</h5>
        <a href="#">LinkedIn</a>
        <a href="#">Twitter</a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <span>&copy; <?= date('Y') ?> Glass Market</span>
    <div class="footer-links">
      <a href="#">Privacy</a>
      <a href="#">Terms</a>
    </div>
  </div>
</footer>

</body>
</html>
