<?php
/**
 * Subscription Required Message Component
 * Shows a professional message when user needs active subscription
 */
?>
<div style="
    background: linear-gradient(135deg, #fff5f5 0%, #ffe9e9 100%);
    border: 2px solid #fecaca;
    border-radius: 16px;
    padding: 48px 32px;
    text-align: center;
    margin: 40px auto;
    max-width: 600px;
">
    <div style="
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        box-shadow: 0 8px 24px rgba(239, 68, 68, 0.3);
    ">
        <svg width="40" height="40" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
    </div>

    <h2 style="
        font-size: 24px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 12px;
    ">
        Subscription Required
    </h2>

    <p style="
        font-size: 16px;
        color: #666;
        line-height: 1.6;
        margin: 0 0 32px;
    ">
        Please activate a subscription before managing or viewing this content. Choose a plan that works for you and get instant access to all features.
    </p>

    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
        <a href="<?php echo VIEWS_URL ?? '/glass-market/resources/views'; ?>/pricing.php"
           style="
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        "
        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(102, 126, 234, 0.4)';"
        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(102, 126, 234, 0.3)';">
            View Plans
        </a>

        <a href="<?php echo VIEWS_URL ?? '/glass-market/resources/views'; ?>/profile.php?tab=subscription"
           style="
            display: inline-block;
            padding: 14px 32px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        "
        onmouseover="this.style.borderColor='#667eea'; this.style.background='#f8f9fa';"
        onmouseout="this.style.borderColor='#e0e0e0'; this.style.background='white';">
            View Subscription
        </a>
    </div>

    <p style="
        font-size: 13px;
        color: #999;
        margin: 24px 0 0;
    ">
        Need help? <a href="mailto:support@glassmarket.com" style="color: #667eea; text-decoration: none; font-weight: 600;">Contact Support</a>
    </p>
</div>
