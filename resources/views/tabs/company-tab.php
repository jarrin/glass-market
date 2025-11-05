<?php
// Company Tab Content
// Expects: $user, $company

// Get company types for dropdown
try {
    $pdo_temp = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo_temp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo_temp->query('SELECT type FROM company_types ORDER BY type');
    $company_types = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $company_types = ['Glass Recycle Plant', 'Glass Factory', 'Collection Company', 'Trader', 'Other'];
}
?>
<div class="tab-panel" id="tab-company">
    <h2 class="section-title">Company Information</h2>
    
    <?php if ($company): ?>
        <!-- Company Edit Form -->
        <form method="POST" action="" style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <input type="hidden" name="company_id" value="<?php echo htmlspecialchars($company['id']); ?>">
            
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 32px;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 36px; color: white; font-weight: 700;">
                    <?php echo strtoupper(substr($company['name'], 0, 1)); ?>
                </div>
                <div>
                    <h3 style="margin: 0 0 4px; font-size: 28px; font-weight: 700; color: #1f2937;">
                        <?php echo htmlspecialchars($company['name']); ?>
                    </h3>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">
                        Member since <?php echo isset($company['created_at']) ? date('M Y', strtotime($company['created_at'])) : 'N/A'; ?>
                    </p>
                </div>
            </div>

            <!-- Company Type -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                    Company Type *
                </label>
                <select 
                    name="company_type" 
                    required
                    style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937; background: white;"
                >
                    <?php foreach ($company_types as $type): ?>
                        <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($company['company_type'] ?? 'Other') === $type ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Description -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                    Company Description
                </label>
                <textarea 
                    name="description" 
                    rows="4"
                    placeholder="Tell potential customers about your company..."
                    style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937; resize: vertical; font-family: inherit;"
                ><?php echo htmlspecialchars($company['description'] ?? ''); ?></textarea>
            </div>

            <!-- Address Line 1 -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                    Address Line 1
                </label>
                <input 
                    type="text" 
                    name="address_line1" 
                    value="<?php echo htmlspecialchars($company['address_line1'] ?? ''); ?>"
                    placeholder="Street address"
                    style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937;"
                >
            </div>

            <!-- Address Line 2 -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                    Address Line 2
                </label>
                <input 
                    type="text" 
                    name="address_line2" 
                    value="<?php echo htmlspecialchars($company['address_line2'] ?? ''); ?>"
                    placeholder="Apartment, suite, unit, building, floor, etc."
                    style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937;"
                >
            </div>

            <!-- City, Postal Code, Country Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                        City
                    </label>
                    <input 
                        type="text" 
                        name="city" 
                        value="<?php echo htmlspecialchars($company['city'] ?? ''); ?>"
                        placeholder="City"
                        style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937;"
                    >
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                        Postal Code
                    </label>
                    <input 
                        type="text" 
                        name="postal_code" 
                        value="<?php echo htmlspecialchars($company['postal_code'] ?? ''); ?>"
                        placeholder="Postal Code"
                        style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937;"
                    >
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                        Country
                    </label>
                    <input 
                        type="text" 
                        name="country" 
                        value="<?php echo htmlspecialchars($company['country'] ?? ''); ?>"
                        placeholder="Country"
                        style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937;"
                    >
                </div>
            </div>

            <!-- Website and Phone Row -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                        Website
                    </label>
                    <input 
                        type="url" 
                        name="website" 
                        value="<?php echo htmlspecialchars($company['website'] ?? ''); ?>"
                        placeholder="https://example.com"
                        style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937;"
                    >
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                        Phone
                    </label>
                    <input 
                        type="tel" 
                        name="phone" 
                        value="<?php echo htmlspecialchars($company['phone'] ?? ''); ?>"
                        placeholder="+1234567890"
                        style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937;"
                    >
                </div>
            </div>

            <!-- Submit Button -->
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button 
                    type="submit" 
                    name="update_company"
                    style="
                        flex: 1;
                        padding: 14px 24px;
                        background: #2f6df5;
                        color: white;
                        border: none;
                        border-radius: 10px;
                        font-size: 15px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.2s ease;
                    "
                    onmouseover="this.style.background='#1d4ed8'"
                    onmouseout="this.style.background='#2f6df5'"
                >
                    💾 Update Company Information
                </button>
            </div>
        </form>

        <!-- Company Listings Section -->
        <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 20px; font-weight: 700; color: #1f2937;">Company Listings</h3>
                <a 
                    href="<?php echo VIEWS_URL; ?>/create.php" 
                    style="
                        padding: 10px 20px;
                        background: #10b981;
                        color: white;
                        text-decoration: none;
                        border-radius: 8px;
                        font-weight: 600;
                        font-size: 14px;
                    "
                >
                    + Create Listing
                </a>
            </div>
            <p style="margin: 0; font-size: 14px; color: #6b7280;">
                Create listings associated with your company. They will appear on your company's shop page and in the Sellers directory.
            </p>
        </div>

    <?php else: ?>
        <!-- Create Company Form -->
        <form method="POST" action="" style="background: white; padding: 40px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div style="text-align: center; margin-bottom: 32px;">
                <div style="font-size: 64px; margin-bottom: 16px;">🏢</div>
                <h3 style="margin: 0 0 8px; font-size: 24px; font-weight: 700; color: #1f2937;">
                    Create Your Company
                </h3>
                <p style="margin: 0; font-size: 14px; color: #6b7280; max-width: 500px; margin-left: auto; margin-right: auto;">
                    Set up your company profile to create listings, build your brand, and appear in the Sellers directory.
                </p>
            </div>

            <!-- Company Name -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                    Company Name *
                </label>
                <input 
                    type="text" 
                    name="company_name" 
                    required
                    placeholder="Enter your company name"
                    style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937;"
                >
            </div>

            <!-- Company Type -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                    Company Type *
                </label>
                <select 
                    name="company_type" 
                    required
                    style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937; background: white;"
                >
                    <?php foreach ($company_types as $type): ?>
                        <option value="<?php echo htmlspecialchars($type); ?>">
                            <?php echo htmlspecialchars($type); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Description -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                    Company Description (Optional)
                </label>
                <textarea 
                    name="description" 
                    rows="3"
                    placeholder="Brief description of your company..."
                    style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937; resize: vertical; font-family: inherit;"
                ></textarea>
            </div>

            <!-- Address -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                    Address (Optional)
                </label>
                <input 
                    type="text" 
                    name="address_line1" 
                    placeholder="Street address"
                    style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937;"
                >
            </div>

            <!-- City and Country Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                        City (Optional)
                    </label>
                    <input 
                        type="text" 
                        name="city" 
                        placeholder="City"
                        style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937;"
                    >
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                        Country (Optional)
                    </label>
                    <input 
                        type="text" 
                        name="country" 
                        placeholder="Country"
                        style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #1f2937;"
                    >
                </div>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                name="create_company"
                style="
                    width: 100%;
                    padding: 14px 24px;
                    background: #2f6df5;
                    color: white;
                    border: none;
                    border-radius: 10px;
                    font-size: 15px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.2s ease;
                "
                onmouseover="this.style.background='#1d4ed8'"
                onmouseout="this.style.background='#2f6df5'"
            >
                🏢 Create Company
            </button>
        </form>
    <?php endif; ?>
</div>
