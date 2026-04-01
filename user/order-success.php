<?php
session_start();
require_once "../includes/db.php";

// If someone tries to access this page directly without placing an order, send them home
if (!isset($_SESSION['last_order_id'])) {
    header("Location: ../public/index.php");
    exit;
}

$orderId = $_SESSION['last_order_id'];

// We don't unset the session ID immediately just in case they refresh the page, 
// but in a strict production environment, you might clear it here.

include "../includes/header.php"; 
?>

<style>
/* Luxury Success UI */
.success-wrapper {
    min-height: 60vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 60px 20px;
    background: #050505;
}

.success-card {
    background: #111;
    border: 1px solid #333;
    border-radius: 16px;
    padding: 50px 40px;
    max-width: 600px;
    width: 100%;
    text-align: center;
    box-shadow: 0 15px 35px rgba(255, 215, 0, 0.05);
    position: relative;
    overflow: hidden;
}

/* Subtle gold glow at the top of the card */
.success-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, transparent, gold, transparent);
}

/* Animated Checkmark */
.checkmark-circle {
    width: 80px;
    height: 80px;
    background: rgba(0, 255, 136, 0.1);
    border: 2px solid #00ff88;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto 25px;
    animation: scaleIn 0.5s ease-out;
}

.checkmark {
    color: #00ff88;
    font-size: 40px;
    animation: popIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    opacity: 0;
}

.success-title {
    font-family: 'Playfair Display', serif;
    color: gold;
    font-size: 36px;
    margin-bottom: 15px;
}

.success-message {
    color: #aaa;
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 30px;
}

.order-number {
    display: inline-block;
    background: #1a1a1a;
    border: 1px dashed gold;
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 20px;
    font-family: monospace;
    letter-spacing: 2px;
    margin-bottom: 40px;
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.btn-primary, .btn-secondary {
    padding: 14px 28px;
    border-radius: 30px;
    font-weight: bold;
    text-decoration: none;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 14px;
}

.btn-primary {
    background: gold;
    color: #000;
    border: 1px solid gold;
}

.btn-primary:hover {
    background: #ffcc00;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255, 215, 0, 0.2);
}

.btn-secondary {
    background: transparent;
    color: #fff;
    border: 1px solid #444;
}

.btn-secondary:hover {
    border-color: #fff;
    background: rgba(255, 255, 255, 0.05);
}

/* Animations */
@keyframes scaleIn {
    0% { transform: scale(0); }
    100% { transform: scale(1); }
}

@keyframes popIn {
    0% { transform: scale(0); opacity: 0; }
    80% { transform: scale(1.2); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="success-wrapper">
    <div class="success-card">
        
        <div class="checkmark-circle">
            <i class="fas fa-check checkmark"></i>
        </div>
        
        <h1 class="success-title">Order Confirmed</h1>
        
        <p class="success-message">
            Thank you for choosing The Whispering Spoon.<br>
            Our chefs have received your order and are beginning preparations.
        </p>
        
        <div>
            <span style="color: #666; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px;">Your Order Number</span>
            <div class="order-number">
                #<?php echo str_pad($orderId, 5, '0', STR_PAD_LEFT); ?>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="my-orders.php" class="btn-primary">Track Order</a>
            <a href="../public/menu.php" class="btn-secondary">Back to Menu</a>
        </div>
        
    </div>
</div>

<?php include "../includes/footer.php"; ?>