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
        :root {
            --contact-bg: #f5f5f7;
            --contact-text: #1d1d1f;
            --contact-muted: #6e6e73;
            --contact-accent: #2f6df5;
            --contact-card-bg: rgba(255, 255, 255, 0.9);
            --contact-border: rgba(15, 23, 42, 0.08);
        }

        body {
            font-family: "SF Pro Display", "SF Pro Text", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--contact-bg);
            color: var(--contact-text);
            margin: 0;
            line-height: 1.6;
        }
        
        .contact-hero {
            background: #f5f5f7;
            color: white;
            padding: 100px 0 80px;
            text-align: center;
            margin-top: 64px;
        }
        
        .contact-hero h1 {
            font-size: clamp(36px, 6vw, 56px);
            font-weight: 700;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
            color: black;
        }
        
        .contact-hero p {
            font-size: 18px;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            color: black;
        }
        
        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 32px 100px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            margin-bottom: 60px;
        }

        .contact-form-card {
            background: var(--contact-card-bg);
            border: 1px solid var(--contact-border);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        .contact-form-card h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 24px;
            color: var(--contact-text);
            letter-spacing: -0.01em;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--contact-text);
            font-size: 15px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--contact-border);
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            background: white;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--contact-accent);
            box-shadow: 0 0 0 3px rgba(47, 109, 245, 0.1);
        }

        .form-group textarea {
            min-height: 140px;
            resize: vertical;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: var(--contact-accent);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(47, 109, 245, 0.2);
        }

        .submit-btn:hover {
            background: #1e4db8;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(47, 109, 245, 0.3);
        }

        .contact-methods {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .contact-method {
            background: var(--contact-card-bg);
            border: 1px solid var(--contact-border);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        .contact-method:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .contact-method-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 12px;
        }

        .contact-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(47, 109, 245, 0.1) 0%, rgba(30, 77, 184, 0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .contact-icon svg {
            width: 24px;
            height: 24px;
            color: black;
        }

        .contact-method h3 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            color: var(--contact-text);
        }

        .contact-method p {
            color: var(--contact-muted);
            font-size: 15px;
            margin: 8px 0 0 0;
            line-height: 1.6;
        }

        .contact-method a {
            color: var(--contact-accent);
            text-decoration: none;
            font-weight: 500;
        }

        .contact-method a:hover {
            text-decoration: underline;
        }

        .faq-section {
            margin-top: 60px;
        }

        .faq-section-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 24px;
            color: var(--contact-text);
            letter-spacing: -0.01em;
            text-align: center;
        }

        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
            max-width: 900px;
            margin: 0 auto;
        }

        .faq-item {
            background: var(--contact-card-bg);
            border: 1px solid var(--contact-border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: box-shadow 0.3s ease;
        }

        .faq-item:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .faq-question {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            padding: 24px;
            font-size: 17px;
            font-weight: 600;
            color: var(--contact-text);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            transition: background 0.2s ease;
        }

        .faq-question:hover {
            background: rgba(47, 109, 245, 0.04);
        }

        .faq-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            transition: transform 0.3s ease;
        }

        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding: 0 24px;
        }

        .faq-item.active .faq-answer {
            max-height: 500px;
            padding: 0 24px 24px;
        }

        .faq-answer-content {
            color: var(--contact-muted);
            font-size: 15px;
            line-height: 1.7;
        }

        @media (max-width: 968px) {
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }
        }

        @media (max-width: 768px) {
            .contact-hero {
                padding: 80px 0 60px;
            }

            .contact-container {
                padding: 40px 20px 80px;
            }

            .contact-form-card {
                padding: 32px 24px;
            }

            .contact-method {
                padding: 24px 20px;
            }

            .faq-question {
                padding: 20px;
                font-size: 16px;
            }

            .faq-answer {
                padding: 0 20px;
            }

            .faq-item.active .faq-answer {
                padding: 0 20px 20px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    
    <div class="contact-hero">
        <h1>Get in Touch</h1>
        <p>We're here to help with any questions or concerns</p>
    </div>
    
    <div class="contact-container">
        <div class="contact-grid">
            <div class="contact-form-card">
                <h2>Send us a Message</h2>
                <form method="POST" id="contactForm">
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

            <div class="contact-methods">
                <div class="contact-method">
                    <div class="contact-method-header">
                        <div class="contact-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3>Email Support</h3>
                    </div>
                    <p>Get a response within 24 hours<br>
                    <a href="mailto:support@glassmarket.com">support@glassmarket.com</a></p>
                </div>

                <div class="contact-method">
                    <div class="contact-method-header">
                        <div class="contact-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h3>Live Chat</h3>
                    </div>
                    <p>Available Monday-Friday<br>9:00 AM - 6:00 PM EST</p>
                </div>

                <div class="contact-method">
                    <div class="contact-method-header">
                        <div class="contact-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3>Office Location</h3>
                    </div>
                    <p>123 Glass Street<br>New York, NY 10001<br>United States</p>
                </div>

                <div class="contact-method">
                    <div class="contact-method-header">
                        <div class="contact-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3>Help Center</h3>
                    </div>
                    <p>Browse our knowledge base for instant answers<br>
                    <a href="<?php echo PUBLIC_URL; ?>/help">Visit Help Center →</a></p>
                </div>
            </div>
        </div>

        <div class="faq-section">
            <h2 class="faq-section-title">Frequently Asked Questions</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question">
                        <span>What is the best way to contact support?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>For urgent issues, we recommend using our live chat feature during business hours (Monday-Friday, 9 AM - 6 PM EST). For non-urgent inquiries, email us at support@glassmarket.com and we'll respond within 24 hours. You can also use the contact form on this page.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>How quickly will I receive a response?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>We aim to respond to all inquiries within 24 hours during business days. Live chat responses are typically immediate during business hours. For urgent technical issues, please mark your message as "Technical Support" for priority handling.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>Can I schedule a call with your team?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes! For complex inquiries or partnership discussions, we're happy to schedule a call. Please use the contact form above and select "Partnership Opportunity" or mention your preference for a call in your message, and we'll coordinate a convenient time.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>Do you offer support in multiple languages?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Currently, our primary support language is English. However, we're expanding our multilingual support and can accommodate Dutch, German, and French inquiries. Please specify your preferred language in your message.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>Where can I find answers without contacting support?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Our comprehensive Help Center contains answers to the most common questions about buying, selling, payments, shipping, and account management. Visit the Help Center to browse articles organized by category, or use the search function to find specific topics.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    
    <script>
        (function() {
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                
                question.addEventListener('click', () => {
                    const isActive = item.classList.contains('active');
                    
                    faqItems.forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.classList.remove('active');
                        }
                    });
                    
                    if (isActive) {
                        item.classList.remove('active');
                    } else {
                        item.classList.add('active');
                    }
                });
            });

            document.getElementById('contactForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const btn = this.querySelector('.submit-btn');
                const originalText = btn.textContent;
                btn.textContent = 'Message Sent!';
                btn.style.background = '#10b981';
                
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.style.background = '';
                    this.reset();
                }, 3000);
            });
        })();
    </script>
</body>
</html>
