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
  <style>
    :root {
      --about-bg: #f5f5f7;
      --about-text: #1d1d1f;
      --about-muted: #6e6e73;
      --about-accent: #2f6df5;
      --about-card-bg: rgba(255, 255, 255, 0.9);
      --about-border: rgba(15, 23, 42, 0.08);
    }

    body.about-body {
      background: var(--about-bg);
      color: var(--about-text);
      font-family: "SF Pro Display", "SF Pro Text", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .about-page {
      padding: 0 0 120px;
    }

    .about-shell {
      max-width: 1120px;
      margin: 0 auto;
      padding: 0 32px;
    }

    .about-hero {
      padding: 96px 0 72px;
      text-align: center;
    }

    .about-hero-kicker {
      display: inline-block;
      font-size: 14px;
      font-weight: 600;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      color: var(--about-muted);
      margin-bottom: 20px;
    }

    .about-hero-title {
      font-size: clamp(38px, 7vw, 64px);
      font-weight: 700;
      line-height: 1.1;
      margin-bottom: 24px;
      color: var(--about-text);
    }

    .about-hero-subtitle {
      font-size: 20px;
      color: var(--about-muted);
      line-height: 1.6;
      max-width: 760px;
      margin: 0 auto 24px;
    }

    .about-hero-description {
      font-size: 17px;
      color: var(--about-text);
      line-height: 1.7;
      max-width: 680px;
      margin: 0 auto 40px;
    }

    .about-hero-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      justify-content: center;
    }

    .about-btn-primary,
    .about-btn-secondary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 14px 28px;
      border-radius: 999px;
      font-size: 15px;
      font-weight: 600;
      text-decoration: none;
      transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
    }

    .about-btn-primary {
      background: var(--about-text);
      color: white;
      box-shadow: 0 18px 40px -20px rgba(0, 0, 0, 0.45);
    }

    .about-btn-primary:hover {
      transform: translateY(-2px);
      background: #111013;
      box-shadow: 0 24px 50px -20px rgba(0, 0, 0, 0.5);
    }

    .about-btn-secondary {
      background: rgba(255, 255, 255, 0.6);
      color: var(--about-text);
      border: 1px solid rgba(15, 23, 42, 0.08);
    }

    .about-btn-secondary:hover {
      transform: translateY(-2px);
      background: white;
      box-shadow: 0 18px 40px -24px rgba(15, 23, 42, 0.3);
    }

    .about-stats {
      padding: 20px 0 80px;
    }

    .about-stats-grid {
      display: grid;
      gap: 20px;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .about-stat-card {
      background: var(--about-card-bg);
      border-radius: 24px;
      padding: 32px;
      border: 1px solid var(--about-border);
      box-shadow: 0 24px 50px -40px rgba(15, 23, 42, 0.35);
      backdrop-filter: blur(12px);
    }

    .about-stat-value {
      font-size: 32px;
      font-weight: 700;
      margin-bottom: 8px;
      color: var(--about-text);
    }

    .about-stat-label {
      font-size: 15px;
      color: var(--about-muted);
      letter-spacing: 0.03em;
      text-transform: uppercase;
    }

    .about-pill-section {
      margin-bottom: 100px;
    }

    .about-pill-section-header {
      display: flex;
      flex-direction: column;
      gap: 16px;
      margin-bottom: 40px;
      text-align: left;
    }

    .about-pill-title {
      font-size: 36px;
      font-weight: 700;
      color: var(--about-text);
    }

    .about-pill-text {
      font-size: 18px;
      color: var(--about-muted);
      line-height: 1.8;
      max-width: 720px;
    }

    .about-block-grid {
      display: grid;
      gap: 28px;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    }

    .about-value-card {
      background: var(--about-card-bg);
      border-radius: 28px;
      padding: 32px;
      border: 1px solid var(--about-border);
      position: relative;
      overflow: hidden;
    }

    .about-value-card::before {
      content: '';
      position: absolute;
      inset: -40% 50% 60% -40%;
      background: radial-gradient(circle at top right, rgba(47, 109, 245, 0.18), transparent 60%);
      opacity: 0.8;
    }

    .about-value-title {
      font-size: 22px;
      font-weight: 600;
      color: var(--about-text);
      margin-bottom: 12px;
      position: relative;
      z-index: 1;
    }

    .about-value-text {
      font-size: 16px;
      color: var(--about-muted);
      line-height: 1.7;
      position: relative;
      z-index: 1;
    }

    .about-duo {
      display: grid;
      gap: 28px;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      margin-bottom: 96px;
    }

    .about-duo-card {
      padding: 36px;
      background: var(--about-card-bg);
      border: 1px solid var(--about-border);
      border-radius: 32px;
      box-shadow: 0 30px 60px -45px rgba(15, 23, 42, 0.45);
    }

    .about-duo-title {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 16px;
    }

    .about-duo-text {
      font-size: 17px;
      color: var(--about-muted);
      line-height: 1.8;
    }

    .about-team {
      padding: 90px 0;
    }

    .about-team-card {
      background: linear-gradient(135deg, rgba(255,255,255,0.92) 0%, rgba(244, 246, 255, 0.95) 100%);
      border-radius: 36px;
      padding: 48px;
      border: 1px solid rgba(47, 109, 245, 0.08);
      box-shadow: 0 40px 80px -60px rgba(47, 109, 245, 0.55);
    }

    .about-team-title {
      font-size: 32px;
      font-weight: 700;
      margin-bottom: 18px;
      color: var(--about-text);
    }

    .about-team-text {
      font-size: 18px;
      color: var(--about-muted);
      line-height: 1.8;
      max-width: 820px;
    }

    .about-cta {
      margin: 120px 0 0;
      border-radius: 40px;
      padding: 72px 48px;
      background: radial-gradient(circle at top right, rgba(47, 109, 245, 0.22), rgba(17, 24, 39, 0.85));
      color: white;
      text-align: center;
      box-shadow: 0 40px 80px -60px rgba(17, 24, 39, 0.65);
    }

    .about-cta-title {
      font-size: clamp(32px, 5vw, 48px);
      font-weight: 700;
      margin-bottom: 20px;
    }

    .about-cta-text {
      font-size: 18px;
      opacity: 0.85;
      line-height: 1.8;
      max-width: 680px;
      margin: 0 auto 32px;
    }

    .about-cta-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      justify-content: center;
    }

    .about-cta-primary,
    .about-cta-secondary {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 28px;
      border-radius: 999px;
      font-size: 15px;
      font-weight: 600;
      text-decoration: none;
      transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
    }

    .about-cta-primary {
      background: white;
      color: #0f172a;
      box-shadow: 0 24px 50px -22px rgba(255, 255, 255, 0.55);
    }

    .about-cta-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 32px 60px -24px rgba(255, 255, 255, 0.7);
    }

    .about-cta-secondary {
      color: white;
      border: 1px solid rgba(255, 255, 255, 0.45);
      opacity: 0.85;
    }

    .about-cta-secondary:hover {
      opacity: 1;
      transform: translateY(-2px);
      background: rgba(255, 255, 255, 0.12);
    }

    @media (max-width: 768px) {
      .about-shell {
        padding: 0 20px;
      }

      .about-hero {
        padding: 72px 0 56px;
      }

      .about-cta {
        padding: 56px 32px;
        border-radius: 30px;
      }
    }
  </style>
