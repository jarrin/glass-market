<?php
session_start();
require_once __DIR__ . '/../../../../includes/admin-guard.php';

// Database connection
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$success_message = '';
$error_message = '';
$page_slug = $_GET['page'] ?? 'about-us';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_content'])) {
        foreach ($_POST['content'] as $section_id => $content_value) {
            // Check if content exists
            $stmt = $pdo->prepare("SELECT id FROM page_content WHERE section_id = :section_id");
            $stmt->execute(['section_id' => $section_id]);
            
            if ($stmt->fetch()) {
                // Update existing content
                $update = $pdo->prepare("
                    UPDATE page_content 
                    SET content_value = :content_value, 
                        updated_by = :user_id,
                        updated_at = NOW()
                    WHERE section_id = :section_id
                ");
                $update->execute([
                    'content_value' => $content_value,
                    'section_id' => $section_id,
                    'user_id' => $_SESSION['user_id'] ?? null
                ]);
            } else {
                // Insert new content
                $insert = $pdo->prepare("
                    INSERT INTO page_content (section_id, content_value, updated_by)
                    VALUES (:section_id, :content_value, :user_id)
                ");
                $insert->execute([
                    'section_id' => $section_id,
                    'content_value' => $content_value,
                    'user_id' => $_SESSION['user_id'] ?? null
                ]);
            }
        }
        
        $success_message = 'Page content updated successfully!';
    }
    
    // Fetch page details
    $page_stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = :slug");
    $page_stmt->execute(['slug' => $page_slug]);
    $page = $page_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$page) {
        throw new Exception("Page not found");
    }
    
    // Fetch all sections with their content
    $sections_stmt = $pdo->prepare("
        SELECT 
            ps.id as section_id,
            ps.section_key,
            ps.section_type,
            ps.section_label,
            ps.display_order,
            pc.content_value
        FROM page_sections ps
        LEFT JOIN page_content pc ON ps.id = pc.section_id
        WHERE ps.page_id = :page_id
        ORDER BY ps.display_order ASC
    ");
    $sections_stmt->execute(['page_id' => $page['id']]);
    $sections = $sections_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $error_message = $e->getMessage();
}

$admin_name = $_SESSION['user_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?php echo htmlspecialchars($page['title'] ?? 'Page'); ?> - Glass Market Admin</title>
    <link rel="stylesheet" href="/glass-market/public/css/admin-dashboard.css">
    <style>
        .page-editor {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        
        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: #1f1a17;
        }
        
        .back-btn {
            padding: 10px 24px;
            background: #201b15;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-btn:hover {
            background: #2a2623;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 16px 24px;
            margin-bottom: 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .editor-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 32px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #2a2623;
            margin-bottom: 8px;
        }
        
        .form-input,
        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #d4cfc7;
            border-radius: 8px;
            font-size: 15px;
            font-family: ui-serif, Georgia, 'Times New Roman', Times, serif;
            transition: all 0.3s;
            background: #fafaf8;
        }
        
        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #6b6460;
            background: white;
        }
        
        .form-textarea {
            min-height: 120px;
            resize: vertical;
            line-height: 1.6;
        }
        
        .form-actions {
            display: flex;
            gap: 12px;
            padding-top: 24px;
            border-top: 2px solid #f3eee6;
            margin-top: 40px;
        }
        
        .btn-primary {
            flex: 1;
            padding: 14px 28px;
            background: #201b15;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: #2a2623;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(32,27,21,0.3);
        }
        
        .btn-secondary {
            padding: 14px 28px;
            background: transparent;
            color: #6b6460;
            border: 2px solid #d4cfc7;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-secondary:hover {
            border-color: #6b6460;
            color: #2a2623;
        }
    </style>
</head>
<body>
    <div class="page-editor">
        <div class="page-header">
            <h1 class="page-title">Edit: <?php echo htmlspecialchars($page['title'] ?? 'Page'); ?></h1>
            <a href="../dashboard.php" class="back-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">✓ <?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error">⚠ <?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <div class="editor-card">
            <form method="POST" id="contentForm">
                <?php foreach ($sections as $section): ?>
                    <div class="form-group">
                        <label class="form-label" for="section_<?php echo $section['section_id']; ?>">
                            <?php echo htmlspecialchars($section['section_label']); ?>
                        </label>
                        
                        <?php if ($section['section_type'] === 'text'): ?>
                            <input 
                                type="text" 
                                class="form-input" 
                                id="section_<?php echo $section['section_id']; ?>"
                                name="content[<?php echo $section['section_id']; ?>]"
                                value="<?php echo htmlspecialchars($section['content_value'] ?? ''); ?>"
                                placeholder="Enter <?php echo strtolower($section['section_label']); ?>"
                            >
                        <?php elseif ($section['section_type'] === 'textarea'): ?>
                            <textarea 
                                class="form-textarea" 
                                id="section_<?php echo $section['section_id']; ?>"
                                name="content[<?php echo $section['section_id']; ?>]"
                                rows="5"
                                placeholder="Enter <?php echo strtolower($section['section_label']); ?>"
                            ><?php echo htmlspecialchars($section['content_value'] ?? ''); ?></textarea>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <div class="form-actions">
                    <button type="submit" name="save_content" class="btn-primary">
                        Save Changes
                    </button>
                    <a href="../dashboard.php" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
