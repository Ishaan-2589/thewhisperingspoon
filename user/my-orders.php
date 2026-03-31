<?php
session_start();
require_once "../includes/db.php";

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: /TheWhisperingSpoon/auth/login.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];
$orders = [];
$hasDbOrders = false;
$errorMsg = "";

// First, check if orders table exists.
$tableCheckSql = "SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'orders'";
$tableCheckResult = mysqli_query($conn, $tableCheckSql);

if ($tableCheckResult) {
    $row = mysqli_fetch_assoc($tableCheckResult);
    if ($row && intval($row['cnt']) > 0) {
        $hasDbOrders = true;

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
WHERE o.user_id = ?
ORDER BY o.created_at DESC, o.id DESC";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($result)) {
                $orderId = $row['order_id'];

                if (!isset($orders[$orderId])) {
                    $orders[$orderId] = [
                        'id' => $orderId,
                        'status' => $row['status'] ?? 'Pending',
                        'total' => $row['total_amount'] ?? 0,
                        'created_at' => $row['created_at'] ?? '',
                        'items' => [],
                    ];
                }

                $orders[$orderId]['items'][] = [
                    'menu_item_id' => $row['menu_item_id'],
                    'name' => $row['item_name'] ?? 'Unknown item',
                    'image' => $row['item_image'] ?? '',
                    'quantity' => (int)$row['quantity'],
                    'price' => (float)$row['item_price'],
                    'subtotal' => (float)$row['quantity'] * (float)$row['item_price'],
                ];
            }

            mysqli_stmt_close($stmt);

            // If any orders are present, we have that data.
            if (empty($orders)) {
                // no orders yet
            }
        } else {
            $errorMsg = "Unable to load orders. Please try again later.";
        }
    }
} else {
    // fallback to session-based completed orders (if checkout flow stores them there)
    if (isset($_SESSION['completed_orders']) && is_array($_SESSION['completed_orders'])) {
        $orders = $_SESSION['completed_orders'];
    }
}

include "../includes/header.php";
?>

<section class="menu-section">
  <h2 class="heading-font">My Orders</h2>

  <?php if (!empty($errorMsg)): ?>
    <p style="color: #f44336;"><?= htmlspecialchars($errorMsg) ?></p>
  <?php endif; ?>

  <?php if (empty($orders)): ?>
    <p>You have no orders yet. Place an order from the menu to see it here.</p>
    <p><a href="/TheWhisperingSpoon/public/menu.php" class="login-btn" style="max-width: 200px;">Browse Menu</a></p>
  <?php else: ?>

    <?php foreach ($orders as $order): ?>
      <div class="order-card">
        <h3>Order #<?= htmlspecialchars($order['id']) ?></h3>
        <p>Placed: <?= htmlspecialchars(date('d M Y H:i', strtotime($order['created_at'] ?? 'now'))) ?></p>
        <p>Status: <?= htmlspecialchars($order['status'] ?? 'Pending') ?></p>
        <p>Total: ₹<?= number_format($order['total'], 2) ?></p>

        <div class="order-items">
          <?php foreach ($order['items'] as $item): ?>
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
    <?php endforeach; ?>

  <?php endif; ?>
</section>

<?php include "../includes/footer.php"; ?>
