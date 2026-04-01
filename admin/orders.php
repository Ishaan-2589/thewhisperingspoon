<?php
session_start();
require_once "../includes/db.php";
require_once "../includes/admin-auth.php";

// 1. THE ACTUAL UPDATE LOGIC (No Placeholders!)
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

        $success = "Order #".str_pad($orderId, 5, '0', STR_PAD_LEFT)." marked as ".$status."!";
    }
}

// 2. Fetch Orders
$query = "SELECT o.*, u.name as user_name, u.email as user_email
          FROM orders o
          LEFT JOIN users u ON o.user_id = u.id
          ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $query);
$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}
?>

<?php include "../includes/admin-header.php"; ?>

<style>
body { background-color: #050505; color: #fff; }
.admin-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 20px; }
.page-header h1 { color: gold; font-family: 'Playfair Display', serif; font-size: 32px; margin: 0; }
.btn-back { color: #888; text-decoration: none; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; transition: color 0.3s; }
.btn-back:hover { color: gold; }

.table-wrapper { background: #111; border: 1px solid #222; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
table { width: 100%; border-collapse: collapse; text-align: left; }
th { background: #1a1a1a; color: gold; padding: 18px 20px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #333; }
td { padding: 18px 20px; border-bottom: 1px solid #222; color: #ccc; font-size: 14px; vertical-align: middle; }
tr:hover { background: #151515; }

.status-select { 
    background: #000; color: #fff; border: 1px solid #444; padding: 8px 12px; 
    border-radius: 6px; font-size: 13px; font-weight: bold; cursor: pointer; outline: none; transition: 0.3s;
}
.status-select:focus { border-color: gold; }
.status-select option[value="Pending"] { color: #ffcc00; }
.status-select option[value="Preparing"] { color: #00c3ff; }
.status-select option[value="Ready"] { color: #00ff88; }
.status-select option[value="Delivered"] { color: #888; }
.status-select option[value="Cancelled"] { color: #ff4444; }

.btn-view { background: #222; color: gold; border: 1px solid #444; padding: 6px 12px; border-radius: 4px; font-size: 12px; cursor: pointer; text-transform: uppercase; }
.btn-view:hover { border-color: gold; background: #333; }

.details-row td { background: #0a0a0a; padding: 20px; border-bottom: 2px solid #333;}
.details-list { list-style: none; padding: 0; margin: 0; }
.details-list li { padding: 8px 0; border-bottom: 1px dashed #222; display: flex; justify-content: space-between; }
.details-list li:last-child { border-bottom: none; }
</style>

<div class="admin-container">
    <div class="page-header">
        <h1><i class="fas fa-receipt" style="margin-right: 10px; font-size: 24px; color: #555;"></i> Order Management</h1>
        <div>
            <a href="live-kitchen.php" style="background: rgba(0,255,136,0.1); color: #00ff88; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 14px; margin-right: 15px; border: 1px solid #00ff88;"><i class="fas fa-tv"></i> Live Kitchen</a>
            <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <?php if (isset($success)): ?>
        <div style="background: rgba(0,255,136,0.1); color: #00ff88; padding: 15px; border-radius: 8px; border: 1px solid #00ff88; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date & Time</th>
                    <th>Total</th>
                    <th>Live Status</th>
                    <th style="text-align: right;">Items</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td style="font-family: monospace; font-size: 16px; font-weight: bold; color: #fff;">#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div style="color: gold; font-weight: bold; margin-bottom: 4px;"><?php echo htmlspecialchars($order['user_name'] ?? 'Guest'); ?></div>
                            <div style="font-size: 12px; color: #666;"><?php echo htmlspecialchars($order['user_email'] ?? 'N/A'); ?></div>
                        </td>
                        <td><?php echo date('d M, Y', strtotime($order['created_at'])); ?><br><small style="color:#666;"><?php echo date('h:i A', strtotime($order['created_at'])); ?></small></td>
                        <td style="color: #fff; font-weight: bold;">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                        
                        <td>
                            <form method="POST" style="margin: 0;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="status-select" onchange="this.form.submit()" style="border-left: 4px solid 
                                    <?php 
                                        if($order['status']=='Pending') echo '#ffcc00';
                                        elseif($order['status']=='Preparing') echo '#00c3ff';
                                        elseif($order['status']=='Ready') echo '#00ff88';
                                        elseif($order['status']=='Cancelled') echo '#ff4444';
                                        else echo '#888';
                                    ?>;">
                                    <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Preparing" <?php echo $order['status'] === 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                                    <option value="Ready" <?php echo $order['status'] === 'Ready' ? 'selected' : ''; ?>>Ready</option>
                                    <option value="Delivered" <?php echo $order['status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        
                        <td style="text-align: right;">
                            <button class="btn-view" onclick="toggleOrderDetails(<?php echo $order['id']; ?>)">View Details <i class="fas fa-chevron-down"></i></button>
                        </td>
                    </tr>
                    
                    <tr id="details-<?php echo $order['id']; ?>" class="details-row" style="display: none;">
                        <td colspan="6">
                            <?php
                            $itemsQuery = "SELECT oi.*, mi.name FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE oi.order_id = ?";
                            $stmt = mysqli_prepare($conn, $itemsQuery);
                            mysqli_stmt_bind_param($stmt, "i", $order['id']);
                            mysqli_stmt_execute($stmt);
                            $itemsResult = mysqli_stmt_get_result($stmt);
                            ?>
                            <ul class="details-list">
                                <?php while ($item = mysqli_fetch_assoc($itemsResult)): ?>
                                    <li>
                                        <span><span style="color: gold; font-weight: bold; margin-right: 10px;"><?php echo $item['quantity']; ?>x</span> <?php echo htmlspecialchars($item['name']); ?></span>
                                        <span style="color: #aaa;">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                            <?php mysqli_stmt_close($stmt); ?>
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
    if (detailsRow.style.display === 'none') {
        detailsRow.style.display = 'table-row';
    } else {
        detailsRow.style.display = 'none';
    }
}
</script>

<?php include "../includes/footer.php"; ?>