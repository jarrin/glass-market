<?php 
session_start();
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Glass Market</title>
    <link rel="stylesheet" href="<?php echo PUBLIC_URL; ?>/css/app.css">
    <style>
        .terms-page {
            padding: 60px 0 100px;
            background: #f8f9fa;
        }
        
        .terms-hero {
            background: linear-gradient(135deg, #2f6df5 0%, #1e4db8 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 60px;
        }
        
        .terms-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .terms-hero p {
            font-size: 18px;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .terms-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 32px;
        }
        
        .terms-card {
            background: white;
            border-radius: 16px;
            padding: 48px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        
        .terms-card h2 {
            font-size: 28px;
            margin-top: 40px;
            margin-bottom: 20px;
            color: #1d1d1f;
        }
        
        .terms-card h3 {
            font-size: 22px;
            margin-top: 32px;
            margin-bottom: 16px;
            color: #1d1d1f;
        }
        
        .terms-card p {
            color: #6e6e73;
            line-height: 1.8;
            margin-bottom: 16px;
        }
        
        .terms-card ul {
            color: #6e6e73;
            line-height: 1.8;
            margin-bottom: 24px;
            padding-left: 24px;
        }
        
        .terms-card li {
            margin-bottom: 12px;
        }
        
        .terms-card strong {
            color: #1d1d1f;
        }
        
        .last-updated {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 8px;
            font-size: 14px;
            color: #6e6e73;
            margin-bottom: 32px;
        }
        
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffa500;
            padding: 20px 24px;
            margin: 24px 0;
            border-radius: 8px;
        }
        
        .warning-box p {
            margin: 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    
    <div class="terms-page">
        <div class="terms-hero">
            <h1>Terms of Service</h1>
            <p>Agreement for using Glass Market platform</p>
        </div>
        
        <div class="terms-container">
            <div class="terms-card">
                <div class="last-updated">
                    <strong>Last Updated:</strong> November 3, 2025
                </div>
                
                <p>Welcome to Glass Market. By accessing or using our platform, you agree to be bound by these Terms of Service ("Terms"). Please read them carefully.</p>
                
                <div class="warning-box">
                    <p><strong>Important:</strong> These Terms contain a mandatory arbitration provision and class action waiver. Please review Section 14 carefully.</p>
                </div>
                
                <h2>1. Acceptance of Terms</h2>
                <p>By creating an account, accessing, or using Glass Market, you agree to:</p>
                <ul>
                    <li>Comply with these Terms and all applicable laws</li>
                    <li>Our <a href="<?php echo PUBLIC_URL; ?>/privacy" style="color: #2f6df5;">Privacy Policy</a></li>
                    <li>Our <a href="<?php echo PUBLIC_URL; ?>/seller-guidelines" style="color: #2f6df5;">Seller Guidelines</a> (if selling)</li>
                    <li>Any additional policies and guidelines posted on our platform</li>
                </ul>
                
                <p>If you do not agree to these Terms, you may not use our services.</p>
                
                <h2>2. Eligibility</h2>
                <p>To use Glass Market, you must:</p>
                <ul>
                    <li>Be at least 18 years old</li>
                    <li>Have the legal capacity to enter into binding contracts</li>
                    <li>Not be prohibited from using our services under applicable law</li>
                    <li>Provide accurate and complete registration information</li>
                    <li>Maintain the security of your account credentials</li>
                </ul>
                
                <h2>3. Account Responsibilities</h2>
                
                <h3>You are responsible for:</h3>
                <ul>
                    <li>All activity that occurs under your account</li>
                    <li>Maintaining the confidentiality of your password</li>
                    <li>Notifying us immediately of any unauthorized use</li>
                    <li>Providing truthful and accurate information</li>
                    <li>Updating your information to keep it current</li>
                </ul>
                
                <h3>You may NOT:</h3>
                <ul>
                    <li>Share your account with others</li>
                    <li>Create multiple accounts to circumvent restrictions</li>
                    <li>Impersonate another person or entity</li>
                    <li>Use automated tools to access our platform (bots, scrapers)</li>
                    <li>Sell, transfer, or rent your account to others</li>
                </ul>
                
                <h2>4. Platform Usage</h2>
                
                <h3>Permitted Use</h3>
                <p>You may use Glass Market to:</p>
                <ul>
                    <li>Browse and purchase glass products</li>
                    <li>List and sell glass products (if approved as a seller)</li>
                    <li>Communicate with other users for legitimate transactions</li>
                    <li>Access features and tools provided by our platform</li>
                </ul>
                
                <h3>Prohibited Activities</h3>
                <p>You may NOT:</p>
                <ul>
                    <li>Violate any laws or regulations</li>
                    <li>Infringe on intellectual property rights</li>
                    <li>Post false, misleading, or deceptive content</li>
                    <li>Engage in fraudulent transactions</li>
                    <li>Harass, threaten, or abuse other users</li>
                    <li>Spam or send unsolicited communications</li>
                    <li>Attempt to circumvent fees or payments</li>
                    <li>Interfere with platform operation or security</li>
                    <li>Transmit viruses or malicious code</li>
                    <li>Collect user data without permission</li>
                    <li>Complete transactions off-platform to avoid fees</li>
                </ul>
                
                <h2>5. Buying on Glass Market</h2>
                
                <h3>Purchase Process</h3>
                <ul>
                    <li>All purchases are binding contracts between buyer and seller</li>
                    <li>Prices are set by sellers; we facilitate the transaction</li>
                    <li>Payment is processed through our secure payment system</li>
                    <li>Shipping costs and delivery times are determined by the seller</li>
                    <li>You agree to pay all applicable fees, taxes, and shipping costs</li>
                </ul>
                
                <h3>Buyer Obligations</h3>
                <ul>
                    <li>Make prompt payment for purchases</li>
                    <li>Provide accurate shipping information</li>
                    <li>Communicate professionally with sellers</li>
                    <li>Follow our <a href="<?php echo PUBLIC_URL; ?>/returns" style="color: #2f6df5;">return policy</a> for issues</li>
                    <li>Report problems through proper channels</li>
                </ul>
                
                <h2>6. Selling on Glass Market</h2>
                
                <h3>Seller Requirements</h3>
                <ul>
                    <li>Comply with our <a href="<?php echo PUBLIC_URL; ?>/seller-guidelines" style="color: #2f6df5;">Seller Guidelines</a></li>
                    <li>List only items you own and can legally sell</li>
                    <li>Provide accurate descriptions and photos</li>
                    <li>Honor all sales and ship items promptly</li>
                    <li>Respond to buyer inquiries within 24 hours</li>
                    <li>Package items safely and securely</li>
                    <li>Follow our shipping and handling policies</li>
                </ul>
                
                <h3>Prohibited Items</h3>
                <p>You may NOT sell:</p>
                <ul>
                    <li>Stolen, counterfeit, or illegal items</li>
                    <li>Items containing hazardous materials</li>
                    <li>Items that violate intellectual property rights</li>
                    <li>Recalled or unsafe products</li>
                    <li>Items you don't possess (drop-shipping prohibited)</li>
                </ul>
                
                <h2>7. Fees and Payments</h2>
                
                <h3>Platform Fees</h3>
                <ul>
                    <li>Sellers pay a commission on successful sales (see <a href="<?php echo PUBLIC_URL; ?>/pricing" style="color: #2f6df5;">pricing page</a>)</li>
                    <li>Buyers may incur payment processing fees</li>
                    <li>Fees are automatically deducted from transaction amounts</li>
                    <li>We reserve the right to change fees with 30 days' notice</li>
                </ul>
                
                <h3>Payment Processing</h3>
                <ul>
                    <li>We use third-party payment processors</li>
                    <li>Payments are held until order completion</li>
                    <li>Seller payouts are processed weekly</li>
                    <li>All transactions must go through our platform</li>
                    <li>Attempting to avoid fees violates these Terms</li>
                </ul>
                
                <h2>8. Intellectual Property</h2>
                
                <h3>Our Content</h3>
                <p>Glass Market owns all rights to:</p>
                <ul>
                    <li>Platform design, features, and functionality</li>
                    <li>Glass Market trademarks, logos, and branding</li>
                    <li>Software, code, and algorithms</li>
                    <li>Content created by us (help articles, guides, etc.)</li>
                </ul>
                
                <h3>Your Content</h3>
                <p>You retain ownership of content you post (listings, photos, descriptions). However, you grant us a worldwide, non-exclusive license to use, display, and promote your content on our platform and marketing materials.</p>
                
                <h3>Copyright Complaints</h3>
                <p>If you believe content on our platform infringes your copyright, contact us at dmca@glassmarket.com with:</p>
                <ul>
                    <li>Description of the copyrighted work</li>
                    <li>Location of infringing content (URL)</li>
                    <li>Your contact information</li>
                    <li>Statement of good faith belief</li>
                    <li>Statement of accuracy and authority</li>
                    <li>Physical or electronic signature</li>
                </ul>
                
                <h2>9. Disclaimers and Limitations</h2>
                
                <h3>Platform "As Is"</h3>
                <p>Glass Market is provided "as is" and "as available" without warranties of any kind, express or implied, including:</p>
                <ul>
                    <li>Merchantability or fitness for a particular purpose</li>
                    <li>Uninterrupted or error-free operation</li>
                    <li>Accuracy or reliability of content</li>
                    <li>Quality of products or services</li>
                </ul>
                
                <h3>Third-Party Transactions</h3>
                <p>We are a marketplace platform. We:</p>
                <ul>
                    <li>Do NOT own, sell, or ship the glass products</li>
                    <li>Do NOT guarantee product quality or accuracy of listings</li>
                    <li>Are NOT responsible for seller or buyer conduct</li>
                    <li>Facilitate transactions but are not a party to them</li>
                </ul>
                
                <h3>Limitation of Liability</h3>
                <p>To the maximum extent permitted by law, Glass Market and its officers, directors, employees, and agents will NOT be liable for:</p>
                <ul>
                    <li>Indirect, incidental, or consequential damages</li>
                    <li>Loss of profits, revenue, or data</li>
                    <li>Product defects or delivery issues</li>
                    <li>Disputes between users</li>
                    <li>Unauthorized access to your account</li>
                    <li>Platform downtime or technical issues</li>
                </ul>
                
                <p><strong>Maximum Liability:</strong> Our total liability for any claim will not exceed the amount you paid us in the 12 months preceding the claim, or $100, whichever is greater.</p>
                
                <h2>10. Indemnification</h2>
                <p>You agree to indemnify and hold harmless Glass Market from any claims, damages, or expenses arising from:</p>
                <ul>
                    <li>Your violation of these Terms</li>
                    <li>Your violation of any laws or third-party rights</li>
                    <li>Your use of the platform</li>
                    <li>Content you post or transactions you engage in</li>
                </ul>
                
                <h2>11. Termination</h2>
                
                <h3>We May Terminate or Suspend</h3>
                <p>Your account or access to our platform at any time, without notice, for:</p>
                <ul>
                    <li>Violation of these Terms</li>
                    <li>Fraudulent or illegal activity</li>
                    <li>Harm to other users or the platform</li>
                    <li>Inactivity for extended periods</li>
                    <li>Any reason at our sole discretion</li>
                </ul>
                
                <h3>You May Terminate</h3>
                <p>Your account at any time by contacting us or using account settings. Upon termination:</p>
                <ul>
                    <li>Complete all pending transactions</li>
                    <li>Outstanding fees remain due</li>
                    <li>We may retain certain information per legal requirements</li>
                </ul>
                
                <h2>12. Dispute Resolution</h2>
                
                <h3>Between Users</h3>
                <p>Disputes between buyers and sellers should be resolved directly. We may assist but are not obligated to intervene.</p>
                
                <h3>With Glass Market</h3>
                <p>For disputes with us, please first contact support@glassmarket.com. We'll work in good faith to resolve the issue.</p>
                
                <h2>13. Governing Law</h2>
                <p>These Terms are governed by the laws of the State of New York, United States, without regard to conflict of law principles.</p>
                
                <h2>14. Arbitration Agreement</h2>
                <p><strong>Please read this section carefully.</strong> It affects your rights.</p>
                
                <p>Any dispute arising from these Terms or use of our platform will be resolved through binding arbitration rather than in court, except you may assert claims in small claims court if they qualify.</p>
                
                <h3>Class Action Waiver</h3>
                <p>You agree that disputes will be resolved on an individual basis. You waive the right to participate in class actions, class arbitrations, or representative proceedings.</p>
                
                <h3>Opt-Out</h3>
                <p>You may opt out of this arbitration agreement within 30 days of creating your account by emailing optout@glassmarket.com with your name and statement that you wish to opt out.</p>
                
                <h2>15. General Provisions</h2>
                
                <h3>Modifications</h3>
                <p>We may modify these Terms at any time. We'll notify you of material changes via email or platform notice. Continued use after changes constitutes acceptance.</p>
                
                <h3>Severability</h3>
                <p>If any provision is found unenforceable, it will be modified to reflect the parties' intent, and the remaining provisions will remain in effect.</p>
                
                <h3>Waiver</h3>
                <p>Our failure to enforce any provision does not waive our right to enforce it later.</p>
                
                <h3>Assignment</h3>
                <p>We may assign these Terms to any party. You may not assign your rights or obligations without our consent.</p>
                
                <h3>Entire Agreement</h3>
                <p>These Terms, along with our Privacy Policy and other incorporated policies, constitute the entire agreement between you and Glass Market.</p>
                
                <h2>16. Contact Information</h2>
                <p>Questions about these Terms? Contact us:</p>
                
                <ul style="list-style: none; padding: 0; margin-top: 20px;">
                    <li><strong>Email:</strong> legal@glassmarket.com</li>
                    <li><strong>Phone:</strong> 1-800-GLASS-123</li>
                    <li><strong>Mail:</strong> Glass Market Legal Department<br>123 Glass Street<br>New York, NY 10001</li>
                </ul>
                
                <div style="background: #f0f4ff; border-left: 4px solid #2f6df5; padding: 20px 24px; margin: 32px 0; border-radius: 8px;">
                    <p style="margin: 0; color: #1d1d1f;"><strong>Thank you for using Glass Market!</strong> We're committed to providing a safe, transparent marketplace for glass buyers and sellers.</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
