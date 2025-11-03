<?php 
session_start();
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Guidelines - Glass Market</title>
    <link rel="stylesheet" href="<?php echo PUBLIC_URL; ?>/css/app.css">
    <style>
        .guidelines-page {
            padding: 60px 0 100px;
            background: #f8f9fa;
        }
        
        .guidelines-hero {
            background: linear-gradient(135deg, #2f6df5 0%, #1e4db8 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 60px;
        }
        
        .guidelines-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .guidelines-hero p {
            font-size: 20px;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .guidelines-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 32px;
        }
        
        .guidelines-card {
            background: white;
            border-radius: 16px;
            padding: 48px;
            margin-bottom: 32px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        
        .guidelines-card h2 {
            font-size: 32px;
            margin-bottom: 24px;
            color: #1d1d1f;
        }
        
        .guidelines-card h3 {
            font-size: 24px;
            margin-top: 32px;
            margin-bottom: 16px;
            color: #1d1d1f;
        }
        
        .guidelines-card p {
            color: #6e6e73;
            line-height: 1.8;
            margin-bottom: 16px;
        }
        
        .guidelines-card ul, .guidelines-card ol {
            color: #6e6e73;
            line-height: 1.8;
            margin-bottom: 24px;
            padding-left: 24px;
        }
        
        .guidelines-card li {
            margin-bottom: 12px;
        }
        
        .guidelines-card strong {
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
        
        .do-dont-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin: 24px 0;
        }
        
        .do-box {
            background: #d4edda;
            padding: 24px;
            border-radius: 12px;
            border-left: 4px solid #28a745;
        }
        
        .dont-box {
            background: #f8d7da;
            padding: 24px;
            border-radius: 12px;
            border-left: 4px solid #dc3545;
        }
        
        .do-box h4, .dont-box h4 {
            margin-top: 0;
            margin-bottom: 12px;
            font-size: 18px;
        }
        
        .do-box ul, .dont-box ul {
            margin: 0;
            padding-left: 20px;
        }
        
        @media (max-width: 768px) {
            .do-dont-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    
    <div class="guidelines-page">
        <div class="guidelines-hero">
            <h1>Seller Guidelines</h1>
            <p>Best practices for selling glass on our marketplace</p>
        </div>
        
        <div class="guidelines-container">
            <div class="guidelines-card">
                <h2>Welcome, Sellers!</h2>
                <p>Thank you for choosing Glass Market as your selling platform. These guidelines will help you create successful listings, maintain high seller standards, and provide excellent customer experiences.</p>
                
                <div class="info-box">
                    <p><strong>💡 Quick Tip:</strong> Following these guidelines helps you rank higher in search results and build buyer trust.</p>
                </div>
                
                <h3>✅ Getting Started</h3>
                <p>Before you start selling:</p>
                <ol>
                    <li><strong>Complete your profile:</strong> Add a profile photo, bio, and business information</li>
                    <li><strong>Verify your account:</strong> Complete email and phone verification</li>
                    <li><strong>Set up payments:</strong> Add your payout method in seller dashboard</li>
                    <li><strong>Review our fees:</strong> Understand our <a href="<?php echo PUBLIC_URL; ?>/pricing" style="color: #2f6df5;">pricing structure</a></li>
                    <li><strong>Read policies:</strong> Familiarize yourself with our <a href="<?php echo PUBLIC_URL; ?>/terms" style="color: #2f6df5;">terms of service</a></li>
                </ol>
                
                <h3>📝 Creating Quality Listings</h3>
                
                <h4>Title Requirements</h4>
                <ul>
                    <li>Use clear, descriptive titles (50-80 characters recommended)</li>
                    <li>Include key details: type, size, color, thickness</li>
                    <li>Avoid ALL CAPS or excessive punctuation!!!</li>
                    <li>Don't use misleading or clickbait titles</li>
                </ul>
                
                <div class="do-dont-grid">
                    <div class="do-box">
                        <h4>✓ DO</h4>
                        <ul>
                            <li>"Clear Tempered Glass Panel 48x36 - 1/4 inch"</li>
                            <li>"Frosted Glass Shower Door - Modern Design"</li>
                            <li>"Vintage Stained Glass Window - Art Deco"</li>
                        </ul>
                    </div>
                    <div class="dont-box">
                        <h4>✗ DON'T</h4>
                        <ul>
                            <li>"AMAZING GLASS!!! BUY NOW!!!"</li>
                            <li>"glass"</li>
                            <li>"Best Glass Ever - Cheap Price"</li>
                        </ul>
                    </div>
                </div>
                
                <h4>Description Best Practices</h4>
                <ul>
                    <li><strong>Be detailed:</strong> Include dimensions, thickness, color, and condition</li>
                    <li><strong>Be honest:</strong> Disclose any scratches, chips, or imperfections</li>
                    <li><strong>Include specifications:</strong> Tempered, laminated, UV-resistant, etc.</li>
                    <li><strong>Mention applications:</strong> What can this glass be used for?</li>
                    <li><strong>Use proper formatting:</strong> Bullet points and paragraphs for readability</li>
                </ul>
                
                <h4>Photography Guidelines</h4>
                <ul>
                    <li><strong>Minimum 3 photos required</strong>, 6-8 recommended</li>
                    <li>Use natural lighting or proper studio lighting</li>
                    <li>Show the item from multiple angles</li>
                    <li>Include close-ups of any defects or unique features</li>
                    <li>Use a clean, uncluttered background</li>
                    <li>Photos should be at least 1000x1000 pixels</li>
                    <li>No watermarks or logos (except your brand)</li>
                </ul>
                
                <div class="warning-box">
                    <p><strong>⚠️ Warning:</strong> Using stock photos or images from other sellers will result in listing removal and potential account suspension.</p>
                </div>
                
                <h3>💰 Pricing Your Items</h3>
                <ul>
                    <li>Research similar items to price competitively</li>
                    <li>Factor in material costs, labor, and platform fees</li>
                    <li>Consider offering bundle deals for multiple items</li>
                    <li>Be transparent about shipping costs</li>
                    <li>Update prices if market conditions change</li>
                </ul>
                
                <h3>📦 Shipping Responsibilities</h3>
                <p>As a seller, you are responsible for:</p>
                <ul>
                    <li><strong>Proper packaging:</strong> Follow our <a href="<?php echo PUBLIC_URL; ?>/shipping" style="color: #2f6df5;">packaging guidelines</a></li>
                    <li><strong>Timely shipping:</strong> Ship within 2 business days of payment</li>
                    <li><strong>Accurate weights:</strong> Provide correct dimensions and weight</li>
                    <li><strong>Tracking updates:</strong> Upload tracking numbers within 24 hours of shipping</li>
                    <li><strong>Insurance:</strong> Insure high-value items (recommended for $100+)</li>
                </ul>
                
                <h3>🤝 Customer Service Standards</h3>
                <p>Maintain excellent customer relationships by:</p>
                <ul>
                    <li>Responding to messages within 24 hours</li>
                    <li>Being professional and courteous in all communications</li>
                    <li>Addressing issues promptly and fairly</li>
                    <li>Accepting legitimate return requests per our policy</li>
                    <li>Requesting feedback after successful transactions</li>
                </ul>
                
                <h3>🚫 Prohibited Items & Practices</h3>
                
                <h4>You may NOT sell:</h4>
                <ul>
                    <li>Stolen or counterfeit glass products</li>
                    <li>Glass containing hazardous materials (asbestos, lead paint)</li>
                    <li>Items you don't actually possess (drop-shipping)</li>
                    <li>Glass that doesn't meet safety standards for its intended use</li>
                    <li>Recalled products or items with known defects</li>
                </ul>
                
                <h4>Prohibited Practices:</h4>
                <ul>
                    <li>Manipulating reviews or ratings</li>
                    <li>Offering to complete transactions off-platform</li>
                    <li>Spamming or creating duplicate listings</li>
                    <li>Misrepresenting item condition or specifications</li>
                    <li>Fee avoidance or payment circumvention</li>
                    <li>Harassment or discrimination of buyers</li>
                </ul>
                
                <div class="warning-box">
                    <p><strong>⚠️ Policy Violations:</strong> Breaking these rules may result in listing removal, account suspension, or permanent ban.</p>
                </div>
                
                <h3>⭐ Performance Metrics</h3>
                <p>We track seller performance based on:</p>
                <ul>
                    <li><strong>Response Rate:</strong> Aim for 90%+ within 24 hours</li>
                    <li><strong>Shipping Time:</strong> Ship within 2 business days</li>
                    <li><strong>Order Defect Rate:</strong> Keep below 1% (cancellations, returns, complaints)</li>
                    <li><strong>Customer Rating:</strong> Maintain 4.5+ star average</li>
                    <li><strong>On-Time Delivery:</strong> 95%+ delivered by estimated date</li>
                </ul>
                
                <div class="info-box">
                    <p><strong>🏆 Top Seller Status:</strong> Sellers who consistently meet performance standards earn badges, better placement in search results, and lower fees!</p>
                </div>
                
                <h3>💳 Payments & Fees</h3>
                <p>Understanding the money flow:</p>
                <ul>
                    <li>Buyers pay through our secure platform</li>
                    <li>Funds are held until item is delivered</li>
                    <li>Our commission (see <a href="<?php echo PUBLIC_URL; ?>/pricing" style="color: #2f6df5;">pricing page</a>) is deducted automatically</li>
                    <li>Payouts are processed weekly to your linked account</li>
                    <li>You can track earnings in your seller dashboard</li>
                </ul>
                
                <h3>📊 Tips for Success</h3>
                <ol>
                    <li><strong>Optimize for search:</strong> Use relevant keywords in titles and descriptions</li>
                    <li><strong>Competitive pricing:</strong> Research what similar items sell for</li>
                    <li><strong>Fast shipping:</strong> Buyers love quick turnaround times</li>
                    <li><strong>Professional photos:</strong> High-quality images sell better</li>
                    <li><strong>Excellent communication:</strong> Quick, helpful responses build trust</li>
                    <li><strong>Request reviews:</strong> Positive feedback attracts more buyers</li>
                    <li><strong>Offer bundles:</strong> Encourage larger purchases</li>
                    <li><strong>Update inventory:</strong> Keep stock levels current</li>
                    <li><strong>Promote your shop:</strong> Share your listings on social media</li>
                    <li><strong>Stay informed:</strong> Read seller updates and policy changes</li>
                </ol>
                
                <h3>📞 Seller Support</h3>
                <p>Need help? We're here for you:</p>
                <ul>
                    <li><strong>Seller Dashboard:</strong> Access analytics and manage listings</li>
                    <li><strong>Help Center:</strong> Browse <a href="<?php echo PUBLIC_URL; ?>/help" style="color: #2f6df5;">seller resources</a></li>
                    <li><strong>Email Support:</strong> sellers@glassmarket.com</li>
                    <li><strong>Phone:</strong> 1-800-GLASS-123 (Mon-Fri, 9 AM - 6 PM EST)</li>
                    <li><strong>Seller Community:</strong> Join our forums to connect with other sellers</li>
                </ul>
                
                <div class="info-box">
                    <p><strong>🚀 Ready to Start Selling?</strong> Head to your <a href="<?php echo PUBLIC_URL; ?>/listings" style="color: #2f6df5;">seller dashboard</a> to create your first listing!</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