</head>
<body class="about-body">

<?php include __DIR__ . '/../../includes/navbar.php'; ?>
<?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>
<main class="about-page">
  <div class="about-shell">
    <section class="about-hero">
      <?php if (!empty($page_content['hero_kicker'])): ?>
        <span class="about-hero-kicker"><?php echo htmlspecialchars($page_content['hero_kicker']); ?></span>
      <?php endif; ?>
      <h1 class="about-hero-title"><?php echo htmlspecialchars($page_content['hero_title'] ?? 'About Glass Market'); ?></h1>
      <?php if (!empty($page_content['hero_subtitle'])): ?>
        <p class="about-hero-subtitle"><?php echo nl2br(htmlspecialchars($page_content['hero_subtitle'])); ?></p>
      <?php endif; ?>
      <?php if (!empty($page_content['hero_description'])): ?>
        <p class="about-hero-description"><?php echo nl2br(htmlspecialchars($page_content['hero_description'])); ?></p>
      <?php endif; ?>

      <?php
        // Default button links if not set in database
        $primary_label = trim($page_content['hero_primary_label'] ?? 'Explore Collection');
        $primary_url = trim($page_content['hero_primary_url'] ?? '/glass-market/resources/views/browse.php');
        $secondary_label = trim($page_content['hero_secondary_label'] ?? 'Contact Us');
        $secondary_url = trim($page_content['hero_secondary_url'] ?? '/glass-market/resources/views/contact.php');
      ?>
      <?php if ($primary_label || $secondary_label): ?>
        <div class="about-hero-actions">
          <?php if ($primary_label && $primary_url): ?>
            <a href="<?php echo htmlspecialchars($primary_url); ?>" class="about-btn-primary"><?php echo htmlspecialchars($primary_label); ?></a>
          <?php endif; ?>
          <?php if ($secondary_label && $secondary_url): ?>
            <a href="<?php echo htmlspecialchars($secondary_url); ?>" class="about-btn-secondary"><?php echo htmlspecialchars($secondary_label); ?></a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </section>

    <section class="about-stats">
      <div class="about-stats-grid">
        <?php for ($i = 1; $i <= 4; $i++):
          $value = trim($page_content["stats_{$i}_value"] ?? '');
          $label = trim($page_content["stats_{$i}_label"] ?? '');
          if (!$value && !$label) { continue; }
        ?>
          <div class="about-stat-card">
            <?php if ($value): ?><div class="about-stat-value"><?php echo htmlspecialchars($value); ?></div><?php endif; ?>
            <?php if ($label): ?><div class="about-stat-label"><?php echo htmlspecialchars($label); ?></div><?php endif; ?>
          </div>
        <?php endfor; ?>
      </div>
    </section>

    <section class="about-duo">
      <div class="about-duo-card">
        <h2 class="about-duo-title"><?php echo htmlspecialchars($page_content['mission_title'] ?? 'Our Mission'); ?></h2>
        <p class="about-duo-text"><?php echo nl2br(htmlspecialchars($page_content['mission_text'] ?? 'Our mission statement.')); ?></p>
      </div>
      <div class="about-duo-card">
        <h2 class="about-duo-title"><?php echo htmlspecialchars($page_content['vision_title'] ?? 'Our Vision'); ?></h2>
        <p class="about-duo-text"><?php echo nl2br(htmlspecialchars($page_content['vision_text'] ?? 'Our vision statement.')); ?></p>
      </div>
    </section>

    <section class="about-pill-section">
      <div class="about-pill-section-header">
        <h2 class="about-pill-title"><?php echo htmlspecialchars($page_content['values_title'] ?? 'Our Values'); ?></h2>
        <?php if (!empty($page_content['values_intro'])): ?>
          <p class="about-pill-text"><?php echo nl2br(htmlspecialchars($page_content['values_intro'])); ?></p>
        <?php endif; ?>
      </div>

      <div class="about-block-grid">
        <?php for ($i = 1; $i <= 3; $i++):
          $title = trim($page_content["values_item_{$i}_title"] ?? '');
          $text = trim($page_content["values_item_{$i}_text"] ?? '');
          if (!$title && !$text) { continue; }
        ?>
          <div class="about-value-card">
            <?php if ($title): ?><h3 class="about-value-title"><?php echo htmlspecialchars($title); ?></h3><?php endif; ?>
            <?php if ($text): ?><p class="about-value-text"><?php echo nl2br(htmlspecialchars($text)); ?></p><?php endif; ?>
          </div>
        <?php endfor; ?>
      </div>
    </section>

    <section class="about-team">
      <div class="about-team-card">
        <h2 class="about-team-title"><?php echo htmlspecialchars($page_content['team_title'] ?? 'Our Team'); ?></h2>
        <p class="about-team-text"><?php echo nl2br(htmlspecialchars($page_content['team_text'] ?? 'Meet the people behind Glass Market.')); ?></p>
      </div>
    </section>

    <section class="about-cta">
      <h2 class="about-cta-title"><?php echo htmlspecialchars($page_content['cta_title'] ?? 'Join the Circular Glass Movement'); ?></h2>
      <p class="about-cta-text"><?php echo nl2br(htmlspecialchars($page_content['cta_text'] ?? 'Whether you sell, ship, or source cullet, Glass Market is your always-on operations partner. Start trading today and be part of the sustainable glass revolution.')); ?></p>
      <?php
        // Check if user is logged in
        $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
        
        // Default CTA button links
        $cta_primary_label = trim($page_content['cta_primary_label'] ?? '');
        $cta_primary_url = trim($page_content['cta_primary_url'] ?? '');
        $cta_secondary_label = trim($page_content['cta_secondary_label'] ?? 'Browse Listings');
        $cta_secondary_url = trim($page_content['cta_secondary_url'] ?? '/glass-market/resources/views/browse.php');
        
        // If not logged in, show "Start Selling" button, otherwise don't show it
        if (!$isLoggedIn && empty($cta_primary_label)) {
          $cta_primary_label = 'Start Selling';
          $cta_primary_url = '/glass-market/resources/views/register.php';
        }
      ?>
      <div class="about-cta-actions">
        <?php if (!$isLoggedIn && $cta_primary_label && $cta_primary_url): ?>
          <a class="about-cta-primary" href="<?php echo htmlspecialchars($cta_primary_url); ?>"><?php echo htmlspecialchars($cta_primary_label); ?></a>
        <?php endif; ?>
        <?php if ($cta_secondary_label && $cta_secondary_url): ?>
          <a class="about-cta-secondary" href="<?php echo htmlspecialchars($cta_secondary_url); ?>"><?php echo htmlspecialchars($cta_secondary_label); ?></a>
        <?php endif; ?>
      </div>
    </section>
  </div>
</main>

  <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
