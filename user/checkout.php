<?php
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: /auth/login.php");
    exit;
}

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];

// Calculate total amount securely
$grandTotal = 0;
foreach ($_SESSION['cart'] as $id => $quantity) {
    $result = mysqli_query($conn, "SELECT price FROM menu_items WHERE id = $id");
    if ($item = mysqli_fetch_assoc($result)) {
        $grandTotal += $item['price'] * $quantity;
    }
}

$statusQuery = "SELECT setting_value FROM settings WHERE setting_key = 'store_status'";
$statusResult = mysqli_fetch_assoc(mysqli_query($conn, $statusQuery));
$storeStatus = $statusResult['setting_value'] ?? 'open';

if ($storeStatus === 'closed') {
    $error = "We're sorry! The kitchen is currently closed. We cannot accept new orders at this time.";
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $paymentMethod = $_POST['payment_method'];
    $paymentId = isset($_POST['payment_id']) ? $_POST['payment_id'] : 'COD';
    $specialRequests = trim($_POST['special_requests'] ?? ''); 

    $orderQuery = "INSERT INTO orders (user_id, total_amount, payment_method, special_requests, status, payment_id, created_at) VALUES (?, ?, ?, ?, 'Pending', ?, NOW())";
    $stmt = mysqli_prepare($conn, $orderQuery);
    mysqli_stmt_bind_param($stmt, "idsss", $userId, $grandTotal, $paymentMethod, $specialRequests, $paymentId);
    
    if (mysqli_stmt_execute($stmt)) {
        $orderId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // Insert individual items
        $itemQuery = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmtItem = mysqli_prepare($conn, $itemQuery);
        
        foreach ($_SESSION['cart'] as $id => $quantity) {
            $result = mysqli_query($conn, "SELECT price FROM menu_items WHERE id = $id");
            if ($item = mysqli_fetch_assoc($result)) {
                mysqli_stmt_bind_param($stmtItem, "iiid", $orderId, $id, $quantity, $item['price']);
                mysqli_stmt_execute($stmtItem);
            }
        }
        mysqli_stmt_close($stmtItem);

        unset($_SESSION['cart']);
        $_SESSION['last_order_id'] = $orderId;
        header("Location: order-success.php");
        exit;
    } else {
        $error = "Something went wrong saving your order.";
    }
}

include "../includes/header.php"; 
?>

