<?php
session_start();
require_once "../includes/db.php";

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: /auth/login.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];

// --- ONE-CLICK REORDER LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reorder_id'])) {
    $reorderId = (int) $_POST['reorder_id'];
    
    // 1. Fetch all items from that specific past order
    $itemsQuery = "SELECT menu_item_id, quantity FROM order_items WHERE order_id = ?";
    $stmtReorder = mysqli_prepare($conn, $itemsQuery);
    mysqli_stmt_bind_param($stmtReorder, "i", $reorderId);
    mysqli_stmt_execute($stmtReorder);
    $result = mysqli_stmt_get_result($stmtReorder);
    
    // 2. Ensure cart exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // 3. Add those items back into the current session cart
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['menu_item_id'];
        $qty = $row['quantity'];
        
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $qty;
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    }
    mysqli_stmt_close($stmtReorder);
    
    // 4. Redirect straight to the cart
    header("Location: cart.php");
    exit;
}

// Fetch user's orders
$query = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$ordersResult = mysqli_stmt_get_result($stmt);

include "../includes/header.php"; 
?>

<style>
.orders-hero { text-align: center; padding: 40px 20px; border-bottom: 1px solid #333; margin-bottom: 40px; background: #050505;}
.orders-hero h1 { font-size: 42px; color: gold; font-family: 'Playfair Display', serif; margin-bottom: 10px; }

.orders-wrapper { max-width: 900px; margin: 0 auto; padding: 0 20px 80px; min-height: 50vh;}

.empty-orders { text-align: center; padding: 60px 20px; background: #111; border: 1px dashed #333; border-radius: 16px; }
.empty-orders i { font-size: 48px; color: #444; margin-bottom: 20px; display: block; }
.empty-orders p { color: #888; font-size: 18px; margin-bottom: 25px; }

.order-card { background: #0a0a0a; border: 1px solid #222; border-radius: 16px; margin-bottom: 30px; overflow: hidden; transition: border-color 0.3s; }
.order-card:hover { border-color: #332a10; box-shadow: 0 10px 30px rgba(255, 215, 0, 0.05); }

.order-header { background: #111; padding: 20px 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #222; }
.order-header-left h3 { color: #fff; font-family: monospace; font-size: 18px; letter-spacing: 1px; margin-bottom: 5px; }
.order-date { color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }

.order-header-right { text-align: right; display: flex; flex-direction: column; align-items: flex-end;}
.order-total { color: gold; font-size: 20px; font-weight: bold; font-family: 'Playfair Display', serif; margin-bottom: 8px; }

.status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;}
.status-Pending { background: rgba(255, 204, 0, 0.1); border: 1px solid #ffcc00; color: #ffcc00; }
.status-Preparing { background: rgba(0, 195, 255, 0.1); border: 1px solid #00c3ff; color: #00c3ff; }
.status-Ready { background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; color: #00ff88; }
.status-Delivered { background: rgba(136, 136, 136, 0.1); border: 1px solid #888; color: #888; }
.status-Cancelled { background: rgba(255, 68, 68, 0.1); border: 1px solid #ff4444; color: #ff4444; }

.order-body { padding: 25px; background: #050505; }
.order-item { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #1a1a1a; }
.order-item:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
.order-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #222; }
.order-item-info { flex-grow: 1; }
.order-item-name { color: #ddd; font-size: 16px; margin-bottom: 4px; }
.order-item-qty { color: #888; font-size: 13px; }
.order-item-price { color: #aaa; font-size: 15px; }

.btn-menu { display: inline-block; background: gold; color: #000; padding: 12px 30px; border-radius: 30px; font-weight: bold; text-decoration: none; transition: transform 0.3s; }
.btn-menu:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(255, 215, 0, 0.2); }

.btn-reorder { background: transparent; border: 1px solid gold; color: gold; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: bold; cursor: pointer; transition: 0.3s; text-transform: uppercase; letter-spacing: 1px; display: inline-flex; align-items: center; gap: 6px;}
.btn-reorder:hover { background: gold; color: #000; box-shadow: 0 4px 10px rgba(255, 215, 0, 0.2); }
</style>

<div class="orders-hero">
    <h1>Your Culinary History</h1>
    <p style="color: #888;">Track your active orders and quickly reorder your favorites.</p>
</div>

<div class="orders-wrapper">
    <?php if (mysqli_num_rows($ordersResult) === 0): ?>
        <div class="empty-orders">
            <i class="fas fa-receipt"></i>
            <p>You haven't placed any orders yet.</p>
            <a href="/public/menu.php" class="btn-menu">Explore The Menu</a>
        </div>
    <?php else: ?>
        
        <?php while ($order = mysqli_fetch_assoc($ordersResult)): ?>
            <div class="order-card">
                <div class="order-header">
                    <div class="order-header-left">
                        <h3>Order #<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></h3>
                        <div class="order-date">
                            <i class="far fa-calendar-alt" style="margin-right: 5px;"></i> 
                            <?php echo date('M d, Y • h:i A', strtotime($order['created_at'])); ?>
                        </div>
                    </div>
                    <div class="order-header-right">
                        <div class="order-total">₹<?php echo number_format($order['total_amount'], 2); ?></div>
                        <span class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo $order['status']; ?>
                        </span>
                        
                        <div style="display: flex; gap: 10px; margin-top: 5px;">
                            <a href="receipt.php?id=<?php echo $order['id']; ?>" target="_blank" class="btn-reorder" style="border-color: #888; color: #ccc;">
                                <i class="fas fa-file-pdf"></i> Receipt
                            </a>

                            <?php if ($order['status'] !== 'Cancelled'): ?>
                            <form method="POST" style="margin: 0;">
                                <input type="hidden" name="reorder_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" class="btn-reorder"><i class="fas fa-redo-alt"></i> Reorder</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="order-body">
                    <?php
                    $itemsQuery = "SELECT oi.*, mi.name, mi.image FROM order_items oi 
                                   JOIN menu_items mi ON oi.menu_item_id = mi.id 
                                   WHERE oi.order_id = ?";
                    $stmtItems = mysqli_prepare($conn, $itemsQuery);
                    mysqli_stmt_bind_param($stmtItems, "i", $order['id']);
                    mysqli_stmt_execute($stmtItems);
                    $itemsResult = mysqli_stmt_get_result($stmtItems);
                    
                    while ($item = mysqli_fetch_assoc($itemsResult)):
                    ?>
                        <div class="order-item">
                            <img src="/assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="Dish" onerror="this.src='/assets/images/others/placeholder.jpg'">
                            <div class="order-item-info">
                                <div class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="order-item-qty">Quantity: <?php echo $item['quantity']; ?></div>
                            </div>
                            <div class="order-item-price">
                                ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </div>
                        </div>
                    <?php endwhile; mysqli_stmt_close($stmtItems); ?>
                </div>
            </div>
        <?php endwhile; ?>

    <?php endif; ?>
    <?php mysqli_stmt_close($stmt); ?>
</div>

<?php include "../includes/footer.php"; ?>