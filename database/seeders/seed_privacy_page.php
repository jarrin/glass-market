<?php
/**
 * Seed Privacy Page Content into Database
 * Run this file to populate Privacy page with editable content
 */

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Seeding Privacy page...\n";
    
    // Insert or update Privacy page
    $stmt = $pdo->prepare("INSERT INTO pages (slug, title) VALUES (?, ?) 
                           ON DUPLICATE KEY UPDATE title = VALUES(title)");
    $stmt->execute(['privacy', 'Privacy Policy']);
    
    $pageId = $pdo->lastInsertId();
    if (!$pageId) {
        $stmt = $pdo->prepare("SELECT id FROM pages WHERE slug = ?");
        $stmt->execute(['privacy']);
        $pageId = $stmt->fetchColumn();
    }
    echo "✓ Created Privacy page (ID: $pageId)\n";
    
    // Define all Privacy page sections
    $sections = [
        // Hero Section
        ['hero_title', 'Hero Title', 'text', 'hero', 1, 'Privacy Policy'],
        ['hero_subtitle', 'Hero Subtitle', 'text', 'hero', 2, 'How we collect, use, and protect your information'],
        ['last_updated', 'Last Updated Date', 'text', 'hero', 3, 'November 3, 2025'],
        
        // Introduction
        ['intro_text', 'Introduction Text', 'textarea', 'introduction', 4, 'At Glass Market, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform.'],
        ['info_box_text', 'Info Box Text', 'textarea', 'introduction', 5, 'Your Rights: You have the right to access, correct, or delete your personal information at any time. Contact us at privacy@glassmarket.com for assistance.'],
        
        // Section 1: Information We Collect
        ['section_1_title', 'Section 1 Title', 'text', 'section_1', 6, '1. Information We Collect'],
        ['section_1_subtitle_1', 'Information You Provide Subtitle', 'text', 'section_1', 7, 'Information You Provide'],
        ['section_1_intro_1', 'Information You Provide Intro', 'textarea', 'section_1', 8, 'We collect information you voluntarily provide when using our services:'],
        ['section_1_info_1', 'Account Information', 'text', 'section_1', 9, 'Account Information: Name, email address, phone number, password'],
        ['section_1_info_2', 'Profile Information', 'text', 'section_1', 10, 'Profile Information: Profile photo, bio, business name, location'],
        ['section_1_info_3', 'Payment Information', 'text', 'section_1', 11, 'Payment Information: Credit card details, billing address, payment history'],
        ['section_1_info_4', 'Listing Information', 'text', 'section_1', 12, 'Listing Information: Product descriptions, photos, pricing'],
        ['section_1_info_5', 'Communication', 'text', 'section_1', 13, 'Communication: Messages, reviews, support inquiries'],
        ['section_1_info_6', 'Verification Data', 'text', 'section_1', 14, 'Verification Data: Government ID, business documents (for sellers)'],
        ['section_1_subtitle_2', 'Automatically Collected Subtitle', 'text', 'section_1', 15, 'Automatically Collected Information'],
        ['section_1_intro_2', 'Automatically Collected Intro', 'textarea', 'section_1', 16, 'When you use our platform, we automatically collect:'],
        ['section_1_auto_1', 'Device Information', 'text', 'section_1', 17, 'Device Information: IP address, browser type, operating system'],
        ['section_1_auto_2', 'Usage Data', 'text', 'section_1', 18, 'Usage Data: Pages viewed, time spent, clicks, search queries'],
        ['section_1_auto_3', 'Location Data', 'text', 'section_1', 19, 'Location Data: Approximate location based on IP address'],
        ['section_1_auto_4', 'Cookies & Tracking', 'text', 'section_1', 20, 'Cookies & Tracking: Session data, preferences, analytics'],
        
        // Section 2: How We Use Your Information
        ['section_2_title', 'Section 2 Title', 'text', 'section_2', 21, '2. How We Use Your Information'],
        ['section_2_intro', 'Section 2 Introduction', 'textarea', 'section_2', 22, 'We use your information to:'],
        ['section_2_use_1', 'Provide Services', 'text', 'section_2', 23, 'Provide Services: Process transactions, facilitate buying and selling'],
        ['section_2_use_2', 'Account Management', 'text', 'section_2', 24, 'Account Management: Create and manage your account'],
        ['section_2_use_3', 'Communication', 'text', 'section_2', 25, 'Communication: Send order updates, notifications, and support responses'],
        ['section_2_use_4', 'Improve Platform', 'text', 'section_2', 26, 'Improve Platform: Analyze usage to enhance features and user experience'],
        ['section_2_use_5', 'Security', 'text', 'section_2', 27, 'Security: Detect fraud, prevent abuse, and protect user safety'],
        ['section_2_use_6', 'Marketing', 'text', 'section_2', 28, 'Marketing: Send promotional emails (you can opt out anytime)'],
        ['section_2_use_7', 'Legal Compliance', 'text', 'section_2', 29, 'Legal Compliance: Meet legal obligations and enforce our terms'],
        ['section_2_use_8', 'Personalization', 'text', 'section_2', 30, 'Personalization: Customize content and recommendations'],
        
        // Section 3: Information Sharing
        ['section_3_title', 'Section 3 Title', 'text', 'section_3', 31, '3. Information Sharing'],
        ['section_3_subtitle_1', 'We Share Subtitle', 'text', 'section_3', 32, 'We Share Your Information With:'],
        ['section_3_share_1', 'Other Users', 'text', 'section_3', 33, 'Other Users: Buyers and sellers see necessary transaction information'],
        ['section_3_share_2', 'Service Providers', 'text', 'section_3', 34, 'Service Providers: Payment processors, shipping carriers, email services'],
        ['section_3_share_3', 'Business Partners', 'text', 'section_3', 35, 'Business Partners: Marketing partners (only with your consent)'],
        ['section_3_share_4', 'Legal Authorities', 'text', 'section_3', 36, 'Legal Authorities: When required by law or to protect rights'],
        ['section_3_share_5', 'Business Transfers', 'text', 'section_3', 37, 'Business Transfers: In case of merger, acquisition, or sale'],
        ['section_3_subtitle_2', 'We Do NOT Subtitle', 'text', 'section_3', 38, 'We Do NOT:'],
        ['section_3_not_1', 'Not Sell Data', 'text', 'section_3', 39, 'Sell your personal information to third parties'],
        ['section_3_not_2', 'Not Share Unrelated', 'text', 'section_3', 40, 'Share your data for unrelated purposes without consent'],
        ['section_3_not_3', 'Not Disclose Payment', 'text', 'section_3', 41, 'Disclose your payment details to other users'],
        
        // Section 4: Data Security
        ['section_4_title', 'Section 4 Title', 'text', 'section_4', 42, '4. Data Security'],
        ['section_4_intro', 'Section 4 Introduction', 'textarea', 'section_4', 43, 'We implement security measures to protect your information:'],
        ['section_4_security_1', 'Encryption', 'text', 'section_4', 44, 'Encryption: SSL/TLS encryption for data transmission'],
        ['section_4_security_2', 'Secure Storage', 'text', 'section_4', 45, 'Secure Storage: Encrypted databases and secure servers'],
        ['section_4_security_3', 'Access Controls', 'text', 'section_4', 46, 'Access Controls: Limited employee access to personal data'],
        ['section_4_security_4', 'Regular Audits', 'text', 'section_4', 47, 'Regular Audits: Security assessments and vulnerability testing'],
        ['section_4_security_5', 'Payment Security', 'text', 'section_4', 48, 'Payment Security: PCI-DSS compliant payment processing'],
        ['section_4_disclaimer', 'Security Disclaimer', 'textarea', 'section_4', 49, 'However, no method of transmission over the internet is 100% secure. While we strive to protect your data, we cannot guarantee absolute security.'],
        
        // Section 5: Your Privacy Rights
        ['section_5_title', 'Section 5 Title', 'text', 'section_5', 50, '5. Your Privacy Rights'],
        ['section_5_subtitle', 'Rights Subtitle', 'text', 'section_5', 51, 'You Have the Right To:'],
        ['section_5_right_1', 'Access', 'text', 'section_5', 52, 'Access: Request a copy of your personal information'],
        ['section_5_right_2', 'Correction', 'text', 'section_5', 53, 'Correction: Update or correct inaccurate information'],
        ['section_5_right_3', 'Deletion', 'text', 'section_5', 54, 'Deletion: Request deletion of your account and data'],
        ['section_5_right_4', 'Opt-Out', 'text', 'section_5', 55, 'Opt-Out: Unsubscribe from marketing emails'],
        ['section_5_right_5', 'Data Portability', 'text', 'section_5', 56, 'Data Portability: Receive your data in a portable format'],
        ['section_5_right_6', 'Object', 'text', 'section_5', 57, 'Object: Object to certain processing of your data'],
        ['section_5_right_7', 'Withdraw Consent', 'text', 'section_5', 58, 'Withdraw Consent: Revoke consent where applicable'],
        ['section_5_contact', 'Exercise Rights Text', 'textarea', 'section_5', 59, 'To exercise these rights, contact us at privacy@glassmarket.com or through your account settings.'],
        
        // Contact Information
        ['contact_title', 'Contact Section Title', 'text', 'contact', 100, '15. Contact Us'],
        ['contact_intro', 'Contact Introduction', 'textarea', 'contact', 101, 'If you have questions about this Privacy Policy or our privacy practices, please contact us:'],
        ['contact_email', 'Contact Email', 'text', 'contact', 102, 'privacy@glassmarket.com'],
        ['contact_phone', 'Contact Phone', 'text', 'contact', 103, '1-800-GLASS-123'],
        ['contact_address', 'Contact Address', 'text', 'contact', 104, 'Glass Market, 123 Glass Street, New York, NY 10001'],
        ['contact_dpo', 'Data Protection Officer Email', 'text', 'contact', 105, 'dpo@glassmarket.com'],
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
    echo "\n✅ Successfully seeded Privacy page!\n";
    echo "\nYou can now:\n";
    echo "1. Access admin panel to edit: /resources/views/admin/pages/edit.php?page=privacy\n";
    echo "2. View the page: /public/privacy.php\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
