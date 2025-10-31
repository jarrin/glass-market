<?php 
session_start(); 

// Database connection to fetch page content
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$page_content = [];

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch all content for the about-us page
    $stmt = $pdo->prepare("
        SELECT 
            ps.section_key,
            pc.content_value
        FROM pages p
        JOIN page_sections ps ON p.id = ps.page_id
        LEFT JOIN page_content pc ON ps.id = pc.section_id
        WHERE p.slug = 'about-us'
        ORDER BY ps.display_order
    ");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convert to associative array for easy access
    foreach ($results as $row) {
        $page_content[$row['section_key']] = $row['content_value'] ?? '';
    }
    
} catch (PDOException $e) {
    error_log("Error fetching page content: " . $e->getMessage());
    // Use default values if database fails
    $page_content = [
        'hero_title' => 'About Glass Market',
        'hero_subtitle' => 'Connecting the global glass recycling industry',
        'mission_title' => 'Our Mission',
        'mission_text' => 'Learn more about our mission.',
        'vision_title' => 'Our Vision',
        'vision_text' => 'Our vision for the future.',
        'values_title' => 'Our Values',
        'values_text' => 'Our core values.',
        'team_title' => 'Our Team',
        'team_text' => 'Meet our team.'
    ];
}

require_once __DIR__ . '/../../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($page_content['hero_title'] ?? 'About Glass Market'); ?></title>
  <link rel="stylesheet" href="../css/app.css">
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>
<?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>

  <!-- Hero -->
  <section class="hero hero-muted text-center">
    <div class="container">
      <h1 class="page-title"><?php echo htmlspecialchars($page_content['hero_title'] ?? 'About Glass Market'); ?></h1>
      <p class="page-subtitle">
        <?php echo nl2br(htmlspecialchars($page_content['hero_subtitle'] ?? 'Connecting the global glass recycling industry')); ?>
      </p>
    </div>
  </section>

  <!-- Stats - These can be made dynamic later if needed -->
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

  <!-- Mission -->
  <section class="mission text-center">
    <div class="container">
      <h2><?php echo htmlspecialchars($page_content['mission_title'] ?? 'Our Mission'); ?></h2>
      <p>
        <?php echo nl2br(htmlspecialchars($page_content['mission_text'] ?? 'Our mission statement.')); ?>
      </p>
    </div>
  </section>

  <!-- Vision -->
  <section class="story text-center">
    <div class="container">
      <h2><?php echo htmlspecialchars($page_content['vision_title'] ?? 'Our Vision'); ?></h2>
      <p>
        <?php echo nl2br(htmlspecialchars($page_content['vision_text'] ?? 'Our vision statement.')); ?>
      </p>
    </div>
  </section>

 <!-- Values -->
<section class="values">
  <div class="container text-center">
    <h2><?php echo htmlspecialchars($page_content['values_title'] ?? 'Our Values'); ?></h2>
    <p style="max-width: 800px; margin: 0 auto 40px; color: #6b6460; line-height: 1.8;">
      <?php echo nl2br(htmlspecialchars($page_content['values_text'] ?? 'Our core values.')); ?>
    </p>
  </div>
</section>

  <!-- Team -->
  <section class="mission text-center">
    <div class="container">
      <h2><?php echo htmlspecialchars($page_content['team_title'] ?? 'Our Team'); ?></h2>
      <p>
        <?php echo nl2br(htmlspecialchars($page_content['team_text'] ?? 'Our team information.')); ?>
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
