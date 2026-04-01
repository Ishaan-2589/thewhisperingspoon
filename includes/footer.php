<style>
.site-footer {
    background: #050505;
    border-top: 1px solid #222;
    padding: 60px 20px 20px;
    text-align: center;
    color: #888;
    font-family: 'Roboto', sans-serif;
}
.footer-brand {
    font-family: 'Playfair Display', serif;
    color: gold;
    font-size: 28px;
    margin-bottom: 20px;
    letter-spacing: 1px;
}
.footer-links {
    display: flex;
    gap: 30px;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 30px;
}
.footer-links a {
    color: #ccc;
    text-decoration: none;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 1px;
    transition: 0.3s;
}
.footer-links a:hover { color: gold; }
.footer-socials {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 30px;
}
.footer-socials a {
    color: #555;
    font-size: 20px;
    transition: 0.3s;
}
.footer-socials a:hover { color: gold; transform: translateY(-3px); }
.footer-copy {
    border-top: 1px solid #1a1a1a;
    padding-top: 20px;
    font-size: 12px;
}
</style>

<footer class="site-footer">
    <div style="max-width: 1200px; margin: 0 auto;">
        
        <div class="footer-brand">The Whispering Spoon</div>
        
        <div class="footer-links">
            <a href="/TheWhisperingSpoon/public/index.php">Home</a>
            <a href="/TheWhisperingSpoon/public/menu.php">Menu</a>
            <a href="/TheWhisperingSpoon/public/about.php">About Us</a>
            <a href="/TheWhisperingSpoon/public/contact.php">Contact</a>
            <a href="/TheWhisperingSpoon/public/legal.php">Privacy & Terms</a>
        </div>

        <div class="footer-socials">
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
        </div>

        <div class="footer-copy">
            &copy; <?php echo date('Y'); ?> The Whispering Spoon. All rights reserved.
        </div>
    </div>

    <button id="scrollTopBtn" style="display:none; position:fixed; bottom:20px; right:20px; background:gold; color:#000; border:none; padding:12px 16px; border-radius:50%; cursor:pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.5); z-index: 999; transition: 0.3s;"><i class="fas fa-arrow-up"></i></button>
</footer>

<script>
let mybutton = document.getElementById("scrollTopBtn");
window.onscroll = function() {
    if(!mybutton) return;
    if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
        mybutton.style.display = "block";
    } else {
        mybutton.style.display = "none";
    }
};
if(mybutton) {
    mybutton.onclick = function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };
}
</script>

</body>
</html>