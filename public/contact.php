<?php 
session_start();
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In a live environment, you would use PHPMailer here to send the email.
    // For now, we simulate a successful submission.
    $success = true;
}

include "../includes/header.php"; 
?>

<style>
.contact-hero { background: #050505; padding: 60px 20px; text-align: center; border-bottom: 1px solid #222; }
.contact-hero h1 { font-family: 'Playfair Display', serif; font-size: 48px; color: gold; margin-bottom: 10px; }
.contact-hero p { color: #888; font-size: 16px; }

.contact-wrapper { max-width: 1100px; margin: 60px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 50px; }

.contact-info { background: #0a0a0a; padding: 40px; border-radius: 16px; border: 1px solid #222; }
.contact-info h3 { color: #fff; font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 10px;}
.info-item { display: flex; align-items: flex-start; gap: 15px; margin-bottom: 25px; }
.info-item i { color: gold; font-size: 20px; margin-top: 4px; }
.info-text h4 { color: #ccc; margin: 0 0 5px 0; font-size: 16px; }
.info-text p { color: #666; margin: 0; font-size: 14px; line-height: 1.5; }

.contact-form { background: #111; padding: 40px; border-radius: 16px; border: 1px solid #222; }

/* --- FORM & SUBJECT DROPDOWN STYLES --- */
.form-group { margin-bottom: 20px; }
.form-group label { display: block; color: #aaa; margin-bottom: 8px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;}

/* Added .form-group select here to style the Subject dropdown */
.form-group input, .form-group textarea, .form-group select { 
    width: 100%; 
    padding: 14px; 
    background: #000; 
    border: 1px solid #333; 
    color: #fff; 
    border-radius: 6px; 
    font-family: 'Roboto', sans-serif; 
    box-sizing: border-box; 
    transition: 0.3s;
    appearance: none; /* Removes default browser styling on the arrow */
}

/* Custom arrow for the Subject dropdown to keep it luxurious */
.form-group select {
    background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23FFD700%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
    background-repeat: no-repeat;
    background-position: right 15px top 50%;
    background-size: 12px auto;
}

/* Dropdown option styles (Note: Most browsers limit how much you can style <option> tags, but setting the background helps) */
.form-group select option {
    background: #111;
    color: #fff;
    padding: 10px;
}

.form-group input:focus, .form-group textarea:focus, .form-group select:focus { 
    outline: none; 
    border-color: gold; 
}

.btn-submit { width: 100%; padding: 16px; background: gold; color: #000; border: none; border-radius: 30px; font-weight: bold; font-size: 16px; text-transform: uppercase; cursor: pointer; transition: 0.3s; margin-top: 10px;}
.btn-submit:hover { background: #ffcc00; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(255, 215, 0, 0.2); }

.success-msg { background: rgba(0,255,136,0.1); border: 1px solid #00ff88; color: #00ff88; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 20px; }

@media (max-width: 768px) { .contact-wrapper { grid-template-columns: 1fr; } }
</style>

<div class="contact-hero">
    <h1>Get in Touch</h1>
    <p>We are here to assist you with reservations, private events, and general inquiries.</p>
</div>

<div class="contact-wrapper">
    <div class="contact-info">
        <h3>Visit Us</h3>
        
        <div class="info-item">
            <i class="fas fa-map-marker-alt"></i>
            <div class="info-text">
                <h4>Address</h4>
                <p>123 Culinary Avenue<br>Vasant Kunj, New Delhi 110070<br>India</p>
            </div>
        </div>

        <div class="info-item">
            <i class="fas fa-phone-alt"></i>
            <div class="info-text">
                <h4>Phone</h4>
                <p>+91 98765 43210<br><small style="color: #555;">Lines open from 10:00 AM</small></p>
            </div>
        </div>

        <div class="info-item">
            <i class="far fa-envelope"></i>
            <div class="info-text">
                <h4>Email</h4>
                <p>reservations@whisperingspoon.com<br>events@whisperingspoon.com</p>
            </div>
        </div>

        <div class="info-item">
            <i class="far fa-clock"></i>
            <div class="info-text">
                <h4>Operating Hours</h4>
                <p>Monday - Thursday: 6:00 PM - 11:00 PM<br>Friday - Sunday: 5:00 PM - 12:00 AM</p>
            </div>
        </div>
    </div>

    <div class="contact-form">
        <?php if ($success): ?>
            <div class="success-msg">
                <i class="fas fa-paper-plane" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                <strong>Message Sent!</strong><br>Thank you for reaching out. Our concierge team will contact you shortly.
            </div>
        <?php else: ?>
            <h3 style="color: #fff; font-family: 'Playfair Display', serif; font-size: 24px; margin-top: 0; margin-bottom: 25px;">Send a Message</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" name="name" required placeholder="John Doe">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="john@example.com">
                </div>
                <div class="form-group">
                    <label>Subject</label>
                    <select name="subject" required>
                        <option value="reservation">Reservation Inquiry</option>
                        <option value="private_event">Private Dining / Events</option>
                        <option value="feedback">Feedback</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" rows="5" required placeholder="How can we help you?"></textarea>
                </div>
                <button type="submit" class="btn-submit">Submit Inquiry</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include "../includes/footer.php"; ?>