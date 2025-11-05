<?php<div class="tab-panel" id="tab-company">

// Company Tab Content    <h2 class="section-title">Company Information</h2>

// Expects: $user, $company    

?>    <?php if ($company): ?>

<div class="tab-panel" id="tab-company">        <!-- Company Overview -->

    <h2 class="section-title">Company Information</h2>        <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">

                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 32px; justify-content: space-between;">

    <?php if ($company): ?>                <div style="display: flex; align-items: center; gap: 16px;">

        <!-- Company Overview -->                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 36px; color: white; font-weight: 700;">

        <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">                        <?php echo strtoupper(substr($company['name'], 0, 1)); ?>

            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 32px; justify-content: space-between; flex-wrap: wrap;">                    </div>

                <div style="display: flex; align-items: center; gap: 16px;">                    <div>

                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 36px; color: white; font-weight: 700;">                        <h3 style="margin: 0 0 4px; font-size: 28px; font-weight: 700; color: #1f2937;">

                        <?php echo strtoupper(substr($company['name'], 0, 1)); ?>                            <?php echo htmlspecialchars($company['name']); ?>

                    </div>                        </h3>

                    <div>                        <p style="margin: 0; font-size: 14px; color: #6b7280;">

                        <h3 style="margin: 0 0 4px; font-size: 28px; font-weight: 700; color: #1f2937;">                            <?php echo htmlspecialchars($company['company_type'] ?? 'Other'); ?> • Member since <?php echo isset($company['created_at']) ? date('M Y', strtotime($company['created_at'])) : 'N/A'; ?>

                            <?php echo htmlspecialchars($company['name']); ?>                        </p>

                        </h3>                    </div>

                        <p style="margin: 0; font-size: 14px; color: #6b7280;">                </div>

                            <?php echo htmlspecialchars($company['company_type'] ?? 'Other'); ?> • Member since <?php echo isset($company['created_at']) ? date('M Y', strtotime($company['created_at'])) : 'N/A'; ?>                <a 

                        </p>                    href="<?php echo VIEWS_URL; ?>/company/edit-company.php" 

                    </div>                    style="

                </div>                        padding: 12px 24px;

                <a                         background: #667eea;

                    href="<?php echo VIEWS_URL; ?>/company/edit-company.php"                         color: white;

                    style="                        text-decoration: none;

                        padding: 12px 24px;                        border-radius: 10px;

                        background: #667eea;                        font-weight: 600;

                        color: white;                        font-size: 14px;

                        text-decoration: none;                        transition: all 0.2s ease;

                        border-radius: 10px;                    "

                        font-weight: 600;                    onmouseover="this.style.background='#5568d3'"

                        font-size: 14px;                    onmouseout="this.style.background='#667eea'"

                        transition: all 0.2s ease;                >

                    "                    ✏️ Edit Company

                    onmouseover="this.style.background='#5568d3'"                </a>

                    onmouseout="this.style.background='#667eea'"            </div>

                >

                    ✏️ Edit Company            <!-- Company Details Grid -->

                </a>            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; padding: 24px; background: #f9fafb; border-radius: 12px;">

            </div>                <?php if (!empty($company['description'])): ?>

                <div style="grid-column: 1 / -1;">

            <!-- Company Details Grid -->                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #6b7280; margin-bottom: 8px;">Description</p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; padding: 24px; background: #f9fafb; border-radius: 12px;">                    <p style="margin: 0; font-size: 15px; color: #1f2937; line-height: 1.6;">

                <?php if (!empty($company['description'])): ?>                        <?php echo nl2br(htmlspecialchars($company['description'])); ?>

                <div style="grid-column: 1 / -1;">                    </p>

                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #6b7280; margin-bottom: 8px;">Description</p>                </div>

                    <p style="margin: 0; font-size: 15px; color: #1f2937; line-height: 1.6;">                <?php endif; ?>

                        <?php echo nl2br(htmlspecialchars($company['description'])); ?>

                    </p>                <?php if (!empty($company['address_line1'])): ?>

                </div>                <div>

                <?php endif; ?>                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #6b7280; margin-bottom: 4px;">📍 Address</p>

                    <p style="margin: 0; font-size: 15px; color: #1f2937;">

                <?php if (!empty($company['address_line1'])): ?>                        <?php echo htmlspecialchars($company['address_line1']); ?>

                <div>                        <?php if (!empty($company['address_line2'])): ?>

                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #6b7280; margin-bottom: 4px;">📍 Address</p>                            <br><?php echo htmlspecialchars($company['address_line2']); ?>

                    <p style="margin: 0; font-size: 15px; color: #1f2937;">                        <?php endif; ?>

                        <?php echo htmlspecialchars($company['address_line1']); ?>                    </p>

                        <?php if (!empty($company['address_line2'])): ?>                </div>

                            <br><?php echo htmlspecialchars($company['address_line2']); ?>                <?php endif; ?>

                        <?php endif; ?>

                    </p>                <?php if (!empty($company['city']) || !empty($company['postal_code']) || !empty($company['country'])): ?>

                </div>                <div>

                <?php endif; ?>                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #6b7280; margin-bottom: 4px;">🌍 Location</p>

                    <p style="margin: 0; font-size: 15px; color: #1f2937;">

                <?php if (!empty($company['city']) || !empty($company['postal_code']) || !empty($company['country'])): ?>                        <?php 

                <div>                        $location_parts = array_filter([

                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #6b7280; margin-bottom: 4px;">🌍 Location</p>                            $company['city'] ?? '',

                    <p style="margin: 0; font-size: 15px; color: #1f2937;">                            $company['postal_code'] ?? '',

                        <?php                             $company['country'] ?? ''

                        $location_parts = array_filter([                        ]);

                            $company['city'] ?? '',                        echo htmlspecialchars(implode(', ', $location_parts));

                            $company['postal_code'] ?? '',                        ?>

                            $company['country'] ?? ''                    </p>

                        ]);                </div>

                        echo htmlspecialchars(implode(', ', $location_parts));                <?php endif; ?>

                        ?>

                    </p>                <?php if (!empty($company['phone'])): ?>

                </div>                <div>

                <?php endif; ?>                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #6b7280; margin-bottom: 4px;">📞 Phone</p>

                    <p style="margin: 0; font-size: 15px; color: #1f2937;">

                <?php if (!empty($company['phone'])): ?>                        <a href="tel:<?php echo htmlspecialchars($company['phone']); ?>" style="color: #667eea; text-decoration: none;">

                <div>                            <?php echo htmlspecialchars($company['phone']); ?>

                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #6b7280; margin-bottom: 4px;">📞 Phone</p>                        </a>

                    <p style="margin: 0; font-size: 15px; color: #1f2937;">                    </p>

                        <a href="tel:<?php echo htmlspecialchars($company['phone']); ?>" style="color: #667eea; text-decoration: none;">                </div>

                            <?php echo htmlspecialchars($company['phone']); ?>                <?php endif; ?>

                        </a>

                    </p>                <?php if (!empty($company['website'])): ?>

                </div>                <div>

                <?php endif; ?>                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #6b7280; margin-bottom: 4px;">🌐 Website</p>

                    <p style="margin: 0; font-size: 15px; color: #1f2937;">

                <?php if (!empty($company['website'])): ?>                        <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank" style="color: #667eea; text-decoration: none;">

                <div>                            <?php echo htmlspecialchars($company['website']); ?>

                    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #6b7280; margin-bottom: 4px;">🌐 Website</p>                        </a>

                    <p style="margin: 0; font-size: 15px; color: #1f2937;">                    </p>

                        <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank" rel="noopener noreferrer" style="color: #667eea; text-decoration: none;">                </div>

                            <?php echo htmlspecialchars($company['website']); ?>                <?php endif; ?>

                        </a>            </div>

                    </p>

                </div>            <!-- Listings Link -->

                <?php endif; ?>            <div style="margin-top: 24px; padding-top: 24px; border-top: 2px solid #e5e7eb;">

            </div>                <a 

                    href="<?php echo VIEWS_URL; ?>/create.php"

            <!-- Listings Link -->                    style="

            <div style="margin-top: 24px; padding-top: 24px; border-top: 2px solid #e5e7eb;">                        display: inline-block;

                <a                         padding: 12px 24px;

                    href="<?php echo VIEWS_URL; ?>/create.php"                        background: #10b981;

                    style="                        color: white;

                        display: inline-block;                        text-decoration: none;

                        padding: 12px 24px;                        border-radius: 8px;

                        background: #10b981;                        font-weight: 600;

                        color: white;                        font-size: 14px;

                        text-decoration: none;                    "

                        border-radius: 8px;                >

                        font-weight: 600;                    + Create Listing

                        font-size: 14px;                </a>

                        transition: all 0.2s ease;            </div>

                    "        </div>

                    onmouseover="this.style.background='#059669'"

                    onmouseout="this.style.background='#10b981'"    <?php else: ?>

                >        <!-- No Company - Prompt to Create -->

                    + Create Listing        <div style="background: white; padding: 40px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">

                </a>            <div style="font-size: 64px; margin-bottom: 16px;">🏢</div>

            </div>            <h3 style="margin: 0 0 8px; font-size: 24px; font-weight: 700; color: #1f2937;">

        </div>                No Company Yet

            </h3>

    <?php else: ?>            <p style="margin: 0 0 24px; font-size: 14px; color: #6b7280; max-width: 500px; margin-left: auto; margin-right: auto;">

        <!-- No Company - Prompt to Create -->                Create your company profile to start listing products, build your brand, and appear in the Sellers directory.

        <div style="background: white; padding: 40px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">            </p>

            <div style="font-size: 64px; margin-bottom: 16px;">🏢</div>            <a 

            <h3 style="margin: 0 0 8px; font-size: 24px; font-weight: 700; color: #1f2937;">                href="<?php echo VIEWS_URL; ?>/company/create-company.php"

                No Company Yet                style="

            </h3>                    display: inline-block;

            <p style="margin: 0 0 24px; font-size: 14px; color: #6b7280; max-width: 500px; margin-left: auto; margin-right: auto;">                    padding: 14px 32px;

                Create your company profile to start listing products, build your brand, and appear in the Sellers directory.                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

            </p>                    color: white;

            <a                     text-decoration: none;

                href="<?php echo VIEWS_URL; ?>/company/create-company.php"                    border-radius: 12px;

                style="                    font-weight: 700;

                    display: inline-block;                    font-size: 15px;

                    padding: 14px 32px;                    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);

                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);                "

                    color: white;            >

                    text-decoration: none;                🚀 Create Company

                    border-radius: 12px;            </a>

                    font-weight: 700;        </div>

                    font-size: 15px;    <?php endif; ?>

                    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);</div>

                    transition: all 0.2s ease;        <!-- Company Edit Form -->

                "        <form method="POST" action="" style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">

                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.6)'"            <input type="hidden" name="company_id" value="<?php echo htmlspecialchars($company['id']); ?>">

                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.4)'"            

            >            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 32px;">

                🚀 Create Company                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 36px; color: white; font-weight: 700;">

            </a>                    <?php echo strtoupper(substr($company['name'], 0, 1)); ?>

        </div>                </div>

    <?php endif; ?>                <div>

</div>                    <h3 style="margin: 0 0 4px; font-size: 28px; font-weight: 700; color: #1f2937;">

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
