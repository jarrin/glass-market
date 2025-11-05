<?php
// Listings Tab Content  
// Expects: $user, $user_listings_count
// This will load listings from database

$user_listings = [];
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare('
        SELECT l.*, c.name as company_name
        FROM listings l
        LEFT JOIN companies c ON l.company_id = c.id
        WHERE l.user_id = :user_id
        ORDER BY l.created_at DESC
    ');
    $stmt->execute(['user_id' => $user['id']]);
    $user_listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Silent fail
}
?>
<div class="tab-panel" id="tab-listings">
    <h2 class="section-title">My Listings</h2>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px; color: #6b7280;">
            You have <strong style="color: #2f6df5;"><?php echo count($user_listings); ?></strong> personal listing(s)
        </p>
        <a 
            href="<?php echo VIEWS_URL; ?>/create.php" 
            style="
                padding: 10px 20px;
                background: #2f6df5;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
                font-size: 14px;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            "
        >
            <span>➕</span> Create New Listing
        </a>
    </div>

    <?php if (!empty($user_listings)): ?>
        <div style="display: grid; gap: 16px;">
            <?php foreach ($user_listings as $listing): ?>
                <?php
                    // Get real image or use placeholder
                    $imageUrl = "https://picsum.photos/seed/glass{$listing['id']}/200/200";
                    if (!empty($listing['image_path'])) {
                        $imageUrl = PUBLIC_URL . '/' . $listing['image_path'];
                    }
                ?>
                <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; gap: 20px; align-items: center;">
                    <!-- Listing Image -->
                    <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Glass listing" style="width: 80px; height: 80px; border-radius: 10px; object-fit: cover; flex-shrink: 0;">
                    
                    <!-- Listing Details -->
                    <div style="flex: 1; min-width: 0;">
                        <h3 style="margin: 0 0 8px; font-size: 18px; font-weight: 600; color: #1f2937;">
                            <?php echo htmlspecialchars($listing['quantity_note'] ?? $listing['glass_type'] ?? 'Untitled Listing'); ?>
                        </h3>
                        <div style="display: flex; gap: 16px; flex-wrap: wrap; font-size: 13px; color: #6b7280;">
                            <?php if (!empty($listing['glass_type'])): ?>
                            <span>🔹 <?php echo htmlspecialchars($listing['glass_type']); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($listing['quantity_tons'])): ?>
                            <span>⚖️ <?php echo htmlspecialchars($listing['quantity_tons']); ?> tons</span>
                            <?php endif; ?>
                            <?php if (!empty($listing['storage_location'])): ?>
                            <span>📍 <?php echo htmlspecialchars($listing['storage_location']); ?></span>
                            <?php endif; ?>
                            <?php if (isset($listing['created_at'])): ?>
                            <span>🕐 <?php echo date('M d, Y', strtotime($listing['created_at'])); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Status & Actions -->
                    <div style="text-align: right; flex-shrink: 0;">
                        <div style="
                            padding: 6px 12px;
                            border-radius: 20px;
                            font-size: 12px;
                            font-weight: 600;
                            background: <?php echo (!empty($listing['published']) && $listing['published'] == 1) ? '#dcfce7' : '#fef2f2'; ?>;
                            color: <?php echo (!empty($listing['published']) && $listing['published'] == 1) ? '#16a34a' : '#dc2626'; ?>;
                            display: inline-block;
                            margin-bottom: 8px;
                        ">
                            <?php echo (!empty($listing['published']) && $listing['published'] == 1) ? '✓ Published' : '⏸ Draft'; ?>
                        </div>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end;">
                            <a 
                                href="<?php echo VIEWS_URL; ?>/listings.php?id=<?php echo $listing['id']; ?>" 
                                style="padding: 6px 12px; background: #f3f4f6; color: #1f2937; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: 600;"
                            >
                                👁 View
                            </a>
                            <a 
                                href="<?php echo VIEWS_URL; ?>/edit-listing.php?id=<?php echo $listing['id']; ?>" 
                                style="padding: 6px 12px; background: #2f6df5; color: white; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: 600;"
                            >
                                ✏️ Edit
                            </a>
                            <form method="POST" action="<?php echo VIEWS_URL; ?>/profile.php?tab=listings" style="display: inline; margin: 0;">
                                <input type="hidden" name="listing_id" value="<?php echo $listing['id']; ?>">
                                <input type="hidden" name="new_status" value="<?php echo $listing['published'] == 1 ? 0 : 1; ?>">
                                <?php if ($listing['published'] == 0): ?>
                                    <button type="submit" name="toggle_publish" 
                                        style="padding: 6px 12px; background: #16a34a; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;"
                                        onclick="return confirm('Make this listing public?')"
                                    >
                                        ✓ Publish
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="toggle_publish"
                                        style="padding: 6px 12px; background: #dc2626; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;"
                                        onclick="return confirm('Unpublish this listing?')"
                                    >
                                        ⏸ Unpublish
                                    </button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($user_listings) > 5): ?>
        <div style="text-align: center; margin-top: 24px;">
            <p style="font-size: 14px; color: #6b7280; margin: 0;">
                Showing first 5 listings. Total: <?php echo count($user_listings); ?>
            </p>
        </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Empty State -->
        <div style="text-align: center; padding: 60px 20px; background: #f9fafb; border-radius: 16px; border: 2px dashed #d1d5db;">
            <div style="font-size: 64px; margin-bottom: 16px;">📦</div>
            <h3 style="margin: 0 0 8px; font-size: 20px; font-weight: 600; color: #1f2937;">
                No Listings Yet
            </h3>
            <p style="margin: 0 0 24px; font-size: 14px; color: #6b7280;">
                Start selling by creating your first personal glass listing.
            </p>
            <a 
                href="<?php echo VIEWS_URL; ?>/create.php" 
                style="
                    display: inline-block;
                    padding: 12px 24px;
                    background: #2f6df5;
                    color: white;
                    text-decoration: none;
                    border-radius: 10px;
                    font-weight: 600;
                    font-size: 14px;
                "
            >
                ➕ Create Your First Listing
            </a>
        </div>
    <?php endif; ?>
</div>
