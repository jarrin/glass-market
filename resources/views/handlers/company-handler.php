<?php
/**
 * Company Update Handler
 * Handles company information updates
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_company'])) {
    $company_id = $_POST['company_id'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;
    
    if (!$company_id || !$user_id) {
        $_SESSION['profile_error'] = 'Invalid company or user.';
    } else {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Verify user owns this company
            $stmt = $pdo->prepare('SELECT id FROM companies WHERE id = :company_id AND owner_user_id = :user_id');
            $stmt->execute(['company_id' => $company_id, 'user_id' => $user_id]);
            
            if (!$stmt->fetch()) {
                $_SESSION['profile_error'] = 'You do not have permission to edit this company.';
            } else {
                // Update company information
                $stmt = $pdo->prepare('
                    UPDATE companies SET
                        description = :description,
                        address_line1 = :address_line1,
                        address_line2 = :address_line2,
                        postal_code = :postal_code,
                        city = :city,
                        country = :country,
                        company_type = :company_type,
                        website = :website,
                        phone = :phone
                    WHERE id = :company_id AND owner_user_id = :user_id
                ');
                
                $stmt->execute([
                    'description' => trim($_POST['description'] ?? ''),
                    'address_line1' => trim($_POST['address_line1'] ?? ''),
                    'address_line2' => trim($_POST['address_line2'] ?? ''),
                    'postal_code' => trim($_POST['postal_code'] ?? ''),
                    'city' => trim($_POST['city'] ?? ''),
                    'country' => trim($_POST['country'] ?? ''),
                    'company_type' => $_POST['company_type'] ?? 'Other',
                    'website' => trim($_POST['website'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'company_id' => $company_id,
                    'user_id' => $user_id
                ]);
                
                $_SESSION['profile_success'] = 'Company information updated successfully!';
            }
        } catch (PDOException $e) {
            $_SESSION['profile_error'] = 'Failed to update company: ' . $e->getMessage();
            error_log("Company update error: " . $e->getMessage());
        }
    }
    
    // Redirect to prevent form resubmission
    header('Location: ' . VIEWS_URL . '/profile.php?tab=company');
    exit;
}

// Handle company creation for users without a company
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_company'])) {
    $user_id = $_SESSION['user_id'] ?? null;
    $company_name = trim($_POST['company_name'] ?? '');
    
    if (!$user_id) {
        $_SESSION['profile_error'] = 'You must be logged in to create a company.';
    } elseif (empty($company_name)) {
        $_SESSION['profile_error'] = 'Company name is required.';
    } else {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Check if user already has a company
            $stmt = $pdo->prepare('SELECT company_id FROM users WHERE id = :user_id');
            $stmt->execute(['user_id' => $user_id]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!empty($user_data['company_id'])) {
                $_SESSION['profile_error'] = 'You already have a company associated with your account.';
            } else {
                // Create new company
                $stmt = $pdo->prepare('
                    INSERT INTO companies (
                        name,
                        description,
                        address_line1,
                        city,
                        country,
                        company_type,
                        owner_user_id,
                        created_at
                    ) VALUES (
                        :name,
                        :description,
                        :address_line1,
                        :city,
                        :country,
                        :company_type,
                        :owner_user_id,
                        NOW()
                    )
                ');
                
                $stmt->execute([
                    'name' => $company_name,
                    'description' => trim($_POST['description'] ?? ''),
                    'address_line1' => trim($_POST['address_line1'] ?? ''),
                    'city' => trim($_POST['city'] ?? ''),
                    'country' => trim($_POST['country'] ?? ''),
                    'company_type' => $_POST['company_type'] ?? 'Other',
                    'owner_user_id' => $user_id
                ]);
                
                $company_id = $pdo->lastInsertId();
                
                // Link company to user
                $stmt = $pdo->prepare('UPDATE users SET company_id = :company_id WHERE id = :user_id');
                $stmt->execute(['company_id' => $company_id, 'user_id' => $user_id]);
                
                $_SESSION['profile_success'] = 'Company created successfully!';
            }
        } catch (PDOException $e) {
            $_SESSION['profile_error'] = 'Failed to create company: ' . $e->getMessage();
            error_log("Company creation error: " . $e->getMessage());
        }
    }
    
    // Redirect to prevent form resubmission
    header('Location: ' . VIEWS_URL . '/profile.php?tab=company');
    exit;
}
