<?php
// Company Tab Content
// Expects: $user, $company

// Get company listings if company exists
$company_listings = [];
$total_company_listings = 0;
$page = isset($_GET['company_page']) ? max(1, intval($_GET['company_page'])) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

if ($company) {
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get total count
        $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM listings WHERE company_id = :company_id');
        $stmt->execute(['company_id' => $company['id']]);
        $total_company_listings = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Get paginated listings
        $stmt = $pdo->prepare('
            SELECT l.*,
                   COALESCE(li.image_path, l.image_path) as image_path
            FROM listings l
            LEFT JOIN listing_images li ON l.id = li.listing_id AND li.is_main = 1
            WHERE l.company_id = :company_id
            ORDER BY l.created_at DESC
            LIMIT :limit OFFSET :offset
        ');
        $stmt->bindValue(':company_id', $company['id'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $company_listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Silent fail
    }
}

$total_pages = ceil($total_company_listings / $per_page);
?>

<div class="tab-panel" id="tab-company">
    <h2 class="section-title">Company Information</h2>
    
    <?php if ($company): ?>
        <!-- Company exists - show overview -->
        <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 32px;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 32px; justify-content: space-between; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 80px; height: 80px; background: #1f2937; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 36px; color: white; font-weight: 700;">
                        <?php echo strtoupper(substr($company['name'], 0, 1)); ?>
                    </div>
                    <div>
                        <h3 style="margin: 0 0 4px; font-size: 28px; font-weight: 700; color: #1f2937;">
                            <?php echo htmlspecialchars($company['name']); ?>
                        </h3>
                        <p style="margin: 0; font-size: 14px; color: #6b7280;">
                            <?php echo htmlspecialchars($company['company_type'] ?? 'Other'); ?>
                        </p>
                    </div>
                </div>
                <a href="<?php echo VIEWS_URL; ?>/company/edit-company.php" style="padding: 12px 24px; background: #1f2937; color: white; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 14px;">
                    Edit Company
                </a>
            </div>
        </div>

        <!-- Company Listings Section -->
        <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h3 style="margin: 0; font-size: 20px; font-weight: 700; color: #1f2937;">
                    Company Listings
                    <span style="font-size: 14px; color: #6b7280; font-weight: 500; margin-left: 8px;">
                        (<?php echo $total_company_listings; ?> total)
                    </span>
                </h3>
                <a href="<?php echo VIEWS_URL; ?>/company/create-company-listing.php" style="display: inline-block; padding: 12px 24px; background: #10b981; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">
                    + Create Company Listing
                </a>
            </div>

            <?php if (!empty($company_listings)): ?>
                <div style="display: grid; gap: 16px; margin-bottom: 24px;">
                    <?php foreach ($company_listings as $listing): ?>
                        <?php
                            // Get real image or use placeholder
                            $imageUrl = "https://picsum.photos/seed/glass{$listing['id']}/200/200";
                            if (!empty($listing['image_path'])) {
                                $imageUrl = PUBLIC_URL . '/' . $listing['image_path'];
                            }
                        ?>
                        <div style="background: #f9fafb; padding: 20px; border-radius: 12px; border: 1px solid #e5e7eb; display: flex; gap: 20px; align-items: center;">
                            <!-- Listing Image -->
                            <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Glass listing" style="width: 80px; height: 80px; border-radius: 10px; object-fit: cover; flex-shrink: 0;">
                            
                            <!-- Listing Details -->
                            <div style="flex: 1; min-width: 0;">
                                <h4 style="margin: 0 0 8px; font-size: 18px; font-weight: 600; color: #1f2937;">
                                    <?php echo htmlspecialchars($listing['quantity_note'] ?? $listing['glass_type'] ?? 'Untitled Listing'); ?>
                                </h4>
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
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="<?php echo VIEWS_URL; ?>/listings.php?id=<?php echo $listing['id']; ?>" style="padding: 6px 12px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                        View
                                    </a>
                                    <a href="<?php echo VIEWS_URL; ?>/company/edit-company-listing.php?id=<?php echo $listing['id']; ?>" style="padding: 6px 12px; background: #6b7280; color: white; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div style="display: flex; justify-content: center; align-items: center; gap: 8px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                        <?php if ($page > 1): ?>
                            <a href="?tab=company&company_page=<?php echo $page - 1; ?>" style="padding: 8px 16px; background: #f3f4f6; color: #374151; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                ← Previous
                            </a>
                        <?php endif; ?>
                        
                        <span style="padding: 8px 16px; color: #6b7280; font-size: 14px; font-weight: 500;">
                            Page <?php echo $page; ?> of <?php echo $total_pages; ?>
                        </span>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?tab=company&company_page=<?php echo $page + 1; ?>" style="padding: 8px 16px; background: #f3f4f6; color: #374151; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                Next →
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 40px 20px; background: #f9fafb; border-radius: 12px; border: 1px solid #e5e7eb;">
                    <div style="font-size: 48px; margin-bottom: 12px;">📦</div>
                    <h4 style="margin: 0 0 8px; font-size: 18px; font-weight: 600; color: #1f2937;">No Company Listings Yet</h4>
                    <p style="margin: 0 0 20px; font-size: 14px; color: #6b7280;">
                        Create your first company listing to get started.
                    </p>
                    <a href="<?php echo VIEWS_URL; ?>/company/create-company-listing.php" style="display: inline-block; padding: 12px 24px; background: #10b981; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">
                        + Create Company Listing
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- No company - prompt to create -->
        <div style="background: white; padding: 40px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
            <div style="font-size: 64px; margin-bottom: 16px;">🏢</div>
            <h3 style="margin: 0 0 8px; font-size: 24px; font-weight: 700; color: #1f2937;">No Company Yet</h3>
            <p style="margin: 0 0 24px; font-size: 14px; color: #6b7280; max-width: 500px; margin-left: auto; margin-right: auto;">
                Create your company profile to start listing products.
            </p>
            <a href="<?php echo VIEWS_URL; ?>/company/create-company.php" style="display: inline-block; padding: 14px 32px; background: #1f2937; color: white; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 15px;">
                Create Company
            </a>
        </div>
    <?php endif; ?>
</div>
