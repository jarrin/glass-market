<?php
// Blurry glass navbar include
// Usage: <?php include __DIR__ . '/includes/navbar.php'; ?>



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

/* Responsive adjustments */
@media (max-width: 820px) {
  .glass-navbar .search { min-width: 180px; max-width: 260px; }
  .glass-navbar .nav-links { display: none; }
}

@media (max-width: 480px) {
  .glass-navbar { padding: 8px 12px; }
  .glass-navbar .search { display: none; }
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
    <a href="<?php echo VIEWS_URL; ?>/categories.php">Categories</a>
    <a href="<?php echo VIEWS_URL; ?>/about.php">About</a>
  </nav>

  <div class="search" role="search">
    <input type="search" placeholder="Search glass art, crystals..." aria-label="Search">
  </div>

  <div class="icons" aria-hidden="false">
    <a class="icon-circle" href="/account" title="Account">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5z" stroke="#111" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M20 22c0-3.314-2.686-6-6-6H10c-3.314 0-6 2.686-6 6" stroke="#111" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>
  </div>
</header>
