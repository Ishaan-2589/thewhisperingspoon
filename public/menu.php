<?php 
include "../includes/header.php"; 
include "../includes/db.php";

// Fetch menu items from database
$query = "SELECT * FROM menu_items ORDER BY category, name";
$result = mysqli_query($conn, $query);

$menuItems = [];
while ($row = mysqli_fetch_assoc($result)) {
    $menuItems[$row['category']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>The Whispering Spoon - Menu</title>

  <link
    href="https://fonts.googleapis.com/css2?family=Allura&family=Dancing+Script&family=Great+Vibes&family=Parisienne&family=Playfair+Display:wght@500;700&family=Roboto:wght@400;700&family=Satisfy&family=Sacramento&family=Tangerine&display=swap"
    rel="stylesheet">
    
</head>

<body>

  <!-- Scroll to Top -->
  <button onclick="topFunction()" id="scrollTopBtn" title="Go to top">↑</button>

<div class="hero">
  <h1 class="heading-font"></h1>
</div>

  <!-- Menu Section -->
  <section class="menu-section" id="menu">
    <h2 class="heading-font">Our Menu</h2>
 <div class="veg-toggle-wrapper">
    <span>All</span>

    <label class="switch">
      <input type="checkbox" id="vegToggle" onchange="applyVegFilter()">
      <span class="slider"></span>
    </label>

    <span class="veg-label">🟢 Veg Only</span>
  </div>

    <div class="filter-buttons">
      <button class="filter-btn active" onclick="filterMenu('all', this)">All</button>
      <button class="filter-btn" onclick="filterMenu('soup', this)">Soup</button>
      <button class="filter-btn" onclick="filterMenu('salad', this)">Salad</button>
      <button class="filter-btn" onclick="filterMenu('nibbles', this)">Nibbles</button>
      <button class="filter-btn" onclick="filterMenu('sandwich', this)">Sandwich</button>
      <button class="filter-btn" onclick="filterMenu('pasta', this)">Pasta</button>
      <button class="filter-btn" onclick="filterMenu('pizza', this)">Pizza</button>
      <button class="filter-btn" onclick="filterMenu('desserts', this)">Desserts</button>
      <button class="filter-btn" onclick="filterMenu('beverages', this)">Beverages</button>
    </div>

    <div class="soup">
      <h3 class="category-heading">Soup</h3>
      <div class="menu-items-container">
        <?php foreach ($menuItems['soup'] as $item): ?>
          <div class="menu-card soup <?php echo $item['is_veg'] ? 'veg' : 'nonveg'; ?>">
            <img src="/TheWhisperingSpoon/assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" onclick="openLightbox(this)">
            <div class="info">
              <h3><?php echo $item['name']; ?></h3>
              <p><?php echo $item['description']; ?></p>
              <div class="price">₹<?php echo number_format($item['price'], 2); ?></div>
              <button class="add-cart-btn" data-id="<?php echo $item['id']; ?>">Add to Cart</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="salad">
      <h3 class="category-heading">Salad</h3>
      <div class="menu-items-container">
        <?php foreach ($menuItems['salad'] as $item): ?>
          <div class="menu-card salad <?php echo $item['is_veg'] ? 'veg' : 'nonveg'; ?>">
            <img src="/TheWhisperingSpoon/assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" onclick="openLightbox(this)">
            <div class="info">
              <h3><?php echo $item['name']; ?></h3>
              <p><?php echo $item['description']; ?></p>
              <div class="price">₹<?php echo number_format($item['price'], 2); ?></div>
              <button class="add-cart-btn" data-id="<?php echo $item['id']; ?>">Add to Cart</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="nibbles">
      <h3 class="category-heading">Nibbles</h3>
      <div class="menu-items-container">
        <?php foreach ($menuItems['nibbles'] as $item): ?>
          <div class="menu-card nibbles <?php echo $item['is_veg'] ? 'veg' : 'nonveg'; ?>">
            <img src="/TheWhisperingSpoon/assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" onclick="openLightbox(this)">
            <div class="info">
              <h3><?php echo $item['name']; ?></h3>
              <p><?php echo $item['description']; ?></p>
              <div class="price">₹<?php echo number_format($item['price'], 2); ?></div>
              <button class="add-cart-btn" data-id="<?php echo $item['id']; ?>">Add to Cart</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="sandwich">
        <h3 class="category-heading">Sandwich</h3>
        <div class="menu-items-container">
          <?php foreach ($menuItems['sandwich'] as $item): ?>
            <div class="menu-card sandwich <?php echo $item['is_veg'] ? 'veg' : 'nonveg'; ?>">
              <img src="/TheWhisperingSpoon/assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" onclick="openLightbox(this)">
              <div class="info">
                <h3><?php echo $item['name']; ?></h3>
                <p><?php echo $item['description']; ?></p>
                <div class="price">₹<?php echo number_format($item['price'], 2); ?></div>
                <button class="add-cart-btn" data-id="<?php echo $item['id']; ?>">Add to Cart</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="pasta">
        <h3 class="category-heading">Pasta</h3>
        <div class="menu-items-container">
          <?php foreach ($menuItems['pasta'] as $item): ?>
            <div class="menu-card pasta <?php echo $item['is_veg'] ? 'veg' : 'nonveg'; ?>">
              <img src="/TheWhisperingSpoon/assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" onclick="openLightbox(this)">
              <div class="info">
                <h3><?php echo $item['name']; ?></h3>
                <p><?php echo $item['description']; ?></p>
                <div class="price">₹<?php echo number_format($item['price'], 2); ?></div>
                <button class="add-cart-btn" data-id="<?php echo $item['id']; ?>">Add to Cart</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="pizza">
        <h3 class="category-heading">Pizza</h3>
        <div class="menu-items-container">
          <?php foreach ($menuItems['pizza'] as $item): ?>
            <div class="menu-card pizza <?php echo $item['is_veg'] ? 'veg' : 'nonveg'; ?>">
              <img src="/TheWhisperingSpoon/assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" onclick="openLightbox(this)">
              <div class="info">
                <h3><?php echo $item['name']; ?></h3>
                <p><?php echo $item['description']; ?></p>
                <div class="price">₹<?php echo number_format($item['price'], 2); ?></div>
                <button class="add-cart-btn" data-id="<?php echo $item['id']; ?>">Add to Cart</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="desserts">
        <h3 class="category-heading">Desserts</h3>
        <div class="menu-items-container">
          <?php foreach ($menuItems['desserts'] as $item): ?>
            <div class="menu-card desserts <?php echo $item['is_veg'] ? 'veg' : 'nonveg'; ?>">
              <img src="/TheWhisperingSpoon/assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" onclick="openLightbox(this)">
              <div class="info">
                <h3><?php echo $item['name']; ?></h3>
                <p><?php echo $item['description']; ?></p>
                <div class="price">₹<?php echo number_format($item['price'], 2); ?></div>
                <button class="add-cart-btn" data-id="<?php echo $item['id']; ?>">Add to Cart</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="beverages">
        <h3 class="category-heading">Beverages</h3>
        <div class="menu-items-container">
          <?php foreach ($menuItems['beverages'] as $item): ?>
            <div class="menu-card beverages <?php echo $item['is_veg'] ? 'veg' : 'nonveg'; ?>">
              <img src="/TheWhisperingSpoon/assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" onclick="openLightbox(this)">
              <div class="info">
                <h3><?php echo $item['name']; ?></h3>
                <p><?php echo $item['description']; ?></p>
                <div class="price">₹<?php echo number_format($item['price'], 2); ?></div>
                <button class="add-cart-btn" data-id="<?php echo $item['id']; ?>">Add to Cart</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

  </section>

  <!-- Lightbox -->
  <div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <img id="lightbox-img" src="#" alt="Zoomed Dish">
  </div>
</body>

</html>
<?php include "../includes/footer.php"; ?>
