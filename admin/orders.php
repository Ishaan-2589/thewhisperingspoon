<?php
session_start();
require_once "../includes/db.php";
require_once "../includes/admin-auth.php";

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = (int) $_POST['order_id'];
    $status = $_POST['status'];

    $validStatuses = ['Pending', 'Preparing', 'Ready', 'Delivered', 'Cancelled'];
    if (in_array($status, $validStatuses)) {
        $updateQuery = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "si", $status, $orderId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $success = "Order status updated successfully!";
    }
}

// Get all orders with user details
$query = "SELECT o.*, u.name as user_name, u.email as user_email
          FROM orders o
          JOIN users u ON o.user_id = u.id
          ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $query);
$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}
?>

<?php include "../includes/header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .orders-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .orders-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .orders-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th, .orders-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .orders-table th {
            background-color: #f8f8f8;
            font-weight: bold;
            color: #8B4513;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: bold;
        }

        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-preparing { background-color: #d1ecf1; color: #0c5460; }
        .status-ready { background-color: #d4edda; color: #155724; }
        .status-delivered { background-color: #d1ecf1; color: #0c5460; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }

        .status-select {
            padding: 4px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .update-btn {
            background: #8B4513;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
        }

        .update-btn:hover {
            background: #654321;
        }

        .order-details {
            background: #f9f9f9;
            padding: 10px;
            margin-top: 5px;
            border-radius: 4px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #8B4513;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="orders-container">
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    <h1 style="color: #8B4513; margin-bottom: 20px;">Manage Orders</h1>

    <?php if (isset($success)): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="orders-table">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="status-select" onchange="this.form.submit()">
                                    <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Preparing" <?php echo $order['status'] === 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                                    <option value="Ready" <?php echo $order['status'] === 'Ready' ? 'selected' : ''; ?>>Ready</option>
                                    <option value="Delivered" <?php echo $order['status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                        <td>
                            <button onclick="toggleOrderDetails(<?php echo $order['id']; ?>)" class="update-btn">View Details</button>
                        </td>
                    </tr>
                    <tr id="details-<?php echo $order['id']; ?>" style="display: none;">
                        <td colspan="7">
                            <div class="order-details">
                                <h4>Order Items:</h4>
                                <?php
                                $itemsQuery = "SELECT oi.*, mi.name FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE oi.order_id = ?";
                                $stmt = mysqli_prepare($conn, $itemsQuery);
                                mysqli_stmt_bind_param($stmt, "i", $order['id']);
                                mysqli_stmt_execute($stmt);
                                $itemsResult = mysqli_stmt_get_result($stmt);
                                ?>
                                <ul>
                                    <?php while ($item = mysqli_fetch_assoc($itemsResult)): ?>
                                        <li><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?> = ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></li>
                                    <?php endwhile; ?>
                                </ul>
                                <?php mysqli_stmt_close($stmt); ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleOrderDetails(orderId) {
    const detailsRow = document.getElementById('details-' + orderId);
    detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
}
</script>

</body>
</html>

<?php include "../includes/footer.php"; ?>