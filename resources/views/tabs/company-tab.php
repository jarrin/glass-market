<?php
// Company Tab Content
// Expects: $user, $company
?>
<div class="tab-panel" id="tab-company">
    <h2 class="section-title">Company Information</h2>
    
    <?php if ($company): ?>
        <!-- Company Details Card -->
        <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 32px; color: white; font-weight: 700;">
                    <?php echo strtoupper(substr($company['name'], 0, 1)); ?>
                </div>
                <div>
                    <h3 style="margin: 0 0 4px; font-size: 24px; font-weight: 700;"><?php echo htmlspecialchars($company['name']); ?></h3>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">
                        Member since <?php echo isset($company['created_at']) ? date('M Y', strtotime($company['created_at'])) : 'N/A'; ?>
                    </p>
                </div>
            </div>

            <div style="display: grid; gap: 16px;">
                <!-- Address -->
                <?php if (!empty($company['address'])): ?>
                <div style="padding: 16px; background: #f9fafb; border-radius: 10px;">
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Address</div>
                    <div style="font-size: 15px; color: #1f2937; font-weight: 500;"><?php echo htmlspecialchars($company['address']); ?></div>
                </div>
                <?php endif; ?>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <!-- City -->
                    <?php if (!empty($company['city'])): ?>
                    <div style="padding: 16px; background: #f9fafb; border-radius: 10px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">City</div>
                        <div style="font-size: 15px; color: #1f2937; font-weight: 500;"><?php echo htmlspecialchars($company['city']); ?></div>
                    </div>
                    <?php endif; ?>

                    <!-- Country -->
                    <?php if (!empty($company['country'])): ?>
                    <div style="padding: 16px; background: #f9fafb; border-radius: 10px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Country</div>
                        <div style="font-size: 15px; color: #1f2937; font-weight: 500;"><?php echo htmlspecialchars($company['country']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Website -->
                <?php if (!empty($company['website'])): ?>
                <div style="padding: 16px; background: #f9fafb; border-radius: 10px;">
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Website</div>
                    <a href="<?php echo htmlspecialchars($company['website']); ?>" target="_blank" style="font-size: 15px; color: #2f6df5; font-weight: 500; text-decoration: none;">
                        <?php echo htmlspecialchars($company['website']); ?> →
                    </a>
                </div>
                <?php endif; ?>

                <!-- Description -->
                <?php if (!empty($company['description'])): ?>
                <div style="padding: 16px; background: #f9fafb; border-radius: 10px;">
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">About</div>
                    <div style="font-size: 14px; color: #1f2937; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($company['description'])); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Edit Company Button -->
        <div style="background: #fffbeb; padding: 20px; border-radius: 12px; border: 1px solid #fbbf24;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 24px;">ℹ️</span>
                <div style="flex: 1;">
                    <div style="font-size: 14px; color: #92400e;">
                        <strong>Need to update company information?</strong><br>
                        Contact support to modify company details.
                    </div>
                </div>
                <a 
                    href="<?php echo BASE_URL; ?>/contact.php" 
                    style="
                        padding: 10px 20px;
                        background: #f59e0b;
                        color: white;
                        text-decoration: none;
                        border-radius: 8px;
                        font-weight: 600;
                        font-size: 14px;
                        white-space: nowrap;
                    "
                >
                    Contact Support
                </a>
            </div>
        </div>

    <?php else: ?>
        <!-- No Company State -->
        <div style="text-align: center; padding: 60px 20px; background: #f9fafb; border-radius: 16px; border: 2px dashed #d1d5db;">
            <div style="font-size: 64px; margin-bottom: 16px;">🏢</div>
            <h3 style="margin: 0 0 8px; font-size: 20px; font-weight: 600; color: #1f2937;">
                No Company Associated
            </h3>
            <p style="margin: 0 0 24px; font-size: 14px; color: #6b7280; max-width: 400px; margin-left: auto; margin-right: auto;">
                You haven't linked your account to a company yet. Companies can manage multiple listings and have a dedicated shop page.
            </p>
            <a 
                href="<?php echo BASE_URL; ?>/contact.php" 
                style="
                    display: inline-block;
                    padding: 12px 24px;
                    background: #2f6df5;
                    color: white;
                    text-decoration: none;
                    border-radius: 10px;
                    font-weight: 600;
                    font-size: 14px;
                "
            >
                Contact Us to Create Company
            </a>
        </div>
    <?php endif; ?>
</div>
