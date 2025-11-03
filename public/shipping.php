<?php 
session_start();
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Information - Glass Market</title>
    <link rel="stylesheet" href="<?php echo PUBLIC_URL; ?>/css/app.css">
    <style>
        .shipping-page {
            padding: 60px 0 100px;
            background: #f8f9fa;
        }
        
        .shipping-hero {
            background: linear-gradient(135deg, #2f6df5 0%, #1e4db8 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 60px;
        }
        
        .shipping-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .shipping-hero p {
            font-size: 20px;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .shipping-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 32px;
        }
        
        .shipping-card {
            background: white;
            border-radius: 16px;
            padding: 48px;
            margin-bottom: 32px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        
        .shipping-card h2 {
            font-size: 32px;
            margin-bottom: 24px;
            color: #1d1d1f;
        }
        
        .shipping-card h3 {
            font-size: 24px;
            margin-top: 32px;
            margin-bottom: 16px;
            color: #1d1d1f;
        }
        
        .shipping-card p {
            color: #6e6e73;
            line-height: 1.8;
            margin-bottom: 16px;
        }
        
        .shipping-card ul, .shipping-card ol {
            color: #6e6e73;
            line-height: 1.8;
            margin-bottom: 24px;
            padding-left: 24px;
        }
        
        .shipping-card li {
            margin-bottom: 12px;
        }
        
        .shipping-card strong {
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
        
        .pricing-table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
        }
        
        .pricing-table th,
        .pricing-table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #e5e5ea;
        }
        
        .pricing-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #1d1d1f;
        }
        
        .pricing-table tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    
    <div class="shipping-page">
        <div class="shipping-hero">
            <h1>Shipping & Delivery</h1>
            <p>Everything you need to know about shipping glass safely</p>
        </div>
        
        <div class="shipping-container">
            <div class="shipping-card">
                <h2>Shipping Guidelines</h2>
                <p>At Glass Market, we understand that shipping glass requires special care and attention. We've partnered with leading carriers to ensure your glass arrives safely and on time.</p>
                
                <h3>🚚 Shipping Methods</h3>
                <p>We offer several shipping options to meet your needs:</p>
                <ul>
                    <li><strong>Standard Shipping:</strong> 5-7 business days - Most economical option</li>
                    <li><strong>Express Shipping:</strong> 2-3 business days - Faster delivery</li>
                    <li><strong>Priority Overnight:</strong> Next business day - Fastest option</li>
                    <li><strong>Freight Shipping:</strong> For large or bulk orders - Custom quote required</li>
                </ul>
                
                <h3>📦 Packaging Requirements</h3>
                <p>Proper packaging is crucial for glass shipments. All sellers must follow these guidelines:</p>
                <ol>
                    <li><strong>Use sturdy double-walled boxes</strong> designed for fragile items</li>
                    <li><strong>Wrap each piece individually</strong> with bubble wrap (minimum 2 layers)</li>
                    <li><strong>Fill void spaces</strong> with packing peanuts or crumpled paper</li>
                    <li><strong>Label clearly</strong> with "FRAGILE" and "GLASS" on all sides</li>
                    <li><strong>Include handling arrows</strong> showing proper orientation</li>
                    <li><strong>Use corner protectors</strong> for sheet glass and mirrors</li>
                </ol>
                
                <div class="warning-box">
                    <p><strong>⚠️ Important:</strong> Improperly packaged items may be rejected by carriers or not covered by shipping insurance. Always follow packaging best practices.</p>
                </div>
                
                <h3>💰 Shipping Costs</h3>
                <p>Shipping costs are calculated based on:</p>
                <ul>
                    <li>Package weight and dimensions</li>
                    <li>Shipping method selected</li>
                    <li>Destination distance</li>
                    <li>Insurance value (recommended for all glass shipments)</li>
                </ul>
                
                <table class="pricing-table">
                    <thead>
                        <tr>
                            <th>Shipping Method</th>
                            <th>Delivery Time</th>
                            <th>Base Rate (up to 10 lbs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Standard</td>
                            <td>5-7 business days</td>
                            <td>$12.99</td>
                        </tr>
                        <tr>
                            <td>Express</td>
                            <td>2-3 business days</td>
                            <td>$24.99</td>
                        </tr>
                        <tr>
                            <td>Priority Overnight</td>
                            <td>Next business day</td>
                            <td>$49.99</td>
                        </tr>
                        <tr>
                            <td>Freight (Pallets)</td>
                            <td>Varies</td>
                            <td>Custom Quote</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="info-box">
                    <p><strong>💡 Pro Tip:</strong> Shipping insurance is highly recommended for all glass shipments. It typically costs 2-3% of the declared value.</p>
                </div>
                
                <h3>📍 Tracking Your Order</h3>
                <p>Once your order ships, you'll receive:</p>
                <ul>
                    <li>Email confirmation with tracking number</li>
                    <li>Real-time tracking updates</li>
                    <li>Estimated delivery date</li>
                    <li>Delivery confirmation upon arrival</li>
                </ul>
                
                <h3>🌍 International Shipping</h3>
                <p>We currently ship to select international destinations. International orders may be subject to:</p>
                <ul>
                    <li>Customs duties and import taxes (buyer responsibility)</li>
                    <li>Extended delivery times (10-21 business days)</li>
                    <li>Additional documentation requirements</li>
                    <li>Restricted items based on destination country</li>
                </ul>
                
                <h3>❌ Delivery Issues</h3>
                <p>If you experience any problems with delivery:</p>
                <ul>
                    <li><strong>Damaged items:</strong> Take photos immediately and contact us within 48 hours</li>
                    <li><strong>Lost packages:</strong> Report after 2 business days past expected delivery</li>
                    <li><strong>Wrong items:</strong> Contact us immediately for a return label</li>
                    <li><strong>Missing items:</strong> Verify your packing slip and report within 24 hours</li>
                </ul>
                
                <div class="info-box">
                    <p><strong>📞 Need Help?</strong> Our shipping support team is available Monday-Friday, 9 AM - 6 PM EST. Email us at <a href="mailto:shipping@glassmarket.com" style="color: #2f6df5;">shipping@glassmarket.com</a> or call 1-800-GLASS-123.</p>
                </div>
                
                <h3>📋 Seller Shipping Checklist</h3>
                <p>Before shipping your glass:</p>
                <ol>
                    <li>✓ Confirm order details and shipping address</li>
                    <li>✓ Package item according to guidelines above</li>
                    <li>✓ Print shipping label from your seller dashboard</li>
                    <li>✓ Add insurance (recommended)</li>
                    <li>✓ Drop off at carrier location or schedule pickup</li>
                    <li>✓ Update tracking information in the system</li>
                    <li>✓ Notify buyer that item has shipped</li>
                </ol>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
