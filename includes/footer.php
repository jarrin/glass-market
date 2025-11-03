<?php
// Load config if not already loaded
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config.php';
}
?>
<footer class="footer">
    <div class="container footer-top">
        <div class="footer-brand">
            <a class="brand" href="<?php echo PUBLIC_URL; ?>/index.php"><span class="brand-mark"></span><span>GLASS MARKET</span></a>
            <p>Discover all types of glass for every job!<br>The best place to buy and sell glass</p>
        </div>
        <div class="footer-cols">
            <div class="footer-col">
                <h5>Shop</h5>
                <a href="<?php echo BASE_URL; ?>/resources/views/browse.php">Browse All</a>
            </div>
            <div class="footer-col">
                <h5>Sell</h5>
                <a href="<?php echo BASE_URL; ?>/resources/views/pricing.php">Start Selling</a>
                <a href="<?php echo BASE_URL; ?>/resources/views/my-listings.php">Seller Dashboard</a>
                <a href="<?php echo PUBLIC_URL; ?>/seller-guidelines.php">Guidelines</a>
                <a href="<?php echo BASE_URL; ?>/resources/views/pricing.php">Fees</a>
            </div>
            <div class="footer-col">
                <h5>Support</h5>
                <a href="<?php echo PUBLIC_URL; ?>/help.php">Help Center</a>
                <a href="<?php echo PUBLIC_URL; ?>/contact.php">Contact Us</a>
                <a href="<?php echo PUBLIC_URL; ?>/shipping.php">Shipping</a>
                <a href="<?php echo PUBLIC_URL; ?>/returns.php">Returns</a>
            </div>
        </div>
    </div>
    <div class="container footer-bottom">
        <div>© 2025 Glass Market. All rights reserved.</div>
        <div class="footer-links">
            <a href="<?php echo PUBLIC_URL; ?>/privacy.php">Privacy</a>
            <a href="<?php echo PUBLIC_URL; ?>/terms.php">Terms</a>
            <a href="<?php echo BASE_URL; ?>/resources/views/about.php">About</a>
        </div>
    </div>
</footer>
