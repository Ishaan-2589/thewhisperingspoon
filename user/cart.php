<?php
session_start();
require_once "../includes/db.php";
include "../includes/header.php";

$total = 0;
?>

<section class="menu-section">
  <h2 class="heading-font">Your Cart</h2>

<?php if (empty($_SESSION['cart'])): ?>
  <p>Your cart is empty.</p>
<?php else: ?>

  <div class="cart-container">

  <?php
  foreach ($_SESSION['cart'] as $id => $quantity):

      $result = mysqli_query($conn, "SELECT * FROM menu_items WHERE id = $id");
      $item = mysqli_fetch_assoc($result);

      if (!$item) continue;

      $subtotal = $item['price'] * $quantity;
      $total += $subtotal;
  ?>

    <div class="cart-item">
      <img src="/TheWhisperingSpoon/assets/images/<?php echo $item['image']; ?>" alt="">

      <div class="cart-info">
        <h3><?php echo $item['name']; ?></h3>
        <p>Price: ₹<?php echo $item['price']; ?></p>

        <!-- Quantity Controls -->
        <div class="cart-qty-controls">
          <a href="remove-from-cart.php?id=<?php echo $id; ?>" class="qty-btn">−</a>

          <span class="qty-number">
            <?php echo $quantity; ?>
          </span>

          <a href="increase.php?id=<?php echo $id; ?>" class="qty-btn">+</a>
        </div>

        <p class="subtotal">
          Subtotal: ₹<?php echo $subtotal; ?>
        </p>
      </div>
    </div>

  <?php endforeach; ?>

  </div>

  <div class="cart-total-box">
    <h3>Total: ₹<?php echo $total; ?></h3>
  </div>

<?php endif; ?>

</section>

<?php include "../includes/footer.php"; ?>
