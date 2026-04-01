<?php 
session_start();
include "../includes/header.php"; 
?>

<style>
.legal-wrapper { max-width: 800px; margin: 60px auto 100px; padding: 0 20px; font-family: 'Roboto', sans-serif; color: #bbb; }
.legal-wrapper h1 { font-family: 'Playfair Display', serif; color: gold; font-size: 42px; text-align: center; margin-bottom: 10px; }
.legal-wrapper .last-updated { text-align: center; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 50px; border-bottom: 1px solid #222; padding-bottom: 30px;}

.legal-section { margin-bottom: 50px; background: #0a0a0a; padding: 40px; border-radius: 12px; border: 1px solid #1a1a1a; }
.legal-section h2 { color: #fff; font-size: 24px; margin-top: 0; margin-bottom: 20px; font-family: 'Playfair Display', serif; }
.legal-section h3 { color: #ddd; font-size: 18px; margin-top: 30px; margin-bottom: 10px; }
.legal-section p { line-height: 1.8; margin-bottom: 15px; font-size: 15px; }
.legal-section ul { padding-left: 20px; line-height: 1.8; margin-bottom: 20px; }
.legal-section li { margin-bottom: 8px; }
</style>

<div class="legal-wrapper">
    <h1>Legal Information</h1>
    <div class="last-updated">Last Updated: October 2025</div>

    <div class="legal-section">
        <h2>Terms of Service</h2>
        <p>Welcome to The Whispering Spoon. By accessing our website, placing an order, or making a reservation, you agree to be bound by these Terms of Service.</p>
        
        <h3>1. Reservations and Cancellations</h3>
        <p>Table reservations are subject to availability. We kindly request that any cancellations be made at least 12 hours in advance via your profile dashboard or by contacting our team directly. Repeated no-shows may result in a suspension of booking privileges.</p>
        
        <h3>2. Online Ordering</h3>
        <p>All online orders are prepared fresh upon receipt. Estimated delivery or pickup times are approximations and may vary during peak hours. If you select "Pay Online," payments are securely processed by our third-party payment gateways.</p>
        
        <h3>3. Dietary Restrictions</h3>
        <p>While we take extreme care to accommodate dietary restrictions and allergies noted in the "Special Instructions" box, our kitchen handles nuts, dairy, gluten, and shellfish. Cross-contamination is possible, and we cannot guarantee a 100% allergen-free environment.</p>
    </div>

    <div class="legal-section">
        <h2>Privacy Policy</h2>
        <p>Your privacy is of the utmost importance to us. This policy outlines how we collect, use, and protect your personal data.</p>
        
        <h3>1. Information We Collect</h3>
        <ul>
            <li><strong>Account Information:</strong> Name, email address, and encrypted passwords.</li>
            <li><strong>Order Data:</strong> Phone numbers, delivery addresses, and order history.</li>
            <li><strong>Payment Information:</strong> We do not store raw credit card details on our servers. All transactions are securely handled by certified payment processors.</li>
        </ul>
        
        <h3>2. How We Use Your Data</h3>
        <p>We use your information exclusively to provide our services—such as fulfilling orders, confirming reservations, and generating digital receipts. We will never sell, rent, or trade your personal information to third-party marketing companies.</p>
        
        <h3>3. Account Deletion</h3>
        <p>You have the right to request the deletion of your account and associated data at any time by contacting our support team at privacy@whisperingspoon.com.</p>
    </div>
</div>

<?php include "../includes/footer.php"; ?>