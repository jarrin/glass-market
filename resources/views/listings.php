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
$stmt = $pdo->prepare("SELECT l.*, u.company_name, u.email AS company_email FROM listings l LEFT JOIN users u ON (l.company_id = u.company_id) WHERE l.id = ? AND l.published = 1 LIMIT 1");
$stmt->execute([$id]);
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$listing) {
    die('Listing niet gevonden.');
}

// Afbeelding bepalen
$imageUrl = "https://picsum.photos/seed/glass{$listing['id']}/800/800"; // fallback placeholder
if (!empty($listing['image_path'])) {
    $imageUrl = PUBLIC_URL . '/' . ltrim($listing['image_path'], '/');
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($listing['quantity_note']) ?> - Glass Market</title>
    <link rel="stylesheet" href="../css/app.css">
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<!-- Hero -->
<section class="hero hero-muted text-center">
  <div class="container">
    <h1 class="page-title"><?= htmlspecialchars($listing['quantity_note']) ?></h1>
    <?php if (!empty($listing['glass_type_other'])): ?>
        <p class="page-subtitle"><?= htmlspecialchars($listing['glass_type_other']) ?></p>
    <?php endif; ?>
  </div>
</section>

<!-- Detailsectie -->
<section class="section">
  <div class="container" style="max-width:900px; margin:0 auto;">
    <div class="media" style="height:400px; overflow:hidden; margin-bottom:20px;">
        <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>"
             alt="<?= htmlspecialchars($listing['quantity_note'], ENT_QUOTES, 'UTF-8') ?>"
             style="width:100%;height:100%;object-fit:cover;display:block">
    </div>

    <h2><?= htmlspecialchars($listing['quantity_note']) ?></h2>


    <?php if ($listing['glass_type_other']): ?>
      <p><em><?= htmlspecialchars($listing['glass_type_other']) ?></em></p>
    <?php endif; ?>



    <p><strong>Notes:</strong><br>
      <?= nl2br(htmlspecialchars($listing['quality_notes'] ?? 'Geen opmerkingen')) ?>
    </p>

    <p><strong>Quantity:</strong>
      <?= htmlspecialchars($listing['quantity_tons'] ?? '-') ?> ton
    </p>

    <?php if ($listing['company_name']): ?>
      <p><strong>Company:</strong> <?= htmlspecialchars($listing['company_name']) ?></p>
      <div style="margin: 10px 0;">
        <a href="mailto:<?= htmlspecialchars($listing['company_email']) ?>" class="btn btn-primary" style="background: #166534; color: #fff; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 600;">Contact company</a>
      </div>
    <?php endif; ?>

    <div style="margin-top:30px;">
      <a href="browse.php" class="btn btn-secondary">← Back to listings</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>
