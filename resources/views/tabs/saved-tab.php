<?php
// Saved Listings Tab Content  
// Expects: $user
// This will load saved listings from database with pagination

$page = isset($_GET['saved_page']) ? max(1, (int)$_GET['saved_page']) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

$saved_listings = [];
$total_saved = 0;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get total count
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM saved_listings WHERE user_id = :user_id');
    $stmt->execute(['user_id' => $user['id']]);
    $total_saved = $stmt->fetchColumn();
    
    // Get paginated saved listings
    $stmt = $pdo->prepare('
        SELECT l.*, c.name as company_name, sl.created_at as saved_at,
               u.name as seller_name
        FROM saved_listings sl
        INNER JOIN listings l ON sl.listing_id = l.id
        LEFT JOIN companies c ON l.company_id = c.id
        LEFT JOIN users u ON l.user_id = u.id
        WHERE sl.user_id = :user_id AND l.published = 1
        ORDER BY sl.created_at DESC
        LIMIT :limit OFFSET :offset
    ');
    $stmt->bindValue(':user_id', $user['id'], PDO::PARAM_INT);
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $saved_listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Silent fail
}

$total_pages = ceil($total_saved / $per_page);
?>
<div class="tab-panel" id="tab-saved">
    <h2 class="section-title">Saved Listings</h2>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <p style="margin: 0; font-size: 14px; color: #6b7280;">
            You have <strong style="color: #2f6df5;"><?php echo $total_saved; ?></strong> saved listing(s)
        </p>
    </div>

    <?php if (!empty($saved_listings)): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 32px;">
            <?php foreach ($saved_listings as $listing): ?>
                <?php
                    // Get real image or use placeholder
                    $imageUrl = "https://picsum.photos/seed/glass{$listing['id']}/400/400";
                    if (!empty($listing['image_path'])) {
                        $imageUrl = PUBLIC_URL . '/' . $listing['image_path'];
                    }
                    
                    $listingTitle = $listing['quantity_note'] ?? $listing['glass_type'] ?? 'Untitled Listing';
                    $sellerName = !empty($listing['company_name']) ? $listing['company_name'] : ($listing['seller_name'] ?? 'Private Seller');
                ?>
                <a href="<?php echo VIEWS_URL; ?>/listings.php?id=<?php echo $listing['id']; ?>" 
                   style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
                          text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s; display: block;"
                   onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.1)';"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.05)';">
                    
                    <!-- Image -->
                    <div style="position: relative; padding-top: 100%; background: #f3f4f6; overflow: hidden;">
                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                             alt="<?php echo htmlspecialchars($listingTitle); ?>" 
                             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                        
                        <!-- Saved Badge -->
                        <div style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.6); 
                                    border-radius: 50%; padding: 8px; backdrop-filter: blur(4px);">
                            <svg width="20" height="20" fill="#ef4444" viewBox="0 0 24 24">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Details -->
                    <div style="padding: 16px;">
                        <h3 style="margin: 0 0 8px; font-size: 16px; font-weight: 600; color: #1f2937; 
                                   display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; 
                                   overflow: hidden; line-height: 1.4;">
                            <?php echo htmlspecialchars($listingTitle); ?>
                        </h3>
                        
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 13px; color: #6b7280;">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <?php echo htmlspecialchars($sellerName); ?>
                        </div>
                        
                        <div style="display: flex; gap: 12px; flex-wrap: wrap; font-size: 12px; color: #6b7280; margin-bottom: 12px;">
                            <?php if (!empty($listing['glass_type'])): ?>
                            <span>🔹 <?php echo htmlspecialchars($listing['glass_type']); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($listing['quantity_tons'])): ?>
                            <span>⚖️ <?php echo htmlspecialchars($listing['quantity_tons']); ?> tons</span>
                            <?php endif; ?>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 16px; font-weight: 700; color: #059669;">
                                <?php echo !empty($listing['price_text']) ? htmlspecialchars($listing['price_text']) : 'Contact for Price'; ?>
                            </span>
                            <span style="font-size: 11px; color: #9ca3af;">
                                Saved <?php echo date('M j', strtotime($listing['saved_at'])); ?>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div style="display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 32px;">
            <?php if ($page > 1): ?>
            <a href="?tab=saved&saved_page=<?php echo $page - 1; ?>" 
               style="padding: 8px 16px; background: white; border: 1px solid #d1d5db; border-radius: 8px; 
                      text-decoration: none; color: #374151; font-size: 14px; font-weight: 500;">
                ← Previous
            </a>
            <?php endif; ?>
            
            <div style="display: flex; gap: 4px;">
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                <a href="?tab=saved&saved_page=<?php echo $i; ?>" 
                   style="padding: 8px 12px; background: <?php echo $i === $page ? '#2f6df5' : 'white'; ?>; 
                          border: 1px solid <?php echo $i === $page ? '#2f6df5' : '#d1d5db'; ?>; 
                          border-radius: 8px; text-decoration: none; 
                          color: <?php echo $i === $page ? 'white' : '#374151'; ?>; 
                          font-size: 14px; font-weight: 500; min-width: 40px; text-align: center;">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
            </div>
            
            <?php if ($page < $total_pages): ?>
            <a href="?tab=saved&saved_page=<?php echo $page + 1; ?>" 
               style="padding: 8px 16px; background: white; border: 1px solid #d1d5db; border-radius: 8px; 
                      text-decoration: none; color: #374151; font-size: 14px; font-weight: 500;">
                Next →
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- Empty State -->
        <div style="text-align: center; padding: 60px 20px; background: #f9fafb; border-radius: 16px; border: 2px dashed #d1d5db;">
            <div style="font-size: 64px; margin-bottom: 16px;">💙</div>
            <h3 style="margin: 0 0 8px; font-size: 20px; font-weight: 600; color: #1f2937;">
                No Saved Listings Yet
            </h3>
            <p style="margin: 0 0 24px; font-size: 14px; color: #6b7280;">
                Save listings you're interested in to easily find them later.
            </p>
            <a 
                href="<?php echo VIEWS_URL; ?>/browse.php" 
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
                Browse Listings
            </a>
        </div>
    <?php endif; ?>
</div>
