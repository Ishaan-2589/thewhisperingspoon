<?php
session_start();

$id = (int) $_GET['id'];

if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]++;
}

header("Location: cart.php");
exit;
