<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>The Whispering Spoon</title>

  <!-- Main CSS -->
  <link rel="stylesheet" href="/TheWhisperingSpoon/assets/css/style.css" />

  <!-- Fonts -->
  <link
    href="https://fonts.googleapis.com/css2?family=Allura&family=Great+Vibes&family=Parisienne&family=Roboto:wght@400;700&family=Sacramento&family=Tangerine&display=swap"
    rel="stylesheet">
</head>

<body>

<!-- Scroll to Top -->
<button onclick="topFunction()" id="scrollTopBtn" title="Go to top">↑</button>

<!-- Header -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header>
  <h1 class="logo-font">The Whispering Spoon</h1>

  <nav>
    <a href="/TheWhisperingSpoon/public/index.php">Home</a>
    <a href="/TheWhisperingSpoon/public/menu.php">Menu</a>
 <!-- CART LINK -->
    <a href="/TheWhisperingSpoon/user/cart.php">
      🛒 Cart
      (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)
    </a>
    <a href="/TheWhisperingSpoon/user/my-orders.php">My Orders</a>
    <?php if (isset($_SESSION["user_id"])): ?>
      <a href="/TheWhisperingSpoon/user/profile.php">Profile</a>
      <a href="#" class="logout-trigger">Logout</a>
    <?php else: ?>
      <a href="/TheWhisperingSpoon/auth/login.php">Login</a>
      <a href="/TheWhisperingSpoon/auth/register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>
