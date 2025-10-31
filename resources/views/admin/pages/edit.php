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
            'nav_caption' => 'Hero headline & CTA',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#a7c4ff" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9"/></svg>'
        ],
        'stats' => [
            'title' => 'Impact Stats',
            'description' => 'Four key metrics that sit directly below the hero.',
            'nav_caption' => 'Metrics row under hero',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#80efd6" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h3v12H4zM10.5 10h3v8h-3zM17 4h3v14h-3z"/></svg>'
        ],
        'mission' => [
            'title' => 'Mission Block',
            'description' => 'Explain why Glass Market exists and the change you drive.',
            'nav_caption' => 'Mission column copy',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#ffdba8" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3"/><circle cx="12" cy="12" r="9"/></svg>'
        ],
        'vision' => [
            'title' => 'Vision Block',
            'description' => 'Share the horizon you are building toward.',
            'nav_caption' => 'Vision column copy',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#e0baff" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>'
        ],
        'values' => [
            'title' => 'Values Grid',
            'description' => 'Appears as three cards highlighting how you operate.',
            'nav_caption' => 'Three value cards',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#ffadc0" width="24" height="24"><rect x="4" y="4" width="7" height="7" rx="2"/><rect x="13" y="4" width="7" height="7" rx="2"/><rect x="4" y="13" width="7" height="7" rx="2"/></svg>'
        ],
        'team' => [
            'title' => 'Team Highlight',
            'description' => 'Introduce the people behind Glass Market.',
            'nav_caption' => 'Team intro copy',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#b4e4ff" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 14c2.21 0 4 1.79 4 4v1H4v-1c0-2.21 1.79-4 4-4"/><circle cx="12" cy="8" r="4"/></svg>'
        ],
        'cta' => [
            'title' => 'Primary CTA',
            'description' => 'Final call-to-action that closes the About page.',
            'nav_caption' => 'Bottom CTA banner',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#ffcfae" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>'
        ],
        'general' => [
            'title' => 'Additional Fields',
            'description' => 'Any remaining copy blocks that do not fit above.',
            'nav_caption' => 'Other configurable copy',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#cbd5ff" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>'
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
            background: radial-gradient(130% 145% at 12% -10%, #1e2537 0%, #0d1423 50%, #05070d 100%);
            color: #eef3fb;
            font-family: "SF Pro Display", "SF Pro Text", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            padding: 72px 0 110px;
        }

        .page-editor {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 36px;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .editor-hero {
            position: relative;
            background: linear-gradient(135deg, rgba(42, 74, 132, 0.55) 0%, rgba(16, 26, 48, 0.92) 100%);
            border: 1px solid rgba(122, 163, 255, 0.22);
            border-radius: 36px;
            padding: 40px 48px;
            backdrop-filter: blur(28px);
            box-shadow: 0 45px 120px -60px rgba(13, 24, 45, 0.9);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 28px;
            overflow: hidden;
        }

        .editor-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(120% 120% at 110% -10%, rgba(147, 185, 255, 0.55) 0%, rgba(25, 37, 61, 0.4) 48%, transparent 68%);
            pointer-events: none;
        }

        .page-title {
            font-size: clamp(34px, 5.8vw, 46px);
            margin: 0;
            font-weight: 700;
            letter-spacing: -0.015em;
        }

        .page-meta {
            font-size: 14px;
            color: rgba(214, 226, 255, 0.78);
            margin-top: 10px;
        }

        .back-btn {
            padding: 14px 28px;
            background: rgba(12, 18, 32, 0.6);
            color: #eef3fb;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(155, 189, 255, 0.32);
            transition: transform 0.3s ease, background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
            backdrop-filter: blur(20px);
            position: relative;
            z-index: 1;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            background: rgba(31, 47, 82, 0.75);
            border-color: rgba(182, 209, 255, 0.55);
            box-shadow: 0 24px 56px -30px rgba(31, 53, 101, 0.9);
        }

        .alert {
            padding: 20px 26px;
            border-radius: 16px;
            font-weight: 600;
            font-size: 14px;
            border: 1px solid transparent;
            box-shadow: 0 30px 80px -60px rgba(4, 9, 20, 0.85);
            backdrop-filter: blur(18px);
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(23, 120, 79, 0.35) 0%, rgba(13, 61, 39, 0.55) 100%);
            border-color: rgba(115, 227, 170, 0.45);
            color: #c8fde2;
        }

        .alert-error {
            background: linear-gradient(135deg, rgba(173, 38, 59, 0.32) 0%, rgba(93, 17, 30, 0.6) 100%);
            border-color: rgba(255, 134, 154, 0.42);
            color: #ffe5e9;
        }

        .editor-card {
            background: rgba(10, 15, 27, 0.78);
            border: 1px solid rgba(128, 154, 214, 0.12);
            border-radius: 40px;
            padding: 40px;
            backdrop-filter: blur(30px);
            box-shadow: 0 55px 140px -80px rgba(5, 9, 18, 0.9);
        }

        .editor-layout {
            display: flex;
            gap: 40px;
            align-items: flex-start;
        }

        .editor-sidebar {
            width: 260px;
            position: sticky;
            top: 120px;
        }

        .sidebar-card {
            background: rgba(8, 13, 25, 0.82);
            border: 1px solid rgba(108, 140, 212, 0.2);
            border-radius: 28px;
            padding: 30px 24px;
            backdrop-filter: blur(26px);
            box-shadow: 0 38px 90px -72px rgba(8, 14, 30, 0.9);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sidebar-title {
            font-size: 12px;
            letter-spacing: 0.26em;
            text-transform: uppercase;
            color: rgba(190, 205, 243, 0.6);
        }

        .sidebar-links {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .sidebar-link {
            background: rgba(24, 36, 62, 0.32);
            border: 1px solid rgba(133, 165, 240, 0.18);
            border-radius: 20px;
            padding: 16px 18px;
            display: flex;
            gap: 14px;
            align-items: flex-start;
            width: 100%;
            color: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-align: left;
            transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
        }

        .sidebar-link:hover {
            background: rgba(42, 62, 102, 0.45);
            border-color: rgba(176, 204, 255, 0.4);
            box-shadow: 0 25px 60px -48px rgba(32, 54, 98, 0.8);
            transform: translateX(4px);
        }

        .sidebar-link.active {
            border-color: rgba(164, 192, 255, 0.8);
            background: linear-gradient(135deg, rgba(73, 110, 192, 0.55) 0%, rgba(39, 68, 134, 0.65) 100%);
            box-shadow: 0 30px 70px -46px rgba(70, 111, 198, 0.85);
        }

        .sidebar-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(168, 190, 232, 0.5);
            margin-top: 4px;
            flex-shrink: 0;
            transition: background 0.25s ease, transform 0.25s ease;
        }

        .sidebar-link.active .sidebar-dot {
            background: #a5c3ff;
            transform: scale(1.2);
        }

        .sidebar-label {
            display: block;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-size: 12px;
            color: rgba(224, 232, 255, 0.85);
        }

        .sidebar-caption {
            display: block;
            font-size: 12px;
            color: rgba(177, 193, 227, 0.64);
            margin-top: 5px;
        }

        .editor-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 56px;
        }

        .group-block {
            padding: 40px 36px;
            border-radius: 36px;
            border: 1px solid rgba(123, 156, 230, 0.16);
            background: linear-gradient(150deg, rgba(28, 40, 70, 0.58) 0%, rgba(11, 17, 31, 0.92) 100%);
            box-shadow: 0 50px 110px -78px rgba(7, 11, 21, 0.9);
            display: flex;
            flex-direction: column;
            gap: 32px;
            position: relative;
            scroll-margin-top: 130px;
            overflow: hidden;
        }

        .group-block::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: radial-gradient(110% 100% at 115% -10%, rgba(123, 156, 230, 0.35) 0%, rgba(41, 58, 99, 0.25) 34%, transparent 64%);
            opacity: 0.55;
            pointer-events: none;
        }

        .group-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            position: relative;
            z-index: 1;
        }

        .group-header-main {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .group-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: rgba(194, 215, 255, 0.16);
            border: 1px solid rgba(175, 204, 255, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .group-header h2 {
            margin: 0;
            font-size: 22px;
            letter-spacing: -0.01em;
        }

        .group-header p {
            margin: 8px 0 0;
            font-size: 14px;
            color: rgba(207, 220, 250, 0.72);
        }

        .group-count {
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: rgba(185, 200, 234, 0.75);
            background: rgba(103, 136, 210, 0.22);
            border-radius: 999px;
            padding: 7px 18px;
        }

        .group-fields {
            position: relative;
            z-index: 1;
        }

        .form-stack {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .form-group {
            padding: 28px 30px;
            border-radius: 26px;
            border: 1px solid rgba(125, 154, 226, 0.2);
            background: linear-gradient(150deg, rgba(26, 38, 66, 0.75) 0%, rgba(15, 22, 39, 0.92) 100%);
            position: relative;
            transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
            display: flex;
            flex-direction: column;
            gap: 18px;
            overflow: hidden;
        }

        .form-group::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            background: linear-gradient(135deg, rgba(126, 162, 233, 0.26) 0%, rgba(51, 70, 111, 0.18) 42%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .form-group:hover,
        .form-group:focus-within {
            border-color: rgba(164, 192, 255, 0.7);
            transform: translateY(-2px);
            box-shadow: 0 32px 80px -58px rgba(45, 70, 128, 0.85);
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
            font-size: 12px;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            font-weight: 700;
            color: rgba(223, 232, 255, 0.82);
        }

        .form-key {
            font-size: 11px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgba(180, 197, 236, 0.7);
            background: rgba(26, 38, 66, 0.6);
            border: 1px solid rgba(135, 165, 233, 0.33);
            border-radius: 999px;
            padding: 6px 16px;
        }

        .form-input,
        .form-textarea {
            width: 100%;
            padding: 18px 20px;
            border: 1px solid rgba(138, 167, 236, 0.25);
            border-radius: 18px;
            font-size: 16px;
            font-family: "SF Pro Text", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #f3f6ff;
            background: rgba(17, 25, 44, 0.72);
            transition: border-color 0.3s ease, background 0.3s ease, box-shadow 0.3s ease;
        }

        .form-input::placeholder,
        .form-textarea::placeholder {
            color: rgba(195, 209, 237, 0.46);
        }

        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: rgba(176, 204, 255, 0.85);
            background: rgba(27, 36, 64, 0.88);
            box-shadow: 0 30px 70px -48px rgba(87, 132, 224, 0.9);
        }

        .form-textarea {
            min-height: 170px;
            resize: vertical;
            line-height: 1.7;
        }

        .form-hint {
            font-size: 12px;
            color: rgba(188, 204, 236, 0.64);
            margin: 0;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            justify-content: flex-end;
            padding-top: 36px;
            border-top: 1px solid rgba(126, 154, 226, 0.22);
            margin-top: 48px;
        }

        .btn-primary {
            padding: 16px 40px;
            border-radius: 999px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            color: #061229;
            background: linear-gradient(135deg, #a9d6ff 0%, #5c93ff 52%, #3b6df2 100%);
            cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            box-shadow: 0 38px 92px -42px rgba(92, 147, 255, 0.95);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 46px 110px -46px rgba(92, 147, 255, 1);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            padding: 16px 32px;
            border-radius: 999px;
            border: 1px solid rgba(155, 188, 255, 0.35);
            background: rgba(19, 28, 48, 0.65);
            color: #eef3fb;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.25s ease, border-color 0.25s ease, transform 0.25s ease, box-shadow 0.25s ease;
            backdrop-filter: blur(16px);
        }

        .btn-secondary:hover {
            background: rgba(43, 60, 96, 0.78);
            border-color: rgba(190, 214, 255, 0.55);
            transform: translateY(-1px);
            box-shadow: 0 26px 64px -44px rgba(44, 66, 108, 0.85);
        }

        @media (max-width: 768px) {
            body.editor-body {
                padding: 48px 0 80px;
            }

            .page-editor {
                padding: 0 24px;
            }

            .editor-card {
                padding: 32px 26px;
                border-radius: 32px;
            }

            .editor-layout {
                flex-direction: column;
                gap: 28px;
            }

            .editor-sidebar {
                width: 100%;
                position: static;
            }

            .sidebar-card {
                flex-direction: column;
                padding: 26px 22px;
            }

            .sidebar-links {
                flex-direction: row;
                overflow-x: auto;
                padding-bottom: 6px;
            }

            .sidebar-link {
                min-width: 200px;
            }

            .form-group {
                padding: 24px 24px 26px;
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
                        min-width: 170px;
                </svg>
                Back to Dashboard
            </a>
                        padding: 30px 24px;

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
                                    <?php 
                                        $meta = $group_meta[$group_key] ?? ['title' => ucfirst($group_key), 'description' => '', 'icon' => '', 'nav_caption' => ''];
                                        $field_count = count($grouped_sections[$group_key]);
                                        $nav_caption = trim($meta['nav_caption'] ?? '');
                                        $sidebar_caption = $nav_caption ? $nav_caption . ' • ' . $field_count . ' fields' : $field_count . ' fields';
                                    ?>
                                    <button type="button" class="sidebar-link" data-target="group-<?php echo htmlspecialchars($group_key); ?>">
                                        <span class="sidebar-dot"></span>
                                        <div>
                                            <span class="sidebar-label"><?php echo htmlspecialchars($meta['title']); ?></span>
                                            <span class="sidebar-caption"><?php echo htmlspecialchars($sidebar_caption); ?></span>
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

            var manualScroll = false;
            var manualTimer = null;

            function setActiveLink(targetId) {
                if (!targetId) {
                    return;
                }
                links.forEach(function(link) {
                    var matches = link.getAttribute('data-target') === targetId;
                    link.classList.toggle('active', matches);
                });
            }

            links.forEach(function(link) {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    var targetId = link.getAttribute('data-target');
                    var section = document.getElementById(targetId);

                    manualScroll = true;
                    clearTimeout(manualTimer);
                    manualTimer = setTimeout(function() {
                        manualScroll = false;
                    }, 700);

                    setActiveLink(targetId);

                    if (section) {
                        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            var initialTarget = links[0] ? links[0].getAttribute('data-target') : '';
            setActiveLink(initialTarget);

            if (!('IntersectionObserver' in window)) {
                return;
            }

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting && !manualScroll) {
                        setActiveLink(entry.target.id);
                    }
                });
            }, {
                rootMargin: '-42% 0px -48% 0px',
                threshold: 0.1
            });

            sections.forEach(function(section) {
                observer.observe(section);
            });
        })();
    </script>
</body>
</html>
