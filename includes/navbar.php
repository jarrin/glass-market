<?php
// Blurry glass navbar include
// Usage: <?php include __DIR__ . '/includes/navbar.php'; ?>

<style>
/* Navbar root */
.glass-navbar {
  position: relative;
  width: 100%;
  z-index: 1000;
  display: flex;
  align-items: center;
  gap: 24px;
  padding: 10px 22px;
  box-sizing: border-box;
  /* semi-transparent background to sit on top of page content */
  background: rgba(255,255,255,0.35);
  -webkit-backdrop-filter: blur(8px) saturate(120%);
  backdrop-filter: blur(8px) saturate(120%);
  border-bottom: 1px solid rgba(255,255,255,0.25);
  box-shadow: 0 6px 18px rgba(0,0,0,0.08);
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
  padding: 6px 8px;
  border-radius: 6px;
}

.glass-navbar .nav-links a:hover {
  background: rgba(0,0,0,0.05);
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
  background: rgba(255,255,255,0.6);
  outline: none;
  font-size: 13px;
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
  background: rgba(255,255,255,0.5);
  border: 1px solid rgba(0,0,0,0.06);
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
</style>

<header class="glass-navbar" role="banner">
  <a href="/" class="brand" aria-label="Glass Market home">
    <span class="logo-bars" aria-hidden="true"></span>
    <span style="font-size:16px;">GLASS MARKET</span>
  </a>

  <nav class="nav-links" role="navigation" aria-label="Primary">
    <a href="/browse">Browse</a>
    <a href="/categories">Categories</a>
    <a href="/sellers">Sellers</a>
    <a href="/about">About</a>
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
