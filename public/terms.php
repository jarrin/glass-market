<?php 
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db_connect.php';

// Fetch Terms page content from database
$page_slug = 'terms';

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
        .terms-page {
            padding: 60px 0 100px;
            background: #f8f9fa;
        }
        
        .terms-hero {
            background: linear-gradient(135deg, #2f6df5 0%, #1e4db8 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 60px;
        }
        
        .terms-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .terms-hero p {
            font-size: 18px;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .terms-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 32px;
        }
        
        .terms-card {
            background: white;
            border-radius: 16px;
            padding: 48px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        
        .terms-card h2 {
            font-size: 28px;
            margin-top: 40px;
            margin-bottom: 20px;
            color: #1d1d1f;
        }
        
        .terms-card h3 {
            font-size: 22px;
            margin-top: 32px;
            margin-bottom: 16px;
            color: #1d1d1f;
        }
        
        .terms-card p {
            color: #6e6e73;
            line-height: 1.8;
            margin-bottom: 16px;
        }
        
        .terms-card ul {
            color: #6e6e73;
            line-height: 1.8;
            margin-bottom: 24px;
            padding-left: 24px;
        }
        
        .terms-card li {
            margin-bottom: 12px;
        }
        
        .terms-card strong {
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
        
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffa500;
            padding: 20px 24px;
            margin: 24px 0;
            border-radius: 8px;
        }
        
        .warning-box p {
            margin: 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    
    <div class="terms-page">
        <div class="terms-hero">
            <h1><?php echo htmlspecialchars($content['hero_title'] ?? 'Terms of Service'); ?></h1>
            <p><?php echo htmlspecialchars($content['hero_subtitle'] ?? ''); ?></p>
        </div>
        
        <div class="terms-container">
            <div class="terms-card">
                <div class="last-updated">
                    <strong>Last Updated:</strong> <?php echo htmlspecialchars($content['last_updated'] ?? ''); ?>
                </div>
                
                <p><?php echo nl2br(htmlspecialchars($content['intro_text'] ?? '')); ?></p>
                
                <?php if (!empty($content['warning_text'])): ?>
                <div class="warning-box">
                    <p><strong><?php echo nl2br(htmlspecialchars($content['warning_text'])); ?></strong></p>
                </div>
                <?php endif; ?>
                
                <!-- Section 1: Acceptance of Terms -->
                <h2><?php echo htmlspecialchars($content['section_1_title'] ?? ''); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($content['section_1_intro'] ?? '')); ?></p>
                <ul>
                    <li><?php echo htmlspecialchars($content['section_1_point_1'] ?? ''); ?></li>
                    <li>Our <a href="<?php echo PUBLIC_URL; ?>/privacy.php" style="color: #2f6df5;"><?php echo htmlspecialchars($content['section_1_point_2'] ?? 'Privacy Policy'); ?></a></li>
                    <li>Our <a href="<?php echo PUBLIC_URL; ?>/seller-guidelines.php" style="color: #2f6df5;"><?php echo htmlspecialchars($content['section_1_point_3'] ?? 'Seller Guidelines (if selling)'); ?></a></li>
                    <li><?php echo htmlspecialchars($content['section_1_point_4'] ?? ''); ?></li>
                </ul>
                <p><?php echo nl2br(htmlspecialchars($content['section_1_closing'] ?? '')); ?></p>
                
                <!-- Section 2: Eligibility -->
                <h2><?php echo htmlspecialchars($content['section_2_title'] ?? ''); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($content['section_2_intro'] ?? '')); ?></p>
                <ul>
                    <li><?php echo htmlspecialchars($content['section_2_point_1'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_2_point_2'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_2_point_3'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_2_point_4'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_2_point_5'] ?? ''); ?></li>
                </ul>
                
                <!-- Section 3: Account Responsibilities -->
                <h2><?php echo htmlspecialchars($content['section_3_title'] ?? ''); ?></h2>
                <h3><?php echo htmlspecialchars($content['section_3_subtitle_1'] ?? ''); ?></h3>
                <ul>
                    <li><?php echo htmlspecialchars($content['section_3_resp_1'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_3_resp_2'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_3_resp_3'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_3_resp_4'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_3_resp_5'] ?? ''); ?></li>
                </ul>
                <h3><?php echo htmlspecialchars($content['section_3_subtitle_2'] ?? ''); ?></h3>
                <ul>
                    <li><?php echo htmlspecialchars($content['section_3_prohibit_1'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_3_prohibit_2'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_3_prohibit_3'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_3_prohibit_4'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_3_prohibit_5'] ?? ''); ?></li>
                </ul>
                
                <!-- Section 4: Platform Usage -->
                <h2><?php echo htmlspecialchars($content['section_4_title'] ?? ''); ?></h2>
                <h3><?php echo htmlspecialchars($content['section_4_subtitle_1'] ?? ''); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($content['section_4_intro_1'] ?? '')); ?></p>
                <ul>
                    <li><?php echo htmlspecialchars($content['section_4_permitted_1'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_4_permitted_2'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_4_permitted_3'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_4_permitted_4'] ?? ''); ?></li>
                </ul>
                <h3><?php echo htmlspecialchars($content['section_4_subtitle_2'] ?? ''); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($content['section_4_intro_2'] ?? '')); ?></p>
                <ul>
                    <li><?php echo htmlspecialchars($content['section_4_prohibited_1'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_4_prohibited_2'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_4_prohibited_3'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_4_prohibited_4'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_4_prohibited_5'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_4_prohibited_6'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_4_prohibited_7'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_4_prohibited_8'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_4_prohibited_9'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_4_prohibited_10'] ?? ''); ?></li>
                    <li><?php echo htmlspecialchars($content['section_4_prohibited_11'] ?? ''); ?></li>
                </ul>
                
                <!-- Contact Information -->
                <h2><?php echo htmlspecialchars($content['contact_title'] ?? ''); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($content['contact_intro'] ?? '')); ?></p>
                
                <ul style="list-style: none; padding: 0; margin-top: 20px;">
                    <li><strong>Email:</strong> <?php echo htmlspecialchars($content['contact_email'] ?? ''); ?></li>
                    <li><strong>Phone:</strong> <?php echo htmlspecialchars($content['contact_phone'] ?? ''); ?></li>
                    <li><strong>Mail:</strong> <?php echo nl2br(htmlspecialchars($content['contact_address'] ?? '')); ?></li>
                </ul>
                
                <?php if (!empty($content['closing_message'])): ?>
                <div style="background: #f0f4ff; border-left: 4px solid #2f6df5; padding: 20px 24px; margin: 32px 0; border-radius: 8px;">
                    <p style="margin: 0; color: #1d1d1f;"><strong><?php echo nl2br(htmlspecialchars($content['closing_message'])); ?></strong></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
