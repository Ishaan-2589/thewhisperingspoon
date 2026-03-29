<?php
session_start();

header('Content-Type: application/json');

if (!isset($_POST['id'])) {
    echo json_encode(["status" => "error", "message" => "No ID"]);
    exit;
}

$id = (int) $_POST['id'];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]++;
} else {
    $_SESSION['cart'][$id] = 1;
}

$totalItems = array_sum($_SESSION['cart']);

echo json_encode([
    "status" => "success",
    "totalItems" => $totalItems
]);

exit;
