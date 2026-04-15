<?php
session_start();
require_once "../includes/db.php";
include "../includes/header.php";

$total = 0;
?>

<style>
.cart-hero { text-align: center; padding: 40px 20px; border-bottom: 1px solid #333; margin-bottom: 40px; background: #050505;}
.cart-hero h1 { font-size: 48px; color: gold; font-family: 'Playfair Display', serif; margin-bottom: 10px; }
.cart-wrapper { max-width: 900px; margin: 0 auto; padding: 0 20px; min-height: 50vh; }
.empty-cart { text-align: center; padding: 50px; color: #888; font-size: 18px; }

.cart-item { background: #111; border: 1px solid #222; border-radius: 12px; display: flex; gap: 20px; padding: 20px; margin-bottom: 20px; align-items: center; transition: border-color 0.3s; }
.cart-item:hover { border-color: #443a1a; }
.cart-item img { width: 120px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #333; }
.cart-info { flex-grow: 1; }
.cart-info h3 { color: #fff; font-family: 'Playfair Display', serif; margin-bottom: 5px; font-size: 22px; }
.cart-info p { color: #888; margin-bottom: 10px; font-size: 14px;}

.qty-controls { display: flex; align-items: center; gap: 15px; }
.qty-btn { background: #222; color: gold; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none; font-size: 20px; font-weight: bold; border: 1px solid #444; transition: all 0.3s; }
.qty-btn:hover { background: gold; color: #000; border-color: gold; }
.qty-number { color: #fff; font-size: 18px; font-weight: bold; width: 30px; text-align: center; }

.item-subtotal { font-size: 22px; color: gold; font-weight: bold; margin-left: auto; text-align: right; }

.cart-summary { background: #111; border: 1px solid gold; border-radius: 12px; padding: 30px; margin-top: 40px; text-align: right; margin-bottom: 60px; box-shadow: 0 10px 30px rgba(255,215,0,0.05);}
.cart-summary h2 { color: #aaa; font-size: 18px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
.cart-summary .grand-total { color: gold; font-size: 36px; font-weight: bold; margin-bottom: 25px; font-family: 'Playfair Display', serif; }
.checkout-btn { display: inline-block; background: gold; color: #000; padding: 15px 40px; border-radius: 30px; font-size: 18px; font-weight: bold; text-decoration: none; transition: transform 0.3s; }
.checkout-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(255, 215, 0, 0.2); }
</style>

<div class="cart-hero">
    <h1>Your Culinary Journey</h1>
    <p style="color: #888;">Review your selected dishes before we prepare them.</p>
</div>

<div class="cart-wrapper">
<?php if (empty($_SESSION['cart'])): ?>
    <div class="empty-cart">
        <p>Your cart is currently empty. The kitchen awaits your order!</p>
        <a href="../public/menu.php" class="checkout-btn" style="margin-top: 25px;">Explore Our Menu</a>
    </div>
<?php else: ?>

    <?php
    foreach ($_SESSION['cart'] as $id => $quantity):
        $result = mysqli_query($conn, "SELECT * FROM menu_items WHERE id = $id");
        $item = mysqli_fetch_assoc($result);
        
        if (!$item) continue;
        
        $subtotal = $item['price'] * $quantity;
        $total += $subtotal;
    ?>
        <div class="cart-item">
            <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" onerror="this.src='../assets/images/others/placeholder.jpg'">
            
            <div class="cart-info">
                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                <p>₹<?php echo number_format($item['price'], 2); ?> per portion</p>
                
                <div class="qty-controls">
                    <a href="remove-from-cart.php?id=<?php echo $id; ?>" class="qty-btn">−</a>
                    <span class="qty-number"><?php echo $quantity; ?></span>
                    <a href="increase.php?id=<?php echo $id; ?>" class="qty-btn">+</a>
                </div>
            </div>
            
            <div class="item-subtotal">
                ₹<?php echo number_format($subtotal, 2); ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="cart-summary">
        <h2>Total Bill</h2>
        <div class="grand-total">₹<?php echo number_format($total, 2); ?></div>
        
        <div style="display: flex; gap: 15px; justify-content: flex-end;">
            <a href="clear-cart.php" class="checkout-btn" style="background: transparent; border: 1px solid #ff4444; color: #ff4444;">Empty Cart</a>
            <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        </div>
    </div>

<?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>