<?php 
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db_connect.php';

// Fetch Privacy page content from database
$page_slug = 'privacy';

try {
    // Get page
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ? AND is_active = 1");
    $stmt->execute([$page_slug]);
    $page = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$page) {
        die('Page not found. Please run the seed script first.');
    }
    
    // Get all sections with content
    $stmt = $pdo->prepare("
        SELECT ps.section_key, ps.section_type, ps.section_group, pc.content_value
        FROM page_sections ps
        LEFT JOIN page_content pc ON ps.id = pc.section_id
        WHERE ps.page_id = ?
        ORDER BY ps.display_order
    ");
    $stmt->execute([$page['id']]);
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organize sections by key for easy access
    $content = [];
    foreach ($sections as $section) {
        $content[$section['section_key']] = $section['content_value'] ?? '';
    }
    
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page['title']); ?> - Glass Market</title>
    <link rel="stylesheet" href="<?php echo PUBLIC_URL; ?>/css/app.css">
    <style>
        .policy-page {
            padding: 60px 0 100px;
            background: #f8f9fa;
        }
        
        .policy-hero {
            background: linear-gradient(135deg, #2f6df5 0%, #1e4db8 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 60px;
        }
        
        .policy-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .policy-hero p {
            font-size: 18px;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .policy-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 32px;
        }
        
        .policy-card {
            background: white;
            border-radius: 16px;
            padding: 48px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        
        .policy-card h2 {
            font-size: 28px;
            margin-top: 40px;
            margin-bottom: 20px;
            color: #1d1d1f;
        }
        
        .policy-card h3 {
            font-size: 22px;
            margin-top: 32px;
            margin-bottom: 16px;
            color: #1d1d1f;
        }
        
        .policy-card p {
            color: #6e6e73;
            line-height: 1.8;
            margin-bottom: 16px;
        }
        
        .policy-card ul {
            color: #6e6e73;
            line-height: 1.8;
            margin-bottom: 24px;
            padding-left: 24px;
        }
        
        .policy-card li {
            margin-bottom: 12px;
        }
        
        .policy-card strong {
            color: #1d1d1f;
        }
        
        .last-updated {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 8px;
            font-size: 14px;
            color: #6e6e73;
            margin-bottom: 32px;
        }
        
        .info-box {
            background: #f0f4ff;
            border-left: 4px solid #2f6df5;
            padding: 20px 24px;
            margin: 24px 0;
            border-radius: 8px;
        }
        
        .info-box p {
            margin: 0;
            color: #1d1d1f;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    
    <div class="policy-page">
        <div class="policy-hero">
            <h1><?php echo htmlspecialchars($content['hero_title'] ?? 'Privacy Policy'); ?></h1>
            <p><?php echo htmlspecialchars($content['hero_subtitle'] ?? ''); ?></p>
        </div>
        
        <div class="policy-container">
            <div class="policy-card">
                <div class="last-updated">
                    <strong>Last Updated:</strong> <?php echo htmlspecialchars($content['last_updated'] ?? ''); ?>
                </div>
                
                <p><?php echo nl2br(htmlspecialchars($content['intro_text'] ?? '')); ?></p>
                
                <?php if (!empty($content['info_box_text'])): ?>
                <div class="info-box">
                    <p><strong><?php echo nl2br(htmlspecialchars($content['info_box_text'])); ?></strong></p>
                </div>
                <?php endif; ?>
                
                <!-- Section 1: Information We Collect -->
                <h2><?php echo htmlspecialchars($content['section_1_title'] ?? ''); ?></h2>
                
                <h3><?php echo htmlspecialchars($content['section_1_subtitle_1'] ?? ''); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($content['section_1_intro_1'] ?? '')); ?></p>
                <ul>
                    <li><strong><?php echo htmlspecialchars($content['section_1_info_1'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_1_info_2'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_1_info_3'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_1_info_4'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_1_info_5'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_1_info_6'] ?? ''); ?></strong></li>
                </ul>
                
                <h3><?php echo htmlspecialchars($content['section_1_subtitle_2'] ?? ''); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($content['section_1_intro_2'] ?? '')); ?></p>
                <ul>
                    <li><strong><?php echo htmlspecialchars($content['section_1_auto_1'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_1_auto_2'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_1_auto_3'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_1_auto_4'] ?? ''); ?></strong></li>
                </ul>
                
                <!-- Section 2: How We Use Your Information -->
                <h2><?php echo htmlspecialchars($content['section_2_title'] ?? ''); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($content['section_2_intro'] ?? '')); ?></p>
                <ul>
                    <li><strong><?php echo htmlspecialchars($content['section_2_use_1'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_2_use_2'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_2_use_3'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_2_use_4'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_2_use_5'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_2_use_6'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_2_use_7'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_2_use_8'] ?? ''); ?></strong></li>
                </ul>
                
                <!-- Section 3: Information Sharing -->
                <h2><?php echo htmlspecialchars($content['section_3_title'] ?? ''); ?></h2>
                
                <h3><?php echo htmlspecialchars($content['section_3_subtitle_1'] ?? ''); ?></h3>
                <ul>
                    <li><strong><?php echo htmlspecialchars($content['section_3_share_1'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_3_share_2'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_3_share_3'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_3_share_4'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_3_share_5'] ?? ''); ?></strong></li>
                </ul>
                
                <h3><?php echo htmlspecialchars($content['section_3_subtitle_2'] ?? ''); ?></h3>
                <ul>
                    <li><?php echo htmlspecialchars($content['section_3_not_1'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_3_not_2'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_3_not_3'] ?? ''); ?></li>
                </ul>
                
                <!-- Section 4: Data Security -->
                <h2><?php echo htmlspecialchars($content['section_4_title'] ?? ''); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($content['section_4_intro'] ?? '')); ?></p>
                <ul>
                    <li><strong><?php echo htmlspecialchars($content['section_4_security_1'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_4_security_2'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_4_security_3'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_4_security_4'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_4_security_5'] ?? ''); ?></strong></li>
                </ul>
                
                <p><?php echo nl2br(htmlspecialchars($content['section_4_disclaimer'] ?? '')); ?></p>
                
                <!-- Section 5: Your Privacy Rights -->
                <h2><?php echo htmlspecialchars($content['section_5_title'] ?? ''); ?></h2>
                
                <h3><?php echo htmlspecialchars($content['section_5_subtitle'] ?? ''); ?></h3>
                <ul>
                    <li><strong><?php echo htmlspecialchars($content['section_5_right_1'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_5_right_2'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_5_right_3'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_5_right_4'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_5_right_5'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_5_right_6'] ?? ''); ?></strong></li>
                    <li><strong><?php echo htmlspecialchars($content['section_5_right_7'] ?? ''); ?></strong></li>
                </ul>
                
                <p><?php echo nl2br(htmlspecialchars($content['section_5_contact'] ?? '')); ?></p>
                
                <!-- Contact Information -->
                <h2><?php echo htmlspecialchars($content['contact_title'] ?? ''); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($content['contact_intro'] ?? '')); ?></p>
                
                <ul style="list-style: none; padding: 0;">
                    <li><strong>Email:</strong> <?php echo htmlspecialchars($content['contact_email'] ?? ''); ?></li>
                    <li><strong>Phone:</strong> <?php echo htmlspecialchars($content['contact_phone'] ?? ''); ?></li>
                    <li><strong>Mail:</strong> <?php echo htmlspecialchars($content['contact_address'] ?? ''); ?></li>
                    <li><strong>Data Protection Officer:</strong> <?php echo htmlspecialchars($content['contact_dpo'] ?? ''); ?></li>
                </ul>
                
                <div class="info-box" style="margin-top: 40px;">
                    <p><strong>Quick Links:</strong> 
                        <a href="<?php echo PUBLIC_URL; ?>/terms.php" style="color: #2f6df5;">Terms of Service</a> | 
                        <a href="<?php echo PUBLIC_URL; ?>/help.php" style="color: #2f6df5;">Help Center</a> | 
                        <a href="<?php echo PUBLIC_URL; ?>/contact.php" style="color: #2f6df5;">Contact Us</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
