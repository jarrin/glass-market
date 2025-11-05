<?php
// Blurry glass navbar include
// Usage: <?php include __DIR__ . '/includes/navbar.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$user_name = $is_logged_in ? ($_SESSION['user_name'] ?? 'User') : '';
$user_email = $is_logged_in ? ($_SESSION['user_email'] ?? '') : '';
$user_avatar = $is_logged_in ? ($_SESSION['user_avatar'] ?? '') : '';
$is_admin = $is_logged_in ? ($_SESSION['is_admin'] ?? 0) : 0;
?>

<style>
/* Navbar root */
.glass-navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  z-index: 1000;
  display: flex;
  align-items: center;
  gap: 24px;
  padding: 12px 24px;
  box-sizing: border-box;
  /* Liquid glass effect */
  background: rgba(255, 255, 255, 0.25);
  -webkit-backdrop-filter: blur(20px) saturate(180%);
  backdrop-filter: blur(20px) saturate(180%);
  border-bottom: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08),
              inset 0 1px 0 rgba(255, 255, 255, 0.5);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Fallback for browsers without backdrop-filter */
.no-backdrop .glass-navbar {
  background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(255,255,255,0.85));
}

.glass-navbar .brand {
  display: flex;
  align-items: center;
  gap: 10px;
  font-family: Georgia, 'Times New Roman', serif;
  font-weight: 700;
  color: #101010;
  text-decoration: none;
  padding: 6px 10px;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.glass-navbar .brand .logo-bars {
  width: 18px;
  height: 18px;
  border-left: 3px solid #1b1b1b;
  border-right: 3px solid #1b1b1b;
  transform: scaleY(1.1);
}

.glass-navbar .nav-links {
  display: flex;
  gap: 18px;
  margin-left: 12px;
}

.glass-navbar .nav-links a {
  color: rgba(10,10,10,0.9);
  text-decoration: none;
  font-size: 14px;
  padding: 8px 12px;
  border-radius: 8px;
  transition: all 0.2s ease;
  position: relative;
}

.glass-navbar .nav-links a:hover {
  background: rgba(255,255,255,0.4);
  -webkit-backdrop-filter: blur(10px);
  backdrop-filter: blur(10px);
  color: rgba(10,10,10,1);
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.glass-navbar .search {
  margin-left: auto;
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 320px;
  max-width: 520px;
}

.glass-navbar .search input[type="search"] {
  width: 100%;
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid rgba(16,16,16,0.08);
  background: rgba(255,255,255,0.4);
  -webkit-backdrop-filter: blur(10px);
  backdrop-filter: blur(10px);
  outline: none;
  font-size: 13px;
  transition: all 0.2s ease;
}

.glass-navbar .search input[type="search"]:focus {
  background: rgba(255,255,255,0.6);
  border-color: rgba(16,16,16,0.15);
  box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
}

.glass-navbar .icons {
  display: flex;
  gap: 10px;
  margin-left: 12px;
}

.icon-circle {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: rgba(255,255,255,0.3);
  -webkit-backdrop-filter: blur(10px);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.3);
  transition: all 0.2s ease;
  cursor: pointer;
}

.icon-circle:hover {
  background: rgba(255,255,255,0.5);
  border-color: rgba(255,255,255,0.5);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* Auth buttons */
.auth-buttons {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-left: 12px;
}

/* User info display */
.user-info-display {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 6px 12px;
  border-radius: 8px;
  background: rgba(255,255,255,0.3);
  -webkit-backdrop-filter: blur(10px);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.3);
  transition: all 0.2s ease;
}

.user-info-display:hover {
  background: rgba(255,255,255,0.4);
  border-color: rgba(255,255,255,0.4);
}

.user-name-text {
  font-size: 14px;
  font-weight: 500;
  color: rgba(10,10,10,0.9);
  white-space: nowrap;
}

.btn-login, .btn-register {
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
  white-space: nowrap;
}

.btn-login {
  color: rgba(10,10,10,0.9);
  background: rgba(255,255,255,0.4);
  -webkit-backdrop-filter: blur(10px);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(16,16,16,0.08);
}

.btn-login:hover {
  background: rgba(255,255,255,0.6);
  border-color: rgba(16,16,16,0.15);
  color: rgba(10,10,10,1);
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.btn-register {
  color: white;
  background: rgba(16,16,16,0.85);
  border: 1px solid rgba(16,16,16,0.9);
}

.btn-register:hover {
  background: rgba(10,10,10,0.95);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  transform: translateY(-1px);
}

/* User profile section */
.user-profile {
  position: relative;
  margin-left: 12px;
}

/* Hamburger Menu Button */
.hamburger-menu {
  display: none;
  width: 40px;
  height: 40px;
  border: none;
  background: rgba(255,255,255,0.4);
  -webkit-backdrop-filter: blur(10px);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(16,16,16,0.08);
  border-radius: 8px;
  cursor: pointer;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 5px;
  padding: 8px;
  transition: all 0.3s ease;
  margin-left: auto;
}

.hamburger-menu:hover {
  background: rgba(255,255,255,0.6);
  border-color: rgba(16,16,16,0.15);
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.hamburger-menu span {
  display: block;
  width: 20px;
  height: 2px;
  background: rgba(10,10,10,0.9);
  border-radius: 2px;
  transition: all 0.3s ease;
}

.hamburger-menu.active span:nth-child(1) {
  transform: translateY(7px) rotate(45deg);
}

.hamburger-menu.active span:nth-child(2) {
  opacity: 0;
}

.hamburger-menu.active span:nth-child(3) {
  transform: translateY(-7px) rotate(-45deg);
}

/* Mobile Menu Overlay */
.mobile-menu-overlay {
  display: none;
  position: fixed;
  top: 64px;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.4);
  -webkit-backdrop-filter: blur(4px);
  backdrop-filter: blur(4px);
  z-index: 999;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease;
}

.mobile-menu-overlay.active {
  opacity: 1;
  pointer-events: auto;
}

/* Mobile Menu Panel */
.mobile-menu {
  display: none;
  position: fixed;
  top: 64px;
  right: -100%;
  width: 280px;
  max-width: 85vw;
  height: calc(100vh - 64px);
  background: rgba(255, 255, 255, 0.95);
  -webkit-backdrop-filter: blur(20px) saturate(180%);
  backdrop-filter: blur(20px) saturate(180%);
  border-left: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: -8px 0 32px rgba(0, 0, 0, 0.12);
  z-index: 1000;
  transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow-y: auto;
  padding: 24px 0;
  pointer-events: none;
}

.mobile-menu.active {
  right: 0;
  pointer-events: auto;
}

.mobile-menu-section {
  padding: 0 24px;
  margin-bottom: 32px;
}

.mobile-menu-section h3 {
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: rgba(10,10,10,0.5);
  margin-bottom: 12px;
}

.mobile-menu-links {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.mobile-menu-links a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 10px;
  color: rgba(10,10,10,0.9);
  text-decoration: none;
  font-size: 15px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.mobile-menu-links a:hover {
  background: rgba(47, 109, 245, 0.08);
  color: rgba(10,10,10,1);
}

.mobile-menu-links svg {
  width: 20px;
  height: 20px;
  stroke: currentColor;
}

.mobile-user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: rgba(255,255,255,0.6);
  -webkit-backdrop-filter: blur(10px);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  margin-bottom: 8px;
}

.mobile-user-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: rgba(255,255,255,0.4);
  border: 2px solid rgba(255,255,255,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.mobile-user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.mobile-user-name {
  font-size: 15px;
  font-weight: 600;
  color: rgba(10,10,10,0.9);
}

.mobile-auth-buttons {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.mobile-auth-buttons .btn-login,
.mobile-auth-buttons .btn-register {
  width: 100%;
  text-align: center;
  padding: 12px 16px;
  border-radius: 10px;
}

/* Responsive */
@media (max-width: 768px) {
  .hamburger-menu {
    display: flex;
  }

  .mobile-menu,
  .mobile-menu-overlay {
    display: block;
  }

  .glass-navbar .nav-links {
    display: none;
  }

  .glass-navbar .search {
    display: none;
  }

  .glass-navbar .auth-buttons {
    display: none;
  }

  .glass-navbar {
    padding: 12px 16px;
  }
}

/* User profile section */
  width: 40px;
  height: 40px;
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid rgba(255,255,255,0.5);
  background: rgba(255,255,255,0.3);
  -webkit-backdrop-filter: blur(10px);
  backdrop-filter: blur(10px);
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.profile-avatar:hover {
  border-color: rgba(255,255,255,0.7);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.profile-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.profile-avatar-default {
  width: 20px;
  height: 20px;
  color: #111;
}

/* Dropdown menu */
.profile-dropdown {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  min-width: 220px;
  background: rgba(255, 255, 255, 0.95);
  -webkit-backdrop-filter: blur(20px) saturate(180%);
  backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 1001;
}

.user-profile:hover .profile-dropdown {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-header {
  padding: 16px;
  border-bottom: 1px solid rgba(0,0,0,0.05);
}

.dropdown-header .user-name {
  font-weight: 600;
  color: #111;
  font-size: 14px;
  margin-bottom: 4px;
}

.dropdown-header .user-email {
  font-size: 12px;
  color: rgba(0,0,0,0.6);
}

.dropdown-menu {
  padding: 8px;
}

.dropdown-menu a {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  color: rgba(10,10,10,0.9);
  text-decoration: none;
  font-size: 14px;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.dropdown-menu a:hover {
  background: rgba(0,0,0,0.05);
  color: rgba(10,10,10,1);
}

.dropdown-menu a svg {
  width: 16px;
  height: 16px;
}

.dropdown-divider {
  height: 1px;
  background: rgba(0,0,0,0.05);
  margin: 8px 0;
}

.dropdown-menu .logout-link {
  color: #dc2626;
}

.dropdown-menu .logout-link:hover {
  background: rgba(220, 38, 38, 0.08);
}

/* Responsive adjustments */
@media (max-width: 820px) {
  .glass-navbar .search { min-width: 180px; max-width: 260px; }
  .glass-navbar .nav-links { display: none; }
}

@media (max-width: 680px) {
  .user-name-text { display: none; } /* Hide name on smaller screens */
  .user-info-display { padding: 0; background: none; border: none; }
}

@media (max-width: 480px) {
  .glass-navbar { padding: 8px 12px; }
  .glass-navbar .search { display: none; }
  .btn-login, .btn-register {
    padding: 6px 12px;
    font-size: 13px;
  }
  .auth-buttons { margin-left: auto; }
  .user-profile { margin-left: auto; }
  .profile-dropdown { min-width: 200px; }
}

/* Scrolled state - extra glassmorphism */
.glass-navbar.scrolled {
  background: rgba(255, 255, 255, 0.2);
  -webkit-backdrop-filter: blur(24px) saturate(200%);
  backdrop-filter: blur(24px) saturate(200%);
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1),
              inset 0 1px 0 rgba(255, 255, 255, 0.6);
}
</style>

<script>
// Add scroll effect
window.addEventListener('scroll', function() {
  const navbar = document.querySelector('.glass-navbar');
  if (window.scrollY > 50) {
    navbar.classList.add('scrolled');
  } else {
    navbar.classList.remove('scrolled');
  }
});
</script>

<header class="glass-navbar" role="banner">
  <a href="<?php echo PUBLIC_URL; ?>/index.php" class="brand" aria-label="Glass Market home">
    <span class="logo-bars" aria-hidden="true"></span>
    <span style="font-size:16px;">GLASS MARKET</span>
  </a>

  <nav class="nav-links" role="navigation" aria-label="Primary">
    <a href="<?php echo VIEWS_URL; ?>/browse.php">Browse</a>
    <a href="<?php echo VIEWS_URL; ?>/sellers.php">Sellers</a>
    <a href="<?php echo VIEWS_URL; ?>/about.php">About</a>
  </nav>

  <div class="search">

  </div>

  <div class="auth-buttons">
    <?php if (isset($is_logged_in) && $is_logged_in): ?>
      <!-- Logged in: Show user info -->
      <div class="user-info-display">
        <a href="<?php echo VIEWS_URL; ?>/profile.php" class="icon-circle" title="Profile" style="margin: 0;">
          <?php if (!empty($user_avatar)): ?>
            <img src="<?php echo htmlspecialchars($user_avatar); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
          <?php else: ?>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          <?php endif; ?>
        </a>
        <span class="user-name-text"><?php echo htmlspecialchars($user_name); ?></span>
      </div>

      <?php if (isset($is_admin) && $is_admin == 1): ?>
        <a href="<?php echo VIEWS_URL; ?>/admin/dashboard.php" class="btn-register" style="display: flex; align-items: center; gap: 6px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
            <path d="M2 17l10 5 10-5"></path>
            <path d="M2 12l10 5 10-5"></path>
          </svg>
          Admin
        </a>
      <?php endif; ?>

      <a href="<?php echo VIEWS_URL; ?>/logout.php" class="btn-login">Logout</a>
    <?php else: ?>
      <!-- Not logged in: Show profile icon + login/register -->
      <a href="<?php echo VIEWS_URL; ?>/profile.php" class="icon-circle" title="Profile">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
          <circle cx="12" cy="7" r="4"></circle>
        </svg>
      </a>
      <a href="<?php echo VIEWS_URL; ?>/login.php" class="btn-login">Login</a>
      <a href="<?php echo VIEWS_URL; ?>/register.php" class="btn-register">Register</a>
    <?php endif; ?>
  </div>

  <!-- Hamburger Menu Button -->
  <button class="hamburger-menu" id="hamburgerBtn" aria-label="Menu">
    <span></span>
    <span></span>
    <span></span>
  </button>
</header>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

<!-- Mobile Menu Panel -->
<div class="mobile-menu" id="mobileMenu">
  <?php if (isset($is_logged_in) && $is_logged_in): ?>
    <!-- User Info Section -->
    <div class="mobile-menu-section">
      <div class="mobile-user-info">
        <div class="mobile-user-avatar">
          <?php if (!empty($user_avatar)): ?>
            <img src="<?php echo htmlspecialchars($user_avatar); ?>" alt="Profile">
          <?php else: ?>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          <?php endif; ?>
        </div>
        <div>
          <div class="mobile-user-name"><?php echo htmlspecialchars($user_name); ?></div>
          <div style="font-size: 13px; color: rgba(10,10,10,0.6);"><?php echo htmlspecialchars($user_email); ?></div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Navigation Links -->
  <div class="mobile-menu-section">
    <h3>Navigation</h3>
    <div class="mobile-menu-links">
      <a href="<?php echo VIEWS_URL; ?>/browse.php">
        <svg viewBox="0 0 24 24" fill="none">
          <rect x="3" y="3" width="7" height="7" stroke="currentColor" stroke-width="2"/>
          <rect x="14" y="3" width="7" height="7" stroke="currentColor" stroke-width="2"/>
          <rect x="14" y="14" width="7" height="7" stroke="currentColor" stroke-width="2"/>
          <rect x="3" y="14" width="7" height="7" stroke="currentColor" stroke-width="2"/>
        </svg>
        Browse Collection
      </a>
      <a href="<?php echo VIEWS_URL; ?>/sellers.php">
        <svg viewBox="0 0 24 24" fill="none">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
          <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2"/>
        </svg>
        Sellers
      </a>
      <a href="<?php echo VIEWS_URL; ?>/about.php">
        <svg viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
          <path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        About
      </a>
      <?php if (isset($is_logged_in) && $is_logged_in): ?>
        <a href="<?php echo VIEWS_URL; ?>/profile.php">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
          </svg>
          My Profile
        </a>
        <?php if (isset($is_admin) && $is_admin == 1): ?>
          <a href="<?php echo VIEWS_URL; ?>/admin/dashboard.php">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M12 2L2 7l10 5 10-5-10-5z" stroke="currentColor" stroke-width="2"/>
              <path d="M2 17l10 5 10-5" stroke="currentColor" stroke-width="2"/>
              <path d="M2 12l10 5 10-5" stroke="currentColor" stroke-width="2"/>
            </svg>
            Admin Dashboard
          </a>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Auth Buttons -->
  <div class="mobile-menu-section">
    <?php if (isset($is_logged_in) && $is_logged_in): ?>
      <div class="mobile-auth-buttons">
        <a href="<?php echo VIEWS_URL; ?>/logout.php" class="btn-login">Logout</a>
      </div>
    <?php else: ?>
      <h3>Account</h3>
      <div class="mobile-auth-buttons">
        <a href="<?php echo VIEWS_URL; ?>/login.php" class="btn-login">Login</a>
        <a href="<?php echo VIEWS_URL; ?>/register.php" class="btn-register">Register</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
  // Mobile menu toggle - prevent duplicate event listeners
  (function() {
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    // Exit if elements don't exist
    if (!hamburgerBtn || !mobileMenu || !mobileMenuOverlay) {
      return;
    }

    function toggleMobileMenu(event) {
      // Prevent event from bubbling
      if (event) {
        event.stopPropagation();
      }
      
      const isActive = mobileMenu.classList.contains('active');
      
      hamburgerBtn.classList.toggle('active');
      mobileMenu.classList.toggle('active');
      mobileMenuOverlay.classList.toggle('active');
      document.body.style.overflow = !isActive ? 'hidden' : '';
    }

    function closeMobileMenu(event) {
      if (event) {
        event.stopPropagation();
      }
      
      if (mobileMenu.classList.contains('active')) {
        hamburgerBtn.classList.remove('active');
        mobileMenu.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        document.body.style.overflow = '';
      }
    }

    // Only add event listeners once
    hamburgerBtn.addEventListener('click', toggleMobileMenu, { once: false });
    mobileMenuOverlay.addEventListener('click', closeMobileMenu, { once: false });

    // Close menu when clicking a link
    document.querySelectorAll('.mobile-menu-links a').forEach(link => {
      link.addEventListener('click', closeMobileMenu, { once: false });
    });

    // Prevent clicks inside the menu from closing it
    mobileMenu.addEventListener('click', function(event) {
      event.stopPropagation();
    });
  })();
</script>

<?php 
// Include push notification checker for logged-in users
if ($is_logged_in) {
    include __DIR__ . '/push-notification-checker.php';
}
?>
