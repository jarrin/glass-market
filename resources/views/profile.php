<?php
session_start();

require_once __DIR__ . '/../../config.php';

// Require authentication
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ' . VIEWS_URL . '/login.php');
    exit;
}

// Database credentials
$db_host = '127.0.0.1';
$db_name = 'glass_market';
$db_user = 'root';
$db_pass = '';

$error_message = '';
$success_message = '';

// Load current user from DB
$user = [
    'id' => $_SESSION['user_id'] ?? null,
    'name' => $_SESSION['user_name'] ?? 'User',
    'email' => $_SESSION['user_email'] ?? '',
    'avatar' => $_SESSION['user_avatar'] ?? '',
    'company_name' => '',
    'created_at' => null,
];

$user_listings_count = 0;
$company = null;

if ($user['id']) {
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare('SELECT id, name, company_name, email, avatar, created_at, company_id FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $user['id']]);
        $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($dbUser) {
            $user = array_merge($user, $dbUser);
            
            // Get company data if user has a company
            if (!empty($dbUser['company_id'])) {
                $stmt = $pdo->prepare('SELECT * FROM companies WHERE id = :company_id LIMIT 1');
                $stmt->execute(['company_id' => $dbUser['company_id']]);
                $company = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
        
        // Get user's listings count
        $stmt = $pdo->prepare('
            SELECT COUNT(*) as count
            FROM listings l
            LEFT JOIN companies c ON l.company_id = c.id
            LEFT JOIN users u ON c.id = u.company_id
            WHERE u.id = :user_id
        ');
        $stmt->execute(['user_id' => $user['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_listings_count = $result['count'] ?? 0;
    } catch (PDOException $e) {
        // Silently continue with session data if DB fails
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    
    if (empty($name)) {
        $error_message = 'Name is required.';
    } else {
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
                $upload_dir = __DIR__ . '/../../public/uploads/avatars/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $new_filename = 'avatar_' . $user['id'] . '_' . time() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                        $avatar_path = PUBLIC_URL . '/uploads/avatars/' . $new_filename;
                    }
                }
            }
            
            $stmt = $pdo->prepare('UPDATE users SET name = :name, company_name = :company_name, avatar = :avatar WHERE id = :id');
            $stmt->execute([
                'name' => $name,
                'company_name' => $company_name,
                'avatar' => $avatar_path,
                'id' => $user['id']
            ]);
            
            // Update session
            $_SESSION['user_name'] = $name;
            $_SESSION['user_avatar'] = $avatar_path;
            $user['name'] = $name;
            $user['company_name'] = $company_name;
            $user['avatar'] = $avatar_path;
            
            $_SESSION['profile_success'] = 'Profile updated successfully!';
            
            // Redirect to prevent form resubmission
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } catch (PDOException $e) {
            $error_message = 'Failed to update profile: ' . $e->getMessage();
        }
    }
}

// Get profile success message from session
if (isset($_SESSION['profile_success'])) {
    if (empty($success_message)) {
        $success_message = $_SESSION['profile_success'];
    }
    unset($_SESSION['profile_success']);
}

// Handle company update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_company'])) {
    $company_name = trim($_POST['company_name'] ?? '');
    $company_type = trim($_POST['company_type'] ?? '');
    $company_website = trim($_POST['company_website'] ?? '');
    $company_phone = trim($_POST['company_phone'] ?? '');
    $company_description = trim($_POST['company_description'] ?? '');
    $company_address1 = trim($_POST['company_address1'] ?? '');
    $company_address2 = trim($_POST['company_address2'] ?? '');
    $company_postal_code = trim($_POST['company_postal_code'] ?? '');
    $company_city = trim($_POST['company_city'] ?? '');
    $company_country = trim($_POST['company_country'] ?? '');
    
    if (empty($company_name)) {
        $error_message = 'Company name is required.';
    } else {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $company_logo = $company['logo'] ?? null; // Keep existing logo by default
            
            // Handle logo upload
            if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../public/uploads/company_logos/';
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $new_filename = 'company_' . $user['company_id'] . '_' . time() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $upload_path)) {
                        $company_logo = '/glass-market/public/uploads/company_logos/' . $new_filename;
                    }
                }
            }
            
            // Check if user has a company
            if (!empty($user['company_id'])) {
                // Update existing company
                $stmt = $pdo->prepare('
                    UPDATE companies 
                    SET name = :name, 
                        company_type = :company_type, 
                        website = :website, 
                        phone = :phone,
                        description = :description,
                        logo = :logo,
                        address_line1 = :address1,
                        address_line2 = :address2,
                        postal_code = :postal_code,
                        city = :city,
                        country = :country
                    WHERE id = :company_id AND owner_user_id = :user_id
                ');
                $stmt->execute([
                    'name' => $company_name,
                    'company_type' => $company_type,
                    'website' => $company_website,
                    'phone' => $company_phone,
                    'description' => $company_description,
                    'logo' => $company_logo,
                    'address1' => $company_address1,
                    'address2' => $company_address2,
                    'postal_code' => $company_postal_code,
                    'city' => $company_city,
                    'country' => $company_country,
                    'company_id' => $user['company_id'],
                    'user_id' => $user['id']
                ]);
            } else {
                // Create new company
                $stmt = $pdo->prepare('
                    INSERT INTO companies (name, company_type, website, phone, description, logo, address_line1, address_line2, postal_code, city, country, owner_user_id, created_at)
                    VALUES (:name, :company_type, :website, :phone, :description, :logo, :address1, :address2, :postal_code, :city, :country, :user_id, NOW())
                ');
                $stmt->execute([
                    'name' => $company_name,
                    'company_type' => $company_type,
                    'website' => $company_website,
                    'phone' => $company_phone,
                    'description' => $company_description,
                    'logo' => $company_logo,
                    'address1' => $company_address1,
                    'address2' => $company_address2,
                    'postal_code' => $company_postal_code,
                    'city' => $company_city,
                    'country' => $company_country,
                    'user_id' => $user['id']
                ]);
                
                $company_id = $pdo->lastInsertId();
                
                // Link user to company
                $stmt = $pdo->prepare('UPDATE users SET company_id = :company_id WHERE id = :user_id');
                $stmt->execute([
                    'company_id' => $company_id,
                    'user_id' => $user['id']
                ]);
                
                $user['company_id'] = $company_id;
            }
            
            // Reload company data
            $stmt = $pdo->prepare('SELECT * FROM companies WHERE id = :company_id LIMIT 1');
            $stmt->execute(['company_id' => $user['company_id']]);
            $company = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Update session company_name for backwards compatibility
            $_SESSION['user_company'] = $company_name;
            
            $success_message = 'Company information updated successfully!';
        } catch (PDOException $e) {
            $error_message = 'Database error: ' . $e->getMessage();
        }
    }
}

// Handle email management
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_email'])) {
    $email_address = trim($_POST['email_address'] ?? '');
    $email_type = $_POST['email_type'] ?? '';
    $email_label = trim($_POST['email_label'] ?? '');
    
    if (empty($email_address) || empty($email_type)) {
        $error_message = 'Email address and type are required.';
    } elseif (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create user_emails table if it doesn't exist
            $pdo->exec("CREATE TABLE IF NOT EXISTS user_emails (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                email_address VARCHAR(255) NOT NULL,
                email_type VARCHAR(50) NOT NULL,
                email_label VARCHAR(100),
                is_verified TINYINT(1) DEFAULT 0,
                is_primary TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            $stmt = $pdo->prepare('
                INSERT INTO user_emails (user_id, email_address, email_type, email_label, is_verified)
                VALUES (:user_id, :email, :type, :label, 1)
            ');
            
            $stmt->execute([
                'user_id' => $user['id'],
                'email' => $email_address,
                'type' => $email_type,
                'label' => $email_label ?: ucfirst($email_type) . ' Email'
            ]);
            
            $success_message = 'Email added successfully!';
        } catch (PDOException $e) {
            $error_message = 'Failed to add email: ' . $e->getMessage();
        }
    }
}

