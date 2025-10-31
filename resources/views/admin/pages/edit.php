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

    $last_updated = '';
    if (!empty($page['updated_at']) && $page['updated_at'] !== '0000-00-00 00:00:00') {
        try {
            $last_updated = (new DateTime($page['updated_at']))->format('M j, Y');
        } catch (Exception $dateError) {
            $last_updated = '';
        }
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

    $group_order = ['hero', 'stats', 'mission', 'vision', 'values', 'team', 'cta', 'general'];
    $group_meta = [
        'hero' => [
            'title' => 'Hero Experience',
            'description' => 'Headline, subcopy, and CTA that define the first impression.',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#9cc2ff" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9"/></svg>'
        ],
        'stats' => [
            'title' => 'Impact Stats',
            'description' => 'Four key metrics that sit directly below the hero.',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#a4f4d0" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h3v12H4zM10.5 10h3v8h-3zM17 4h3v14h-3z"/></svg>'
        ],
        'mission' => [
            'title' => 'Mission Block',
            'description' => 'Explain why Glass Market exists and the change you drive.',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#ffd59c" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3"/><circle cx="12" cy="12" r="9"/></svg>'
        ],
        'vision' => [
            'title' => 'Vision Block',
            'description' => 'Share the horizon you are building toward.',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#f6b2ff" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>'
        ],
        'values' => [
            'title' => 'Values Grid',
            'description' => 'Appears as three cards highlighting how you operate.',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#ff9ca6" width="24" height="24"><rect x="4" y="4" width="7" height="7" rx="2"/><rect x="13" y="4" width="7" height="7" rx="2"/><rect x="4" y="13" width="7" height="7" rx="2"/></svg>'
        ],
        'team' => [
            'title' => 'Team Highlight',
            'description' => 'Introduce the people behind Glass Market.',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#b8f0ff" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 14c2.21 0 4 1.79 4 4v1H4v-1c0-2.21 1.79-4 4-4"/><circle cx="12" cy="8" r="4"/></svg>'
        ],
        'cta' => [
            'title' => 'Primary CTA',
            'description' => 'Final call-to-action that closes the About page.',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#ffcf9c" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>'
        ],
        'general' => [
            'title' => 'Additional Fields',
            'description' => 'Any remaining copy blocks that do not fit above.',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#c9d1ff" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>'
        ],
    ];

    $grouped_sections = [];
    foreach ($sections as $section) {
        $section_key = $section['section_key'] ?? '';
        if (strpos($section_key, 'hero_') === 0) {
            $group_key = 'hero';
        } elseif (strpos($section_key, 'stats_') === 0) {
            $group_key = 'stats';
        } elseif (strpos($section_key, 'mission_') === 0) {
            $group_key = 'mission';
        } elseif (strpos($section_key, 'vision_') === 0) {
            $group_key = 'vision';
        } elseif (strpos($section_key, 'values_') === 0) {
            $group_key = 'values';
        } elseif (strpos($section_key, 'team_') === 0) {
            $group_key = 'team';
        } elseif (strpos($section_key, 'cta_') === 0) {
            $group_key = 'cta';
        } else {
            $group_key = 'general';
        }

        if (!isset($grouped_sections[$group_key])) {
            $grouped_sections[$group_key] = [];
        }

        $grouped_sections[$group_key][] = $section;
    }
    
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
        body.editor-body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(140% 140% at 0% 0%, #28231d 0%, #171310 45%, #090807 100%);
            color: #f5f3ef;
            font-family: "SF Pro Display", "SF Pro Text", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            padding: 64px 0 96px;
        }

        .page-editor {
            max-width: 1080px;
            margin: 0 auto;
            padding: 0 32px;
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .editor-hero {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 32px;
            padding: 36px 40px;
            backdrop-filter: blur(24px);
            box-shadow: 0 45px 120px -80px rgba(0, 0, 0, 0.8);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
        }

        .page-title {
            font-size: clamp(32px, 6vw, 42px);
            margin: 0;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .page-meta {
            font-size: 14px;
            opacity: 0.7;
            margin-top: 8px;
        }

        .back-btn {
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.08);
            color: #f5f3ef;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            transition: transform 0.3s ease, background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
            backdrop-filter: blur(20px);
        }

        .back-btn:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.28);
            box-shadow: 0 20px 40px -24px rgba(0, 0, 0, 0.8);
        }

        .alert {
            padding: 18px 24px;
            border-radius: 18px;
            font-weight: 600;
            font-size: 14px;
            border: 1px solid transparent;
            box-shadow: 0 20px 50px -30px rgba(0, 0, 0, 0.65);
        }

        .alert-success {
            background: rgba(12, 99, 54, 0.22);
            border-color: rgba(44, 186, 109, 0.35);
            color: #c7f8d1;
        }

        .alert-error {
            background: rgba(176, 23, 43, 0.2);
            border-color: rgba(255, 103, 129, 0.28);
            color: #ffd7dd;
        }

        .editor-card {
            background: rgba(12, 11, 10, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 36px;
            padding: 48px;
            backdrop-filter: blur(26px);
            box-shadow: 0 60px 140px -90px rgba(0, 0, 0, 0.85);
        }

        .editor-layout {
            display: flex;
            gap: 36px;
            align-items: flex-start;
        }

        .editor-sidebar {
            width: 240px;
            position: sticky;
            top: 120px;
        }

        .sidebar-card {
            background: rgba(12, 11, 10, 0.68);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 28px;
            padding: 28px 24px;
            backdrop-filter: blur(18px);
            box-shadow: 0 30px 80px -60px rgba(0, 0, 0, 0.85);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sidebar-title {
            font-size: 13px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.58);
        }

        .sidebar-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .sidebar-link {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid transparent;
            border-radius: 18px;
            padding: 14px 16px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            width: 100%;
            color: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-align: left;
            transition: background 0.25s ease, border-color 0.25s ease, transform 0.25s ease;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(4px);
        }

        .sidebar-link.active {
            border-color: rgba(156, 194, 255, 0.5);
            background: rgba(156, 194, 255, 0.16);
            box-shadow: 0 18px 40px -28px rgba(156, 194, 255, 0.6);
        }

        .sidebar-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            margin-top: 4px;
            flex-shrink: 0;
            transition: background 0.25s ease;
        }

        .sidebar-link.active .sidebar-dot {
            background: #9cc2ff;
        }

        .sidebar-label {
            display: block;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.72);
        }

        .sidebar-caption {
            display: block;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.45);
            margin-top: 4px;
        }

        .editor-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 48px;
        }

        .group-block {
            padding: 36px 32px;
            border-radius: 32px;
            border: 1px solid rgba(255, 255, 255, 0.07);
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.06) 0%, rgba(16, 18, 24, 0.62) 100%);
            box-shadow: 0 45px 100px -80px rgba(0, 0, 0, 0.8);
            display: flex;
            flex-direction: column;
            gap: 28px;
            position: relative;
            scroll-margin-top: 120px;
        }

        .group-block::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.02) 55%, rgba(255, 255, 255, 0) 100%);
            opacity: 0.4;
            pointer-events: none;
        }

        .group-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            position: relative;
            z-index: 1;
        }

        .group-header-main {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .group-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .group-header h2 {
            margin: 0;
            font-size: 20px;
            letter-spacing: -0.01em;
        }

        .group-header p {
            margin: 6px 0 0;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.65);
        }

        .group-count {
            font-size: 12px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.45);
            background: rgba(255, 255, 255, 0.08);
            border-radius: 999px;
            padding: 6px 14px;
        }

        .group-fields {
            position: relative;
            z-index: 1;
        }

        .form-stack {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .form-group {
            padding: 26px 28px 28px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            background: linear-gradient(155deg, rgba(255, 255, 255, 0.05) 0%, rgba(20, 18, 16, 0.35) 100%);
            position: relative;
            transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .form-group::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.03) 48%, rgba(255, 255, 255, 0) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .form-group:hover,
        .form-group:focus-within {
            border-color: rgba(88, 141, 255, 0.42);
            transform: translateY(-2px);
            box-shadow: 0 30px 80px -60px rgba(17, 24, 39, 0.8);
        }

        .form-group:hover::after,
        .form-group:focus-within::after {
            opacity: 1;
        }

        .form-label-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: space-between;
            align-items: center;
        }

        .form-label {
            font-size: 13px;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.82);
        }

        .form-key {
            font-size: 12px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 999px;
            padding: 6px 14px;
        }

        .form-input,
        .form-textarea {
            width: 100%;
            padding: 16px 18px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            font-size: 16px;
            font-family: "SF Pro Text", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #f9f6f2;
            background: rgba(255, 255, 255, 0.06);
            transition: border-color 0.3s ease, background 0.3s ease, box-shadow 0.3s ease;
        }

        .form-input::placeholder,
        .form-textarea::placeholder {
            color: rgba(255, 255, 255, 0.35);
        }

        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: rgba(116, 170, 255, 0.7);
            background: rgba(20, 28, 45, 0.65);
            box-shadow: 0 18px 48px -28px rgba(17, 80, 197, 0.75);
        }

        .form-textarea {
            min-height: 160px;
            resize: vertical;
            line-height: 1.7;
        }

        .form-hint {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.45);
            margin: 0;
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: flex-end;
            padding-top: 32px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            margin-top: 40px;
        }

        .btn-primary {
            padding: 15px 36px;
            border-radius: 999px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            color: #0a0a0d;
            background: linear-gradient(135deg, #9dd3ff 0%, #4f8bff 55%, #2b68ff 100%);
            cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            box-shadow: 0 35px 70px -40px rgba(79, 139, 255, 0.9);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 45px 90px -45px rgba(79, 139, 255, 0.95);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            padding: 15px 30px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: transparent;
            color: #f5f3ef;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.25s ease, border-color 0.25s ease, transform 0.25s ease;
            backdrop-filter: blur(14px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.35);
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            body.editor-body {
                padding: 40px 0 72px;
            }

            .page-editor {
                padding: 0 20px;
            }

            .editor-card {
                padding: 32px 24px;
                border-radius: 28px;
            }

            .editor-layout {
                flex-direction: column;
                gap: 24px;
            }

            .editor-sidebar {
                width: 100%;
                position: static;
            }

            .sidebar-card {
                flex-direction: column;
                padding: 24px 20px;
            }

            .sidebar-links {
                flex-direction: row;
                overflow-x: auto;
                padding-bottom: 4px;
            }

            .sidebar-link {
                min-width: 180px;
            }

            .form-group {
                padding: 22px 22px 24px;
            }

            .form-input,
            .form-textarea {
                font-size: 15px;
            }
        }

        @media (max-width: 480px) {
            .sidebar-link {
                min-width: 160px;
            }

            .group-block {
                padding: 28px 24px;
            }
        }
    </style>
</head>
<body class="editor-body">
    <div class="page-editor">
        <div class="editor-hero">
            <div>
                <h1 class="page-title">Edit: <?php echo htmlspecialchars($page['title'] ?? 'Page'); ?></h1>
                <p class="page-meta">
                    Slug: <?php echo htmlspecialchars($page_slug); ?>
                    <?php if (!empty($last_updated)): ?>
                        • Last updated <?php echo htmlspecialchars($last_updated); ?>
                    <?php endif; ?>
                </p>
            </div>
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
                <div class="editor-layout">
                    <aside class="editor-sidebar">
                        <div class="sidebar-card">
                            <span class="sidebar-title">Section Map</span>
                            <div class="sidebar-links">
                                <?php foreach ($group_order as $group_key): ?>
                                    <?php if (!isset($grouped_sections[$group_key])) { continue; } ?>
                                    <?php $meta = $group_meta[$group_key] ?? ['title' => ucfirst($group_key), 'description' => '', 'icon' => '']; ?>
                                    <button type="button" class="sidebar-link" data-target="group-<?php echo htmlspecialchars($group_key); ?>">
                                        <span class="sidebar-dot"></span>
                                        <div>
                                            <span class="sidebar-label"><?php echo htmlspecialchars($meta['title']); ?></span>
                                            <span class="sidebar-caption"><?php echo count($grouped_sections[$group_key]); ?> fields</span>
                                        </div>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </aside>

                    <div class="editor-content">
                        <?php foreach ($group_order as $group_key): ?>
                            <?php if (!isset($grouped_sections[$group_key])) { continue; } ?>
                            <?php $meta = $group_meta[$group_key] ?? ['title' => ucfirst($group_key), 'description' => '', 'icon' => '']; ?>
                            <section id="group-<?php echo htmlspecialchars($group_key); ?>" class="group-block">
                                <div class="group-header">
                                    <div class="group-header-main">
                                        <span class="group-icon"><?php echo $meta['icon']; ?></span>
                                        <div>
                                            <h2><?php echo htmlspecialchars($meta['title']); ?></h2>
                                            <?php if (!empty($meta['description'])): ?>
                                                <p><?php echo htmlspecialchars($meta['description']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <span class="group-count"><?php echo count($grouped_sections[$group_key]); ?> fields</span>
                                </div>

                                <div class="group-fields form-stack">
                                    <?php foreach ($grouped_sections[$group_key] as $section): ?>
                                        <?php
                                            $hint_text = ($section['section_type'] === 'textarea')
                                                ? 'Paragraph field - line breaks are preserved on the site.'
                                                : 'Short field - ideal for titles, stats, or CTA labels.';
                                            if ($group_key === 'stats') {
                                                $hint_text = 'Stat block - keep numbers punchy and labels descriptive.';
                                            } elseif ($group_key === 'values') {
                                                $hint_text = 'Value card copy - titles stay short, descriptions run 2–3 sentences.';
                                            } elseif ($group_key === 'cta') {
                                                $hint_text = 'Call-to-action copy - pair a bold ask with an optional supporting line.';
                                            }
                                        ?>
                                        <div class="form-group">
                                            <div class="form-label-row">
                                                <label class="form-label" for="section_<?php echo $section['section_id']; ?>">
                                                    <?php echo htmlspecialchars($section['section_label']); ?>
                                                </label>
                                                <span class="form-key"><?php echo htmlspecialchars(strtoupper($section['section_key'])); ?></span>
                                            </div>

                                            <?php if ($section['section_type'] === 'text'): ?>
                                                <input
                                                    type="text"
                                                    class="form-input"
                                                    id="section_<?php echo $section['section_id']; ?>"
                                                    name="content[<?php echo $section['section_id']; ?>]"
                                                    value="<?php echo htmlspecialchars($section['content_value'] ?? ''); ?>"
                                                    placeholder="Enter <?php echo htmlspecialchars(strtolower($section['section_label'])); ?>"
                                                    autocomplete="off"
                                                    spellcheck="true"
                                                >
                                            <?php elseif ($section['section_type'] === 'textarea'): ?>
                                                <textarea
                                                    class="form-textarea"
                                                    id="section_<?php echo $section['section_id']; ?>"
                                                    name="content[<?php echo $section['section_id']; ?>]"
                                                    rows="5"
                                                    placeholder="Enter <?php echo htmlspecialchars(strtolower($section['section_label'])); ?>"
                                                    spellcheck="true"
                                                ><?php echo htmlspecialchars($section['content_value'] ?? ''); ?></textarea>
                                            <?php endif; ?>

                                            <p class="form-hint"><?php echo htmlspecialchars($hint_text); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="../dashboard.php" class="btn-secondary">Cancel</a>
                    <button type="submit" name="save_content" class="btn-primary">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        (function() {
            var links = Array.prototype.slice.call(document.querySelectorAll('.sidebar-link'));
            var sections = Array.prototype.slice.call(document.querySelectorAll('.group-block'));

            if (!links.length || !sections.length) {
                return;
            }

            links.forEach(function(link) {
                link.addEventListener('click', function() {
                    var targetId = link.getAttribute('data-target');
                    var section = document.getElementById(targetId);
                    if (section) {
                        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            if (links[0]) {
                links[0].classList.add('active');
            }

            if (!('IntersectionObserver' in window)) {
                return;
            }

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var id = entry.target.id;
                        links.forEach(function(link) {
                            var matches = link.getAttribute('data-target') === id;
                            if (matches) {
                                link.classList.add('active');
                            } else {
                                link.classList.remove('active');
                            }
                        });
                    }
                });
            }, {
                rootMargin: '-45% 0px -45% 0px',
                threshold: 0.2
            });

            sections.forEach(function(section) {
                observer.observe(section);
            });
        })();
    </script>
</body>
</html>
