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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f7;
            color: #1d1d1f;
        }
        
        .header {
            background: #1d1d1f;
            color: white;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 20px;
            font-weight: 600;
        }
        
        .back-btn {
            padding: 8px 20px;
            background: rgba(255,255,255,0.15);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background: rgba(255,255,255,0.25);
        }
        
        .alert {
            padding: 16px 32px;
            margin: 0;
            text-align: center;
            font-weight: 600;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
        
        .editor-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            height: calc(100vh - 60px);
        }
        
        .editor-panel {
            background: white;
            overflow-y: auto;
            border-right: 1px solid #d2d2d7;
        }
        
        .preview-panel {
            background: #f5f5f7;
            overflow-y: auto;
        }
        
        .panel-header {
            background: #fbfbfd;
            padding: 20px 32px;
            border-bottom: 1px solid #d2d2d7;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .panel-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: #1d1d1f;
        }
        
        .editor-content {
            padding: 32px;
        }
        
        .form-group {
            margin-bottom: 28px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1d1d1f;
            margin-bottom: 8px;
        }
        
        .form-input,
        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d2d2d7;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
        }
        
        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #0071e3;
            box-shadow: 0 0 0 4px rgba(0,113,227,0.1);
        }
        
        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn-save {
            position: sticky;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px 32px;
            background: white;
            border-top: 1px solid #d2d2d7;
        }
        
        .btn-primary {
            width: 100%;
            padding: 14px 28px;
            background: #0071e3;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: #0077ed;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,113,227,0.3);
        }
        
        /* Preview Styles */
        .preview-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .preview-hero {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px;
            margin-bottom: 40px;
        }
        
        .preview-hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        
        .preview-hero p {
            font-size: 20px;
            opacity: 0.95;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .preview-section {
            background: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .preview-section h2 {
            font-size: 32px;
            font-weight: 700;
            color: #1d1d1f;
            margin-bottom: 16px;
        }
        
        .preview-section p {
            font-size: 17px;
            line-height: 1.7;
            color: #515154;
            white-space: pre-wrap;
        }
        
        .preview-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #f5f5f7;
            color: #0071e3;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Edit: <?php echo htmlspecialchars($page['title'] ?? 'Page'); ?></h1>
        <a href="../dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>
    
    <?php if ($success_message): ?>
        <div class="alert alert-success">✓ <?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="alert alert-error">⚠ <?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    
    <div class="editor-container">
        <!-- Editor Panel -->
        <div class="editor-panel">
            <div class="panel-header">
                <h2>Content Editor</h2>
            </div>
            
            <form method="POST" id="contentForm">
                <div class="editor-content">
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
                                    data-section-key="<?php echo $section['section_key']; ?>"
                                >
                            <?php elseif ($section['section_type'] === 'textarea'): ?>
                                <textarea 
                                    class="form-textarea" 
                                    id="section_<?php echo $section['section_id']; ?>"
                                    name="content[<?php echo $section['section_id']; ?>]"
                                    rows="4"
                                    data-section-key="<?php echo $section['section_key']; ?>"
                                ><?php echo htmlspecialchars($section['content_value'] ?? ''); ?></textarea>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="btn-save">
                    <button type="submit" name="save_content" class="btn-primary">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Preview Panel -->
        <div class="preview-panel">
            <div class="panel-header">
                <h2>Live Preview</h2>
            </div>
            
            <div class="preview-container" id="previewContent">
                <!-- Hero Section -->
                <div class="preview-hero">
                    <h1 id="preview_hero_title">About Glass Market</h1>
                    <p id="preview_hero_subtitle">Connecting the global glass recycling industry</p>
                </div>
                
                <!-- Mission Section -->
                <div class="preview-section">
                    <span class="preview-badge">Our Mission</span>
                    <h2 id="preview_mission_title">Our Mission</h2>
                    <p id="preview_mission_text">Content loading...</p>
                </div>
                
                <!-- Vision Section -->
                <div class="preview-section">
                    <span class="preview-badge">Our Vision</span>
                    <h2 id="preview_vision_title">Our Vision</h2>
                    <p id="preview_vision_text">Content loading...</p>
                </div>
                
                <!-- Values Section -->
                <div class="preview-section">
                    <span class="preview-badge">Our Values</span>
                    <h2 id="preview_values_title">Our Values</h2>
                    <p id="preview_values_text">Content loading...</p>
                </div>
                
                <!-- Team Section -->
                <div class="preview-section">
                    <span class="preview-badge">Our Team</span>
                    <h2 id="preview_team_title">Our Team</h2>
                    <p id="preview_team_text">Content loading...</p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Live preview update
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-input, .form-textarea');
            
            // Initial preview load
            inputs.forEach(input => {
                updatePreview(input);
            });
            
            // Listen for changes
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    updatePreview(this);
                });
            });
            
            function updatePreview(input) {
                const sectionKey = input.getAttribute('data-section-key');
                const value = input.value;
                const previewElement = document.getElementById('preview_' + sectionKey);
                
                if (previewElement) {
                    previewElement.textContent = value || 'No content yet...';
                }
            }
        });
    </script>
</body>
</html>
