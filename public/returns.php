<?php 
session_start();
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns & Refunds - Glass Market</title>
    <link rel="stylesheet" href="<?php echo PUBLIC_URL; ?>/css/app.css">
    <style>
        .returns-page {
            padding: 60px 0 100px;
            background: #f8f9fa;
        }
        
        .returns-hero {
            background: linear-gradient(135deg, #2f6df5 0%, #1e4db8 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 60px;
        }
        
        .returns-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .returns-hero p {
            font-size: 20px;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .returns-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 32px;
        }
        
        .returns-card {
            background: white;
            border-radius: 16px;
            padding: 48px;
            margin-bottom: 32px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        
        .returns-card h2 {
            font-size: 32px;
            margin-bottom: 24px;
            color: #1d1d1f;
        }
        
        .returns-card h3 {
            font-size: 24px;
            margin-top: 32px;
            margin-bottom: 16px;
            color: #1d1d1f;
        }
        
        .returns-card p {
            color: #6e6e73;
            line-height: 1.8;
            margin-bottom: 16px;
        }
        
        .returns-card ul, .returns-card ol {
            color: #6e6e73;
            line-height: 1.8;
            margin-bottom: 24px;
            padding-left: 24px;
        }
        
        .returns-card li {
            margin-bottom: 12px;
        }
        
        .returns-card strong {
            color: #1d1d1f;
        }
        
        .info-box {
            background: #f0f4ff;
            border-left: 4px solid #2f6df5;
            padding: 20px 24px;
            margin: 24px 0;
            border-radius: 8px;
        }
        
        .info-box p {
            margin: 0;
            color: #1d1d1f;
        }
        
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 20px 24px;
            margin: 24px 0;
            border-radius: 8px;
        }
        
        .success-box p {
            margin: 0;
            color: #155724;
        }
        
        .steps-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin: 32px 0;
        }
        
        .step-card {
            background: #f8f9fa;
            padding: 24px;
            border-radius: 12px;
            text-align: center;
        }
        
        .step-number {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #2f6df5 0%, #1e4db8 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto 16px;
        }
        
        .step-card h4 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #1d1d1f;
        }
        
        .step-card p {
            font-size: 14px;
            color: #6e6e73;
            margin: 0;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    
    <div class="returns-page">
        <div class="returns-hero">
            <h1>Returns & Refunds</h1>
            <p>Your satisfaction is our priority. Learn about our return policy.</p>
        </div>
        
        <div class="returns-container">
            <div class="returns-card">
                <h2>Our Return Policy</h2>
                <p>We want you to be completely satisfied with your purchase. If you're not happy with your glass product, we're here to help.</p>
                
                <div class="success-box">
                    <p><strong>✓ 30-Day Return Window:</strong> You have 30 days from delivery to initiate a return for eligible items.</p>
                </div>
                
                <h3>📋 Eligible Returns</h3>
                <p>Items that qualify for returns:</p>
                <ul>
                    <li><strong>Damaged items:</strong> Items that arrived broken or damaged during shipping</li>
                    <li><strong>Incorrect items:</strong> You received the wrong product</li>
                    <li><strong>Defective items:</strong> Manufacturing defects or quality issues</li>
                    <li><strong>Not as described:</strong> Item significantly differs from listing description</li>
                    <li><strong>Changed your mind:</strong> Unused items in original packaging (buyer pays return shipping)</li>
                </ul>
                
                <h3>❌ Non-Returnable Items</h3>
                <p>The following items cannot be returned:</p>
                <ul>
                    <li>Custom-cut glass or made-to-order items</li>
                    <li>Items marked as "Final Sale" or "Non-Returnable"</li>
                    <li>Glass that has been installed or used</li>
                    <li>Items without original packaging</li>
                    <li>Returns initiated after 30 days</li>
                </ul>
                
                <h3>📦 Return Process</h3>
                <p>Follow these simple steps to return an item:</p>
                
                <div class="steps-container">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4>Initiate Return</h4>
                        <p>Contact us or use your account dashboard to start the return process</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4>Get Approval</h4>
                        <p>Receive return authorization and shipping label via email</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4>Pack Securely</h4>
                        <p>Package item safely using original packaging if possible</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h4>Ship Back</h4>
                        <p>Drop off at carrier location or schedule a pickup</p>
                    </div>
                </div>
                
                <h3>💰 Refund Process</h3>
                <p>Once we receive your return:</p>
                <ol>
                    <li>We'll inspect the item within 2 business days</li>
                    <li>If approved, refund will be processed within 3-5 business days</li>
                    <li>Refund will be issued to original payment method</li>
                    <li>You'll receive an email confirmation once refund is processed</li>
                </ol>
                
                <div class="info-box">
                    <p><strong>💡 Processing Time:</strong> Please allow 5-10 business days for the refund to appear in your account, depending on your bank or card issuer.</p>
                </div>
                
                <h3>💳 Refund Amounts</h3>
                <table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 16px; text-align: left; border-bottom: 1px solid #e5e5ea;">Reason</th>
                            <th style="padding: 16px; text-align: left; border-bottom: 1px solid #e5e5ea;">Refund Amount</th>
                            <th style="padding: 16px; text-align: left; border-bottom: 1px solid #e5e5ea;">Return Shipping</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 16px; border-bottom: 1px solid #e5e5ea;">Damaged/Defective</td>
                            <td style="padding: 16px; border-bottom: 1px solid #e5e5ea;">100% + original shipping</td>
                            <td style="padding: 16px; border-bottom: 1px solid #e5e5ea;">Free (we provide label)</td>
                        </tr>
                        <tr>
                            <td style="padding: 16px; border-bottom: 1px solid #e5e5ea;">Wrong Item Sent</td>
                            <td style="padding: 16px; border-bottom: 1px solid #e5e5ea;">100% + original shipping</td>
                            <td style="padding: 16px; border-bottom: 1px solid #e5e5ea;">Free (we provide label)</td>
                        </tr>
                        <tr>
                            <td style="padding: 16px; border-bottom: 1px solid #e5e5ea;">Not as Described</td>
                            <td style="padding: 16px; border-bottom: 1px solid #e5e5ea;">100%</td>
                            <td style="padding: 16px; border-bottom: 1px solid #e5e5ea;">Free (we provide label)</td>
                        </tr>
                        <tr>
                            <td style="padding: 16px;">Changed Mind</td>
                            <td style="padding: 16px;">100% (minus restocking fee*)</td>
                            <td style="padding: 16px;">Buyer's responsibility</td>
                        </tr>
                    </tbody>
                </table>
                <p style="font-size: 14px; color: #6e6e73; margin-top: 8px;">*Restocking fee: 15% for items over $100</p>
                
                <h3>🔄 Exchanges</h3>
                <p>We currently don't offer direct exchanges. To exchange an item:</p>
                <ol>
                    <li>Return the original item for a refund</li>
                    <li>Place a new order for the desired item</li>
                </ol>
                <p>This ensures you get the item you want as quickly as possible.</p>
                
                <h3>📸 Damaged Item Claims</h3>
                <p>If your item arrives damaged:</p>
                <ul>
                    <li><strong>Document immediately:</strong> Take clear photos of packaging and damage</li>
                    <li><strong>Report within 48 hours:</strong> Contact us as soon as possible</li>
                    <li><strong>Keep all packaging:</strong> May be needed for carrier claims</li>
                    <li><strong>Don't discard:</strong> Keep the item until claim is resolved</li>
                </ul>
                
                <div class="info-box">
                    <p><strong>📞 Need Help?</strong> Our returns team is here to assist you. Email <a href="mailto:returns@glassmarket.com" style="color: #2f6df5;">returns@glassmarket.com</a> or call 1-800-GLASS-123 (Monday-Friday, 9 AM - 6 PM EST).</p>
                </div>
                
                <h3>🎁 Special Circumstances</h3>
                <p><strong>Holiday Returns:</strong> Items purchased between November 1 and December 24 can be returned until January 31.</p>
                <p><strong>Bulk Orders:</strong> Returns for orders of 10+ items may have special conditions. Contact us for details.</p>
                <p><strong>International Returns:</strong> Additional customs and shipping fees may apply. Contact us before returning.</p>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