// Handle email update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_email'])) {
    $email_id = $_POST['email_id'] ?? 0;
    $email_address = trim($_POST['email_address'] ?? '');
    $email_type = $_POST['email_type'] ?? '';
    $email_label = trim($_POST['email_label'] ?? '');
    
    if (empty($email_address) || empty($email_type)) {
        $error_message = 'Email address and type are required.';
    } elseif (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare('
                UPDATE user_emails 
                SET email_address = :email, email_type = :type, email_label = :label 
                WHERE id = :id AND user_id = :user_id
            ');
            
            $stmt->execute([
                'email' => $email_address,
                'type' => $email_type,
                'label' => $email_label ?: ucfirst($email_type) . ' Email',
                'id' => $email_id,
                'user_id' => $user['id']
            ]);
            
            $success_message = 'Email updated successfully!';
        } catch (PDOException $e) {
            $error_message = 'Failed to update email: ' . $e->getMessage();
        }
    }
}

// Handle email deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_email'])) {
    $email_id = $_POST['email_id'] ?? 0;
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check how many emails the user has
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM user_emails WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $user['id']]);
        $email_count = $stmt->fetchColumn();
        
        // Allow deletion only if more than 0 emails will remain (plus the primary account email)
        if ($email_count > 1) {
            $stmt = $pdo->prepare('DELETE FROM user_emails WHERE id = :id AND user_id = :user_id');
            $stmt->execute([
                'id' => $email_id,
                'user_id' => $user['id']
            ]);
            $success_message = 'Email deleted successfully!';
        } else {
            $error_message = 'You must have at least one additional email address.';
        }
    } catch (PDOException $e) {
        $error_message = 'Failed to delete email.';
    }
}

// Load user's emails
$user_emails = [];
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare('SELECT * FROM user_emails WHERE user_id = :user_id ORDER BY is_primary DESC, created_at DESC');
    $stmt->execute(['user_id' => $user['id']]);
    $user_emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table might not exist yet
}

// Verify password for viewing card details
$show_card_details = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_password'])) {
    $verify_password = $_POST['verify_password'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $user['id']]);
        $db_user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($db_user_data && password_verify($verify_password, $db_user_data['password'])) {
            $show_card_details = true;
            $success_message = 'Password verified. Card details revealed.';
        } else {
            $error_message = 'Incorrect password.';
        }
    } catch (PDOException $e) {
        $error_message = 'Verification failed.';
    }
}

// Handle add/update credit card
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_card'])) {
    $card_number = preg_replace('/\s+/', '', $_POST['card_number'] ?? '');
    $card_name = trim($_POST['card_name'] ?? '');
    $card_expiry = trim($_POST['card_expiry'] ?? '');
    $card_cvv = trim($_POST['card_cvv'] ?? '');
    
    if (empty($card_number) || empty($card_name) || empty($card_expiry) || empty($card_cvv)) {
        $error_message = 'All card fields are required.';
    } elseif (strlen($card_number) < 13 || strlen($card_number) > 19) {
        $error_message = 'Invalid card number.';
    } else {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create payment_cards table if it doesn't exist
            $pdo->exec("CREATE TABLE IF NOT EXISTS payment_cards (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                card_last4 VARCHAR(4) NOT NULL,
                card_brand VARCHAR(20),
                card_holder VARCHAR(255) NOT NULL,
                card_expiry VARCHAR(7) NOT NULL,
                is_default TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // Store only last 4 digits for security
            $last4 = substr($card_number, -4);
            $brand = 'Visa'; // You can add logic to detect brand
            
            $stmt = $pdo->prepare('
                INSERT INTO payment_cards (user_id, card_last4, card_brand, card_holder, card_expiry, is_default)
                VALUES (:user_id, :last4, :brand, :holder, :expiry, 1)
            ');
            
            $stmt->execute([
                'user_id' => $user['id'],
                'last4' => $last4,
                'brand' => $brand,
                'holder' => $card_name,
                'expiry' => $card_expiry
            ]);
            
            $success_message = 'Credit card added successfully!';
        } catch (PDOException $e) {
            $error_message = 'Failed to add card: ' . $e->getMessage();
        }
    }
}

// Load user's credit cards
$user_cards = [];
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare('SELECT * FROM payment_cards WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC');
    $stmt->execute(['user_id' => $user['id']]);
    $user_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table might not exist yet
}

// Load user's subscriptions
$user_subscriptions = [];
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare('SELECT * FROM user_subscriptions WHERE user_id = :user_id ORDER BY created_at DESC');
    $stmt->execute(['user_id' => $user['id']]);
    $user_subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table might not exist yet
}

// Handle subscription cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_subscription'])) {
    $subscription_id = $_POST['subscription_id'] ?? 0;
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verify the subscription belongs to this user before canceling
        $stmt = $pdo->prepare('UPDATE user_subscriptions SET is_active = 0, updated_at = NOW() WHERE id = :id AND user_id = :user_id');
        $stmt->execute([
            'id' => $subscription_id,
            'user_id' => $user['id']
        ]);
        
        if ($stmt->rowCount() > 0) {
            $success_message = 'Subscription cancelled successfully. You will have access until the end date.';
            
            // Reload subscriptions
            $stmt = $pdo->prepare('SELECT * FROM user_subscriptions WHERE user_id = :user_id ORDER BY created_at DESC');
            $stmt->execute(['user_id' => $user['id']]);
            $user_subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error_message = 'Subscription not found or already cancelled.';
        }
    } catch (PDOException $e) {
        $error_message = 'Failed to cancel subscription: ' . $e->getMessage();
    }
}

