<?php 
session_start();
include "../includes/header.php"; 
?>

<style>
/* --- Home Page Luxury Styles --- */

/* 1. Full-Screen Hero Section */
.hero-home {
    /* The gradient overlay ensures the white/gold text is always readable over the image */
    background: linear-gradient(rgba(5, 5, 5, 0.6), rgba(5, 5, 5, 0.9)), url('../assets/images/others/banner.png');
    background-size: cover;
    background-position: center;
    background-attachment: fixed; /* Creates a cool parallax effect when scrolling */
    min-height: 85vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 0 20px;
    border-bottom: 1px solid #222;
}

.hero-home h1 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(40px, 6vw, 72px);
    color: gold;
    margin-bottom: 15px;
    text-shadow: 2px 4px 10px rgba(0,0,0,0.8);
    animation: fadeInUp 1s ease-out forwards;
    opacity: 0;
}

.hero-home p {
    font-family: 'Roboto', sans-serif;
    font-size: clamp(16px, 2vw, 22px);
    color: #e0e0e0;
    max-width: 600px;
    margin-bottom: 40px;
    letter-spacing: 1px;
    line-height: 1.6;
    animation: fadeInUp 1s ease-out 0.3s forwards;
    opacity: 0;
}

/* Dual Call-to-Action Buttons */
.hero-buttons {
    display: flex;
    gap: 20px;
    animation: fadeInUp 1s ease-out 0.6s forwards;
    opacity: 0;
}

.btn-primary-gold {
    background: gold;
    color: #000;
    padding: 15px 35px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: bold;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    border: 1px solid gold;
}

.btn-primary-gold:hover {
    background: #ffcc00;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(255, 215, 0, 0.2);
}

.btn-outline-light {
    background: transparent;
    color: #fff;
    padding: 15px 35px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: bold;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    border: 1px solid #aaa;
}

.btn-outline-light:hover {
    border-color: #fff;
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-3px);
}

/* 2. Features / About Section */
.features-section {
    background-color: #050505;
    padding: 100px 20px;
    text-align: center;
}

.section-title {
    font-family: 'Playfair Display', serif;
    font-size: 42px;
    color: #fff;
    margin-bottom: 20px;
}

.section-subtitle {
    color: gold;
    font-family: 'Parisienne', cursive;
    font-size: 28px;
    margin-bottom: 60px;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
    max-width: 1200px;
    margin: 0 auto;
}

.feature-card {
    background: #111;
    border: 1px solid #222;
    padding: 40px 30px;
    border-radius: 16px;
    transition: all 0.4s ease;
}

.feature-card:hover {
    transform: translateY(-10px);
    border-color: #443a1a;
    box-shadow: 0 15px 30px rgba(255, 215, 0, 0.05);
}

.feature-icon {
    font-size: 48px;
    color: gold;
    margin-bottom: 25px;
}

.feature-card h3 {
    font-family: 'Playfair Display', serif;
    font-size: 24px;
    color: #fff;
    margin-bottom: 15px;
}

.feature-card p {
    color: #888;
    line-height: 1.7;
    font-size: 15px;
}

/* 3. Final CTA Banner */
.bottom-cta {
    background: linear-gradient(135deg, #1a1a1a, #0a0a0a);
    padding: 80px 20px;
    text-align: center;
    border-top: 1px solid gold;
    border-bottom: 1px solid #333;
}

.bottom-cta h2 {
    font-family: 'Playfair Display', serif;
    font-size: 36px;
    color: gold;
    margin-bottom: 20px;
}

.bottom-cta p {
    color: #ccc;
    max-width: 600px;
    margin: 0 auto 30px;
    font-size: 18px;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mobile responsiveness for buttons */
@media (max-width: 600px) {
    .hero-buttons {
        flex-direction: column;
        width: 100%;
        max-width: 300px;
    }
    .btn-primary-gold, .btn-outline-light {
        width: 100%;
    }
}
</style>

<div class="hero-home">
    <h1>Fine Dining, Redefined</h1>
    <p>Experience a symphony of premium flavors, handcrafted dishes, and an unforgettable ambiance at The Whispering Spoon.</p>
    
    <div class="hero-buttons">
        <a href="menu.php" class="btn-primary-gold">
            <i class="fas fa-utensils" style="margin-right: 8px;"></i> View Menu
        </a>
        <a href="../user/book-table.php" class="btn-outline-light">
            <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Book a Table
        </a>
    </div>
</div>

<section class="features-section">
    <h2 class="section-title">The Culinary Experience</h2>
    <p class="section-subtitle">Why dine with us?</p>
    
    <div class="features-grid">
        <div class="feature-card">
            <i class="fas fa-leaf feature-icon"></i>
            <h3>Premium Ingredients</h3>
            <p>We source only the freshest, highest-quality seasonal ingredients from local farms and trusted artisans to ensure every bite is perfection.</p>
        </div>
        
        <div class="feature-card">
            <i class="fas fa-fire-burner feature-icon"></i>
            <h3>Master Chefs</h3>
            <p>Our kitchen is led by award-winning chefs who bring decades of global culinary experience and immense passion to every single plate.</p>
        </div>
        
        <div class="feature-card">
            <i class="fas fa-glass-cheers feature-icon"></i>
            <h3>Luxury Ambiance</h3>
            <p>From the lighting to the music, every detail of our dining room is curated to provide a warm, intimate, and luxurious atmosphere.</p>
        </div>
    </div>
</section>

<section class="bottom-cta">
    <h2>Ready for an unforgettable evening?</h2>
    <p>Whether you want to enjoy our cuisine from the comfort of your home or join us in our elegant dining room, we are ready to serve you.</p>
    
    <?php if (isset($_SESSION["user_id"])): ?>
        <a href="menu.php" class="btn-primary-gold" style="display: inline-block;">Start Your Order</a>
    <?php else: ?>
        <a href="../auth/login.php" class="btn-primary-gold" style="display: inline-block;">Login to Order & Book</a>
    <?php endif; ?>
</section>

<?php include "../includes/footer.php"; ?>