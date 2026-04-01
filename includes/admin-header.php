<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Double-check admin status just in case
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: /TheWhisperingSpoon/admin/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Portal - The Whispering Spoon</title>

  <link rel="stylesheet" href="/TheWhisperingSpoon/assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Roboto:wght@400;700&family=Parisienne&display=swap" rel="stylesheet">

  <style>
  /* Admin-Specific Navbar Styles */
  body {
      background-color: #050505 !important;
      color: #fff !important;
      margin: 0;
  }
  .admin-navbar {
      background-color: #0a0a0a;
      border-bottom: 2px solid gold;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
  }
  .admin-navbar-brand {
      font-family: 'Playfair Display', serif;
      color: gold;
      font-size: 24px;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
      text-shadow: 1px 2px 4px rgba(0,0,0,0.5);
  }
  .admin-nav-links {
      display: flex;
      gap: 20px;
      align-items: center;
  }
  .admin-nav-links a {
      color: #ccc;
      text-decoration: none;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: color 0.3s;
      display: flex;
      align-items: center;
      gap: 6px;
  }
  .admin-nav-links a:hover {
      color: gold;
  }
  .btn-admin-logout {
      background: rgba(255, 68, 68, 0.1);
      color: #ff4444 !important;
      border: 1px solid #ff4444;
      padding: 8px 16px;
      border-radius: 20px;
      font-weight: bold;
      transition: all 0.3s ease;
  }
  .btn-admin-logout:hover {
      background: #ff4444;
      color: #fff !important;
      box-shadow: 0 4px 12px rgba(255, 68, 68, 0.3);
  }
  </style>
</head>
<body>

<header class="admin-navbar">
    <a href="/TheWhisperingSpoon/admin/dashboard.php" class="admin-navbar-brand">
        <i class="fas fa-crown"></i> Admin Central
    </a>
    
    <nav class="admin-nav-links">
        <a href="/TheWhisperingSpoon/admin/dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="/TheWhisperingSpoon/admin/analytics.php" style="color: #00c3ff;"><i class="fas fa-chart-pie"></i> Analytics< a>
        <a href="/TheWhisperingSpoon/admin/orders.php"><i class="fas fa-receipt"></i> Orders</a>
        <a href="/TheWhisperingSpoon/admin/live-kitchen.php"><i class="fas fa-tv"></i> Kitchen</a>
        <a href="/TheWhisperingSpoon/admin/menu.php"><i class="fas fa-utensils"></i> Menu</a>
        <a href="/TheWhisperingSpoon/admin/users.php"><i class="fas fa-users"></i> Customers</a>
        <a href="/TheWhisperingSpoon/admin/bookings.php"><i class="fas fa-chair"></i> Bookings</a>
        
        <a href="/TheWhisperingSpoon/admin/logout.php" class="btn-admin-logout"><i class="fas fa-sign-out-alt"></i> Exit</a>
    </nav>
</header>