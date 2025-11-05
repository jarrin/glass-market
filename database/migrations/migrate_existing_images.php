<?php
/**
 * Migration Script: Migrate existing images from listings.image_path to listing_images table
 * Run this once to migrate all existing listing images to the new multi-image system
 */

$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Starting migration of existing images...\n\n";
    
    // Get all listings with image_path but no entries in listing_images
    $stmt = $pdo->query("
        SELECT l.id, l.image_path 
        FROM listings l
        WHERE l.image_path IS NOT NULL 
        AND l.image_path != '' 
        AND l.image_path != 'image.png'
        AND NOT EXISTS (
            SELECT 1 FROM listing_images li WHERE li.listing_id = l.id
        )
    ");
    
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = count($listings);
    $migrated = 0;
    $errors = 0;
    
    echo "Found {$total} listings with images to migrate...\n\n";
    
    foreach ($listings as $listing) {
        try {
            $insertStmt = $pdo->prepare("
                INSERT INTO listing_images (listing_id, image_path, is_main, display_order)
                VALUES (:listing_id, :image_path, 1, 0)
            ");
            
            $insertStmt->execute([
                'listing_id' => $listing['id'],
                'image_path' => $listing['image_path']
            ]);
            
            $migrated++;
            echo "✓ Migrated listing #{$listing['id']}: {$listing['image_path']}\n";
            
        } catch (Exception $e) {
            $errors++;
            echo "✗ Error migrating listing #{$listing['id']}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n";
    echo "==============================\n";
    echo "Migration Complete!\n";
    echo "==============================\n";
    echo "Total listings: {$total}\n";
    echo "Successfully migrated: {$migrated}\n";
    echo "Errors: {$errors}\n";
    echo "\n";
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
    exit(1);
}
