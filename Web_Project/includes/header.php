<?php 
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
if (!isset($page_title)) $page_title = 'PortfolioLink'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="/Web_Project/assets/css/style.css">
  <link rel="stylesheet" href="/Web_Project/assets/css/profile.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    a, a:link, a:visited, a:hover, a:active, a:focus {
        text-decoration: none !important;
    }
    body {
        font-family: 'Poppins', Arial, Helvetica, sans-serif;
    }
    .header-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      background-color: #2c3e50;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
      min-height: 64px;
    }
    .header-container h1 {
      font-size: 1.8em;
      margin: 0;
      color: #fff;
      font-weight: 700;
    }
    .header-container h1 a {
      color: #fff;
      font-weight: 700;
      font-size: 1em;
    }
    .header-container nav {
      display: flex;
      gap: 18px;
      align-items: center;
    }
    .header-container nav a {
      color: #fff;
      font-weight: 700;
      font-size: 1.18em;
      padding: 8px 12px;
      border-radius: 5px;
      transition: background 0.2s;
    }
    .header-container nav a:hover {
      background: rgba(255,255,255,0.12);
    }
    .badge {
      background: #ff9800;
      color: #fff;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 0.95em;
      margin-left: 6px;
    }
    .hamburger {
      display: none;
      flex-direction: column;
      justify-content: center;
      width: 36px;
      height: 36px;
      cursor: pointer;
      margin-left: 12px;
      z-index: 1001;
    }
    .hamburger span {
      height: 4px;
      width: 100%;
      background: #007bff;
      margin: 5px 0;
      border-radius: 2px;
      transition: 0.3s;
      display: block;
    }
    @media (max-width: 800px) {
      .header-container nav {
        position: absolute;
        top: 64px;
        right: 0;
        background: #fff;
        flex-direction: column;
        align-items: flex-start;
        width: 200px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        padding: 18px 0 18px 0;
        gap: 0;
        display: none;
      }
      .header-container nav.open {
        display: flex;
      }
      .header-container.menu-open .hamburger {
        display: none !important;
      }
      .header-container nav a {
        width: 100%;
        padding: 12px 24px;
        border-radius: 0;
        border-bottom: 1px solid #f0f0f0;
        color: #222;
      }
      .hamburger {
        display: flex;
      }
    }
  </style>
</head>
<body>
<!-- Fixed Navigation -->
<header>
  <div class="header-container">
    <h1><a href="/Web_Project/">PortfolioLink</a></h1>
    <div class="hamburger" onclick="toggleNav()">
      <span></span>
      <span></span>
      <span></span>
    </div>
    <nav id="mainNav">
      <a href="/Web_Project/">Home</a>
      <?php if (is_logged_in()): ?>
        <?php if (can_upload_project()): ?>
        <a href="/Web_Project/upload.php">Upload</a>
        <?php endif; ?>
        <a href="/Web_Project/profile.php?user=<?= htmlspecialchars($_SESSION['user_id']) ?>">Profile</a>
        <?php if (is_admin()): ?>
          <span class="badge">Admin</span>
        <?php endif; ?>
        <a href="/Web_Project/logout.php">Logout</a>
      <?php else: ?>
        <a href="/Web_Project/login.php">Login</a>
        <a href="/Web_Project/signup.php">Sign Up</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<script>
function toggleNav() {
  var nav = document.getElementById('mainNav');
  var header = document.querySelector('.header-container');
  nav.classList.toggle('open');
  header.classList.toggle('menu-open');
}
// Optional: Close nav when clicking outside on mobile
window.addEventListener('click', function(e) {
  var nav = document.getElementById('mainNav');
  var burger = document.querySelector('.hamburger');
  var header = document.querySelector('.header-container');
  if (window.innerWidth <= 800 && nav.classList.contains('open')) {
    if (!nav.contains(e.target) && !burger.contains(e.target)) {
      nav.classList.remove('open');
      header.classList.remove('menu-open');
    }
  }
});
</script>
<main>