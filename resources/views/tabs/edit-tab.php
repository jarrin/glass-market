<?php
// Edit Profile Tab Content
// Expects: $user, $company, $error_message, $success_message
?>
<div class="tab-panel" id="tab-edit">
    <h2 class="section-title">Edit Profile</h2>
    
    <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 600px;">
        <form method="POST" enctype="multipart/form-data">
            <!-- Avatar Upload -->
            <div style="text-align: center; margin-bottom: 32px;">
                <div style="display: inline-block; position: relative;">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;">
                    <?php else: ?>
                        <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: 700;">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <label for="avatar" style="
                        position: absolute;
                        bottom: 0;
                        right: 0;
                        width: 36px;
                        height: 36px;
                        background: #2f6df5;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        cursor: pointer;
                        border: 3px solid white;
                    ">
                        <span style="color: white; font-size: 18px;">📷</span>
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;">
                </div>
                <p style="margin-top: 12px; font-size: 13px; color: #6b7280;">Click camera icon to upload new photo</p>
            </div>

            <!-- Name -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #1f2937;">
                    Full Name <span style="color: #ef4444;">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    value="<?php echo htmlspecialchars($user['name']); ?>" 
                    required
                    style="
                        width: 100%;
                        padding: 12px 16px;
                        border: 2px solid #e5e7eb;
                        border-radius: 8px;
                        font-size: 15px;
                        transition: border 0.2s;
                        box-sizing: border-box;
                    "
                    onfocus="this.style.borderColor='#2f6df5'"
                    onblur="this.style.borderColor='#e5e7eb'"
                >
            </div>

            <!-- Email (readonly) -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #1f2937;">
                    Email Address
                </label>
                <input 
                    type="email" 
                    value="<?php echo htmlspecialchars($user['email']); ?>" 
                    readonly
                    style="
                        width: 100%;
                        padding: 12px 16px;
                        border: 2px solid #e5e7eb;
                        border-radius: 8px;
                        font-size: 15px;
                        background: #f9fafb;
                        color: #6b7280;
                        box-sizing: border-box;
                    "
                >
                <p style="margin-top: 6px; font-size: 12px; color: #6b7280;">Email cannot be changed</p>
            </div>

            <!-- Company Name -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #1f2937;">
                    Company Name (Optional)
                </label>
                <input 
                    type="text" 
                    name="company_name" 
                    value="<?php echo htmlspecialchars($user['company_name'] ?? ''); ?>" 
                    placeholder="Your company name"
                    style="
                        width: 100%;
                        padding: 12px 16px;
                        border: 2px solid #e5e7eb;
                        border-radius: 8px;
                        font-size: 15px;
                        transition: border 0.2s;
                        box-sizing: border-box;
                    "
                    onfocus="this.style.borderColor='#2f6df5'"
                    onblur="this.style.borderColor='#e5e7eb'"
                >
            </div>

            <button 
                type="submit" 
                name="update_profile"
                style="
                    width: 100%;
                    padding: 14px 24px;
                    background: #2f6df5;
                    color: white;
                    border: none;
                    border-radius: 10px;
                    font-size: 16px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: background 0.2s;
                "
                onmouseover="this.style.background='#1e4dd8'"
                onmouseout="this.style.background='#2f6df5'"
            >
                Save Changes
            </button>
        </form>
    </div>

    <!-- Password Change Section -->
    <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 600px; margin-top: 24px;">
        <h3 style="margin: 0 0 16px; font-size: 18px; font-weight: 600;">Change Password</h3>
        <p style="font-size: 14px; color: #6b7280; margin-bottom: 20px;">
            For security reasons, password changes are handled separately.
        </p>
        <a 
            href="<?php echo VIEWS_URL; ?>/change-password.php" 
            style="
                display: inline-block;
                padding: 12px 24px;
                background: #f3f4f6;
                color: #1f2937;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
                font-size: 14px;
                transition: background 0.2s;
            "
            onmouseover="this.style.background='#e5e7eb'"
            onmouseout="this.style.background='#f3f4f6'"
        >
            Change Password
        </a>
    </div>
</div>
