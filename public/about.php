<?php 
session_start();
include "../includes/header.php"; 
?>

<style>
.about-hero { background: linear-gradient(rgba(5, 5, 5, 0.7), rgba(5, 5, 5, 0.9)), url('../assets/images/others/banner.png'); background-size: cover; background-position: center; padding: 100px 20px; text-align: center; border-bottom: 1px solid #333; }
.about-hero h1 { font-family: 'Playfair Display', serif; font-size: 56px; color: gold; margin-bottom: 15px; }
.about-hero p { color: #ccc; font-size: 18px; max-width: 600px; margin: 0 auto; line-height: 1.6; }

.about-wrapper { max-width: 1000px; margin: 60px auto; padding: 0 20px; }
.story-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center; margin-bottom: 80px; }
.story-text h2 { font-family: 'Playfair Display', serif; font-size: 36px; color: #fff; margin-bottom: 20px; }
.story-text p { color: #888; font-size: 16px; line-height: 1.8; margin-bottom: 15px; }
.story-image { width: 100%; height: 400px; object-fit: cover; border-radius: 16px; border: 1px solid #333; box-shadow: -15px 15px 0px rgba(255, 215, 0, 0.1); }

.values-section { text-align: center; background: #0a0a0a; padding: 60px 20px; border-radius: 16px; border: 1px solid #222; }
.values-section h2 { font-family: 'Playfair Display', serif; font-size: 32px; color: gold; margin-bottom: 40px; }
.values-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }
.value-card i { font-size: 36px; color: gold; margin-bottom: 15px; }
.value-card h3 { color: #fff; font-size: 20px; margin-bottom: 10px; }
.value-card p { color: #666; font-size: 14px; line-height: 1.6; }

@media (max-width: 768px) { .story-grid { grid-template-columns: 1fr; } .story-image { box-shadow: none; } }
</style>

<div class="about-hero">
    <h1>Our Heritage</h1>
    <p>Discover the passion, precision, and artistry behind every dish at The Whispering Spoon.</p>
</div>

<div class="about-wrapper">
    <div class="story-grid">
        <div class="story-text">
            <h2>A Culinary Journey</h2>
            <p>Founded in 2018 by Executive Chef Julian Vance, The Whispering Spoon began as a small, intimate dining room with a simple goal: to elevate local, seasonal ingredients through modern, innovative techniques.</p>
            <p>What started as a hidden gem quickly transformed into a premier culinary destination. We believe that fine dining should not be stiff or pretentious, but rather a warm, theatrical experience that engages all the senses.</p>
            <p>Today, our kitchen continues to push boundaries, honoring classic flavor profiles while presenting them in breathtaking, contemporary ways.</p>
        </div>
        <div>
            <img src="https://images.unsplash.com/photo-1577219491135-ce391730fb2c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Chef Cooking" class="story-image">
        </div>
    </div>

    <div class="values-section">
        <h2>Our Core Pillars</h2>
        <div class="values-grid">
            <div class="value-card">
                <i class="fas fa-seedling"></i>
                <h3>Sourcing</h3>
                <p>We partner directly with organic farmers, ethical butchers, and local artisans to ensure our ingredients are unparalleled in freshness.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-fire"></i>
                <h3>Technique</h3>
                <p>Our chefs undergo rigorous training, mastering both ancient culinary traditions and cutting-edge molecular gastronomy.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-wine-glass-alt"></i>
                <h3>Hospitality</h3>
                <p>Service is an art form. Our staff anticipates your needs before you do, ensuring a flawless and memorable evening.</p>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>