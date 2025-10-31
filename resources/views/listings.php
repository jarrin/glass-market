<?php
session_start();
require __DIR__ . '/../../config.php';
require __DIR__ . '/../../includes/db_connect.php';

// Check of er een ID is meegegeven
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

// Afbeelding bepalen (zelfde principe als browse page)
$imageUrl = "https://picsum.photos/seed/glass{$listing['id']}/800/800"; // fallback placeholder
if (!empty($listing['image_path'])) {
    $imageUrl = PUBLIC_URL . '/' . ltrim($listing['image_path'], '/'); // gebruik geüploade afbeelding
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

<!-- Hero -->
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
    <div class="card" style="max-width:900px; margin:0 auto;">
      <div class="media" style="height:400px; overflow:hidden;">
        <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>" 
             alt="<?= htmlspecialchars($listing['glass_type'], ENT_QUOTES, 'UTF-8') ?>" 
             style="width:100%;height:100%;object-fit:cover;display:block">
      </div>

      <div class="card-body">
        <h3 class="card-title"><?= htmlspecialchars($listing['glass_type']) ?></h3>

        <?php if ($listing['glass_type_other']): ?>
          <p class="card-meta"><em><?= htmlspecialchars($listing['glass_type_other']) ?></em></p>
        <?php endif; ?>

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
      <a href="listings.php" class="btn btn-secondary">← Terug naar overzicht</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>
