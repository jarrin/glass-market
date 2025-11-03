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
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)};
    
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
        
        $success_message = 'Content updated successfully!';
    }
    ?>
        
        <style> 
            :root {
                --editor-bg: #f5f6fb;
                --editor-surface: #ffffff;
                --editor-text: #20242c;
                --editor-muted: #6b7280;
                --editor-line: #dce2ef;
                --nav-bg: #14161c;
                --accent: #f8c120;
            }

            * {
                box-sizing: border-box;
            }

            body.editor-body {
                margin: 0;
                min-height: 100vh;
                background: var(--editor-bg);
                color: var(--editor-text);
                font-family: "Inter", "SF Pro Text", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            }

            a {
                color: inherit;
            }

            .editor-shell {
                display: flex;
                min-height: 100vh;
            }

            .side-nav {
                width: 260px;
                background: var(--nav-bg);
                color: #f5f6ff;
                padding: 40px 24px;
                display: flex;
                flex-direction: column;
                gap: 36px;
                position: sticky;
                top: 0;
                height: 100vh;
            }

            .side-brand {
                display: flex;
                align-items: center;
                gap: 14px;
            }

            .brand-mark {
                width: 44px;
                height: 44px;
                border-radius: 14px;
                background: var(--accent);
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                color: #1e2026;
                font-size: 18px;
            }

            .brand-text strong {
                display: block;
                font-size: 18px;
                font-weight: 700;
            }

            .brand-text span {
                display: block;
                font-size: 12px;
                letter-spacing: 0.18em;
                text-transform: uppercase;
                color: rgba(255, 255, 255, 0.5);
            }

            .side-menu {
                display: flex;
                flex-direction: column;
                gap: 12px;
                flex: 1;
            }

            .side-item {
                border: none;
                background: rgba(255, 255, 255, 0.04);
                border-radius: 18px;
                padding: 16px 18px;
                display: flex;
                gap: 14px;
                align-items: flex-start;
                width: 100%;
                color: inherit;
                cursor: pointer;
                text-align: left;
                transition: background 0.25s ease, transform 0.25s ease, border-left 0.25s ease;
                border-left: 4px solid transparent;
            }

            .side-item:hover {
                background: rgba(255, 255, 255, 0.08);
                transform: translateX(4px);
            }

            .side-item.active {
                background: rgba(248, 193, 32, 0.16);
                border-left-color: var(--accent);
                box-shadow: 0 18px 36px -24px rgba(248, 193, 32, 0.55);
            }

            .side-dot {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                margin-top: 4px;
                transition: background 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
                flex-shrink: 0;
            }

            .side-item.active .side-dot {
                background: var(--accent);
                box-shadow: 0 0 0 4px rgba(248, 193, 32, 0.25);
                transform: scale(1.1);
            }

            .side-label {
                display: block;
                font-size: 13px;
                letter-spacing: 0.16em;
                text-transform: uppercase;
                font-weight: 600;
                color: rgba(255, 255, 255, 0.82);
            }

            .side-caption {
                display: block;
                font-size: 12px;
                color: rgba(200, 206, 221, 0.75);
                margin-top: 6px;
            }

            .side-footer {
                font-size: 12px;
                line-height: 1.6;
                color: rgba(255, 255, 255, 0.45);
            }

            .editor-main {
                flex: 1;
                padding: 48px 56px;
                display: flex;
                flex-direction: column;
                gap: 32px;
            }

            .topbar {
                background: var(--editor-surface);
                border-radius: 26px;
                padding: 30px 36px;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 32px;
                box-shadow: 0 28px 58px -40px rgba(33, 43, 72, 0.22);
            }

            .topbar-left {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .crumbs {
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 12px;
                letter-spacing: 0.18em;
                text-transform: uppercase;
                color: #9098a9;
            }

            .crumbs a {
                color: #9098a9;
                text-decoration: none;
            }

            .crumbs span {
                color: var(--accent);
                font-weight: 600;
            }

            .topbar-title {
                margin: 0;
                font-size: 30px;
                font-weight: 700;
                letter-spacing: -0.01em;
            }

            .topbar-caption {
                margin: 0;
                font-size: 14px;
                color: #636b7b;
                max-width: 520px;
            }

            .topbar-actions {
                display: flex;
                align-items: center;
                gap: 16px;
            }

            .search-field {
                position: relative;
            }

            .search-field input {
                padding: 12px 44px 12px 20px;
                border-radius: 999px;
                border: 1px solid #d8deee;
                background: #f9fbff;
                font-size: 14px;
                width: 240px;
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
            }

            .search-field input:focus {
                outline: none;
                border-color: var(--accent);
                box-shadow: 0 12px 30px -18px rgba(248, 193, 32, 0.5);
                background: #ffffff;
            }

            .search-field svg {
                position: absolute;
                right: 16px;
                top: 50%;
                transform: translateY(-50%);
                width: 18px;
                height: 18px;
                stroke: #9aa3b5;
            }

            .action-btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 12px 22px;
                border-radius: 999px;
                font-size: 14px;
                font-weight: 600;
                text-decoration: none;
                transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            }

            .action-btn--ghost {
                border: 1px solid #d8deee;
                background: #ffffff;
                color: #495064;
            }

            .action-btn--ghost:hover {
                border-color: var(--accent);
                color: #242830;
                transform: translateY(-1px);
                box-shadow: 0 14px 30px -18px rgba(248, 193, 32, 0.45);
            }

            .alert {
                padding: 18px 26px;
                border-radius: 18px;
                border: 1px solid #ffdca5;
                background: #fff7e0;
                color: #5c4512;
                font-weight: 600;
                font-size: 14px;
                display: flex;
                align-items: center;
                gap: 10px;
                box-shadow: 0 16px 32px -26px rgba(240, 166, 20, 0.35);
            }

            .alert.alert-error {
                border-color: #ffb3b8;
                background: #ffecec;
                color: #9d1c28;
                box-shadow: 0 16px 32px -26px rgba(204, 44, 60, 0.3);
            }

            .group-collection {
                display: flex;
                flex-direction: column;
                gap: 28px;
            }

            .group-card {
                background: var(--editor-surface);
                border-radius: 24px;
                padding: 32px 34px;
                border: 1px solid var(--editor-line);
                box-shadow: 0 30px 60px -48px rgba(39, 49, 76, 0.16);
                position: relative;
                scroll-margin-top: 120px;
            }

            .group-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 24px;
                border-bottom: 1px solid #edf0f7;
                padding-bottom: 20px;
                margin-bottom: 24px;
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
                background: rgba(248, 193, 32, 0.15);
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .group-header h2 {
                margin: 0;
                font-size: 20px;
                font-weight: 700;
                color: #242830;
            }

            .group-header p {
                margin: 6px 0 0;
                font-size: 14px;
                color: #6b7180;
            }

            .group-count {
                font-size: 12px;
                letter-spacing: 0.18em;
                text-transform: uppercase;
                color: #8c93a5;
                font-weight: 600;
            }

            .form-grid {
                display: grid;
                gap: 24px;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }

            .form-field {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .form-field--wide {
                grid-column: span 2;
            }

            .field-label-row {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                align-items: center;
            }

            .field-label {
                font-size: 12px;
                letter-spacing: 0.18em;
                text-transform: uppercase;
                font-weight: 600;
                color: #343a4a;
            }

            .field-key {
                font-size: 11px;
                letter-spacing: 0.16em;
                text-transform: uppercase;
                color: #9aa3b5;
                background: #f2f4fa;
                border-radius: 999px;
                padding: 4px 12px;
            }

            .form-control {
                width: 100%;
                padding: 16px 18px;
                border-radius: 14px;
                border: 1px solid #d8deee;
                background: #fdfdff;
                font-size: 15px;
                color: #2b3141;
                transition: border-color 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
            }

            .form-control:focus {
                border-color: var(--accent);
                box-shadow: 0 18px 42px -32px rgba(248, 193, 32, 0.55);
                outline: none;
                background: #ffffff;
            }

            textarea.form-control {
                min-height: 150px;
                resize: vertical;
                line-height: 1.6;
            }

            .field-hint {
                font-size: 12px;
                color: #8a92a5;
                margin: 0;
                letter-spacing: 0.05em;
                text-transform: uppercase;
            }

            .form-actions {
                display: flex;
                justify-content: flex-end;
                gap: 16px;
                margin-top: 32px;
                padding-top: 28px;
                border-top: 1px solid #e3e7f2;
            }

            .btn-primary {
                padding: 16px 36px;
                border-radius: 999px;
                border: none;
                background: linear-gradient(135deg, var(--accent) 0%, #ffd46b 100%);
                color: #1d1e23;
                font-weight: 700;
                font-size: 15px;
                cursor: pointer;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                box-shadow: 0 38px 92px -40px rgba(248, 193, 32, 0.65);
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 48px 110px -46px rgba(248, 193, 32, 0.7);
            }

            .btn-primary:active {
                transform: translateY(0);
            }

            .btn-secondary {
                padding: 16px 32px;
                border-radius: 999px;
                border: 1px solid #d8deee;
                background: #ffffff;
                color: #3a4253;
                font-size: 15px;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
            }

            .btn-secondary:hover {
                background: #f9fbff;
                border-color: var(--accent);
                transform: translateY(-1px);
                box-shadow: 0 18px 36px -28px rgba(248, 193, 32, 0.35);
            }

            @media (max-width: 1100px) {
                .editor-shell {
                    flex-direction: column;
                }

                .side-nav {
                    width: 100%;
                    height: auto;
                    position: static;
                    flex-direction: column;
                    gap: 24px;
                    padding: 28px 24px;
                }

                .side-menu {
                    flex-direction: row;
                    overflow-x: auto;
                    padding-bottom: 6px;
                }

                .side-item {
                    min-width: 220px;
                }

                .editor-main {
                    padding: 32px 24px 48px;
                }
            }

            @media (max-width: 640px) {
                .topbar {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 24px;
                }

                .topbar-actions {
                    flex-direction: column;
                    align-items: stretch;
                }

                .search-field input {
                    width: 100%;
                }

                .action-btn {
                    justify-content: center;
                }

                .form-actions {
                    flex-direction: column;
                }

                .btn-secondary,
                .btn-primary {
                    width: 100%;
                }
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
