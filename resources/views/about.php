<?php require_once __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Glass Market</title>
  <link rel="stylesheet" href="../css/app.css">
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>
<?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>

  <!-- Hero -->
  <section class="hero hero-muted text-center">
    <div class="container">
      <h1 class="page-title">About Glass Market</h1>
      <p class="page-subtitle">
        The world's premier marketplace for glass art, crystals, and handcrafted glassware. 
        Connecting artisans with collectors since 2020.
      </p>
    </div>
  </section>

  <!-- Stats -->
  <section class="stats">
    <div class="container">
      <div class="stats-row">
        <div class="stat">
          <div class="num">10K+</div>
          <div class="label">Active Sellers</div>
        </div>
        <div class="stat">
          <div class="num">50K+</div>
          <div class="label">Products Listed</div>
        </div>
        <div class="stat">
          <div class="num">100+</div>
          <div class="label">Countries</div>
        </div>
        <div class="stat">
          <div class="num">4.8</div>
          <div class="label">Average Rating</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Story -->
  <section class="story text-center">
    <div class="container">
      <h2>Our Story</h2>
      <p>
        Glass Market was born from a simple idea: to create a dedicated space where the beauty and artistry of
        glass could be celebrated and shared. We recognized that glass art, from delicate crystal pieces to bold
        contemporary sculptures, deserved a marketplace that understood its unique value.
      </p>
      <p>
        What started as a small platform has grown into a thriving global community of artisans, collectors, and
        enthusiasts. Today, we connect thousands of sellers with buyers who appreciate the craftsmanship,
        history, and beauty of glass art.
      </p>
      <p>
        Every piece on Glass Market tells a story—whether it's a traditional Venetian vase crafted using
        centuries-old techniques or a modern art glass sculpture pushing creative boundaries.
      </p>
    </div>
  </section>

 <!-- Values -->
<section class="values">
  <div class="container text-center">
    <h2>Our Values</h2>
    <div class="values-grid">
      <div class="card value">
        <div class="card-content">
          <div class="value-icon">🛡️</div>
          <h4>Trust & Safety</h4>
          <p>We prioritize secure transactions and buyer protection, ensuring every purchase is safe and reliable.</p>
        </div>
      </div>

      <div class="card value">
        <div class="card-content">
          <div class="value-icon">👥</div>
          <h4>Community First</h4>
          <p>Supporting artisans and collectors worldwide, building connections through shared passion for glass art.</p>
        </div>
      </div>

      <div class="card value">
        <div class="card-content">
          <div class="value-icon">🌍</div>
          <h4>Global Marketplace</h4>
          <p>Connecting buyers and sellers across continents, making unique glass pieces accessible to everyone.</p>
        </div>
      </div>

      <div class="card value">
        <div class="card-content">
          <div class="value-icon">❤️</div>
          <h4>Quality Craftsmanship</h4>
          <p>Celebrating the artistry and skill of glassmakers, from traditional techniques to contemporary designs.</p>
        </div>
      </div>
    </div>
  </div>
</section>


  <!-- Mission -->
  <section class="mission text-center">
    <div class="container">
      <h2>Our Mission</h2>
      <p>
        To preserve and promote the art of glassmaking by providing a trusted platform where artisans can
        showcase their work and collectors can discover unique pieces from around the world.
      </p>
      <p>
        We believe in fair practices, transparent transactions, and fostering meaningful connections between
        makers and buyers. Every transaction on Glass Market supports independent artisans and helps keep
        traditional crafts alive for future generations.
      </p>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta">
    <div class="container">
      <h2>Join Our Community</h2>
      <p class="muted">
        Whether you're an artisan looking to share your craft or a collector seeking unique pieces, Glass Market
        is your home.
      </p>
      <div class="cta-buttons">
        <a href="/browse" class="btn btn-primary">Start Shopping</a>
        <a href="/sell" class="btn btn-secondary">Become a Seller</a>
      </div>
    </div>
  </section>


  <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
