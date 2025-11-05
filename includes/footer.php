<?php
// Load config if not already loaded
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config.php';
}
?>
<style>
/* Professional Footer Styles */
.modern-footer {
    background: linear-gradient(180deg, #f5f5f7 0%, #e8e9ed 100%);
    border-top: 1px solid rgba(15, 23, 42, 0.08);
}

.footer-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 64px 32px 32px;
}

.footer-content {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1fr;
    gap: 48px;
    margin-bottom: 48px;
}

/* Footer Brand Section */
.footer-brand-section {
    max-width: 320px;
}

.footer-logo {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-family: "SF Pro Display", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    font-weight: 700;
    font-size: 18px;
    color: #1d1d1f;
    text-decoration: none;
    margin-bottom: 16px;
    transition: opacity 0.2s ease;
}

.footer-logo:hover {
    opacity: 0.7;
}

.footer-logo-icon {
    width: 20px;
    height: 20px;
    border-left: 3px solid #1d1d1f;
    border-right: 3px solid #1d1d1f;
}

.footer-description {
    font-size: 15px;
    line-height: 1.6;
    color: #6e6e73;
    margin-bottom: 24px;
}

.footer-social {
    display: flex;
    gap: 12px;
}

.footer-social a {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.6);
    border: 1px solid rgba(15, 23, 42, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1d1d1f;
    transition: all 0.2s ease;
}

.footer-social a:hover {
    background: #fff;
    border-color: rgba(47, 109, 245, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.footer-social svg {
    width: 20px;
    height: 20px;
}

/* Footer Column */
.footer-column h5 {
    font-size: 14px;
    font-weight: 600;
    color: #1d1d1f;
    margin-bottom: 16px;
    letter-spacing: -0.01em;
}

.footer-column ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-column ul li {
    margin-bottom: 12px;
}

.footer-column a {
    font-size: 14px;
    color: #6e6e73;
    text-decoration: none;
    transition: color 0.2s ease;
    display: inline-block;
}

.footer-column a:hover {
    color: #2f6df5;
}

/* Footer Bottom */
.footer-bottom {
    padding-top: 32px;
    border-top: 1px solid rgba(15, 23, 42, 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.footer-copyright {
    font-size: 14px;
    color: #6e6e73;
}

.footer-bottom-links {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
}

.footer-bottom-links a {
    font-size: 14px;
    color: #6e6e73;
    text-decoration: none;
    transition: color 0.2s ease;
}

.footer-bottom-links a:hover {
    color: #2f6df5;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .footer-content {
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

    .footer-brand-section {
        max-width: 100%;
    }
}

@media (max-width: 768px) {
    .footer-container {
        padding: 48px 24px 24px;
    }

    .footer-content {
        grid-template-columns: 1fr;
        gap: 32px;
        margin-bottom: 32px;
    }

    .footer-bottom {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        padding-top: 24px;
    }

    .footer-bottom-links {
        gap: 16px;
    }
}

@media (max-width: 480px) {
    .footer-container {
        padding: 40px 20px 20px;
    }

    .footer-content {
        gap: 28px;
    }

    .footer-column h5 {
        font-size: 13px;
        margin-bottom: 14px;
    }

    .footer-column a {
        font-size: 13px;
    }

    .footer-copyright,
    .footer-bottom-links a {
        font-size: 13px;
    }

    .footer-social a {
        width: 36px;
        height: 36px;
    }

    .footer-social svg {
        width: 18px;
        height: 18px;
    }
}
</style>

<footer class="modern-footer">
    <div class="footer-container">
        <div class="footer-content">
            <!-- Brand Section -->
            <div class="footer-brand-section">
                <a href="<?php echo PUBLIC_URL; ?>/index.php" class="footer-logo">
                    <span class="footer-logo-icon"></span>
                    <span>GLASS MARKET</span>
                </a>
                <p class="footer-description">
                    The leading B2B marketplace for glass cullet trading, connecting recyclers, processors, and manufacturers worldwide.
                </p>
                <div class="footer-social">
                    <a href="#" aria-label="LinkedIn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                            <rect x="2" y="9" width="4" height="12"/>
                            <circle cx="4" cy="4" r="2"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="Twitter">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Marketplace -->
            <div class="footer-column">
                <h5>Marketplace</h5>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/resources/views/browse.php">Browse Collection</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/resources/views/sellers.php">Find Sellers</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/resources/views/about.php">About Us</a></li>
                </ul>
            </div>

            <!-- Sell -->
            <div class="footer-column">
                <h5>For Sellers</h5>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/resources/views/pricing.php">Start Selling</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/resources/views/my-listings.php">My Listings</a></li>
                    <li><a href="<?php echo PUBLIC_URL; ?>/seller-guidelines.php">Seller Guidelines</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div class="footer-column">
                <h5>Support</h5>
                <ul>
                    <li><a href="<?php echo PUBLIC_URL; ?>/help.php">Help Center</a></li>
                    <li><a href="<?php echo PUBLIC_URL; ?>/contact.php">Contact Us</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="footer-bottom">
            <div class="footer-copyright">
                © <?php echo date('Y'); ?> Glass Market. All rights reserved.
            </div>
            <div class="footer-bottom-links">
                <a href="<?php echo PUBLIC_URL; ?>/privacy.php">Privacy Policy</a>
                <a href="<?php echo PUBLIC_URL; ?>/terms.php">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>