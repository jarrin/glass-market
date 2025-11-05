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
            $stmt = $pdo->prepare("SELECT id FROM page_content WHERE section_id = :section_id");
            $stmt->execute(['section_id' => $section_id]);
            
            if ($stmt->fetch()) {
                $update = $pdo->prepare("UPDATE page_content SET content_value = :content_value, updated_by = :user_id, updated_at = NOW() WHERE section_id = :section_id");
                $update->execute([
                    'content_value' => $content_value,
                    'section_id' => $section_id,
                    'user_id' => $_SESSION['user_id'] ?? null
                ]);
            } else {
                $insert = $pdo->prepare("INSERT INTO page_content (section_id, content_value, updated_by) VALUES (:section_id, :content_value, :user_id)");
                $insert->execute([
                    'section_id' => $section_id,
                    'content_value' => $content_value,
                    'user_id' => $_SESSION['user_id'] ?? null
                ]);
            }
        }
        $success_message = 'Content updated successfully!';
    }
    
    // Fetch page content
    $stmt = $pdo->prepare("SELECT ps.id as section_id, ps.section_key, ps.section_label, ps.section_type, ps.display_order, pc.content_value, p.title as page_title FROM pages p INNER JOIN page_sections ps ON p.id = ps.page_id LEFT JOIN page_content pc ON ps.id = pc.section_id WHERE p.slug = :page_slug ORDER BY ps.display_order");
    $stmt->execute(['page_slug' => $page_slug]);
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group sections
    $grouped_sections = [];
    foreach ($sections as $section) {
        $parts = explode('_', $section['section_key']);
        $group = $parts[0];
        if (!isset($grouped_sections[$group])) {
            $grouped_sections[$group] = [];
        }
        $grouped_sections[$group][] = $section;
    }
    
    // Define group metadata
    $group_meta = [
        'hero' => ['title' => 'Hero Section', 'description' => 'Main banner content', 'icon' => '🎯'],
        'stats' => ['title' => 'Statistics', 'description' => 'Key metrics', 'icon' => '📊'],
        'mission' => ['title' => 'Mission', 'description' => 'Mission statement', 'icon' => '🎯'],
        'vision' => ['title' => 'Vision', 'description' => 'Vision statement', 'icon' => '🔭'],
        'values' => ['title' => 'Values', 'description' => 'Core values', 'icon' => '⭐'],
        'team' => ['title' => 'Team', 'description' => 'Team information', 'icon' => '👥']
    ];
    
    $group_order = ['hero', 'stats', 'mission', 'vision', 'values', 'team'];
    
    foreach (array_keys($grouped_sections) as $group) {
        if (!in_array($group, $group_order)) {
            $group_order[] = $group;
            if (!isset($group_meta[$group])) {
                $group_meta[$group] = ['title' => ucfirst($group), 'description' => ucfirst($group) . ' section', 'icon' => '📝'];
            }
        }
    }
    
    $page_title = $sections[0]['page_title'] ?? 'Page';
    
} catch (PDOException $e) {
    $error_message = 'Database error: ' . $e->getMessage();
    $sections = [];
    $grouped_sections = [];
    $group_order = [];
    $group_meta = [];
    $page_title = 'Page';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?php echo htmlspecialchars($page_title); ?> - Glass Market</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --success: #10b981;
            --success-light: #d1fae5;
            --danger: #ef4444;
            --dark: #0f172a;
            --dark-soft: #1e293b;
            --dark-muted: #334155;
            --gray: #64748b;
            --gray-light: #cbd5e1;
            --gray-lighter: #e2e8f0;
            --gray-lightest: #f1f5f9;
            --white: #ffffff;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--gray-lightest);
            color: var(--dark);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        .admin-header {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: var(--gray-lightest);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--gray);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .back-btn:hover { background: var(--white); border-color: var(--gray-light); color: var(--dark); }
        .page-info h1 { font-size: 20px; font-weight: 700; color: var(--dark); margin-bottom: 2px; }
        .page-info .slug { font-size: 13px; color: var(--gray); font-family: 'Monaco', monospace; }
        .editor-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 32px;
            align-items: start;
        }
        .sidebar-nav { position: sticky; top: 100px; }
        .nav-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow-sm);
        }
        .nav-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 16px;
        }
        .nav-links { display: flex; flex-direction: column; gap: 4px; }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--dark-muted);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
        }
        .nav-link:hover { background: var(--gray-lightest); color: var(--dark); }
        .nav-link.active { background: var(--primary); color: var(--white); }
        .nav-link .icon { font-size: 16px; width: 20px; text-align: center; }
        .nav-link .count {
            margin-left: auto;
            font-size: 12px;
            color: var(--gray);
            background: var(--gray-lightest);
            padding: 2px 8px;
            border-radius: 12px;
        }
        .nav-link.active .count { background: rgba(255, 255, 255, 0.2); color: var(--white); }
        .editor-main { min-width: 0; }
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-success { background: var(--success-light); color: var(--success); border: 1px solid var(--success); }
        .alert-error { background: #fee2e2; color: var(--danger); border: 1px solid var(--danger); }
        .editor-form { display: flex; flex-direction: column; gap: 24px; }
        .section-group {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            scroll-margin-top: 100px;
        }
        .group-header {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-soft) 100%);
            color: var(--white);
            padding: 20px 24px;
        }
        .group-header-top { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
        .group-icon { font-size: 24px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; }
        .group-title { font-size: 18px; font-weight: 700; }
        .group-badge {
            margin-left: auto;
            background: rgba(255, 255, 255, 0.15);
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 600;
        }
        .group-description { font-size: 13px; color: rgba(255, 255, 255, 0.8); margin: 0; }
        .group-fields { padding: 24px; display: flex; flex-direction: column; gap: 20px; }
        .field-group { display: flex; flex-direction: column; gap: 8px; }
        .field-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--dark);
        }
        .field-key {
            font-family: 'Monaco', monospace;
            font-size: 11px;
            color: var(--gray);
            background: var(--gray-lightest);
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 400;
        }
        .field-input, .field-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            color: var(--dark);
            background: var(--white);
            transition: all 0.2s;
        }
        .field-input:focus, .field-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .field-textarea { min-height: 120px; resize: vertical; line-height: 1.6; }
        .action-bar {
            position: sticky;
            bottom: 0;
            background: var(--white);
            border-top: 1px solid var(--border);
            padding: 20px 32px;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05);
            z-index: 50;
        }
        .action-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .save-info { font-size: 13px; color: var(--gray); }
        .action-buttons { display: flex; gap: 12px; }
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary { background: var(--primary); color: var(--white); box-shadow: var(--shadow-sm); }
        .btn-primary:hover { background: var(--primary-dark); box-shadow: var(--shadow-md); transform: translateY(-1px); }
        .btn-secondary { background: var(--gray-lightest); color: var(--dark-muted); border: 1px solid var(--border); }
        .btn-secondary:hover { background: var(--white); color: var(--dark); }
        .empty-state { text-align: center; padding: 80px 20px; color: var(--gray); }
        .empty-state-icon { font-size: 64px; margin-bottom: 16px; opacity: 0.5; }
        .empty-state h3 { font-size: 18px; font-weight: 600; color: var(--dark-muted); margin-bottom: 8px; }
        @media (max-width: 1024px) {
            .editor-container { grid-template-columns: 1fr; padding: 24px; }
            .sidebar-nav { position: static; }
            .nav-links { flex-direction: row; overflow-x: auto; gap: 8px; }
            .nav-link { white-space: nowrap; }
        }
        @media (max-width: 640px) {
            .header-content { padding: 12px 16px; flex-direction: column; align-items: flex-start; }
            .editor-container { padding: 16px; }
            .action-bar { padding: 16px; }
            .action-content { flex-direction: column; align-items: stretch; }
            .action-buttons { flex-direction: column; }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="header-content">
            <a href="../dashboard.php" class="back-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Dashboard
            </a>
            <div class="page-info">
                <h1>Edit <?php echo htmlspecialchars($page_title); ?></h1>
                <div class="slug"><?php echo htmlspecialchars($page_slug); ?></div>
            </div>
        </div>
    </header>

    <div class="editor-container">
        <aside class="sidebar-nav">
            <div class="nav-card">
                <div class="nav-title">Sections</div>
                <div class="nav-links">
                    <?php foreach ($group_order as $group_key): ?>
                        <?php if (!isset($grouped_sections[$group_key])) continue; ?>
                        <?php 
                            $meta = $group_meta[$group_key] ?? ['title' => ucfirst($group_key), 'icon' => '📝'];
                            $field_count = count($grouped_sections[$group_key]);
                        ?>
                        <button type="button" class="nav-link" onclick="scrollToSection('group-<?php echo htmlspecialchars($group_key); ?>')">
                            <span class="icon"><?php echo $meta['icon']; ?></span>
                            <span><?php echo htmlspecialchars($meta['title']); ?></span>
                            <span class="count"><?php echo $field_count; ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>

        <main class="editor-main">
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($grouped_sections)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📝</div>
                    <h3>No Sections Found</h3>
                    <p>This page doesn't have any editable sections yet.</p>
                </div>
            <?php else: ?>
                <form method="POST" class="editor-form" id="contentForm">
                    <?php foreach ($group_order as $group_key): ?>
                        <?php if (!isset($grouped_sections[$group_key])) continue; ?>
                        <?php $meta = $group_meta[$group_key] ?? ['title' => ucfirst($group_key), 'description' => '', 'icon' => '📝']; ?>
                        <div class="section-group" id="group-<?php echo htmlspecialchars($group_key); ?>">
                            <div class="group-header">
                                <div class="group-header-top">
                                    <div class="group-icon"><?php echo $meta['icon']; ?></div>
                                    <h2 class="group-title"><?php echo htmlspecialchars($meta['title']); ?></h2>
                                    <div class="group-badge"><?php echo count($grouped_sections[$group_key]); ?> Fields</div>
                                </div>
                                <?php if (!empty($meta['description'])): ?>
                                    <p class="group-description"><?php echo htmlspecialchars($meta['description']); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="group-fields">
                                <?php foreach ($grouped_sections[$group_key] as $section): ?>
                                    <div class="field-group">
                                        <label class="field-label">
                                            <?php echo htmlspecialchars($section['section_label']); ?>
                                            <span class="field-key"><?php echo htmlspecialchars($section['section_key']); ?></span>
                                        </label>
                                        <?php if ($section['section_type'] === 'textarea'): ?>
                                            <textarea name="content[<?php echo $section['section_id']; ?>]" class="field-textarea" rows="4"><?php echo htmlspecialchars($section['content_value'] ?? ''); ?></textarea>
                                        <?php else: ?>
                                            <input type="text" name="content[<?php echo $section['section_id']; ?>]" class="field-input" value="<?php echo htmlspecialchars($section['content_value'] ?? ''); ?>">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </form>
            <?php endif; ?>
        </main>
    </div>

    <?php if (!empty($grouped_sections)): ?>
        <div class="action-bar">
            <div class="action-content">
                <div class="save-info">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 4px;">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="16" x2="12" y2="12"/>
                        <line x1="12" y1="8" x2="12.01" y2="8"/>
                    </svg>
                    Make sure to save your changes
                </div>
                <div class="action-buttons">
                    <button type="button" class="btn btn-secondary" onclick="window.location.reload()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
                            <path d="M21 3v5h-5"/>
                        </svg>
                        Reset
                    </button>
                    <button type="submit" form="contentForm" name="save_content" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function scrollToSection(id) {
            const element = document.getElementById(id);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
                event.target.closest('.nav-link').classList.add('active');
            }
        }

        const form = document.getElementById('contentForm');
        const inputs = form?.querySelectorAll('input, textarea');
        let hasChanges = false;

        inputs?.forEach(input => {
            input.addEventListener('input', () => hasChanges = true);
        });

        window.addEventListener('beforeunload', (e) => {
            if (hasChanges) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            }
        });

        form?.addEventListener('submit', () => hasChanges = false);

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.id;
                    document.querySelectorAll('.nav-link').forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('onclick')?.includes(id)) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        }, {
            root: null,
            rootMargin: '-100px 0px -60% 0px',
            threshold: 0
        });

        document.querySelectorAll('.section-group').forEach(section => observer.observe(section));
    </script>
</body>
</html>