<style>
.checkout-hero { text-align: center; padding: 40px 20px; border-bottom: 1px solid #333; margin-bottom: 40px; background: #050505;}
.checkout-hero h1 { font-size: 48px; color: gold; font-family: 'Playfair Display', serif; margin-bottom: 10px; }
.checkout-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px 60px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
@media (max-width: 768px) { .checkout-wrapper { grid-template-columns: 1fr; } }
.checkout-form { background: #111; padding: 30px; border-radius: 12px; border: 1px solid #222; }
.checkout-form h2 { color: gold; font-family: 'Playfair Display', serif; margin-bottom: 25px; border-bottom: 1px solid #333; padding-bottom: 10px;}
.form-group { margin-bottom: 20px; }
.form-group label { display: block; color: #aaa; margin-bottom: 8px; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;}
.form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px; background: #000; border: 1px solid #333; color: #fff; border-radius: 6px; font-family: 'Roboto', sans-serif; transition: border-color 0.3s;}
.form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: gold; }

.quick-tags { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; }
.quick-tag { background: #222; border: 1px solid #444; color: #ccc; padding: 6px 12px; border-radius: 20px; font-size: 12px; cursor: pointer; transition: 0.3s; }
.quick-tag:hover { border-color: gold; color: gold; }

.order-summary { background: #0a0a0a; padding: 30px; border-radius: 12px; border: 1px solid gold; height: fit-content;}
.order-summary h2 { color: #fff; font-family: 'Playfair Display', serif; margin-bottom: 25px; border-bottom: 1px solid #333; padding-bottom: 10px;}
.summary-item { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #222; }
.summary-item img { width: 65px; height: 65px; object-fit: cover; border-radius: 8px; border: 1px solid #444; }
.summary-info { flex-grow: 1; }
.summary-info h4 { color: #ccc; margin-bottom: 4px; font-size: 16px;}
.summary-price { color: gold; font-weight: bold; font-size: 16px; }
.total-row { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; font-size: 24px; font-weight: bold; font-family: 'Playfair Display', serif; }
.total-label { color: #fff; }
.total-amount { color: gold; }
.btn-place-order { display: block; width: 100%; padding: 16px; background: gold; color: #000; border: none; border-radius: 30px; font-size: 18px; font-weight: bold; cursor: pointer; transition: all 0.3s; margin-top: 30px; text-transform: uppercase; letter-spacing: 1px;}
.btn-place-order:hover { background: #ffcc00; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(255, 215, 0, 0.2); }

/* Payment Simulation Overlay */
.payment-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); z-index: 9999; display: none; flex-direction: column; justify-content: center; align-items: center; backdrop-filter: blur(5px); }
.spinner { width: 60px; height: 60px; border: 5px solid #333; border-top: 5px solid gold; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 20px; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
.payment-text { color: gold; font-family: 'Playfair Display', serif; font-size: 24px; letter-spacing: 1px; }
.payment-subtext { color: #888; font-family: 'Roboto', sans-serif; font-size: 14px; margin-top: 10px; }
</style>

<div class="checkout-hero">
    <h1>Finalize Your Order</h1>
    <p style="color: #888;">Provide your details to complete the culinary experience.</p>
</div>

<div class="checkout-wrapper">
    <div class="checkout-form">
        <h2>Delivery Details</h2>
        <?php if(isset($error)) echo "<p style='color: #ff4444; margin-bottom: 15px;'>$error</p>"; ?>
        
        <form id="checkout-form" method="POST" action="checkout.php">
            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" id="payment_method" required>
                    <option value="cod">Cash on Delivery (COD)</option>
                    <option value="online">Pay Online (Card/UPI)</option>
                </select>
            </div>

            <div class="form-group" style="margin-top: 30px; border-top: 1px solid #333; padding-top: 20px;">
                <label><i class="fas fa-utensils"></i> Special Instructions (Optional)</label>
                
                <div class="quick-tags">
                    <span class="quick-tag" onclick="addTag('🌶️ Make it Spicy')">Make it Spicy</span>
                    <span class="quick-tag" onclick="addTag('🌿 Vegan Only')">Vegan Only</span>
                    <span class="quick-tag" onclick="addTag('🥜 Nut Allergy Warning')">Nut Allergy</span>
                    <span class="quick-tag" onclick="addTag('🍴 Extra Cutlery Needed')">Extra Cutlery</span>
                </div>
                
                <textarea name="special_requests" id="special_requests" rows="3" placeholder="Any specific dietary restrictions or cooking instructions?"></textarea>
            </div>
            
            <button type="submit" class="btn-place-order" id="submit-btn">Place Order Now</button><?php if ($storeStatus === 'open'): ?>
                <button type="submit" class="btn-place-order" id="submit-btn">Place Order Now</button>
            <?php else: ?>
                <button type="button" class="btn-place-order" style="background: #444; color: #888; cursor: not-allowed;" disabled>Kitchen is Closed</button>
            <?php endif; ?>
        </form>
    </div>

    <div class="order-summary">
        <h2>Order Summary</h2>
        <?php 
        foreach ($_SESSION['cart'] as $id => $quantity):
            $result = mysqli_query($conn, "SELECT * FROM menu_items WHERE id = $id");
            if ($item = mysqli_fetch_assoc($result)):
        ?>
            <div class="summary-item">
                <img src="/assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="Dish" onerror="this.src='/assets/images/others/placeholder.jpg'">
                <div class="summary-info">
                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                    <p>Qty: <?php echo $quantity; ?></p>
                </div>
                <div class="summary-price">₹<?php echo number_format($item['price'] * $quantity, 2); ?></div>
            </div>
        <?php 
            endif;
        endforeach; 
        ?>
        <div class="total-row">
            <span class="total-label">Grand Total</span>
            <span class="total-amount">₹<?php echo number_format($grandTotal, 2); ?></span>
        </div>
    </div>
</div>

<div class="payment-overlay" id="payment-overlay">
    <div class="spinner"></div>
    <div class="payment-text">Connecting to Secure Gateway...</div>
    <div class="payment-subtext">Please do not close or refresh this window.</div>
</div>

<script>
function addTag(text) {
    const box = document.getElementById('special_requests');
    if (box.value.length > 0) {
        box.value += ", " + text;
    } else {
        box.value = text;
    }
}

// Payment Simulation Script
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    const paymentMethod = document.getElementById('payment_method').value;

    if (paymentMethod === 'online' && !document.getElementById('mock_payment_id')) {
        e.preventDefault(); 
        const overlay = document.getElementById('payment-overlay');
        overlay.style.display = 'flex';

        setTimeout(() => {
            document.querySelector('.payment-text').innerText = "Payment Successful!";
            document.querySelector('.payment-text').style.color = "#00ff88"; 
            document.querySelector('.spinner').style.borderTopColor = "#00ff88";
            document.querySelector('.payment-subtext').innerText = "Redirecting to your receipt...";

            const randomTxnId = 'MOCK_TXN_' + Math.floor(Math.random() * 900000 + 100000);
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'payment_id';
            hiddenInput.id = 'mock_payment_id';
            hiddenInput.value = randomTxnId;
            
            this.appendChild(hiddenInput);

            setTimeout(() => { this.submit(); }, 800);
        }, 2500); 
    }
});
</script>

<?php include "../includes/footer.php"; ?>