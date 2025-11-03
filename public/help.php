<?php 
session_start();
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - Glass Market</title>
    <link rel="stylesheet" href="<?php echo PUBLIC_URL; ?>/css/app.css">
    <style>
        .help-page {
            padding: 60px 0 100px;
            background: #f8f9fa;
        }
        
        .help-hero {
            background: linear-gradient(135deg, #2f6df5 0%, #1e4db8 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 60px;
        }
        
        .help-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .help-hero p {
            font-size: 20px;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .help-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 32px;
        }
        
        .help-search {
            max-width: 600px;
            margin: -80px auto 60px;
            position: relative;
        }
        
        .help-search input {
            width: 100%;
            padding: 18px 24px;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-size: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .help-categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 60px;
        }
        
        .help-category {
            background: white;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .help-category:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        
        .help-category-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #2f6df5 0%, #1e4db8 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 20px;
        }
        
        .help-category h3 {
            font-size: 22px;
            margin-bottom: 12px;
            color: #1d1d1f;
        }
        
        .help-category p {
            color: #6e6e73;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .help-category ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .help-category ul li {
            margin-bottom: 10px;
        }
        
        .help-category ul li a {
            color: #2f6df5;
            text-decoration: none;
            font-weight: 500;
        }
        
        .help-category ul li a:hover {
            text-decoration: underline;
        }
        
        .help-contact {
            background: white;
            border-radius: 16px;
            padding: 48px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        
        .help-contact h2 {
            font-size: 32px;
            margin-bottom: 16px;
        }
        
        .help-contact p {
            color: #6e6e73;
            margin-bottom: 32px;
            font-size: 18px;
        }
        
        .help-contact-btn {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #2f6df5 0%, #1e4db8 100%);
            color: white;
            text-decoration: none;
            border-radius: 999px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .help-contact-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(47, 109, 245, 0.4);
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    
    <div class="help-page">
        <div class="help-hero">
            <h1>How can we help you?</h1>
            <p>Search our knowledge base or browse categories below</p>
        </div>
        
        <div class="help-search">
            <input type="text" placeholder="Search for help articles..." id="helpSearch">
        </div>
        
        <div class="help-container">
            <div class="help-categories">
                <div class="help-category">
                    <div class="help-category-icon">🛒</div>
                    <h3>Getting Started</h3>
                    <p>Learn the basics of buying and selling glass on our platform</p>
                    <ul>
                        <li><a href="#how-to-buy">How to buy glass</a></li>
                        <li><a href="#how-to-sell">How to list your glass</a></li>
                        <li><a href="#account-setup">Setting up your account</a></li>
                        <li><a href="#verification">Account verification</a></li>
                    </ul>
                </div>
                
                <div class="help-category">
                    <div class="help-category-icon">💳</div>
                    <h3>Payments & Pricing</h3>
                    <p>Understand our payment system and fee structure</p>
                    <ul>
                        <li><a href="<?php echo PUBLIC_URL; ?>/pricing">View pricing & fees</a></li>
                        <li><a href="#payment-methods">Accepted payment methods</a></li>
                        <li><a href="#refunds">Refunds & disputes</a></li>
                        <li><a href="#invoices">Downloading invoices</a></li>
                    </ul>
                </div>
                
                <div class="help-category">
                    <div class="help-category-icon">📦</div>
                    <h3>Shipping & Delivery</h3>
                    <p>Everything about shipping glass safely</p>
                    <ul>
                        <li><a href="<?php echo PUBLIC_URL; ?>/shipping">Shipping guidelines</a></li>
                        <li><a href="#packaging">Packaging requirements</a></li>
                        <li><a href="#tracking">Tracking your order</a></li>
                        <li><a href="#delivery-issues">Delivery problems</a></li>
                    </ul>
                </div>
                
                <div class="help-category">
                    <div class="help-category-icon">⚖️</div>
                    <h3>Policies & Guidelines</h3>
                    <p>Our rules and seller requirements</p>
                    <ul>
                        <li><a href="<?php echo PUBLIC_URL; ?>/seller-guidelines">Seller guidelines</a></li>
                        <li><a href="<?php echo PUBLIC_URL; ?>/terms">Terms of service</a></li>
                        <li><a href="<?php echo PUBLIC_URL; ?>/privacy">Privacy policy</a></li>
                        <li><a href="<?php echo PUBLIC_URL; ?>/returns">Return policy</a></li>
                    </ul>
                </div>
                
                <div class="help-category">
                    <div class="help-category-icon">🔒</div>
                    <h3>Account & Security</h3>
                    <p>Manage your account and stay secure</p>
                    <ul>
                        <li><a href="#password-reset">Reset your password</a></li>
                        <li><a href="#update-profile">Update profile information</a></li>
                        <li><a href="#security">Account security tips</a></li>
                        <li><a href="#delete-account">Delete your account</a></li>
                    </ul>
                </div>
                
                <div class="help-category">
                    <div class="help-category-icon">🛠️</div>
                    <h3>Technical Support</h3>
                    <p>Get help with technical issues</p>
                    <ul>
                        <li><a href="#browser-issues">Browser compatibility</a></li>
                        <li><a href="#upload-problems">Upload problems</a></li>
                        <li><a href="#mobile-app">Mobile experience</a></li>
                        <li><a href="#report-bug">Report a bug</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="help-contact">
                <h2>Still need help?</h2>
                <p>Our support team is here to assist you</p>
                <a href="<?php echo PUBLIC_URL; ?>/contact" class="help-contact-btn">Contact Support</a>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    
    <script>
        document.getElementById('helpSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const categories = document.querySelectorAll('.help-category');
            
            categories.forEach(category => {
                const text = category.textContent.toLowerCase();
                category.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>
