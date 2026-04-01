<?php
session_start();

// Completely destroy the cart session data
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

// Redirect back to the cart page
header("Location: /TheWhisperingSpoon/user/cart.php");
exit;
?>