<?php
session_start();
require_once "../includes/db.php";

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: /TheWhisperingSpoon/auth/login.php");
    exit;
}

$orderId = $_SESSION['last_order_id'] ?? null;
unset($_SESSION['last_order_id']); // Clear it

if (!$orderId) {
    header("Location: /TheWhisperingSpoon/user/my-orders.php");
    exit;
}

// Fetch order details
$sql = "SELECT
o.id AS order_id,
o.total_amount,
o.status,
o.created_at,
oi.menu_item_id,
oi.quantity,
oi.price AS item_price,
mi.name AS item_name,
mi.image AS item_image
FROM orders o
JOIN order_items oi ON oi.order_id = o.id
LEFT JOIN menu_items mi ON mi.id = oi.menu_item_id
WHERE o.id = ? AND o.user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $orderId, $_SESSION["user_id"]);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$order = null;
$items = [];

while ($row = mysqli_fetch_assoc($result)) {
    if (!$order) {
        $order = [
            'id' => $row['order_id'],
            'status' => $row['status'],
            'total' => $row['total_amount'],
            'created_at' => $row['created_at']
        ];
    }
    $items[] = [
        'name' => $row['item_name'] ?? 'Unknown item',
        'image' => $row['item_image'] ?? '',
        'quantity' => (int)$row['quantity'],
        'price' => (float)$row['item_price'],
        'subtotal' => (float)$row['quantity'] * (float)$row['item_price'],
    ];
}

mysqli_stmt_close($stmt);

if (!$order) {
    header("Location: /TheWhisperingSpoon/user/my-orders.php");
    exit;
}

include "../includes/header.php";
?>

<section class="success-section">
  <div class="success-container">
    <h2 class="heading-font">Order Placed Successfully!</h2>
    <p>Thank you for your order. Your food will be prepared shortly.</p>

    <div class="order-summary">
      <h3>Order #<?= htmlspecialchars($order['id']) ?></h3>
      <p>Status: <?= htmlspecialchars($order['status']) ?></p>
      <p>Total: ₹<?= number_format($order['total'], 2) ?></p>
      <p>Placed on: <?= htmlspecialchars(date('d M Y H:i', strtotime($order['created_at']))) ?></p>

      <div class="order-items">
        <?php foreach ($items as $item): ?>
          <div class="order-item">
            <?php if (!empty($item['image'])): ?>
              <img src="/TheWhisperingSpoon/assets/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="order-item-image">
            <?php endif; ?>
            <div>
              <strong><?= htmlspecialchars($item['name']) ?></strong>
              <p><?= htmlspecialchars($item['quantity']) ?> × ₹<?= number_format($item['price'], 2) ?> = ₹<?= number_format($item['subtotal'], 2) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="success-actions">
      <a href="/TheWhisperingSpoon/user/my-orders.php" class="btn-primary">View My Orders</a>
      <a href="/TheWhisperingSpoon/public/menu.php" class="btn-secondary">Order More</a>
    </div>
  </div>
</section>

<?php include "../includes/footer.php"; ?>
