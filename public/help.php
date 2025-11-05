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
        :root {
            --help-bg: #f5f5f7;
            --help-text: #1d1d1f;
            --help-muted: #6e6e73;
            --help-accent: #2f6df5;
            --help-card-bg: rgba(255, 255, 255, 0.9);
            --help-border: rgba(15, 23, 42, 0.08);
        }

        body {
            font-family: "SF Pro Display", "SF Pro Text", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--help-bg);
            color: var(--help-text);
            margin: 0;
            line-height: 1.6;
        }
        
        .help-hero {
            background: var(--help-bg);
            color: white;
            padding: 100px 0 80px;
            text-align: center;
            margin-top: 64px;
        }
        
        .help-hero h1 {
            font-size: clamp(36px, 6vw, 56px);
            font-weight: 700;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
            color: black;
        }
        
        .help-hero p {
            font-size: 18px;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            color: black;
        }
        
        .help-container {
            max-width: 1024px;
            margin: 0 auto;
            padding: 60px 32px 100px;
        }
        
        .help-section {
            margin-bottom: 48px;
        }

        .help-section-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 24px;
            color: var(--help-text);
            letter-spacing: -0.01em;
        }

        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .faq-item {
            background: var(--help-card-bg);
            border: 1px solid var(--help-border);
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
            color: var(--help-text);
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
            max-height: 1000px;
            padding: 0 24px 24px;
        }

        .faq-answer-content {
            color: var(--help-muted);
            font-size: 15px;
            line-height: 1.7;
        }

        .faq-answer-content ul {
            margin: 12px 0;
            padding-left: 24px;
        }

        .faq-answer-content li {
            margin-bottom: 8px;
        }

        .faq-answer-content a {
            color: var(--help-accent);
            text-decoration: none;
            font-weight: 500;
        }

        .faq-answer-content a:hover {
            text-decoration: underline;
        }

        .contact-card {
            background: linear-gradient(135deg, rgba(47, 109, 245, 0.08) 0%, rgba(30, 77, 184, 0.08) 100%);
            border: 1px solid rgba(47, 109, 245, 0.15);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            margin-top: 60px;
        }

        .contact-card h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--help-text);
        }

        .contact-card p {
            color: var(--help-muted);
            margin-bottom: 24px;
            font-size: 15px;
        }

        .contact-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            background: var(--help-accent);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(47, 109, 245, 0.2);
        }

        .contact-btn:hover {
            background: #1e4db8;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(47, 109, 245, 0.3);
        }

        @media (max-width: 768px) {
            .help-hero {
                padding: 80px 0 60px;
            }

            .help-container {
                padding: 40px 20px 80px;
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

            .contact-card {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    
    <div class="help-hero">
        <h1>Help Center</h1>
        <p>Find answers to your questions about buying and selling glass</p>
    </div>
    
    <div class="help-container">
        <div class="help-section">
            <h2 class="help-section-title">Getting Started</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question">
                        <span>How do I create an account?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Creating an account on Glass Market is simple:</p>
                            <ul>
                                <li>Click the "Register" button in the top right corner</li>
                                <li>Fill in your business information and contact details</li>
                                <li>Verify your email address through the confirmation link</li>
                                <li>Complete your profile to start buying or selling</li>
                            </ul>
                            <p>Business accounts require verification for enhanced security and trust.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>How do I buy glass products?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Purchasing glass on our platform:</p>
                            <ul>
                                <li>Browse our catalog or use the search feature</li>
                                <li>View detailed product specifications and seller ratings</li>
                                <li>Request quotes or contact sellers directly</li>
                                <li>Complete payment through our secure checkout</li>
                                <li>Track your order and communicate with the seller</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>How do I list glass products for sale?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>To list your glass products:</p>
                            <ul>
                                <li>Ensure your account is verified as a seller</li>
                                <li>Click "Create Listing" from your dashboard</li>
                                <li>Add detailed product information, photos, and pricing</li>
                                <li>Set shipping options and availability</li>
                                <li>Review and publish your listing</li>
                            </ul>
                            <p>Check our <a href="<?php echo PUBLIC_URL; ?>/seller-guidelines">Seller Guidelines</a> for best practices.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Payments & Pricing</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question">
                        <span>What are the fees for selling?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Our fee structure is transparent and competitive:</p>
                            <ul>
                                <li><strong>Free Plan:</strong> 5% commission per sale</li>
                                <li><strong>Basic Plan (€29/month):</strong> 3% commission + priority support</li>
                                <li><strong>Professional Plan (€79/month):</strong> 2% commission + featured listings + analytics</li>
                            </ul>
                            <p>No hidden fees. Payment processing fees are separate.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>What payment methods do you accept?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>We accept multiple payment methods through our secure payment processor:</p>
                            <ul>
                                <li>Credit and debit cards (Visa, Mastercard, American Express)</li>
                                <li>Bank transfers (SEPA for EU customers)</li>
                                <li>iDEAL (Netherlands)</li>
                                <li>Business invoicing (for qualified accounts)</li>
                            </ul>
                            <p>All transactions are encrypted and PCI-compliant.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>How do refunds and disputes work?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>We protect both buyers and sellers:</p>
                            <ul>
                                <li>Contact the seller first to resolve any issues</li>
                                <li>Open a dispute through your order dashboard if unresolved</li>
                                <li>Our mediation team reviews all evidence within 48 hours</li>
                                <li>Refunds are processed to the original payment method within 5-10 business days</li>
                            </ul>
                            <p>See our <a href="<?php echo PUBLIC_URL; ?>/returns">Return Policy</a> for full details.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Shipping & Delivery</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question">
                        <span>How should glass products be packaged?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Proper packaging is essential for safe delivery:</p>
                            <ul>
                                <li>Use double-walled cardboard boxes or wooden crates</li>
                                <li>Wrap each piece in bubble wrap or foam padding</li>
                                <li>Add corner protectors for large sheets</li>
                                <li>Fill empty spaces to prevent movement</li>
                                <li>Label as "FRAGILE" and "THIS SIDE UP"</li>
                            </ul>
                            <p>View detailed <a href="<?php echo PUBLIC_URL; ?>/shipping">Shipping Guidelines</a>.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>How do I track my order?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Track your orders easily:</p>
                            <ul>
                                <li>Log in to your account and go to "My Orders"</li>
                                <li>Click on the order number to view details</li>
                                <li>Tracking numbers are provided by the seller</li>
                                <li>Receive email notifications at each shipping milestone</li>
                            </ul>
                            <p>Contact the seller directly if tracking information is missing.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>What if my order arrives damaged?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>If your glass arrives damaged:</p>
                            <ul>
                                <li>Document the damage with photos immediately</li>
                                <li>Keep all packaging materials</li>
                                <li>Contact the seller within 48 hours</li>
                                <li>File a claim with the shipping carrier if applicable</li>
                                <li>Work with the seller on a replacement or refund</li>
                            </ul>
                            <p>Most sellers include insurance for high-value shipments.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="help-section">
            <h2 class="help-section-title">Account & Security</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question">
                        <span>How do I reset my password?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>To reset your password:</p>
                            <ul>
                                <li>Click "Forgot Password" on the login page</li>
                                <li>Enter your registered email address</li>
                                <li>Check your email for a reset link (may be in spam folder)</li>
                                <li>Click the link and create a new strong password</li>
                                <li>Log in with your new credentials</li>
                            </ul>
                            <p>Reset links expire after 1 hour for security.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>How do I update my profile information?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Keep your profile up to date:</p>
                            <ul>
                                <li>Go to "Account Settings" from your dashboard</li>
                                <li>Edit your business name, contact information, or address</li>
                                <li>Update your payment and shipping preferences</li>
                                <li>Save changes and verify your email if changed</li>
                            </ul>
                            <p>Accurate information helps build trust with buyers and sellers.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>Is my data secure?</span>
                        <svg class="faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>We take security seriously:</p>
                            <ul>
                                <li>SSL encryption for all data transmission</li>
                                <li>PCI-DSS compliant payment processing</li>
                                <li>Regular security audits and updates</li>
                                <li>Two-factor authentication available</li>
                                <li>GDPR compliant data handling</li>
                            </ul>
                            <p>Read our <a href="<?php echo PUBLIC_URL; ?>/privacy">Privacy Policy</a> for details.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-card">
            <h3>Still need help?</h3>
            <p>Our support team is ready to assist you with any questions or concerns</p>
            <a href="<?php echo PUBLIC_URL; ?>/contact" class="contact-btn">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Contact Support
            </a>
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
        })();
    </script>
</body>
</html>
