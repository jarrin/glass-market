<?php
session_start();
require_once __DIR__ . '/../../../../includes/admin-guard.php';

// Database connection  
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch all pages
    $stmt = $pdo->query("
        SELECT p.*, 
               COUNT(DISTINCT ps.id) as section_count,
               MAX(pc.updated_at) as last_edited
        FROM pages p
        LEFT JOIN page_sections ps ON p.id = ps.page_id
        LEFT JOIN page_content pc ON ps.id = pc.section_id
        GROUP BY p.id
        ORDER BY p.title
    ");
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error_message = 'Database error: ' . $e->getMessage();
    $pages = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pages - Glass Market Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8f9fa;
            color: #1d1d1f;
            line-height: 1.6;
        }
        
        .header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 32px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 700;
        }
        
        .back-link {
            color: #2f6df5;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 32px;
        }
        
        .intro-text {
            background: #f0f4ff;
            border-left: 4px solid #2f6df5;
            padding: 20px 24px;
            margin-bottom: 32px;
            border-radius: 8px;
        }
        
        .intro-text p {
            margin: 0;
            color: #495057;
        }
        
        .pages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 24px;
        }
        
        .page-card {
            background: white;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: all 0.2s;
            border: 1px solid #e2e8f0;
        }
        
        .page-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .page-header {
            display: flex;
            align-items: start;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        
        .page-title {
            font-size: 20px;
            font-weight: 700;
            color: #1d1d1f;
            margin-bottom: 6px;
        }
        
        .page-slug {
            font-size: 13px;
            color: #6e6e73;
            font-family: 'Monaco', monospace;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .page-status {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-active {
            background: #d1fae5;
            color: #10b981;
        }
        
        .status-inactive {
            background: #fee2e2;
            color: #ef4444;
        }
        
        .page-meta {
            display: flex;
            gap: 20px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
            font-size: 14px;
            color: #64748b;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .edit-btn {
            display: inline-block;
            margin-top: 16px;
            padding: 12px 24px;
            background: #2f6df5;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .edit-btn:hover {
            background: #1e4db8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(47,109,245,0.3);
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #6e6e73;
        }
        
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1>📄 Manage Pages</h1>
            <a href="../dashboard.php" class="back-link">
                ← Back to Dashboard
            </a>
        </div>
    </header>
    
    <div class="container">
        <div class="intro-text">
            <p><strong>💡 Page Management:</strong> Edit the content of your website pages directly from here. All changes are immediately reflected on the live site.</p>
        </div>
        
        <?php if (empty($pages)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h3>No Pages Found</h3>
                <p>Run the seed scripts to create manageable pages.</p>
            </div>
        <?php else: ?>
            <div class="pages-grid">
                <?php foreach ($pages as $page): ?>
                    <div class="page-card">
                        <div class="page-header">
                            <div>
                                <div class="page-title"><?php echo htmlspecialchars($page['title']); ?></div>
                                <div class="page-slug">/<?php echo htmlspecialchars($page['slug']); ?></div>
                            </div>
                            <span class="page-status <?php echo $page['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $page['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </div>
                        
                        <div class="page-meta">
                            <div class="meta-item">
                                📝 <?php echo $page['section_count']; ?> sections
                            </div>
                            <?php if ($page['last_edited']): ?>
                                <div class="meta-item">
                                    🕒 <?php echo date('M d, Y', strtotime($page['last_edited'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <a href="edit.php?page=<?php echo urlencode($page['slug']); ?>" class="edit-btn">
                            Edit Page Content →
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
