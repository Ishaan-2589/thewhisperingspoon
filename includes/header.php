<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$cartCount = !empty($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>The Whispering Spoon</title>

  <link rel="stylesheet" href="/TheWhisperingSpoon/assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link href="https://fonts.googleapis.com/css2?family=Allura&family=Great+Vibes&family=Parisienne&family=Roboto:wght@400;700&family=Sacramento&family=Tangerine&display=swap" rel="stylesheet">
</head>

<body>

<button onclick="topFunction()" id="scrollTopBtn" title="Go to top">↑</button>

<header>
  <h1 class="logo-font">The Whispering Spoon</h1>

  <nav>
    <a href="/TheWhisperingSpoon/public/index.php">Home</a>
    <a href="/TheWhisperingSpoon/public/menu.php">Menu</a>
    <a href="/TheWhisperingSpoon/user/book-table.php"><i class="fas fa-chair"></i> Book Table</a>
    
    <a href="/TheWhisperingSpoon/user/cart.php">
      🛒 Cart (<span id="cart-counter"><?php echo $cartCount; ?></span>)
    </a>
    
    <?php if (isset($_SESSION["user_id"])): ?>
      <a href="/TheWhisperingSpoon/user/profile.php">
        <i class="fas fa-user-circle" style="margin-right: 5px;"></i> Profile
      </a>
      <?php else: ?>
      <a href="/TheWhisperingSpoon/auth/login.php">Login</a>
      <a href="/TheWhisperingSpoon/auth/register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>