<?php
/**
 * Drop and recreate CMS tables
 */

$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Dropping existing CMS tables if they exist...\n";
    $pdo->exec("DROP TABLE IF EXISTS page_content");
    $pdo->exec("DROP TABLE IF EXISTS page_sections");
    $pdo->exec("DROP TABLE IF EXISTS pages");
    echo "✓ Dropped tables\n";
    
    echo "\nCreating fresh CMS tables...\n";
    
    // Create pages table
    $pdo->exec("
        CREATE TABLE pages (
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
        CREATE TABLE page_sections (
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
        CREATE TABLE page_content (
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
    
    echo "\n✅ Successfully created all CMS tables!\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
