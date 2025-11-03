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

.profile-avatar {
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
    <!-- <a href="<?php echo VIEWS_URL; ?>/about.php">About</a> -->
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
</header>
