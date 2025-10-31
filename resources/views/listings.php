<?php
session_start();
require __DIR__ . '/../../config.php';
require __DIR__ . '/../../includes/db_connect.php';

// Check of er een ID is meegegeven in de URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Ongeldige listing ID.');
}

$id = (int) $_GET['id'];

// Haal de specifieke listing op
$stmt = $pdo->prepare("SELECT * FROM listings WHERE id = ? AND published = 1 LIMIT 1");
$stmt->execute([$id]);
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    die('Listing niet gevonden.');
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($listing['glass_type']) ?> - Glass Market</title>
    <link rel="stylesheet" href="../css/app.css">
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<!-- Hero sectie -->
<section class="hero hero-muted text-center">
  <div class="container">
    <h1 class="page-title"><?= htmlspecialchars($listing['glass_type']) ?></h1>
    <?php if (!empty($listing['glass_type_other'])): ?>
        <p class="page-subtitle"><?= htmlspecialchars($listing['glass_type_other']) ?></p>
    <?php endif; ?>
  </div>
</section>

<!-- Detailsectie -->
<section class="section">
  <div class="container">
    <div class="card" style="max-width:800px; margin:0 auto;">
      <img src="/glass-market/assets/images/default-glass.jpg" alt="Glass" />
      <div class="card-body">
        <h3 class="card-title"><?= htmlspecialchars($listing['glass_type']) ?></h3>

        <p class="card-meta"><strong>Locatie opslag:</strong> <?= htmlspecialchars($listing['storage_location'] ?? 'Onbekend') ?></p>
        <p class="card-meta"><strong>Recycled:</strong> <?= htmlspecialchars($listing['recycled']) ?></p>
        <p class="card-meta"><strong>Getest:</strong> <?= htmlspecialchars($listing['tested']) ?></p>

        <p class="card-meta"><strong>Kwaliteit:</strong><br>
          <?= nl2br(htmlspecialchars($listing['quality_notes'] ?? 'Geen opmerkingen')) ?>
        </p>

        <p class="card-meta"><strong>Hoeveelheid:</strong>
          <?= htmlspecialchars($listing['quantity_tons'] ?? '-') ?> ton
          <?= $listing['quantity_note'] ? '(' . htmlspecialchars($listing['quantity_note']) . ')' : '' ?>
        </p>

        <p class="card-price"><strong>Prijs:</strong>
          <?= $listing['price_text'] ? htmlspecialchars($listing['price_text']) . ' ' . htmlspecialchars($listing['currency']) : 'Prijs op aanvraag' ?>
        </p>

        <p class="card-meta"><strong>Beschikbaar tot:</strong>
          <?= $listing['valid_until'] ? htmlspecialchars($listing['valid_until']) : 'Onbekend' ?>
        </p>
      </div>
    </div>

    <div class="center" style="margin-top: 30px;">
      <a href="browse.php" class="btn btn-secondary">← Terug naar overzicht</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>
