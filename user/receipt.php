<?php
session_start();
require_once "../includes/db.php";

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: /auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Invalid Order ID.");
}

$userId = (int) $_SESSION["user_id"];
$orderId = (int) $_GET['id'];

// Fetch Order and User Details
$orderQuery = "SELECT o.*, u.name, u.email, u.phone, u.address 
               FROM orders o 
               JOIN users u ON o.user_id = u.id 
               WHERE o.id = ? AND o.user_id = ?";
$stmt = mysqli_prepare($conn, $orderQuery);
mysqli_stmt_bind_param($stmt, "ii", $orderId, $userId);
mysqli_stmt_execute($stmt);
$orderResult = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($orderResult);
mysqli_stmt_close($stmt);

if (!$order) {
    die("Order not found or access denied.");
}

// Fetch Order Items
$itemsQuery = "SELECT oi.*, mi.name FROM order_items oi 
               JOIN menu_items mi ON oi.menu_item_id = mi.id 
               WHERE oi.order_id = ?";
$stmtItems = mysqli_prepare($conn, $itemsQuery);
mysqli_stmt_bind_param($stmtItems, "i", $orderId);
mysqli_stmt_execute($stmtItems);
$itemsResult = mysqli_stmt_get_result($stmtItems);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt_#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        body { background-color: #f4f4f4; margin: 0; padding: 40px 20px; font-family: 'Roboto', sans-serif; color: #333; }
        .receipt-controls { max-width: 800px; margin: 0 auto 20px; display: flex; justify-content: space-between; align-items: center; }
        .btn { padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; border: none; font-size: 14px; transition: 0.3s; }
        .btn-back { background: #333; color: #fff; }
        .btn-back:hover { background: #000; }
        .btn-download { background: #d4af37; color: #000; }
        .btn-download:hover { background: #b5952f; }
        
        /* The actual paper receipt container */
        .invoice-box { max-width: 800px; margin: 0 auto; padding: 40px; border: 1px solid #ddd; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #d4af37; padding-bottom: 20px; margin-bottom: 30px; }
        .brand h1 { font-family: 'Playfair Display', serif; color: #d4af37; margin: 0 0 5px 0; font-size: 32px; }
        .brand p { margin: 0; color: #666; font-size: 14px; }
        .invoice-details { text-align: right; }
        .invoice-details h2 { margin: 0 0 5px 0; color: #333; font-size: 24px; text-transform: uppercase; letter-spacing: 1px;}
        .invoice-details p { margin: 2px 0; color: #666; font-size: 14px; }
        
        .billing-info { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .info-block { width: 48%; }
        .info-block h3 { font-size: 14px; color: #888; text-transform: uppercase; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px; }
        .info-block p { margin: 4px 0; font-size: 15px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f9f9f9; padding: 12px; text-align: left; font-size: 13px; text-transform: uppercase; color: #666; border-bottom: 2px solid #ddd; }
        td { padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 15px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .totals { width: 50%; float: right; }
        .totals-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; font-size: 15px; }
        .totals-row.grand-total { font-weight: bold; font-size: 20px; color: #000; border-bottom: 2px solid #d4af37; border-top: 2px solid #d4af37; margin-top: 10px; padding: 15px 0;}
        
        .footer { clear: both; padding-top: 50px; text-align: center; color: #888; font-size: 13px; }
        
        .special-note { background: #fffdf5; border-left: 4px solid #d4af37; padding: 15px; margin-bottom: 30px; font-size: 14px; color: #555; }
    </style>
</head>
<body>

<div class="receipt-controls">
    <a href="my-orders.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Back to Orders</a>
    <button onclick="downloadPDF()" class="btn btn-download"><i class="fas fa-file-pdf"></i> Download PDF</button>
</div>

<div class="invoice-box" id="invoice">
    <div class="header">
        <div class="brand">
            <h1>The Whispering Spoon</h1>
            <p>123 Culinary Avenue, New Delhi, India</p>
            <p>contact@whisperingspoon.com | +91 98765 43210</p>
        </div>
        <div class="invoice-details">
            <h2>RECEIPT</h2>
            <p><strong>Order #:</strong> <?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></p>
            <p><strong>Date:</strong> <?php echo date('F j, Y, g:i A', strtotime($order['created_at'])); ?></p>
            <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
        </div>
    </div>

    <div class="billing-info">
        <div class="info-block">
            <h3>Billed To</h3>
            <p><strong><?php echo htmlspecialchars($order['name']); ?></strong></p>
            <p><?php echo htmlspecialchars($order['email']); ?></p>
            <p><?php echo htmlspecialchars($order['phone']); ?></p>
        </div>
        <div class="info-block">
            <h3>Payment & Delivery</h3>
            <p><strong>Method:</strong> <?php echo strtoupper($order['payment_method']); ?></p>
            <p><strong>Txn ID:</strong> <?php echo $order['payment_id'] ? htmlspecialchars($order['payment_id']) : 'N/A'; ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address'] ?? 'Not Provided'); ?></p>
        </div>
    </div>

    <?php if (!empty($order['special_requests'])): ?>
    <div class="special-note">
        <strong>Special Instructions:</strong> <?php echo htmlspecialchars($order['special_requests']); ?>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Item Description</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = mysqli_fetch_assoc($itemsResult)): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td class="text-center"><?php echo $item['quantity']; ?></td>
                <td class="text-right">₹<?php echo number_format($item['price'], 2); ?></td>
                <td class="text-right">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <span>Subtotal</span>
            <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
        </div>
        <div class="totals-row">
            <span>Taxes (Included)</span>
            <span>₹0.00</span>
        </div>
        <div class="totals-row grand-total">
            <span>Total Paid</span>
            <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for dining with The Whispering Spoon!</p>
        <p>This is a computer-generated receipt and does not require a physical signature.</p>
    </div>
</div>

<script>
function downloadPDF() {
    const element = document.getElementById('invoice');
    
    // Configure the PDF settings
    const opt = {
        margin:       0.5,
        filename:     'Receipt_#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?>.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    // Generate PDF
    html2pdf().set(opt).from(element).save();
}
</script>

</body>
</html>