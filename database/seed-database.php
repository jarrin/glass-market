<?php
/**
 * Database Seeding Script
 * Clears test data and injects fresh listings and trial accounts
 * 
 * Usage: php database/seed-database.php
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db_connect.php';

echo "🌱 Glass Market Database Seeding Script\n";
echo "========================================\n\n";

// =============================================================================
// STEP 1: Clear Existing Test Data
// =============================================================================

echo "Step 1: Clearing existing test data...\n";

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Count before deletion
    $listingCount = $pdo->query("SELECT COUNT(*) FROM listings")->fetchColumn();
    $companyCount = $pdo->query("SELECT COUNT(*) FROM companies")->fetchColumn();
    $userCount = $pdo->query("SELECT COUNT(*) FROM users WHERE email LIKE '%@trial.test'")->fetchColumn();
    
    echo "  - Found {$listingCount} listings\n";
    echo "  - Found {$companyCount} companies\n";
    echo "  - Found {$userCount} trial users\n";
    
    // Delete listings first (foreign key constraints)
    $pdo->exec("DELETE FROM listings");
    echo "  ✅ Deleted all listings\n";
    
    // Delete companies
    $pdo->exec("DELETE FROM companies");
    echo "  ✅ Deleted all companies\n";
    
    // Delete trial users
    $pdo->exec("DELETE FROM users WHERE email LIKE '%@trial.test'");
    echo "  ✅ Deleted trial users\n";
    
    // Reset auto-increment counters
    $pdo->exec("ALTER TABLE listings AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE companies AUTO_INCREMENT = 1");
    echo "  ✅ Reset auto-increment counters\n";
    
    $pdo->commit();
    echo "✅ Step 1 Complete: Database cleaned\n\n";
    
} catch (PDOException $e) {
    $pdo->rollBack();
    die("❌ Error clearing data: " . $e->getMessage() . "\n");
}

// =============================================================================
// STEP 2: Create Company for Listings (System Company)
// =============================================================================

echo "Step 2: Creating system company for listings...\n";

try {
    $stmt = $pdo->prepare("
        INSERT INTO companies (name, company_type, description, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        'Glass Market Verified',
        'Trader',
        'Official Glass Market verified listings'
    ]);
    
    $systemCompanyId = $pdo->lastInsertId();
    echo "  ✅ Created system company (ID: {$systemCompanyId})\n";
    echo "✅ Step 2 Complete\n\n";
    
} catch (PDOException $e) {
    die("❌ Error creating system company: " . $e->getMessage() . "\n");
}

// =============================================================================
// STEP 3: Inject 100 Listings with image.png
// =============================================================================

echo "Step 3: Injecting 100 listings...\n";

// Glass types for variety
$glassTypes = [
    'Clear Glass',
    'Green Glass',
    'Brown Glass',
    'Blue Glass',
    'Amber Glass',
    'Other Glass'
];

// Sides
$sides = ['WTS', 'WTB'];

// Currencies
$currencies = ['EUR', 'USD', 'GBP'];

// Sample descriptions
$descriptions = [
    'High-quality recycled glass',
    'Premium grade glass material',
    'Industrial grade glass cullet',
    'Clean sorted glass',
    'Mixed color glass available',
    'Bulk quantities available',
    'Certified recycled content',
    'Ready for immediate pickup',
    'Long-term contract available',
    'FOB pricing negotiable'
];

// Quantity notes
$quantityNotes = [
    'Regular monthly supply',
    'One-time bulk sale',
    'Weekly deliveries available',
    'Ongoing contract',
    'Spot sale available',
    'Pre-sorted material',
    'Container loads',
    'Loose bulk',
    'Palletized',
    'Big bags available'
];

try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("
        INSERT INTO listings (
            company_id,
            side,
            glass_type,
            glass_type_other,
            quantity_tons,
            quantity_note,
            recycled,
            tested,
            price_text,
            currency,
            quality_notes,
            image_path,
            published,
            created_at,
            valid_until
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 90 DAY))
    ");
    
    for ($i = 1; $i <= 100; $i++) {
        // Randomize data
        $glassType = $glassTypes[array_rand($glassTypes)];
        $side = $sides[array_rand($sides)];
        $currency = $currencies[array_rand($currencies)];
        $quantity = rand(10, 5000);
        $pricePerTon = rand(50, 500);
        $recycled = ['yes', 'no', 'unknown'][rand(0, 2)];
        $tested = ['yes', 'no', 'unknown'][rand(0, 2)];
        
        // Generate price text
        $priceText = $currency === 'EUR' ? "€{$pricePerTon}/ton" : 
                    ($currency === 'USD' ? "\${$pricePerTon}/ton" : "£{$pricePerTon}/ton");
        
        $stmt->execute([
            $systemCompanyId,                           // company_id
            $side,                                       // side
            $glassType,                                  // glass_type
            null,                                        // glass_type_other
            $quantity,                                   // quantity_tons
            $quantityNotes[array_rand($quantityNotes)], // quantity_note
            $recycled,                                   // recycled
            $tested,                                     // tested
            $priceText,                                  // price_text
            $currency,                                   // currency
            $descriptions[array_rand($descriptions)],   // quality_notes
            'image.png',                                 // image_path
            1                                            // published
        ]);
        
        if ($i % 10 === 0) {
            echo "  ⏳ Created {$i}/100 listings...\n";
        }
    }
    
    $pdo->commit();
    echo "  ✅ Successfully created 100 listings\n";
    echo "✅ Step 3 Complete\n\n";
    
} catch (PDOException $e) {
    $pdo->rollBack();
    die("❌ Error injecting listings: " . $e->getMessage() . "\n");
}

// =============================================================================
// STEP 4: Inject 50 Trial Accounts
// =============================================================================

echo "Step 4: Injecting 50 trial accounts...\n";

try {
    $pdo->beginTransaction();
    
    // First, create companies for each trial user
    $companyStmt = $pdo->prepare("
        INSERT INTO companies (name, company_type, created_at)
        VALUES (?, ?, NOW())
    ");
    
    // Then create users
    $userStmt = $pdo->prepare("
        INSERT INTO users (
            name,
            email,
            password,
            company_id,
            created_at
        ) VALUES (?, ?, ?, ?, NOW())
    ");
    
    // Create subscriptions table entry for trial users
    $subStmt = $pdo->prepare("
        INSERT INTO user_subscriptions (
            user_id,
            start_date,
            end_date,
            is_trial,
            is_active,
            created_at
        ) VALUES (?, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 1, 1, NOW())
    ");
    
    for ($i = 1; $i <= 50; $i++) {
        $trialName = "Trial User " . str_pad($i, 2, '0', STR_PAD_LEFT);
        $trialEmail = "trial{$i}@trial.test";
        $trialPassword = password_hash('trial123', PASSWORD_BCRYPT);
        $companyName = "Trial Company {$i}";
        $companyType = ['Trader', 'Glass Factory', 'Glass Recycle Plant', 'Collection Company'][rand(0, 3)];
        
        // Create company
        $companyStmt->execute([
            $companyName,
            $companyType
        ]);
        $companyId = $pdo->lastInsertId();
        
        // Create user
        $userStmt->execute([
            $trialName,
            $trialEmail,
            $trialPassword,
            $companyId
        ]);
        $userId = $pdo->lastInsertId();
        
        // Create trial subscription
        $subStmt->execute([$userId]);
        
        if ($i % 10 === 0) {
            echo "  ⏳ Created {$i}/50 trial accounts...\n";
        }
    }
    
    $pdo->commit();
    echo "  ✅ Successfully created 50 trial accounts\n";
    echo "✅ Step 4 Complete\n\n";
    
} catch (PDOException $e) {
    $pdo->rollBack();
    die("❌ Error injecting trial accounts: " . $e->getMessage() . "\n");
}

// =============================================================================
// STEP 5: Summary Report
// =============================================================================

echo "========================================\n";
echo "📊 SEEDING SUMMARY\n";
echo "========================================\n\n";

try {
    $finalListings = $pdo->query("SELECT COUNT(*) FROM listings")->fetchColumn();
    $finalCompanies = $pdo->query("SELECT COUNT(*) FROM companies")->fetchColumn();
    $finalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE email LIKE '%@trial.test'")->fetchColumn();
    $finalSubscriptions = $pdo->query("SELECT COUNT(*) FROM user_subscriptions WHERE is_trial = 1")->fetchColumn();
    
    echo "Database Statistics:\n";
    echo "  📦 Total Listings: {$finalListings}\n";
    echo "  🏢 Total Companies: {$finalCompanies}\n";
    echo "  👥 Trial Users: {$finalUsers}\n";
    echo "  🎟️  Active Trials: {$finalSubscriptions}\n\n";
    
    echo "Listing Breakdown:\n";
    $glassTypeBreakdown = $pdo->query("
        SELECT glass_type, COUNT(*) as count 
        FROM listings 
        GROUP BY glass_type
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($glassTypeBreakdown as $type) {
        echo "  - {$type['glass_type']}: {$type['count']} listings\n";
    }
    
    echo "\n";
    echo "Side Breakdown:\n";
    $sideBreakdown = $pdo->query("
        SELECT side, COUNT(*) as count 
        FROM listings 
        GROUP BY side
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($sideBreakdown as $side) {
        $sideLabel = $side['side'] === 'WTS' ? 'Want to Sell' : 'Want to Buy';
        echo "  - {$sideLabel}: {$side['count']} listings\n";
    }
    
    echo "\n";
    echo "========================================\n";
    echo "✅ SEEDING COMPLETE!\n";
    echo "========================================\n\n";
    
    echo "🔐 Trial Account Credentials:\n";
    echo "   Email: trial1@trial.test through trial50@trial.test\n";
    echo "   Password: trial123\n";
    echo "   Trial Period: 14 days from now\n\n";
    
    echo "📸 Image Path:\n";
    echo "   All listings use: image.png\n";
    echo "   Make sure this file exists in the public directory\n\n";
    
    echo "🎉 Database is ready for testing!\n";
    
} catch (PDOException $e) {
    echo "❌ Error generating summary: " . $e->getMessage() . "\n";
}
