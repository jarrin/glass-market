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
                <a href="#">Browse All</a>
                <a href="#">Categories</a>
                <a href="#">New Arrivals</a>
                <a href="#">Featured</a>
            </div>
            <div class="footer-col">
                <h5>Sell</h5>
                <a href="#">Start Selling</a>
                <a href="#">Seller Dashboard</a>
                <a href="#">Guidelines</a>
                <a href="#">Fees</a>
            </div>
            <div class="footer-col">
                <h5>Support</h5>
                <a href="#">Help Center</a>
                <a href="#">Contact Us</a>
                <a href="#">Shipping</a>
                <a href="#">Returns</a>
            </div>
        </div>
    </div>
    <div class="container footer-bottom">
        <div>© 2025 Glass Market. All rights reserved.</div>
        <div class="footer-links">
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <a href="#">Cookies</a>
        </div>
    </div>
</footer>
