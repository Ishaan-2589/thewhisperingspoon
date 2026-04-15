<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
session_start();
require_once "../includes/db.php";

// Fetch all menu items from the database
$query = "SELECT * FROM menu_items ORDER BY category, name";
$result = mysqli_query($conn, $query);

$menuItems = [];
$categories = [];

while ($row = mysqli_fetch_assoc($result)) {
    $menuItems[] = $row;
    // Keep track of unique categories for our filter buttons
    if (!in_array($row['category'], $categories)) {
        $categories[] = $row['category'];
    }
}
?>

<?php include "../includes/header.php"; ?>

<style>

/* 1. The Hero Banner Fix */
.hero.menu-hero {
    background: linear-gradient(rgba(10, 10, 10, 0.7), rgba(10, 10, 10, 0.9)), url('../assets/images/others/banner.png');
    background-size: cover;
    background-position: center;
    min-height: 250px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    border-bottom: 1px solid #333;
}

/* 2. Sleek Pill-Shaped Filter Buttons */
.filter-wrapper {
    position: sticky;
    top: 0; 
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(10px);
    z-index: 100;
    padding: 20px 0;
    text-align: center;
    border-bottom: 1px solid #222;
}

.filter-btn {
    background: transparent;
    color: #888;
    border: 1px solid #444;
    border-radius: 30px;
    padding: 8px 24px;
    margin: 5px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
}

.filter-btn:hover { border-color: gold; color: #fff; }
.filter-btn.active { background: gold; color: #000; border-color: gold; font-weight: bold; }

/* 3. Perfect Grid Layout for Cards */
.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

/* 4. Cleaner Menu Cards */
.menu-card {
    background: #0f0f0f;
    border: 1px solid #222;
    border-radius: 16px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
}

.menu-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(255, 215, 0, 0.1);
    border-color: #443a1a;
}

.menu-card img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-bottom: 1px solid #222;
}

.menu-card .info { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }

.card-title {
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    color: #fff;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.veg-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; }
.veg-dot.veg { background-color: #00ff88; box-shadow: 0 0 8px rgba(0,255,136,0.4); }
.veg-dot.non-veg { background-color: #ff4444; box-shadow: 0 0 8px rgba(255,68,68,0.4); }

.card-desc { color: #888; font-size: 14px; line-height: 1.5; margin-bottom: 20px; flex-grow: 1; }
.card-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; }
.card-price { font-size: 20px; color: gold; font-weight: bold; }

.btn-add { background: transparent; color: gold; border: 1px solid gold; padding: 8px 20px; border-radius: 20px; font-weight: bold; cursor: pointer; transition: all 0.3s ease; }
.btn-add:hover { background: gold; color: #000; }

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.dish-link { color: inherit; text-decoration: none; transition: color 0.3s; }
.dish-link:hover { color: gold; }
</style>

<section class="hero menu-hero">
    <h1 class="logo-font" style="font-size: 60px; text-shadow: 2px 4px 8px rgba(0,0,0,0.8);">Our Menu</h1>
    <p style="color: gold; font-family: 'Playfair Display', serif; font-size: 22px; text-shadow: 1px 2px 4px rgba(0,0,0,0.8);">Savor the whispers of taste.</p>
</section>

<div class="filter-wrapper">
    <button class="filter-btn active" data-filter="all">All Items</button>
    <?php foreach ($categories as $category): ?>
        <button class="filter-btn" data-filter="<?php echo htmlspecialchars($category); ?>">
            <?php echo ucfirst(htmlspecialchars($category)); ?>
        </button>
    <?php endforeach; ?>
    
    <div style="display: inline-flex; align-items: center; gap: 10px; margin-left: 15px; border-left: 1px solid #444; padding-left: 15px;">
        <span style="color: #aaa; font-size: 14px;">Veg Only</span>
        <label class="switch" style="margin: 0; transform: scale(0.8);">
            <input type="checkbox" id="vegToggle">
            <span class="slider"></span>
        </label>
    </div>
</div>

<div style="background-color: #050505; min-height: 50vh; padding-bottom: 60px;">
    <div class="menu-grid" id="menuGrid">
        <?php foreach ($menuItems as $item): ?>
            <div class="menu-card" 
                 data-category="<?php echo htmlspecialchars($item['category']); ?>" 
                 data-veg="<?php echo $item['is_veg']; ?>">
                 
                <a href="item-details.php?id=<?php echo $item['id']; ?>">
                    <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                         onerror="this.src='../assets/images/others/placeholder.jpg'">
                </a>
                
                <div class="info">
                    <h3 class="card-title">
                        <span class="veg-dot <?php echo $item['is_veg'] ? 'veg' : 'non-veg'; ?>"></span>
                        <a href="item-details.php?id=<?php echo $item['id']; ?>" class="dish-link">
                            <?php echo htmlspecialchars($item['name']); ?>
                        </a>
                    </h3>
                    
                    <p class="card-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                    
                    <div class="card-footer">
                        <span class="card-price">₹<?php echo number_format($item['price'], 2); ?></span>
                        
                        <button class="btn-add" 
                                onclick="addToCart(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars(addslashes($item['name'])); ?>', <?php echo $item['price']; ?>, '<?php echo htmlspecialchars(addslashes($item['image'])); ?>')">
                            Add +
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="toast" class="toast-message">Item added to cart!</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const menuCards = document.querySelectorAll('.menu-card');
    const vegToggle = document.getElementById('vegToggle');

    function applyFilters() {
        const activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
        const isVegOnly = vegToggle.checked;

        menuCards.forEach((card, index) => {
            const itemCategory = card.getAttribute('data-category');
            const isItemVeg = card.getAttribute('data-veg') === '1';

            let categoryMatch = (activeFilter === 'all' || activeFilter === itemCategory);
            let vegMatch = (!isVegOnly || isItemVeg);

            if (categoryMatch && vegMatch) {
                card.style.display = 'flex'; 
                card.style.animation = 'none';
                card.offsetHeight; 
                card.style.animation = `fadeInUp 0.4s ease forwards ${index * 0.05}s`;
            } else {
                card.style.display = 'none';
            }
        });
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            filterBtns.forEach(b => b.classList.remove('active'));
            e.target.classList.add('active');
            applyFilters();
        });
    });

    vegToggle.addEventListener('change', applyFilters);
});

function addToCart(id, name, price, image) {
    let formData = new FormData();
    formData.append('id', id);

    fetch('../user/add-to-cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            document.getElementById('cart-counter').innerText = data.totalItems;
            showToast(`${name} added!`);
        }
    })
    .catch(error => console.error('Error:', error));
}

function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}
</script>

<?php include "../includes/footer.php"; ?>