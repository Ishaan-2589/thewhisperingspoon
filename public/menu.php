<?php include "../includes/header.php"; ?>
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
        <div class="menu-card soup"><img src="/TheWhisperingSpoon/assets/images/soup/manchow.jpg" alt="Manchow" onclick="openLightbox(this)">
          <div class="info">
            <h3>Manchow</h3>
            <p>Hot and spicy Chinese-style soup.</p>
            <div class="price">₹199.00</div>
            <button class="add-cart-btn" data-id="1">
  Add to Cart
</button>


          </div>
        </div>
        <div class="menu-card soup veg"><img src="/TheWhisperingSpoon//assets/images/soup/mushroomcappuccino.jpg" alt="Mushroom Cappuccino"
            onclick="openLightbox(this)">
          <div class="info">
            <h3>Mushroom Cappuccino</h3>
            <p>Earthy mushrooms blended into a creamy soup.</p>
            <div class="price">₹199.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
          </div>
        </div>
        <div class="menu-card soup veg"><img src="/TheWhisperingSpoon//assets/images/soup/creamoftomato.jpg" alt="Cream Of Tomato"
            onclick="openLightbox(this)">
          <div class="info">
            <h3>Cream Of Tomato</h3>
            <p>Classic creamy tomato soup with herbs.</p>
            <div class="price">₹199.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
          </div>
        </div>
        <div class="menu-card soup veg"><img src="/TheWhisperingSpoon//assets/images/soup/hotandsour.jpg" alt="Hot & Sour" onclick="openLightbox(this)">
          <div class="info">
            <h3>Hot & Sour</h3>
            <p>Spicy and tangy Indo-Chinese delight.</p>
            <div class="price">₹199.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
          </div>
        </div>
      </div>
    </div>

    <div class="salad">
      <h3 class="category-heading">Salad</h3>
      <div class="menu-items-container">
        <div class="menu-card salad veg"><img src="/TheWhisperingSpoon//assets/images/salad/greek.jpg" alt="Ancient Greek With Herb"
            onclick="openLightbox(this)">
          <div class="info">
            <h3>Ancient Greek With Herb</h3>
            <p>Classic Greek salad with fresh herbs.</p>
            <div class="price">₹249.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
          </div>
        </div>
        <div class="menu-card salad veg"><img src="/TheWhisperingSpoon//assets/images/salad/caesar.jpg" alt="Caesar Salad" onclick="openLightbox(this)">
          <div class="info">
            <h3>Caesar Salad</h3>
            <p>Crunchy lettuce with creamy Caesar dressing.</p>
            <div class="price">₹249.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
          </div>
        </div>
        <div class="menu-card salad veg"><img src="/TheWhisperingSpoon//assets/images/salad/fruit.jpg" alt="Fruit Salad" onclick="openLightbox(this)">
          <div class="info">
            <h3>Fruit Salad</h3>
            <p>Fresh seasonal fruits with a drizzle of honey.</p>
            <div class="price">₹299.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
          </div>
        </div>
        <div class="menu-card salad nonveg"><img src="/TheWhisperingSpoon//assets/images/salad/chickencaesar.jpeg" alt="Chicken Caesar Salad"
            onclick="openLightbox(this)">
          <div class="info">
            <h3>Chicken Caesar Salad</h3>
            <p>Grilled chicken over crunchy greens.</p>
            <div class="price">₹349.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
          </div>
        </div>
      </div>
    </div>

    <div class="nibbles">
      <h3 class="category-heading">Nibbles</h3>
      <div class="menu-items-container">
        <div class="menu-card nibbles veg"><img src="/TheWhisperingSpoon//assets/images/nibbles/chillipotato.jpg" alt="Chilli Potato"
            onclick="openLightbox(this)">
          <div class="info">
            <h3>Chilli Potato</h3>
            <p>Crispy potatoes tossed in spicy sauce.</p>
            <div class="price">₹299.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
          </div>
        </div>
        <div class="menu-card nibbles veg"><img src="/TheWhisperingSpoon//assets/images/nibbles/springroll.jpg" alt="Spring Roll"
            onclick="openLightbox(this)">
          <div class="info">
            <h3>Spring Roll</h3>
            <p>Savory, crispy, delightful fried wrap.</p>
            <div class="price">₹249.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
          </div>
        </div>
        <div class="menu-card nibbles veg"><img src="/TheWhisperingSpoon//assets/images/nibbles/garlicbread.jpg" alt="Garlic Bread"
            onclick="openLightbox(this)">
          <div class="info">
            <h3>Garlic Bread</h3>
            <p>Toasted bread with herbed garlic butter.</p>
            <div class="price">₹259.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
          </div>
        </div>
        <div class="menu-card nibbles veg"><img src="/TheWhisperingSpoon//assets/images/nibbles/garliccheese.jpeg" alt="Garlic Bread With Cheese"
            onclick="openLightbox(this)">
          <div class="info">
            <h3>Garlic Bread With Cheese</h3>
            <p>Loaded with gooey cheese and garlic flavor.</p>
            <div class="price">₹289.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
          </div>
        </div>
      </div>

      <div class="sandwich">
        <h3 class="category-heading">Sandwich</h3>
        <div class="menu-items-container">
          <div class="menu-card sandwich veg"><img src="/TheWhisperingSpoon//assets/images/sandwich/cheese.jpg" alt="Cheese Sandwich"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Cheese Sandwich</h3>
              <p>Classic grilled cheese sandwich.</p>
              <div class="price">₹299.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card sandwich veg"><img src="/TheWhisperingSpoon//assets/images/sandwich/paneertikka.jpg" alt="Paneer Tikka Sandwich"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Paneer Tikka Sandwich</h3>
              <p>Stuffed with smoky paneer tikka masala.</p>
              <div class="price">₹349.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card sandwich veg"><img src="/TheWhisperingSpoon//assets/images/sandwich/veg.jpg" alt="Veg Medetarian Sandwich"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Veg Medetarian Sandwich</h3>
              <p>Veggies and herbs packed between bread.</p>
              <div class="price">₹249.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card sandwich nonveg"><img src="/TheWhisperingSpoon//assets/images/sandwich/smokychicken.jpeg" alt="Smoky Chicken Sandwich"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Smoky Chicken Sandwich</h3>
              <p>Grilled chicken with smokey flavor.</p>
              <div class="price">₹299.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
        </div>
      </div>

      <div class="pasta">
        <h3 class="category-heading">Pasta</h3>
        <div class="menu-items-container">
          <div class="menu-card pasta veg"><img src="/TheWhisperingSpoon//assets/images/pasta/alfredo.jpg" alt="Alfredo Pasta"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Alfredo (Veg)</h3>
              <p>Creamy white sauce pasta with herbs.</p>
              <div class="price">₹359.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card pasta veg"><img src="/TheWhisperingSpoon//assets/images/pasta/arrabbiata.jpg" alt="Arrabbiata Pasta"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Arrabbiata (Veg)</h3>
              <p>Spicy red tomato-based sauce pasta.</p>
              <div class="price">₹359.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card pasta veg"><img src="/TheWhisperingSpoon//assets/images/pasta/pesto.jpg" alt="Pesto Pasta" onclick="openLightbox(this)">
            <div class="info">
              <h3>Pesto Pasta</h3>
              <p>Fresh basil pesto mixed with cream pasta.</p>
              <div class="price">₹399.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
        </div>
      </div>

      <div class="pizza">
        <h3 class="category-heading">Pizza</h3>
        <div class="menu-items-container">
          <div class="menu-card pizza veg"><img src="/TheWhisperingSpoon//assets/images/pizza/margharita.jpg" alt="Margarita Pizza"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Margarita</h3>
              <p>Tomato, mozzarella & basil on thin crust.</p>
              <div class="price">₹299.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card pizza veg"><img src="/TheWhisperingSpoon//assets/images/pizza/farmhouse.jpg" alt="Farmhouse Veggie"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Farmhouse Veggie</h3>
              <p>Loaded with spicy veggies & capsicum.</p>
              <div class="price">₹329.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card pizza veg"><img src="/TheWhisperingSpoon//assets/images/pizza/paneertikka.jpg" alt="Paneer Tikka Pizza"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Paneer Tikka</h3>
              <p>Paneer with roasted onion & bell peppers.</p>
              <div class="price">₹379.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card pizza nonveg"><img src="/TheWhisperingSpoon//assets/images/pizza/chickentikka.jpg" alt="Chicken Tikka Pizza"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Chicken Tikka</h3>
              <p>Chunks of tikka chicken, onions & cheese.</p>
              <div class="price">₹449.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
        </div>
      </div>

      <div class="desserts">
        <h3 class="category-heading">Desserts</h3>
        <div class="menu-items-container">
          <div class="menu-card desserts veg"><img src="/TheWhisperingSpoon//assets/images/desserts/chocolatelava.jpg" alt="Chocolate Lava Cake"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Chocolate Lava Cake</h3>
              <p>Warm chocolate cake with a molten center.</p>
              <div class="price">₹299.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card desserts veg"><img src="/TheWhisperingSpoon//assets/images/desserts/tiramisu.jpg" alt="Tiramisu"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Classic Tiramisu</h3>
              <p>Layers of coffee-soaked ladyfingers and mascarpone cream.</p>
              <div class="price">₹349.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card desserts veg"><img src="/TheWhisperingSpoon//assets/images/desserts/cheesecake.jpeg" alt="New York Cheesecake"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>New York Cheesecake</h3>
              <p>Rich and creamy baked cheesecake with a graham cracker crust.</p>
              <div class="price">₹329.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card desserts veg"><img src="/TheWhisperingSpoon/assets/images/desserts/browniesizzler.jpg" alt="Brownie Sizzler"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Brownie Sizzler</h3>
              <p>Hot chocolate brownie with vanilla ice cream on a sizzler plate.</p>
              <div class="price">₹399.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
        </div>
      </div>

      <div class="beverages">
        <h3 class="category-heading">Beverages</h3>
        <div class="menu-items-container">
          <div class="menu-card beverages veg"><img src="/TheWhisperingSpoon//assets/images/beverages/icedlatte.jpg" alt="Iced Latte"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Iced Latte</h3>
              <p>Chilled espresso with milk over ice.</p>
              <div class="price">₹189.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card beverages veg"><img src="/TheWhisperingSpoon//assets/images/beverages/freshlimesoda.jpg" alt="Fresh Lime Soda"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Fresh Lime Soda</h3>
              <p>Refreshing lime juice with soda, sweet or salty.</p>
              <div class="price">₹149.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card beverages veg"><img src="/TheWhisperingSpoon//assets/images/beverages/masalachai.jpg" alt="Masala Chai"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Traditional Masala Chai</h3>
              <p>Spiced Indian tea, brewed to perfection.</p>
              <div class="price">₹129.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card beverages veg"><img src="/TheWhisperingSpoon//assets/images/beverages/virginmojito.jpg" alt="Virgin Mojito"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Virgin Mojito</h3>
              <p>Classic refreshing blend of mint, lime, and soda.</p>
              <div class="price">₹219.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
          <div class="menu-card beverages veg"><img src="/TheWhisperingSpoon//assets/images/beverages/coldcoffee.jpeg" alt="Cold Coffee"
              onclick="openLightbox(this)">
            <div class="info">
              <h3>Cold Coffee with Ice Cream</h3>
              <p>Blended coffee with milk, sugar, and a scoop of vanilla ice cream.</p>
              <div class="price">₹249.00</div><button class="add-cart-btn" data-id="1">
  Add to Cart
</button>
            </div>
          </div>
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
