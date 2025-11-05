<?php
// Company Tab Content
// Expects: $user, $company
?>

<div class="tab-panel" id="tab-company">
    <h2 class="section-title">Company Information</h2>
    
    <?php if ($company): ?>
        <!-- Company exists - show overview -->
        <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 32px; justify-content: space-between; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 36px; color: white; font-weight: 700;">
                        <?php echo strtoupper(substr($company['name'], 0, 1)); ?>
                    </div>
                    <div>
                        <h3 style="margin: 0 0 4px; font-size: 28px; font-weight: 700; color: #1f2937;">
                            <?php echo htmlspecialchars($company['name']); ?>
                        </h3>
                        <p style="margin: 0; font-size: 14px; color: #6b7280;">
                            <?php echo htmlspecialchars($company['company_type'] ?? 'Other'); ?>
                        </p>
                    </div>
                </div>
                <a href="<?php echo VIEWS_URL; ?>/company/edit-company.php" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 14px;">
                    Edit Company
                </a>
            </div>
            <p style="margin-bottom: 16px;">View and manage your company information from the edit page.</p>
            <a href="<?php echo VIEWS_URL; ?>/company/create-company-listing.php" style="display: inline-block; padding: 12px 24px; background: #10b981; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">
                + Create Company Listing
            </a>
        </div>
    <?php else: ?>
        <!-- No company - prompt to create -->
        <div style="background: white; padding: 40px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
            <div style="font-size: 64px; margin-bottom: 16px;"></div>
            <h3 style="margin: 0 0 8px; font-size: 24px; font-weight: 700; color: #1f2937;">No Company Yet</h3>
            <p style="margin: 0 0 24px; font-size: 14px; color: #6b7280; max-width: 500px; margin-left: auto; margin-right: auto;">
                Create your company profile to start listing products.
            </p>
            <a href="<?php echo VIEWS_URL; ?>/company/create-company.php" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 15px;">
                Create Company
            </a>
        </div>
    <?php endif; ?>
</div>
