<?php
/**
 * Create CMS Tables and Seed Terms Page Content
 * Run this file once to set up the page management system
 */

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Creating CMS tables...\n";
    
    // Create pages table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS pages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(100) NOT NULL UNIQUE,
            title VARCHAR(255) NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_slug (slug),
            INDEX idx_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Created 'pages' table\n";
    
    // Create page_sections table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS page_sections (
            id INT AUTO_INCREMENT PRIMARY KEY,
            page_id INT NOT NULL,
            section_key VARCHAR(100) NOT NULL,
            section_label VARCHAR(255) NOT NULL,
            section_type VARCHAR(50) DEFAULT 'text',
            section_group VARCHAR(100),
            display_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
            UNIQUE KEY unique_section (page_id, section_key),
            INDEX idx_page_order (page_id, display_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Created 'page_sections' table\n";
    
    // Create page_content table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS page_content (
            id INT AUTO_INCREMENT PRIMARY KEY,
            section_id INT NOT NULL,
            content_value TEXT NOT NULL,
            updated_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (section_id) REFERENCES page_sections(id) ON DELETE CASCADE,
            UNIQUE KEY unique_content (section_id),
            INDEX idx_section (section_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Created 'page_content' table\n";
    
    echo "\nSeeding Terms page...\n";
    
    // Insert or update Terms page
    $stmt = $pdo->prepare("INSERT INTO pages (slug, title) VALUES (?, ?) 
                           ON DUPLICATE KEY UPDATE title = VALUES(title)");
    $stmt->execute(['terms', 'Terms of Service']);
    
    $pageId = $pdo->lastInsertId();
    if (!$pageId) {
        $stmt = $pdo->prepare("SELECT id FROM pages WHERE slug = ?");
        $stmt->execute(['terms']);
        $pageId = $stmt->fetchColumn();
    }
    echo "✓ Created Terms page (ID: $pageId)\n";
    
    // Define all Terms page sections
    $sections = [
        // Hero Section
        ['hero_title', 'Hero Title', 'text', 'hero', 1, 'Terms of Service'],
        ['hero_subtitle', 'Hero Subtitle', 'text', 'hero', 2, 'Agreement for using Glass Market platform'],
        ['last_updated', 'Last Updated Date', 'text', 'hero', 3, 'November 3, 2025'],
        
        // Introduction
        ['intro_text', 'Introduction Text', 'textarea', 'introduction', 4, 'Welcome to Glass Market. By accessing or using our platform, you agree to be bound by these Terms of Service ("Terms"). Please read them carefully.'],
        ['warning_text', 'Warning Box Text', 'textarea', 'introduction', 5, 'Important: These Terms contain a mandatory arbitration provision and class action waiver. Please review Section 14 carefully.'],
        
        // Section 1: Acceptance of Terms
        ['section_1_title', 'Section 1 Title', 'text', 'section_1', 6, '1. Acceptance of Terms'],
        ['section_1_intro', 'Section 1 Introduction', 'textarea', 'section_1', 7, 'By creating an account, accessing, or using Glass Market, you agree to:'],
        ['section_1_point_1', 'Agreement Point 1', 'text', 'section_1', 8, 'Comply with these Terms and all applicable laws'],
        ['section_1_point_2', 'Agreement Point 2', 'text', 'section_1', 9, 'Our Privacy Policy'],
        ['section_1_point_3', 'Agreement Point 3', 'text', 'section_1', 10, 'Our Seller Guidelines (if selling)'],
        ['section_1_point_4', 'Agreement Point 4', 'text', 'section_1', 11, 'Any additional policies and guidelines posted on our platform'],
        ['section_1_closing', 'Section 1 Closing', 'textarea', 'section_1', 12, 'If you do not agree to these Terms, you may not use our services.'],
        
        // Section 2: Eligibility
        ['section_2_title', 'Section 2 Title', 'text', 'section_2', 13, '2. Eligibility'],
        ['section_2_intro', 'Section 2 Introduction', 'textarea', 'section_2', 14, 'To use Glass Market, you must:'],
        ['section_2_point_1', 'Eligibility Point 1', 'text', 'section_2', 15, 'Be at least 18 years old'],
        ['section_2_point_2', 'Eligibility Point 2', 'text', 'section_2', 16, 'Have the legal capacity to enter into binding contracts'],
        ['section_2_point_3', 'Eligibility Point 3', 'text', 'section_2', 17, 'Not be prohibited from using our services under applicable law'],
        ['section_2_point_4', 'Eligibility Point 4', 'text', 'section_2', 18, 'Provide accurate and complete registration information'],
        ['section_2_point_5', 'Eligibility Point 5', 'text', 'section_2', 19, 'Maintain the security of your account credentials'],
        
        // Section 3: Account Responsibilities
        ['section_3_title', 'Section 3 Title', 'text', 'section_3', 20, '3. Account Responsibilities'],
        ['section_3_subtitle_1', 'Responsibilities Subtitle', 'text', 'section_3', 21, 'You are responsible for:'],
        ['section_3_resp_1', 'Responsibility 1', 'text', 'section_3', 22, 'All activity that occurs under your account'],
        ['section_3_resp_2', 'Responsibility 2', 'text', 'section_3', 23, 'Maintaining the confidentiality of your password'],
        ['section_3_resp_3', 'Responsibility 3', 'text', 'section_3', 24, 'Notifying us immediately of any unauthorized use'],
        ['section_3_resp_4', 'Responsibility 4', 'text', 'section_3', 25, 'Providing truthful and accurate information'],
        ['section_3_resp_5', 'Responsibility 5', 'text', 'section_3', 26, 'Updating your information to keep it current'],
        ['section_3_subtitle_2', 'Prohibitions Subtitle', 'text', 'section_3', 27, 'You may NOT:'],
        ['section_3_prohibit_1', 'Prohibition 1', 'text', 'section_3', 28, 'Share your account with others'],
        ['section_3_prohibit_2', 'Prohibition 2', 'text', 'section_3', 29, 'Create multiple accounts to circumvent restrictions'],
        ['section_3_prohibit_3', 'Prohibition 3', 'text', 'section_3', 30, 'Impersonate another person or entity'],
        ['section_3_prohibit_4', 'Prohibition 4', 'text', 'section_3', 31, 'Use automated tools to access our platform (bots, scrapers)'],
        ['section_3_prohibit_5', 'Prohibition 5', 'text', 'section_3', 32, 'Sell, transfer, or rent your account to others'],
        
        // Section 4: Platform Usage
        ['section_4_title', 'Section 4 Title', 'text', 'section_4', 33, '4. Platform Usage'],
        ['section_4_subtitle_1', 'Permitted Use Subtitle', 'text', 'section_4', 34, 'Permitted Use'],
        ['section_4_intro_1', 'Permitted Use Introduction', 'textarea', 'section_4', 35, 'You may use Glass Market to:'],
        ['section_4_permitted_1', 'Permitted Activity 1', 'text', 'section_4', 36, 'Browse and purchase glass products'],
        ['section_4_permitted_2', 'Permitted Activity 2', 'text', 'section_4', 37, 'List and sell glass products (if approved as a seller)'],
        ['section_4_permitted_3', 'Permitted Activity 3', 'text', 'section_4', 38, 'Communicate with other users for legitimate transactions'],
        ['section_4_permitted_4', 'Permitted Activity 4', 'text', 'section_4', 39, 'Access features and tools provided by our platform'],
        ['section_4_subtitle_2', 'Prohibited Activities Subtitle', 'text', 'section_4', 40, 'Prohibited Activities'],
        ['section_4_intro_2', 'Prohibited Activities Introduction', 'textarea', 'section_4', 41, 'You may NOT:'],
        ['section_4_prohibited_1', 'Prohibited Activity 1', 'text', 'section_4', 42, 'Violate any laws or regulations'],
        ['section_4_prohibited_2', 'Prohibited Activity 2', 'text', 'section_4', 43, 'Infringe on intellectual property rights'],
        ['section_4_prohibited_3', 'Prohibited Activity 3', 'text', 'section_4', 44, 'Post false, misleading, or deceptive content'],
        ['section_4_prohibited_4', 'Prohibited Activity 4', 'text', 'section_4', 45, 'Engage in fraudulent transactions'],
        ['section_4_prohibited_5', 'Prohibited Activity 5', 'text', 'section_4', 46, 'Harass, threaten, or abuse other users'],
        ['section_4_prohibited_6', 'Prohibited Activity 6', 'text', 'section_4', 47, 'Spam or send unsolicited communications'],
        ['section_4_prohibited_7', 'Prohibited Activity 7', 'text', 'section_4', 48, 'Attempt to circumvent fees or payments'],
        ['section_4_prohibited_8', 'Prohibited Activity 8', 'text', 'section_4', 49, 'Interfere with platform operation or security'],
        ['section_4_prohibited_9', 'Prohibited Activity 9', 'text', 'section_4', 50, 'Transmit viruses or malicious code'],
        ['section_4_prohibited_10', 'Prohibited Activity 10', 'text', 'section_4', 51, 'Collect user data without permission'],
        ['section_4_prohibited_11', 'Prohibited Activity 11', 'text', 'section_4', 52, 'Complete transactions off-platform to avoid fees'],
        
        // Contact Information
        ['contact_title', 'Contact Section Title', 'text', 'contact', 100, '16. Contact Information'],
        ['contact_intro', 'Contact Introduction', 'textarea', 'contact', 101, 'Questions about these Terms? Contact us:'],
        ['contact_email', 'Contact Email', 'text', 'contact', 102, 'legal@glassmarket.com'],
        ['contact_phone', 'Contact Phone', 'text', 'contact', 103, '1-800-GLASS-123'],
        ['contact_address', 'Contact Address', 'textarea', 'contact', 104, "Glass Market Legal Department\n123 Glass Street\nNew York, NY 10001"],
        ['closing_message', 'Closing Message', 'textarea', 'contact', 105, "Thank you for using Glass Market! We're committed to providing a safe, transparent marketplace for glass buyers and sellers."],
    ];
    
    echo "Inserting sections and content...\n";
    $count = 0;
    
    foreach ($sections as $section) {
        [$key, $label, $type, $group, $order, $content] = $section;
        
        // Insert or update section
        $stmt = $pdo->prepare("
            INSERT INTO page_sections (page_id, section_key, section_label, section_type, section_group, display_order)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                section_label = VALUES(section_label),
                section_type = VALUES(section_type),
                section_group = VALUES(section_group),
                display_order = VALUES(display_order)
        ");
        $stmt->execute([$pageId, $key, $label, $type, $group, $order]);
        
        $sectionId = $pdo->lastInsertId();
        if (!$sectionId) {
            $stmt = $pdo->prepare("SELECT id FROM page_sections WHERE page_id = ? AND section_key = ?");
            $stmt->execute([$pageId, $key]);
            $sectionId = $stmt->fetchColumn();
        }
        
        // Insert or update content
        $stmt = $pdo->prepare("
            INSERT INTO page_content (section_id, content_value)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE content_value = VALUES(content_value)
        ");
        $stmt->execute([$sectionId, $content]);
        $count++;
    }
    
    echo "✓ Inserted $count sections with content\n";
    echo "\n✅ Successfully set up CMS tables and seeded Terms page!\n";
    echo "\nYou can now:\n";
    echo "1. Access admin panel to edit: /resources/views/admin/pages/edit.php?page=terms\n";
    echo "2. View the page: /public/terms.php\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
