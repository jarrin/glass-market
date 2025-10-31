<?php session_start(); ?>
<?php require_once __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
  <title>Categories - Glass Market</title>
</head>
<body>
  <?php include __DIR__ . '/../../includes/navbar.php'; ?>
  <?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>

  <main style="padding-top: 70px;">
    <section class="hero hero-muted text-center">
      <div class="container">
        <h1 class="page-title">Browse by Category</h1>
        <p class="page-subtitle">Explore our curated collection of glass art organized by type</p>
      </div>
    </section>

    <section class="section">
      <div class="container grid grid-4">
        <article class="card">
          <img src="https://picsum.photos/seed/vases/900/600" alt="Vases & Vessels" />
          <div class="card-body">
            <h3 class="card-title">Vases & Vessels</h3>
            <div class="card-meta">124 items</div>
          </div>
        </article>
        
        <article class="card">
          <img src="https://picsum.photos/seed/sculptures/900/600" alt="Sculptures" />
          <div class="card-body">
            <h3 class="card-title">Sculptures</h3>
            <div class="card-meta">86 items</div>
          </div>
        </article>
        
        <article class="card">
          <img src="https://picsum.photos/seed/tableware/900/600" alt="Tableware" />
          <div class="card-body">
            <h3 class="card-title">Tableware</h3>
            <div class="card-meta">203 items</div>
          </div>
        </article>
        
        <article class="card">
          <img src="https://picsum.photos/seed/lighting/900/600" alt="Lighting" />
          <div class="card-body">
            <h3 class="card-title">Lighting</h3>
            <div class="card-meta">67 items</div>
          </div>
        </article>
        
        <article class="card">
          <img src="https://picsum.photos/seed/decorative/900/600" alt="Decorative" />
          <div class="card-body">
            <h3 class="card-title">Decorative</h3>
            <div class="card-meta">145 items</div>
          </div>
        </article>
        
        <article class="card">
          <img src="https://picsum.photos/seed/jewelry/900/600" alt="Jewelry" />
          <div class="card-body">
            <h3 class="card-title">Jewelry</h3>
            <div class="card-meta">92 items</div>
          </div>
        </article>
        
        <article class="card">
          <img src="https://picsum.photos/seed/vintage/900/600" alt="Vintage Collections" />
          <div class="card-body">
            <h3 class="card-title">Vintage Collections</h3>
            <div class="card-meta">58 items</div>
          </div>
        </article>
        
        <article class="card">
          <img src="https://picsum.photos/seed/contemporary/900/600" alt="Contemporary Art" />
          <div class="card-body">
            <h3 class="card-title">Contemporary Art</h3>
            <div class="card-meta">112 items</div>
          </div>
        </article>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