// Handle new glass listing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_listing'])) {
    $title = trim($_POST['glass_title'] ?? '');
    $side = $_POST['side'] ?? 'WTS';
    $glass_type = trim($_POST['glass_type'] ?? '');
    $glass_type_other = trim($_POST['glass_type_other'] ?? '');
    $tons = $_POST['glass_tons'] ?? '';
    $recycled = $_POST['recycled'] ?? 'unknown';
    $tested = $_POST['tested'] ?? 'unknown';
    $storage_location = trim($_POST['storage_location'] ?? '');
    $currency = $_POST['currency'] ?? 'EUR';
    $description = trim($_POST['glass_description'] ?? '');

    if ($glass_type === 'other') {
        $glass_type_mapped = $glass_type_other ?: 'Other';
    } else {
        $glass_type_mapped = ucfirst($glass_type) . ' Glass';
        $glass_type_other = null;
    }
    
    if (empty($title) || empty($tons)) {
        $error_message = 'Title and tonnage are required.';
    } elseif (!is_numeric($tons) || $tons <= 0) {
        $error_message = 'Please enter a valid tonnage.';
    } else {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Handle file upload
            $image_path = null;
            if (isset($_FILES['glass_image']) && $_FILES['glass_image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['glass_image'];
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                $max_size = 5 * 1024 * 1024; // 5MB
                
                // Validate file type
                if (!in_array($file['type'], $allowed_types)) {
                    $error_message = 'Invalid file type. Please upload a JPG, PNG, or WebP image.';
                    throw new Exception($error_message);
                }
                
                // Validate file size
                if ($file['size'] > $max_size) {
                    $error_message = 'File is too large. Maximum size is 5MB.';
                    throw new Exception($error_message);
                }
                
                // Create uploads directory if it doesn't exist
                $upload_dir = __DIR__ . '/../../public/uploads/listings';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Generate unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'listing_' . time() . '_' . uniqid() . '.' . $extension;
                $target_path = $upload_dir . '/' . $filename;
                
                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    $image_path = 'uploads/listings/' . $filename;
                } else {
                    $error_message = 'Failed to upload image.';
                    throw new Exception($error_message);
                }
            }
            
            // Get or create company_id for user
            $stmt = $pdo->prepare('SELECT company_id FROM users WHERE id = :user_id');
            $stmt->execute(['user_id' => $user['id']]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $company_id = $user_data['company_id'] ?? null;
            
            // If user doesn't have a company, create one
            if (!$company_id) {
                $company_name = !empty($user['company_name']) ? $user['company_name'] : $user['name'] . "'s Company";
                
                $stmt = $pdo->prepare('
                    INSERT INTO companies (name, company_type, created_at)
                    VALUES (:name, :type, NOW())
                ');
                $stmt->execute([
                    'name' => $company_name,
                    'type' => 'Other'
                ]);
                
                $company_id = $pdo->lastInsertId();
                
                // Update user with company_id
                $stmt = $pdo->prepare('UPDATE users SET company_id = :company_id WHERE id = :user_id');
                $stmt->execute([
                    'company_id' => $company_id,
                    'user_id' => $user['id']
                ]);
            }
            
            // Insert into existing listings table structure
            $stmt = $pdo->prepare('
                INSERT INTO listings (
                    company_id, 
                    side, 
                    glass_type, 
                    glass_type_other,
                    quantity_tons,
                    recycled,
                    tested,
                    storage_location,
                    currency,
                    quantity_note,
                    quality_notes,
                    image_path,
                    published,
                    created_at
                )
                VALUES (
                    :company_id, :side, :glass_type, :glass_type_other, :quantity_tons, :recycled, :tested, :storage_location, :currency, :quantity_note, :quality_notes, :image_path, :published, NOW()
                )
            ');
            
            $stmt->execute([
                'company_id' => $company_id,
                'side' => $side,
                'glass_type' => $glass_type_mapped,
                'glass_type_other' => $glass_type_other,
                'quantity_tons' => $tons,
                'recycled' => $recycled,
                'tested' => $tested,
                'storage_location' => $storage_location,
                'currency' => $currency,
                'quantity_note' => $title,
                'quality_notes' => $description,
                'image_path' => $image_path,
                'published' => 1 // Published immediately
            ]);
            
            $_SESSION['listing_success'] = 'Glass listing created successfully! Your listing is now live on the marketplace.';
            
            // Redirect to prevent form resubmission
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } catch (Exception $e) {
            $error_message = 'Failed to create listing: ' . $e->getMessage();
        } catch (PDOException $e) {
            $error_message = 'Failed to create listing: ' . $e->getMessage();
        }
    }
}

// Get listing success message from session
if (isset($_SESSION['listing_success'])) {
    $success_message = $_SESSION['listing_success'];
    unset($_SESSION['listing_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Glass Market</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
    <style>
        :root {
            --profile-bg: #f5f5f7;
            --profile-text: #1d1d1f;
            --profile-muted: #6e6e73;
            --profile-accent: #2f6df5;
            --profile-card-bg: rgba(255, 255, 255, 0.9);
            --profile-border: rgba(15, 23, 42, 0.08);
        }

        body {
            font-family: "SF Pro Display", "SF Pro Text", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--profile-bg);
            color: var(--profile-text);
            margin: 0;
            line-height: 1.6;
        }

        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 32px 60px;
        }

        .profile-header {
            background: var(--profile-card-bg);
            border: 1px solid var(--profile-border);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 32px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            gap: 32px;
        }

        .profile-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(47, 109, 245, 0.1) 0%, rgba(30, 77, 184, 0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 3px solid rgba(47, 109, 245, 0.2);
            flex-shrink: 0;
        }

        .profile-avatar-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-avatar-large svg {
            width: 50px;
            height: 50px;
            color: var(--profile-accent);
        }

        .profile-info {
            flex: 1;
        }

        .profile-info h1 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: var(--profile-text);
            letter-spacing: -0.02em;
        }

        .profile-info .email {
            font-size: 15px;
            color: var(--profile-muted);
            margin-bottom: 12px;
        }

        .profile-stats {
            display: flex;
            gap: 32px;
            margin-top: 16px;
        }

        .profile-stat {
            display: flex;
            flex-direction: column;
        }

        .profile-stat-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--profile-text);
        }

        .profile-stat-label {
            font-size: 13px;
            color: var(--profile-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tabs */
        .profile-tabs {
            background: var(--profile-card-bg);
            border: 1px solid var(--profile-border);
            border-radius: 16px 16px 0 0;
            padding: 0 32px;
            display: flex;
            gap: 8px;
            overflow-x: auto;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .profile-tab {
            padding: 16px 24px;
            font-size: 15px;
            font-weight: 600;
            color: var(--profile-muted);
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .profile-tab:hover {
            color: var(--profile-text);
            background: rgba(47, 109, 245, 0.04);
        }

        .profile-tab.active {
            color: var(--profile-accent);
            border-bottom-color: var(--profile-accent);
        }

        .profile-content {
            background: var(--profile-card-bg);
            border: 1px solid var(--profile-border);
            border-top: none;
            border-radius: 0 0 16px 16px;
            padding: 40px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            min-height: 400px;
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--profile-text);
            margin-bottom: 24px;
            letter-spacing: -0.01em;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-danger {
            background: rgba(220, 38, 38, 0.1);
            color: #991b1b;
            border: 1px solid rgba(220, 38, 38, 0.2);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--profile-text);
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            font-size: 15px;
            border: 1px solid var(--profile-border);
            border-radius: 12px;
            background: white;
            transition: all 0.2s ease;
            font-family: inherit;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--profile-accent);
            box-shadow: 0 0 0 3px rgba(47, 109, 245, 0.1);
        }

        .form-group input:disabled {
            background: #f5f5f5;
            color: #999;
            cursor: not-allowed;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            padding: 12px 24px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--profile-accent);
            color: white;
            box-shadow: 0 4px 12px rgba(47, 109, 245, 0.2);
        }

        .btn-primary:hover {
            background: #1e4db8;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(47, 109, 245, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: var(--profile-muted);
            border: 1px solid var(--profile-border);
        }

        .btn-secondary:hover {
            color: var(--profile-text);
            border-color: var(--profile-text);
            background: rgba(0, 0, 0, 0.02);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .profile-container {
                padding: 80px 20px 40px;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
                padding: 32px 24px;
            }

            .profile-stats {
                justify-content: center;
                flex-wrap: wrap;
            }

            .profile-tabs {
                padding: 0 16px;
                gap: 4px;
            }

            .profile-tab {
                padding: 12px 16px;
                font-size: 14px;
            }

            .profile-content {
                padding: 24px 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../includes/navbar.php'; ?>
    <?php include __DIR__ . '/../../includes/subscription-notification.php'; ?>

    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar-large">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="<?php echo htmlspecialchars($user['name']); ?>">
                <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                <div class="email"><?php echo htmlspecialchars($user['email']); ?></div>
                <?php if (!empty($user['company_name'])): ?>
                    <div style="display: inline-block; background: white; border: 1px solid var(--profile-border); padding: 6px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; margin-top: 8px; color: var(--profile-text);">
                        <?php echo htmlspecialchars($user['company_name']); ?>
                    </div>
                <?php endif; ?>
                <div class="profile-stats">
                    <div class="profile-stat">
                        <div class="profile-stat-number"><?php echo $user_listings_count; ?></div>
                        <div class="profile-stat-label">Listings</div>
                    </div>
                    <?php if ($user['created_at']): ?>
                        <div class="profile-stat">
                            <div class="profile-stat-number"><?php echo date('Y', strtotime($user['created_at'])); ?></div>
                            <div class="profile-stat-label">Member Since</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <a href="<?php echo VIEWS_URL; ?>/logout.php" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                    </svg>
                    Logout
                </a>
            </div>
        </div>

        <!-- Alerts -->
        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <!-- Tabs Navigation -->
        <div class="profile-tabs">
            <button class="profile-tab active" data-tab="overview">Overview</button>
            <button class="profile-tab" data-tab="listings">My Listings</button>
            <button class="profile-tab" data-tab="company">Company</button>
            <button class="profile-tab" data-tab="profile">Edit Profile</button>
            <button class="profile-tab" data-tab="subscription">Subscription</button>
        </div>

        <!-- Tab Content -->
        <div class="profile-content">
            <!-- Overview Tab -->
            <div class="tab-panel active" id="tab-overview">
                <h2 class="section-title">Account Overview</h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 32px;">
                    <div style="background: white; border: 1px solid var(--profile-border); border-radius: 16px; padding: 24px; text-align: center; transition: all 0.2s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow=''">
                        <div style="font-size: 32px; font-weight: 700; color: var(--profile-text); margin-bottom: 8px;"><?php echo $user_listings_count; ?></div>
                        <div style="font-size: 13px; color: var(--profile-muted); text-transform: uppercase; letter-spacing: 0.5px;">Active Listings</div>
                    </div>
                    <div style="background: white; border: 1px solid var(--profile-border); border-radius: 16px; padding: 24px; text-align: center; transition: all 0.2s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow=''">
                        <div style="font-size: 32px; font-weight: 700; color: var(--profile-text); margin-bottom: 8px;">0</div>
                        <div style="font-size: 13px; color: var(--profile-muted); text-transform: uppercase; letter-spacing: 0.5px;">Completed Orders</div>
                    </div>
                    <div style="background: white; border: 1px solid var(--profile-border); border-radius: 16px; padding: 24px; text-align: center; transition: all 0.2s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow=''">
                        <div style="font-size: 32px; font-weight: 700; color: var(--profile-text); margin-bottom: 8px;">0</div>
                        <div style="font-size: 13px; color: var(--profile-muted); text-transform: uppercase; letter-spacing: 0.5px;">Reviews</div>
                    </div>
                </div>

                <div style="background: white; border: 1px solid var(--profile-border); border-radius: 12px; padding: 24px; margin-top: 24px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 16px 0; color: var(--profile-text);">Quick Actions</h3>
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <button class="btn btn-primary" onclick="document.querySelector('[data-tab=listings]').click()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Create New Listing
                        </button>
                        <button class="btn btn-secondary" onclick="document.querySelector('[data-tab=profile]').click()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            Edit Profile
                        </button>
                    </div>
                </div>
            </div>

            <!-- My Listings Tab -->
            <div class="tab-panel" id="tab-listings">
                <h2 class="section-title">Add New Glass Listing</h2>
                <p style="font-size: 14px; color: var(--profile-muted); margin-bottom: 24px;">List your green, white, or brown glass. Price will be negotiated after listing.</p>
                
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="company_name_display">Company</label>
                        <input
                            type="text"
                            id="company_name_display"
                            value="<?php echo htmlspecialchars(!empty($user['company_name']) ? $user['company_name'] : $user['name'] . "'s Company"); ?>"
                            disabled
                            style="background: #f5f5f5; color: #666; cursor: not-allowed;"
                        >
                        <small style="font-size: 11px; color: #999;">Update your company name in the profile section below</small>
                    </div>

                    <div class="form-group">
                        <label for="side">Listing Type</label>
                        <select
                            id="side"
                            name="side"
                            style="width: 100%; padding: 12px 14px; font-size: 14px; border: 1.5px solid #ddd; border-radius: 6px; background: #fafafa;"
                            required
                        >
                            <option value="WTS">Want To Sell (WTS)</option>
                            <option value="WTB">Want To Buy (WTB)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="glass_type">Type of Glass (Cullet)</label>
                        <select
                            id="glass_type"
                            name="glass_type"
                            style="width: 100%; padding: 12px 14px; font-size: 14px; border: 1.5px solid #ddd; border-radius: 6px; background: #fafafa;"
                            onchange="toggleOtherGlassType(this)"
                            required
                        >
                            <option value="">Select glass type...</option>
                            <option value="Clear Cullet">Clear Cullet</option>
                            <option value="Green Cullet">Green Cullet</option>
                            <option value="Brown Cullet">Brown Cullet</option>
                            <option value="Amber Cullet">Amber Cullet</option>
                            <option value="Mixed Cullet">Mixed Cullet</option>
                            <option value="Flint Cullet">Flint Cullet</option>
                            <option value="other">Other (specify)</option>
                        </select>
                    </div>

                    <div class="form-group" id="glass_type_other_container" style="display: none;">
                        <label for="glass_type_other">Specify Glass Type</label>
                        <input
                            type="text"
                            id="glass_type_other"
                            name="glass_type_other"
                            placeholder="Enter custom glass type"
                        >
                    </div>

                    <div class="form-group">
                        <label for="glass_title">Listing Title</label>
                        <input
                            type="text"
                            id="glass_title"
                            name="glass_title"
                            placeholder="e.g., Premium Green Cullet - High Quality"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="glass_tons">Quantity (in tons)</label>
                        <input
                            type="number"
                            id="glass_tons"
                            name="glass_tons"
                            placeholder="0.00"
                            step="0.01"
                            min="0"
                            required
                        >
                        <small style="font-size: 11px; color: #999;">Specify the total weight in tons</small>
                    </div>
                    <div class="form-group">
                        <label for="storage_location">Storage Location</label>
                        <input type="text" id="storage_location" name="storage_location" placeholder="Where is the glass stored?"/>
                    </div>
                    <div class="form-group">
                        <label for="currency">Currency</label>
                        <select id="currency" name="currency" required>
                            <option value="EUR" selected>EUR</option>
                            <option value="USD">USD</option>
                            <option value="GBP">GBP</option>
                        </select>
                    </div>
                    <!-- Bestaande velden: -->
                    <div class="form-group">
                        <label for="recycled">Recycled Status</label>
                        <select
                            id="recycled"
                            name="recycled"
                            style="width: 100%; padding: 12px 14px; font-size: 14px; border: 1.5px solid #ddd; border-radius: 6px; background: #fafafa;"
                            required
                        >
                            <option value="recycled">Recycled</option>
                            <option value="not_recycled">Not Recycled</option>
                            <option value="unknown">Unknown</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tested">Testing Status</label>
                        <select
                            id="tested"
                            name="tested"
                            style="width: 100%; padding: 12px 14px; font-size: 14px; border: 1.5px solid #ddd; border-radius: 6px; background: #fafafa;"
                            required
                        >
                            <option value="tested">Tested</option>
                            <option value="untested">Untested</option>
                            <option value="unknown">Unknown</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="storage_location">Storage Location</label>
                        <input
                            type="text"
                            id="storage_location"
                            name="storage_location"
                            placeholder="e.g., Rotterdam warehouse, Dock 5"
                        >
                        <small style="font-size: 11px; color: #999;">Optional - Where is the glass currently stored?</small>
                    </div>

                    <div class="form-group">
                        <label for="price_text">Price</label>
                        <div style="display: grid; grid-template-columns: 1fr 120px; gap: 12px;">
                            <input
                                type="text"
                                id="price_text"
                                name="price_text"
                                placeholder="e.g., €120/ton CIF or Negotiable"
                            >
                            <select
                                id="currency"
                                name="currency"
                                style="width: 100%; padding: 12px 14px; font-size: 14px; border: 1.5px solid #ddd; border-radius: 6px; background: #fafafa;"
                            >
                                <option value="EUR" selected>EUR (€)</option>
                                <option value="USD">USD ($)</option>
                                <option value="GBP">GBP (£)</option>
                                <option value="CNY">CNY (¥)</option>
                                <option value="JPY">JPY (¥)</option>
                            </select>
                        </div>
                        <small style="font-size: 11px; color: #999;">Optional - Enter price or leave blank for negotiation</small>
                    </div>

                    <div class="form-group">
                        <label for="glass_description">Quality Notes / Description</label>
                        <textarea id="glass_description" name="glass_description" rows="4" placeholder="Describe the glass quality, condition, source, etc..." style="width:100%;padding:12px 14px;font-size:14px;border:1.5px solid #ddd;border-radius:6px;background:#fafafa;font-family:inherit;resize:vertical;"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="glass_image">Product Image</label>
                        <input type="file" id="glass_image" name="glass_image" accept="image/jpeg,image/jpg,image/png,image/webp" style="width:100%;padding:12px 14px;font-size:14px;border:1.5px solid #ddd;border-radius:6px;background:#fafafa;">
                        <small style="font-size:11px;color:#999;">Upload a photo of your glass (JPG, PNG, or WebP - Max 5MB)</small>
                    </div>

                    <div style="background: #fffbeb; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 12px; color: #92400e;">
                        💡 <strong>Note:</strong> Your listing will be published immediately and visible to all marketplace users.
                    </div>
                    <div style="margin-top:24px;">
                        <button type="submit" name="add_listing" class="btn btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Create Listing
                        </button>
                    </div>
                </form>
            </div>

            <!-- Company Tab -->
            <div class="tab-panel" id="tab-company">
                <h2 class="section-title">Company Information</h2>
                
                <?php if ($success_message && isset($_POST['update_company'])): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message && isset($_POST['update_company'])): ?>
                    <div class="alert alert-error">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data" class="profile-form">
                    
                    <!-- Company Logo -->
                    <div class="form-group">
                        <label>Company Logo</label>
                        <?php if (!empty($company['logo'])): ?>
                            <div style="margin-bottom: 12px;">
                                <img src="<?php echo htmlspecialchars($company['logo']); ?>" 
                                     alt="Company Logo" 
                                     style="max-width: 200px; max-height: 100px; border-radius: 8px; border: 1px solid var(--profile-border);">
                            </div>
                        <?php endif; ?>
                        <input 
                            type="file" 
                            id="company_logo" 
                            name="company_logo" 
                            accept="image/*"
                            style="padding: 10px; font-size: 14px; border: 1.5px solid var(--profile-border); border-radius: 8px; width: 100%;"
                        >
                        <small style="color: var(--profile-muted); display: block; margin-top: 6px;">
                            Recommended: 400x200px, PNG or JPG
                        </small>
                    </div>

                    <div class="form-grid">
                        <!-- Company Name -->
                        <div class="form-group">
                            <label for="company_name">Company Name <span style="color: #ef4444;">*</span></label>
                            <input 
                                type="text" 
                                id="company_name" 
                                name="company_name" 
                                value="<?php echo htmlspecialchars($company['name'] ?? $user['company_name'] ?? ''); ?>"
                                required
                                placeholder="Enter company name"
                            >
                        </div>

                        <!-- Company Type -->
                        <div class="form-group">
                            <label for="company_type">Company Type</label>
                            <select id="company_type" name="company_type">
                                <option value="Glass Recycle Plant" <?php echo ($company['company_type'] ?? '') === 'Glass Recycle Plant' ? 'selected' : ''; ?>>Glass Recycle Plant</option>
                                <option value="Glass Factory" <?php echo ($company['company_type'] ?? '') === 'Glass Factory' ? 'selected' : ''; ?>>Glass Factory</option>
                                <option value="Collection Company" <?php echo ($company['company_type'] ?? '') === 'Collection Company' ? 'selected' : ''; ?>>Collection Company</option>
                                <option value="Trader" <?php echo ($company['company_type'] ?? '') === 'Trader' ? 'selected' : ''; ?>>Trader</option>
                                <option value="Other" <?php echo ($company['company_type'] ?? 'Other') === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <!-- Website -->
                        <div class="form-group">
                            <label for="company_website">Website</label>
                            <input 
                                type="url" 
                                id="company_website" 
                                name="company_website" 
                                value="<?php echo htmlspecialchars($company['website'] ?? ''); ?>"
                                placeholder="https://example.com"
                            >
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="company_phone">Phone Number</label>
                            <input 
                                type="tel" 
                                id="company_phone" 
                                name="company_phone" 
                                value="<?php echo htmlspecialchars($company['phone'] ?? ''); ?>"
                                placeholder="+31 20 123 4567"
                            >
                        </div>
                    </div>

                    <!-- Company Description -->
                    <div class="form-group">
                        <label for="company_description">Description</label>
                        <textarea 
                            id="company_description" 
                            name="company_description" 
                            rows="4"
                            placeholder="Tell us about your company..."
                            style="resize: vertical;"
                        ><?php echo htmlspecialchars($company['description'] ?? ''); ?></textarea>
                    </div>

                    <h3 style="font-size: 18px; font-weight: 600; margin: 32px 0 16px; color: var(--profile-text);">Company Address</h3>

                    <div class="form-grid">
                        <!-- Address Line 1 -->
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="company_address1">Address Line 1</label>
                            <input 
                                type="text" 
                                id="company_address1" 
                                name="company_address1" 
                                value="<?php echo htmlspecialchars($company['address_line1'] ?? ''); ?>"
                                placeholder="Street address"
                            >
                        </div>

                        <!-- Address Line 2 -->
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="company_address2">Address Line 2</label>
                            <input 
                                type="text" 
                                id="company_address2" 
                                name="company_address2" 
                                value="<?php echo htmlspecialchars($company['address_line2'] ?? ''); ?>"
                                placeholder="Apartment, suite, etc. (optional)"
                            >
                        </div>

                        <!-- Postal Code -->
                        <div class="form-group">
                            <label for="company_postal_code">Postal Code</label>
                            <input 
                                type="text" 
                                id="company_postal_code" 
                                name="company_postal_code" 
                                value="<?php echo htmlspecialchars($company['postal_code'] ?? ''); ?>"
                                placeholder="1000 AA"
                            >
                        </div>

                        <!-- City -->
                        <div class="form-group">
                            <label for="company_city">City</label>
                            <input 
                                type="text" 
                                id="company_city" 
                                name="company_city" 
                                value="<?php echo htmlspecialchars($company['city'] ?? ''); ?>"
                                placeholder="Amsterdam"
                            >
                        </div>

                        <!-- Country -->
                        <div class="form-group">
                            <label for="company_country">Country</label>
                            <input 
                                type="text" 
                                id="company_country" 
                                name="company_country" 
                                value="<?php echo htmlspecialchars($company['country'] ?? ''); ?>"
                                placeholder="Netherlands"
                            >
                        </div>
                    </div>

                    <button type="submit" name="update_company" class="btn btn-primary" style="margin-top: 24px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Save Company Information
                    </button>
                </form>
            </div>

            <!-- Edit Profile Tab -->
            <div class="tab-panel" id="tab-profile">
                <h2 class="section-title">Edit Profile</h2>
                                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="avatar">Profile Picture</label>
                        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 12px;">
                            <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                <?php if (!empty($user['avatar'])): ?>
                                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Current avatar" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div style="flex: 1;">
                                <input
                                    type="file"
                                    id="avatar"
                                    name="avatar"
                                    accept="image/*"
                                    style="width: 100%; padding: 10px; font-size: 13px; border: 1.5px solid #ddd; border-radius: 6px; background: #fafafa;"
                                >
                                <small style="font-size: 11px; color: #999; display: block; margin-top: 6px;">
                                    JPG, PNG, GIF, or WEBP. Max 5MB.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?php echo htmlspecialchars($user['name']); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="company_name">Company Name</label>
                        <input
                            type="text"
                            id="company_name"
                            name="company_name"
                            value="<?php echo htmlspecialchars($user['company_name'] ?? ''); ?>"
                            placeholder="Your company name"
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo htmlspecialchars($user['email']); ?>"
                            disabled
                        >
                        <small style="color: #999; font-size: 11px;">Email cannot be changed</small>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 24px;">
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            Save Changes
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="location.reload()">Cancel</button>
                    </div>
                </form>

                <!-- Email & Payment divider -->
                <div style="margin: 48px 0; border-top: 1px solid var(--profile-border);"></div>

                <!-- Email Management (collapsed in profile tab) -->
                <details style="margin-bottom: 24px;">
                    <summary style="cursor: pointer; font-weight: 600; font-size: 16px; padding: 16px; background: rgba(47, 109, 245, 0.05); border-radius: 12px; list-style: none; display: flex; align-items: center; gap: 8px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        Email Addresses Management
                    </summary>
                    <div style="padding: 20px 0;">
                        <p style="font-size: 14px; color: var(--profile-muted); margin-bottom: 20px;">Manage your email addresses for different purposes (notifications, communications, etc.)</p>
                        
            <!-- Email Management -->
            <div style="display: none;">
                <div class="section-title">Email Addresses</div>
                <p style="font-size: 13px; color: #666; margin-bottom: 20px;">Manage your email addresses for different purposes (notifications, communications, etc.)</p>
                
                <!-- Primary Email -->
                <div style="background: #f0f9ff; padding: 16px; border-radius: 8px; border: 1.5px solid #bfdbfe; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <strong style="font-size: 14px;">Primary Email</strong>
                        <span style="background: #2563eb; color: white; padding: 2px 8px; border-radius: 4px; font-size: 10px; margin-left: 8px;">ACCOUNT</span>
                    </div>
                    <div style="font-size: 13px; color: #1e40af; margin-left: 24px;">
                        <?php echo htmlspecialchars($user['email']); ?>
                    </div>
                    <div style="font-size: 11px; color: #64748b; margin-left: 24px; margin-top: 4px;">
                        Used for login and account recovery
                    </div>
                </div>

                <!-- Additional Emails -->
                <?php if (count($user_emails) > 0): ?>
                    <?php foreach ($user_emails as $email): ?>
                        <div style="background: #fafafa; padding: 16px; border-radius: 8px; border: 1.5px solid #e0e0e0; margin-bottom: 12px;" id="email-<?php echo $email['id']; ?>">
                            <!-- Display Mode -->
                            <div class="email-display-<?php echo $email['id']; ?>">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                <polyline points="22,6 12,13 2,6"></polyline>
                                            </svg>
                                            <strong style="font-size: 14px;"><?php echo htmlspecialchars($email['email_label']); ?></strong>
                                            <span style="background: #666; color: white; padding: 2px 8px; border-radius: 4px; font-size: 10px; text-transform: uppercase;">
                                                <?php echo htmlspecialchars($email['email_type']); ?>
                                            </span>
                                        </div>
                                        <div style="font-size: 13px; color: #666; margin-left: 24px;">
                                            <?php echo htmlspecialchars($email['email_address']); ?>
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <?php if ($email['is_verified']): ?>
                                            <span style="color: #16a34a; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                </svg>
                                                Verified
                                            </span>
                                        <?php endif; ?>
                                        <button onclick="toggleEditEmail(<?php echo $email['id']; ?>)" style="background: transparent; border: none; cursor: pointer; color: #2563eb; padding: 4px;" title="Edit email">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </button>
                                        <?php if (count($user_emails) > 1): ?>
                                            <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this email?');">
                                                <input type="hidden" name="email_id" value="<?php echo $email['id']; ?>">
                                                <button type="submit" name="delete_email" style="background: transparent; border: none; cursor: pointer; color: #dc2626; padding: 4px;" title="Delete email">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Mode -->
                            <div class="email-edit-<?php echo $email['id']; ?>" style="display: none;">
                                <form method="POST" action="">
                                    <input type="hidden" name="email_id" value="<?php echo $email['id']; ?>">
                                    
                                    <div class="form-group" style="margin-bottom: 12px;">
                                        <label for="edit_email_address_<?php echo $email['id']; ?>" style="font-size: 11px;">Email Address</label>
                                        <input
                                            type="email"
                                            id="edit_email_address_<?php echo $email['id']; ?>"
                                            name="email_address"
                                            value="<?php echo htmlspecialchars($email['email_address']); ?>"
                                            required
                                            style="margin-bottom: 0;"
                                        >
                                    </div>

                                    <div class="form-group" style="margin-bottom: 12px;">
                                        <label for="edit_email_type_<?php echo $email['id']; ?>" style="font-size: 11px;">Email Type</label>
                                        <select
                                            id="edit_email_type_<?php echo $email['id']; ?>"
                                            name="email_type"
                                            style="width: 100%; padding: 12px 14px; font-size: 14px; border: 1.5px solid #ddd; border-radius: 6px; background: #fafafa;"
                                            required
                                        >
                                            <option value="notifications" <?php echo $email['email_type'] === 'notifications' ? 'selected' : ''; ?>>Notifications</option>
                                            <option value="communications" <?php echo $email['email_type'] === 'communications' ? 'selected' : ''; ?>>Communications</option>
                                            <option value="billing" <?php echo $email['email_type'] === 'billing' ? 'selected' : ''; ?>>Billing</option>
                                            <option value="support" <?php echo $email['email_type'] === 'support' ? 'selected' : ''; ?>>Support</option>
                                            <option value="marketing" <?php echo $email['email_type'] === 'marketing' ? 'selected' : ''; ?>>Marketing</option>
                                            <option value="other" <?php echo $email['email_type'] === 'other' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>

                                    <div class="form-group" style="margin-bottom: 12px;">
                                        <label for="edit_email_label_<?php echo $email['id']; ?>" style="font-size: 11px;">Custom Label</label>
                                        <input
                                            type="text"
                                            id="edit_email_label_<?php echo $email['id']; ?>"
                                            name="email_label"
                                            value="<?php echo htmlspecialchars($email['email_label']); ?>"
                                            style="margin-bottom: 0;"
                                        >
                                    </div>

                                    <div style="display: flex; gap: 8px;">
                                        <button type="submit" name="update_email" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">Save</button>
                                        <button type="button" onclick="toggleEditEmail(<?php echo $email['id']; ?>)" class="btn btn-secondary" style="font-size: 12px; padding: 8px 16px;">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Add New Email Form -->
                <details style="margin-top: 20px;">
                    <summary style="cursor: pointer; font-weight: 600; font-size: 14px; padding: 12px; background: #f0f0f0; border-radius: 6px;">+ Add New Email Address</summary>
                    <form method="POST" action="" style="margin-top: 16px; padding: 20px; background: #f9f9f9; border-radius: 8px;">
                        <div class="form-group">
                            <label for="email_address">Email Address</label>
                            <input
                                type="email"
                                id="email_address"
                                name="email_address"
                                placeholder="notifications@example.com"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="email_type">Email Type</label>
                            <select
                                id="email_type"
                                name="email_type"
                                style="width: 100%; padding: 12px 14px; font-size: 14px; border: 1.5px solid #ddd; border-radius: 6px; background: #fafafa;"
                                required
                            >
                                <option value="">Select type...</option>
                                <option value="notifications">Notifications</option>
                                <option value="communications">Communications</option>
                                <option value="billing">Billing</option>
                                <option value="support">Support</option>
                                <option value="marketing">Marketing</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="email_label">Custom Label (Optional)</label>
                            <input
                                type="text"
                                id="email_label"
                                name="email_label"
                                placeholder="e.g., New Listings Alerts"
                            >
                            <small style="font-size: 11px; color: #999;">Give this email a friendly name</small>
                        </div>

                        <button type="submit" name="add_email" class="btn btn-primary">Add Email</button>
                    </form>
                </details>
            </div>

            <!-- Payment Information -->
            <div class="profile-section">
                <div class="section-title">Payment Information</div>
                
                <?php if (!$show_card_details && count($user_cards) > 0): ?>
                    <!-- Password verification to view cards -->
                    <div style="background: #fffbeb; padding: 20px; border-radius: 8px; border: 1.5px solid #fde68a; margin-bottom: 20px;">
                        <p style="font-size: 13px; margin-bottom: 12px; color: #92400e;">🔒 Enter your password to view card details</p>
                        <form method="POST" action="" style="display: flex; gap: 12px; align-items: flex-end;">
                            <div class="form-group" style="flex: 1; margin-bottom: 0;">
                                <input
                                    type="password"
                                    name="verify_password"
                                    placeholder="Enter your password"
                                    required
                                    style="margin-bottom: 0;"
                                >
                            </div>
                            <button type="submit" class="btn btn-primary" style="margin-bottom: 0;">Verify</button>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Display saved cards -->
                <?php if (count($user_cards) > 0): ?>
                    <?php foreach ($user_cards as $card): ?>
                        <div style="background: #fafafa; padding: 20px; border-radius: 8px; border: 1.5px solid #e0e0e0; margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">
                                        <?php echo htmlspecialchars($card['card_brand']); ?> 
                                        <?php if ($card['is_default']): ?>
                                            <span style="background: #000; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 10px; margin-left: 8px;">DEFAULT</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size: 13px; color: #666;">
                                        <?php if ($show_card_details): ?>
                                            Card holder: <?php echo htmlspecialchars($card['card_holder']); ?><br>
                                            •••• •••• •••• <?php echo htmlspecialchars($card['card_last4']); ?><br>
                                            Expires: <?php echo htmlspecialchars($card['card_expiry']); ?>
                                        <?php else: ?>
                                            •••• •••• •••• <?php echo htmlspecialchars($card['card_last4']); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <svg width="40" height="25" viewBox="0 0 48 32" fill="none">
                                    <rect width="48" height="32" rx="4" fill="#1434CB"/>
                                    <path d="M21.5 20.5L19.5 11.5H17L19.5 20.5H21.5Z" fill="white"/>
                                </svg>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="background: #fafafa; padding: 20px; border-radius: 8px; border: 1.5px solid #e0e0e0; text-align: center; color: #666;">
                        No payment cards on file. Add one below.
                    </div>
                <?php endif; ?>

                <!-- Add new card form -->
                <details style="margin-top: 20px;">
                    <summary style="cursor: pointer; font-weight: 600; font-size: 14px; padding: 12px; background: #f0f0f0; border-radius: 6px;">+ Add New Credit Card</summary>
                    <form method="POST" action="" style="margin-top: 16px; padding: 20px; background: #f9f9f9; border-radius: 8px;">
                        <div class="form-group">
                            <label for="card_number">Card Number</label>
                            <input
                                type="text"
                                id="card_number"
                                name="card_number"
                                placeholder="1234 5678 9012 3456"
                                maxlength="19"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="card_name">Cardholder Name</label>
                            <input
                                type="text"
                                id="card_name"
                                name="card_name"
                                placeholder="JOHN DOE"
                                style="text-transform: uppercase;"
                                required
                            >
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label for="card_expiry">Expiry Date</label>
                                <input
                                    type="text"
                                    id="card_expiry"
                                    name="card_expiry"
                                    placeholder="MM/YY"
                                    maxlength="5"
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label for="card_cvv">CVV</label>
                                <input
                                    type="text"
                                    id="card_cvv"
                                    name="card_cvv"
                                    placeholder="123"
                                    maxlength="4"
                                    required
                                >
                            </div>
                        </div>

                        <button type="submit" name="add_card" class="btn btn-primary">Add Card</button>
                    </form>
                </details>

                <div style="font-size: 11px; color: #999; margin-top: 16px; text-align: center;">
                    🔒 Your payment information is encrypted and secure
                </div>
            </div>

            <!-- Subscription Tab -->
            <div class="tab-panel" id="tab-subscription">
                <h2 class="section-title">My Subscriptions</h2>
                
                <?php if ($success_message && isset($_POST['cancel_subscription'])): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message && isset($_POST['cancel_subscription'])): ?>
                    <div class="alert alert-error">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <p style="font-size: 14px; color: var(--profile-muted); margin-bottom: 24px;">Manage your active subscriptions and payment plans</p>
                
                <?php if (count($user_subscriptions) > 0): ?>
                    <?php foreach ($user_subscriptions as $subscription): ?>
                        <?php 
                            $is_active = $subscription['is_active'] == 1;
                            $is_trial = $subscription['is_trial'] == 1;
                            $start_date = new DateTime($subscription['start_date']);
                            $end_date = new DateTime($subscription['end_date']);
                            $today = new DateTime();
                            $days_remaining = $today < $end_date ? $today->diff($end_date)->days : 0;
                            $is_expired = $today > $end_date;
                        ?>
                        <div style="background: <?php echo $is_active && !$is_expired ? '#f0fdf4' : 'white'; ?>; padding: 28px; border-radius: 16px; border: 1.5px solid <?php echo $is_active && !$is_expired ? '#bbf7d0' : 'var(--profile-border)'; ?>; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                            <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 20px;">
                                <div style="flex: 1; min-width: 250px;">
                                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="<?php echo $is_active && !$is_expired ? '#16a34a' : '#666'; ?>" stroke-width="2">
                                            <rect x="2" y="5" width="20" height="14" rx="2"/>
                                            <line x1="2" y1="10" x2="22" y2="10"/>
                                        </svg>
                                        <h3 style="font-size: 18px; font-weight: 600; margin: 0; color: var(--profile-text);">
                                            <?php echo $is_trial ? 'Free Trial' : 'Premium Subscription'; ?>
                                        </h3>
                                        <?php if ($is_active && !$is_expired): ?>
                                            <span style="background: #16a34a; color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Active</span>
                                        <?php elseif ($is_expired): ?>
                                            <span style="background: #dc2626; color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Expired</span>
                                        <?php else: ?>
                                            <span style="background: #f59e0b; color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Cancelled</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 20px; margin-bottom: 16px;">
                                        <div>
                                            <div style="font-size: 11px; color: var(--profile-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 6px;">Start Date</div>
                                            <div style="font-size: 15px; color: var(--profile-text); font-weight: 500;"><?php echo $start_date->format('M d, Y'); ?></div>
                                        </div>
                                        <div>
                                            <div style="font-size: 11px; color: var(--profile-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 6px;">End Date</div>
                                            <div style="font-size: 15px; color: var(--profile-text); font-weight: 500;"><?php echo $end_date->format('M d, Y'); ?></div>
                                        </div>
                                        <?php if ($is_active && !$is_expired): ?>
                                            <div>
                                                <div style="font-size: 11px; color: var(--profile-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 6px;">Days Left</div>
                                                <div style="font-size: 15px; color: #16a34a; font-weight: 600;"><?php echo $days_remaining; ?> days</div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--profile-muted);">
                                        <?php if ($is_trial): ?>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="12" y1="16" x2="12" y2="12"/>
                                                <line x1="12" y1="8" x2="12.01" y2="8"/>
                                            </svg>
                                            <span>Free trial period - no payment required</span>
                                        <?php else: ?>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="2" y="5" width="20" height="14" rx="2"/>
                                                <line x1="2" y1="10" x2="22" y2="10"/>
                                            </svg>
                                            <span>Monthly subscription • €9.99/month</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div style="display: flex; flex-direction: column; gap: 10px; align-items: flex-end;">
                                    <?php if ($is_active && !$is_expired): ?>
                                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to cancel this subscription? You will still have access until <?php echo $end_date->format('M d, Y'); ?>.');">
                                            <input type="hidden" name="subscription_id" value="<?php echo $subscription['id']; ?>">
                                            <button type="submit" name="cancel_subscription" class="btn btn-secondary" style="background: #dc2626; color: white; border: none; padding: 10px 20px; font-size: 14px;" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;">
                                                    <path d="M18 6L6 18M6 6l12 12"/>
                                                </svg>
                                                Cancel Subscription
                                            </button>
                                        </form>
                                        <div style="font-size: 11px; color: var(--profile-muted); text-align: right;">
                                            Access continues until end date
                                        </div>
                                    <?php elseif (!$is_active && !$is_expired): ?>
                                        <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #f59e0b; font-weight: 600;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="15" y1="9" x2="9" y2="15"/>
                                                <line x1="9" y1="9" x2="15" y2="15"/>
                                            </svg>
                                            Subscription cancelled
                                        </div>
                                        <div style="font-size: 11px; color: var(--profile-muted);">
                                            Access until <?php echo $end_date->format('M d, Y'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="background: #fffbeb; padding: 20px; border-radius: 12px; border: 1px solid #fde68a; margin-top: 24px;">
                        <div style="display: flex; align-items: start; gap: 14px;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="16" x2="12" y2="12"/>
                                <line x1="12" y1="8" x2="12.01" y2="8"/>
                            </svg>
                            <div>
                                <div style="font-size: 14px; color: #92400e; font-weight: 600; margin-bottom: 6px;">Important Information</div>
                                <div style="font-size: 13px; color: #92400e; line-height: 1.6;">
                                    When you cancel a subscription, you'll continue to have access until the end date. No refunds are provided for partial months. To renew or upgrade, please visit the subscription page.
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="background: white; padding: 60px 40px; border-radius: 16px; border: 1.5px solid var(--profile-border); text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--profile-muted)" stroke-width="1.5" style="margin: 0 auto 24px;">
                            <rect x="2" y="5" width="20" height="14" rx="2"/>
                            <line x1="2" y1="10" x2="22" y2="10"/>
                        </svg>
                        <h3 style="font-size: 20px; font-weight: 600; margin: 0 0 12px 0; color: var(--profile-text);">No Active Subscriptions</h3>
                        <p style="font-size: 15px; margin: 0 0 28px 0; color: var(--profile-muted);">You don't have any subscriptions yet. Subscribe to unlock premium features!</p>
                        <a href="<?php echo VIEWS_URL; ?>/subscription.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; font-size: 15px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            Subscribe Now
                        </a>
                    </div>
                <?php endif; ?>
            </div>

    </div>
    
    <script>
        (function() {
            // Organize sections into collapsible accordions to reduce scrolling
            const sections = document.querySelectorAll('.profile-section');
            let isFirstSection = true;
            
            sections.forEach((section, index) => {
                const title = section.querySelector('.section-title');
                if (!title) return;
                
                const titleText = title.textContent.trim();
                
                // Skip first section (Account Overview) - keep it always visible
                if (isFirstSection) {
                    isFirstSection = false;
                    return;
                }
                
                // Create collapsible wrapper
                const details = document.createElement('details');
                details.style.marginBottom = '16px';
                
                // Create summary
                const summary = document.createElement('summary');
                summary.style.cssText = `
                    cursor: pointer;
                    font-weight: 600;
                    font-size: 18px;
                    padding: 20px 24px;
                    background: var(--profile-card-bg);
                    border: 1px solid var(--profile-border);
                    border-radius: 12px;
                    list-style: none;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    transition: all 0.2s ease;
                    color: var(--profile-text);
                `;
                summary.innerHTML = `
                    <span>${titleText}</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.3s ease;">
                        <path d="M19 9l-7 7-7-7"/>
                    </svg>
                `;
                
                summary.addEventListener('mouseenter', function() {
                    this.style.background = 'rgba(47, 109, 245, 0.04)';
                    this.style.borderColor = 'rgba(47, 109, 245, 0.2)';
                });
                
                summary.addEventListener('mouseleave', function() {
                    this.style.background = 'var(--profile-card-bg)';
                    this.style.borderColor = 'var(--profile-border)';
                });
                
                // Rotate arrow when opened
                details.addEventListener('toggle', function() {
                    const arrow = summary.querySelector('svg');
                    if (details.open) {
                        arrow.style.transform = 'rotate(180deg)';
                    } else {
                        arrow.style.transform = 'rotate(0deg)';
                    }
                });
                
                // Create content wrapper
                const contentWrapper = document.createElement('div');
                contentWrapper.style.cssText = `
                    padding: 24px;
                    background: var(--profile-card-bg);
                    border: 1px solid var(--profile-border);
                    border-top: none;
                    border-radius: 0 0 12px 12px;
                    margin-top: -1px;
                `;
                
                // Move section content into wrapper
                title.remove(); // Remove the title as it's now in summary
                contentWrapper.innerHTML = section.innerHTML;
                
                // Build the collapsible
                details.appendChild(summary);
                details.appendChild(contentWrapper);
                
                // Replace the section with details element
                section.replaceWith(details);
            });

            // Tab functionality
            const tabs = document.querySelectorAll('.profile-tab');
            const tabPanels = document.querySelectorAll('.tab-panel');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const targetTab = tab.getAttribute('data-tab');
                    
                    // Remove active class from all tabs and panels
                    tabs.forEach(t => t.classList.remove('active'));
                    tabPanels.forEach(p => p.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding panel
                    tab.classList.add('active');
                    const targetPanel = document.getElementById('tab-' + targetTab);
                    if (targetPanel) {
                        targetPanel.classList.add('active');
                    }
                });
            });

            // Email toggle function
            function toggleEditEmail(emailId) {
                const displayDiv = document.querySelector('.email-display-' + emailId);
                const editDiv = document.querySelector('.email-edit-' + emailId);
                
                if (displayDiv && editDiv) {
                    if (displayDiv.style.display === 'none') {
                        displayDiv.style.display = 'block';
                        editDiv.style.display = 'none';
                    } else {
                        displayDiv.style.display = 'none';
                        editDiv.style.display = 'block';
                    }
                }
            }

            // Make toggle function global
            window.toggleEditEmail = toggleEditEmail;
        })();
    </script>
</body>
</html>
