<?php 
session_start();
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Glass Market</title>
    <link rel="stylesheet" href="<?php echo PUBLIC_URL; ?>/css/app.css">
    <style>
        .contact-page {
            padding: 60px 0 100px;
            background: #f8f9fa;
        }
        
        .contact-hero {
            background: linear-gradient(135deg, #2f6df5 0%, #1e4db8 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 60px;
        }
        
        .contact-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .contact-hero p {
            font-size: 20px;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 32px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
        }
        
        .contact-form-section {
            background: white;
            border-radius: 16px;
            padding: 48px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        
        .contact-form-section h2 {
            font-size: 28px;
            margin-bottom: 24px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1d1d1f;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2f6df5;
        }
        
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #2f6df5 0%, #1e4db8 100%);
            color: white;
            border: none;
            border-radius: 999px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(47, 109, 245, 0.4);
        }
        
        .contact-info-section {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        
        .contact-card {
            background: white;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        
        .contact-card-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #2f6df5 0%, #1e4db8 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
        }
        
        .contact-card h3 {
            font-size: 20px;
            margin-bottom: 8px;
        }
        
        .contact-card p {
            color: #6e6e73;
            line-height: 1.6;
            margin: 0;
        }
        
        .contact-card a {
            color: #2f6df5;
            text-decoration: none;
            font-weight: 500;
        }
        
        .contact-card a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .contact-container {
                grid-template-columns: 1fr;
            }
            
            .contact-form-section {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    
    <div class="contact-page">
        <div class="contact-hero">
            <h1>Get in Touch</h1>
            <p>We're here to help with any questions or concerns</p>
        </div>
        
        <div class="contact-container">
            <div class="contact-form-section">
                <h2>Send us a Message</h2>
                <form method="POST" action="<?php echo PUBLIC_URL; ?>/contact" id="contactForm">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" required placeholder="John Doe">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required placeholder="john@example.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject" required>
                            <option value="">Select a topic</option>
                            <option value="general">General Inquiry</option>
                            <option value="support">Technical Support</option>
                            <option value="billing">Billing Question</option>
                            <option value="seller">Seller Support</option>
                            <option value="buyer">Buyer Support</option>
                            <option value="partnership">Partnership Opportunity</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required placeholder="How can we help you?"></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>
            
            <div class="contact-info-section">
                <div class="contact-card">
                    <div class="contact-card-icon">📧</div>
                    <h3>Email Support</h3>
                    <p>Get a response within 24 hours</p>
                    <p><a href="mailto:support@glassmarket.com">support@glassmarket.com</a></p>
                </div>
                
                <div class="contact-card">
                    <div class="contact-card-icon">💬</div>
                    <h3>Live Chat</h3>
                    <p>Available Monday-Friday</p>
                    <p>9:00 AM - 6:00 PM EST</p>
                </div>
                
                <div class="contact-card">
                    <div class="contact-card-icon">📍</div>
                    <h3>Office Location</h3>
                    <p>123 Glass Street<br>New York, NY 10001<br>United States</p>
                </div>
                
                <div class="contact-card">
                    <div class="contact-card-icon">❓</div>
                    <h3>Help Center</h3>
                    <p>Browse our knowledge base for instant answers</p>
                    <p><a href="<?php echo PUBLIC_URL; ?>/help">Visit Help Center →</a></p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    
    <script>
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Thank you for your message! We will get back to you within 24 hours.');
            this.reset();
        });
    </script>
</body>
</html>
