<?php require_once __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
  <title>Glass Market</title>
</head>
<body>
  <?php include __DIR__ . '/../../includes/navbar.php'; ?>

  <main style="margin-top: 70px;">
    <section class="section about">
      <div class="container">
        <h2>About Glass Market</h2>
        <p class="center" style="max-width:650px;margin:0 auto;">The world's premier marketplace for glass art, crystals, and handcrafted glassware. Connecting artisans with collectors since 2020.</p>
      </div>
    </section>

    <section class="section">
      <div class="container grid grid-4" style="text-align:center;">
        <div class="stat">
          <h3 style="font-size:2.2rem;">10K+</h3>
          <p class="muted">Active Sellers</p>
        </div>
        <div class="stat">
          <h3 style="font-size:2.2rem;">50K+</h3>
          <p class="muted">Products Listed</p>
        </div>
        <div class="stat">
          <h3 style="font-size:2.2rem;">100+</h3>
          <p class="muted">Countries</p>
        </div>
        <div class="stat">
          <h3 style="font-size:2.2rem;">4.8</h3>
          <p class="muted">Average Rating</p>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
