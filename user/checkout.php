<?php
session_start();
require_once "../includes/db.php";

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: /TheWhisperingSpoon/auth/login.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];

// Check if cart is not empty
if (empty($_SESSION['cart'])) {
    header("Location: /TheWhisperingSpoon/user/cart.php");
    exit;
}

// Calculate total
$total = 0.0;
$cartItems = [];

foreach ($_SESSION['cart'] as $id => $quantity) {
    $id = (int) $id;
    $quantity = (int) $quantity;

    $result = mysqli_query($conn, "SELECT * FROM menu_items WHERE id = $id");
    if (!$result) {
        continue; // Skip invalid items
    }

    $item = mysqli_fetch_assoc($result);
    if ($item) {
        $price = (float) $item['price'];
        $subtotal = $price * $quantity;
        $total += $subtotal;
        $cartItems[] = [
            'id' => $id,
            'quantity' => $quantity,
            'price' => $price
        ];
    }
}

if (empty($cartItems)) {
    // No valid items
    header("Location: /TheWhisperingSpoon/user/cart.php");
    exit;
}

// Insert order
$insertOrderSql = "INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, 'Pending', NOW())";
$stmt = mysqli_prepare($conn, $insertOrderSql);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "id", $userId, $total);

if (mysqli_stmt_execute($stmt)) {
    $orderId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Insert order items
    $insertItemSql = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)";
    $itemStmt = mysqli_prepare($conn, $insertItemSql);
    if (!$itemStmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    foreach ($cartItems as $item) {
        mysqli_stmt_bind_param($itemStmt, "iiid", $orderId, $item['id'], $item['quantity'], $item['price']);
        if (!mysqli_stmt_execute($itemStmt)) {
            die("Execute failed: " . mysqli_stmt_error($itemStmt));
        }
    }

    mysqli_stmt_close($itemStmt);

    // Clear cart
    unset($_SESSION['cart']);

    // Redirect to success page with order ID
    $_SESSION['last_order_id'] = $orderId;
    header("Location: /TheWhisperingSpoon/user/order-success.php");
    exit;

} else {
    mysqli_stmt_close($stmt);
    // Error
    die("Error placing order: " . mysqli_stmt_error($stmt));
}
?>
