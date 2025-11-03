<?php 
session_start();
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Glass Market</title>
    <link rel="stylesheet" href="<?php echo PUBLIC_URL; ?>/css/app.css">
    <style>
        .policy-page {
            padding: 60px 0 100px;
            background: #f8f9fa;
        }
        
        .policy-hero {
            background: linear-gradient(135deg, #2f6df5 0%, #1e4db8 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 60px;
        }
        
        .policy-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .policy-hero p {
            font-size: 18px;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .policy-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 32px;
        }
        
        .policy-card {
            background: white;
            border-radius: 16px;
            padding: 48px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        
        .policy-card h2 {
            font-size: 28px;
            margin-top: 40px;
            margin-bottom: 20px;
            color: #1d1d1f;
        }
        
        .policy-card h3 {
            font-size: 22px;
            margin-top: 32px;
            margin-bottom: 16px;
            color: #1d1d1f;
        }
        
        .policy-card p {
            color: #6e6e73;
            line-height: 1.8;
            margin-bottom: 16px;
        }
        
        .policy-card ul {
            color: #6e6e73;
            line-height: 1.8;
            margin-bottom: 24px;
            padding-left: 24px;
        }
        
        .policy-card li {
            margin-bottom: 12px;
        }
        
        .policy-card strong {
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
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    
    <div class="policy-page">
        <div class="policy-hero">
            <h1>Privacy Policy</h1>
            <p>How we collect, use, and protect your information</p>
        </div>
        
        <div class="policy-container">
            <div class="policy-card">
                <div class="last-updated">
                    <strong>Last Updated:</strong> November 3, 2025
                </div>
                
                <p>At Glass Market, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform.</p>
                
                <div class="info-box">
                    <p><strong>Your Rights:</strong> You have the right to access, correct, or delete your personal information at any time. Contact us at privacy@glassmarket.com for assistance.</p>
                </div>
                
                <h2>1. Information We Collect</h2>
                
                <h3>Information You Provide</h3>
                <p>We collect information you voluntarily provide when using our services:</p>
                <ul>
                    <li><strong>Account Information:</strong> Name, email address, phone number, password</li>
                    <li><strong>Profile Information:</strong> Profile photo, bio, business name, location</li>
                    <li><strong>Payment Information:</strong> Credit card details, billing address, payment history</li>
                    <li><strong>Listing Information:</strong> Product descriptions, photos, pricing</li>
                    <li><strong>Communication:</strong> Messages, reviews, support inquiries</li>
                    <li><strong>Verification Data:</strong> Government ID, business documents (for sellers)</li>
                </ul>
                
                <h3>Automatically Collected Information</h3>
                <p>When you use our platform, we automatically collect:</p>
                <ul>
                    <li><strong>Device Information:</strong> IP address, browser type, operating system</li>
                    <li><strong>Usage Data:</strong> Pages viewed, time spent, clicks, search queries</li>
                    <li><strong>Location Data:</strong> Approximate location based on IP address</li>
                    <li><strong>Cookies & Tracking:</strong> Session data, preferences, analytics</li>
                </ul>
                
                <h2>2. How We Use Your Information</h2>
                <p>We use your information to:</p>
                <ul>
                    <li><strong>Provide Services:</strong> Process transactions, facilitate buying and selling</li>
                    <li><strong>Account Management:</strong> Create and manage your account</li>
                    <li><strong>Communication:</strong> Send order updates, notifications, and support responses</li>
                    <li><strong>Improve Platform:</strong> Analyze usage to enhance features and user experience</li>
                    <li><strong>Security:</strong> Detect fraud, prevent abuse, and protect user safety</li>
                    <li><strong>Marketing:</strong> Send promotional emails (you can opt out anytime)</li>
                    <li><strong>Legal Compliance:</strong> Meet legal obligations and enforce our terms</li>
                    <li><strong>Personalization:</strong> Customize content and recommendations</li>
                </ul>
                
                <h2>3. Information Sharing</h2>
                
                <h3>We Share Your Information With:</h3>
                <ul>
                    <li><strong>Other Users:</strong> Buyers and sellers see necessary transaction information</li>
                    <li><strong>Service Providers:</strong> Payment processors, shipping carriers, email services</li>
                    <li><strong>Business Partners:</strong> Marketing partners (only with your consent)</li>
                    <li><strong>Legal Authorities:</strong> When required by law or to protect rights</li>
                    <li><strong>Business Transfers:</strong> In case of merger, acquisition, or sale</li>
                </ul>
                
                <h3>We Do NOT:</h3>
                <ul>
                    <li>Sell your personal information to third parties</li>
                    <li>Share your data for unrelated purposes without consent</li>
                    <li>Disclose your payment details to other users</li>
                </ul>
                
                <h2>4. Data Security</h2>
                <p>We implement security measures to protect your information:</p>
                <ul>
                    <li><strong>Encryption:</strong> SSL/TLS encryption for data transmission</li>
                    <li><strong>Secure Storage:</strong> Encrypted databases and secure servers</li>
                    <li><strong>Access Controls:</strong> Limited employee access to personal data</li>
                    <li><strong>Regular Audits:</strong> Security assessments and vulnerability testing</li>
                    <li><strong>Payment Security:</strong> PCI-DSS compliant payment processing</li>
                </ul>
                
                <p>However, no method of transmission over the internet is 100% secure. While we strive to protect your data, we cannot guarantee absolute security.</p>
                
                <h2>5. Your Privacy Rights</h2>
                
                <h3>You Have the Right To:</h3>
                <ul>
                    <li><strong>Access:</strong> Request a copy of your personal information</li>
                    <li><strong>Correction:</strong> Update or correct inaccurate information</li>
                    <li><strong>Deletion:</strong> Request deletion of your account and data</li>
                    <li><strong>Opt-Out:</strong> Unsubscribe from marketing emails</li>
                    <li><strong>Data Portability:</strong> Receive your data in a portable format</li>
                    <li><strong>Object:</strong> Object to certain processing of your data</li>
                    <li><strong>Withdraw Consent:</strong> Revoke consent where applicable</li>
                </ul>
                
                <p>To exercise these rights, contact us at privacy@glassmarket.com or through your account settings.</p>
                
                <h2>6. Cookies & Tracking Technologies</h2>
                <p>We use cookies and similar technologies to:</p>
                <ul>
                    <li>Remember your preferences and settings</li>
                    <li>Keep you logged in to your account</li>
                    <li>Analyze site traffic and usage patterns</li>
                    <li>Provide targeted advertising</li>
                    <li>Improve platform functionality</li>
                </ul>
                
                <p>You can control cookies through your browser settings. Note that disabling cookies may limit functionality.</p>
                
                <h3>Types of Cookies We Use:</h3>
                <ul>
                    <li><strong>Essential Cookies:</strong> Required for basic site functionality</li>
                    <li><strong>Performance Cookies:</strong> Help us analyze usage and improve services</li>
                    <li><strong>Functional Cookies:</strong> Remember your preferences</li>
                    <li><strong>Advertising Cookies:</strong> Deliver relevant ads (can be disabled)</li>
                </ul>
                
                <h2>7. Data Retention</h2>
                <p>We retain your information for as long as necessary to:</p>
                <ul>
                    <li>Provide services and maintain your account</li>
                    <li>Comply with legal obligations</li>
                    <li>Resolve disputes and enforce agreements</li>
                    <li>Prevent fraud and maintain security</li>
                </ul>
                
                <p>After account deletion, we may retain certain information for legal and business purposes. Transaction records are typically kept for 7 years.</p>
                
                <h2>8. Children's Privacy</h2>
                <p>Our platform is not intended for children under 18. We do not knowingly collect information from minors. If you believe we have collected information from a child, please contact us immediately.</p>
                
                <h2>9. International Data Transfers</h2>
                <p>Your information may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place to protect your data in accordance with this Privacy Policy.</p>
                
                <h2>10. Third-Party Links</h2>
                <p>Our platform may contain links to third-party websites. We are not responsible for the privacy practices of these sites. We encourage you to read their privacy policies.</p>
                
                <h2>11. Marketing Communications</h2>
                <p>We may send you promotional emails about:</p>
                <ul>
                    <li>New products and features</li>
                    <li>Special offers and discounts</li>
                    <li>Tips and guides</li>
                    <li>Platform updates</li>
                </ul>
                
                <p>You can opt out anytime by:</p>
                <ul>
                    <li>Clicking "Unsubscribe" in any email</li>
                    <li>Updating preferences in your account settings</li>
                    <li>Contacting us at unsubscribe@glassmarket.com</li>
                </ul>
                
                <h2>12. California Privacy Rights (CCPA)</h2>
                <p>California residents have additional rights under the CCPA:</p>
                <ul>
                    <li>Right to know what personal information is collected</li>
                    <li>Right to know if information is sold or shared</li>
                    <li>Right to opt-out of the sale of personal information</li>
                    <li>Right to deletion of personal information</li>
                    <li>Right to non-discrimination for exercising CCPA rights</li>
                </ul>
                
                <p><strong>Note:</strong> We do not sell your personal information.</p>
                
                <h2>13. European Privacy Rights (GDPR)</h2>
                <p>If you are in the European Economic Area (EEA), you have rights under GDPR:</p>
                <ul>
                    <li>Right to access your personal data</li>
                    <li>Right to rectification of inaccurate data</li>
                    <li>Right to erasure ("right to be forgotten")</li>
                    <li>Right to restrict processing</li>
                    <li>Right to data portability</li>
                    <li>Right to object to processing</li>
                    <li>Right to lodge a complaint with supervisory authority</li>
                </ul>
                
                <h2>14. Changes to This Policy</h2>
                <p>We may update this Privacy Policy from time to time. We will notify you of significant changes by:</p>
                <ul>
                    <li>Posting the new policy on this page</li>
                    <li>Updating the "Last Updated" date</li>
                    <li>Sending an email notification (for material changes)</li>
                </ul>
                
                <p>Continued use of our platform after changes constitutes acceptance of the updated policy.</p>
                
                <h2>15. Contact Us</h2>
                <p>If you have questions about this Privacy Policy or our privacy practices, please contact us:</p>
                
                <ul style="list-style: none; padding: 0;">
                    <li><strong>Email:</strong> privacy@glassmarket.com</li>
                    <li><strong>Phone:</strong> 1-800-GLASS-123</li>
                    <li><strong>Mail:</strong> Glass Market, 123 Glass Street, New York, NY 10001</li>
                    <li><strong>Data Protection Officer:</strong> dpo@glassmarket.com</li>
                </ul>
                
                <div class="info-box" style="margin-top: 40px;">
                    <p><strong>Quick Links:</strong> <a href="<?php echo PUBLIC_URL; ?>/terms" style="color: #2f6df5;">Terms of Service</a> | <a href="<?php echo PUBLIC_URL; ?>/help" style="color: #2f6df5;">Help Center</a> | <a href="<?php echo PUBLIC_URL; ?>/contact" style="color: #2f6df5;">Contact Us</a></p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
