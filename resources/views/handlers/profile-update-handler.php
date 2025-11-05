<?php
// Profile Update Handler
// Handles profile updates, avatar uploads, and company updates
// Expects: $user array, database connection settings

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['update_profile'])) {
    return;
}

$name = trim($_POST['name'] ?? '');
$company_name = trim($_POST['company_name'] ?? '');

if (empty($name)) {
    $error_message = 'Name is required.';
    return;
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Add company_name column if it doesn't exist
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN company_name VARCHAR(255) NULL AFTER name");
    } catch (PDOException $e) {
        // Column already exists, ignore error
    }
    
    // Add avatar column if it doesn't exist
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN avatar VARCHAR(500) NULL AFTER email");
    } catch (PDOException $e) {
        // Column already exists, ignore error
    }
    
    $avatar_path = $user['avatar']; // Keep existing avatar by default
    
    // Handle avatar upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../../public/uploads/avatars/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_name = $_FILES['avatar']['name'];
        $file_size = $_FILES['avatar']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file_ext, $allowed_ext)) {
            $error_message = 'Invalid file type. Allowed: ' . implode(', ', $allowed_ext);
            return;
        }
        
        if ($file_size > $max_size) {
            $error_message = 'File too large. Max size: 5MB';
            return;
        }
        
        // Generate unique filename
        $new_filename = 'avatar_' . $user['id'] . '_' . time() . '.' . $file_ext;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($file_tmp, $upload_path)) {
            $avatar_path = '/uploads/avatars/' . $new_filename;
            
            // Delete old avatar if it exists
            if (!empty($user['avatar']) && file_exists(__DIR__ . '/../../../public' . $user['avatar'])) {
                @unlink(__DIR__ . '/../../../public' . $user['avatar']);
            }
        } else {
            $error_message = 'Failed to upload avatar.';
            return;
        }
    }
    
    // Update user profile
    $stmt = $pdo->prepare('
        UPDATE users 
        SET name = :name, company_name = :company_name, avatar = :avatar, updated_at = NOW()
        WHERE id = :id
    ');
    
    $stmt->execute([
        'name' => $name,
        'company_name' => $company_name,
        'avatar' => $avatar_path,
        'id' => $user['id']
    ]);
    
    // Update session
    $_SESSION['user_name'] = $name;
    $_SESSION['user_avatar'] = $avatar_path;
    
    $success_message = 'Profile updated successfully!';
    
    // Reload user data
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $user['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error_message = 'Failed to update profile: ' . $e->getMessage();
}
