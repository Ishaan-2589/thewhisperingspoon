<?php
session_start();
require_once "../includes/db.php";

// 1. Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];

// Fetch user details to pre-fill the form
$userQuery = "SELECT name, email FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $userQuery);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$userResult = mysqli_stmt_get_result($stmt);
$userData = mysqli_fetch_assoc($userResult);
mysqli_stmt_close($stmt);

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guests = (int) $_POST['guests'];
    $message = trim($_POST['message']);

    // Basic Server-Side Validation
    if (empty($name) || empty($email) || empty($phone) || empty($date) || empty($time) || $guests < 1) {
        $error = "Please fill in all required fields.";
    } elseif ($date < date('Y-m-d')) {
        $error = "You cannot book a table in the past.";
    } else {
        // Insert Booking
        $insertQuery = "INSERT INTO bookings (user_id, name, email, phone, date, time, guests, message, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())";
        $stmtInsert = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmtInsert, "isssssis", $userId, $name, $email, $phone, $date, $time, $guests, $message);
        
        if (mysqli_stmt_execute($stmtInsert)) {
            $success = "Your reservation has been received! We will confirm it shortly.";
        } else {
            $error = "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmtInsert);
    }
}

include "../includes/header.php"; 
?>

<style>
/* Luxury Reservation UI */
.booking-hero { text-align: center; padding: 40px 20px; border-bottom: 1px solid #333; margin-bottom: 40px; background: #050505;}
.booking-hero h1 { font-size: 48px; color: gold; font-family: 'Playfair Display', serif; margin-bottom: 10px; }

.booking-wrapper { max-width: 600px; margin: 0 auto; padding: 0 20px 60px; }

.booking-form { background: #111; padding: 40px; border-radius: 12px; border: 1px solid #222; box-shadow: 0 15px 35px rgba(255, 215, 0, 0.05); position: relative; overflow: hidden;}

/* Gold top border accent */
.booking-form::before {
    content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px;
    background: linear-gradient(90deg, transparent, gold, transparent);
}

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

.form-group { margin-bottom: 20px; }
.form-group label { display: block; color: #aaa; margin-bottom: 8px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;}
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 14px; background: #000; border: 1px solid #333; color: #fff; border-radius: 6px; font-family: 'Roboto', sans-serif; transition: border-color 0.3s; box-sizing: border-box;}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: gold; }

/* Customizing Date/Time Picker Icons for Dark Mode */
::-webkit-calendar-picker-indicator { filter: invert(1) sepia(100%) saturate(500%) hue-rotate(1deg) brightness(1.5); cursor: pointer; }

.btn-book { display: block; width: 100%; padding: 16px; background: gold; color: #000; border: none; border-radius: 30px; font-size: 16px; font-weight: bold; cursor: pointer; transition: all 0.3s; margin-top: 20px; text-transform: uppercase; letter-spacing: 2px;}
.btn-book:hover { background: #ffcc00; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(255, 215, 0, 0.2); }

.alert { padding: 15px; border-radius: 8px; margin-bottom: 25px; text-align: center; font-weight: bold; }
.alert-error { background: rgba(255, 68, 68, 0.1); color: #ff4444; border: 1px solid #ff4444; }
.alert-success { background: rgba(0, 255, 136, 0.1); color: #00ff88; border: 1px solid #00ff88; }
</style>

<div class="booking-hero">
    <h1>Reserve Your Table</h1>
    <p style="color: #888;">Secure your spot for an unforgettable dining experience.</p>
</div>

<div class="booking-wrapper">
    <div class="booking-form">
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle" style="margin-right: 8px;"></i> <?php echo $success; ?><br>
                <a href="profile.php" style="color: #00ff88; display: block; margin-top: 10px; font-size: 14px;">View in Profile</a>
            </div>
        <?php else: ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" placeholder="+91 98765 43210" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Time</label>
                    <select name="time" required>
                        <option value="" disabled selected>Select Time</option>
                        <option value="18:00">06:00 PM</option>
                        <option value="18:30">06:30 PM</option>
                        <option value="19:00">07:00 PM</option>
                        <option value="19:30">07:30 PM</option>
                        <option value="20:00">08:00 PM</option>
                        <option value="20:30">08:30 PM</option>
                        <option value="21:00">09:00 PM</option>
                        <option value="21:30">09:30 PM</option>
                        <option value="22:00">10:00 PM</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Number of Guests</label>
                <select name="guests" required>
                    <option value="1">1 Person</option>
                    <option value="2" selected>2 People</option>
                    <option value="3">3 People</option>
                    <option value="4">4 People</option>
                    <option value="5">5 People</option>
                    <option value="6">6 People</option>
                    <option value="7">7+ People (Large Group)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Special Requests (Optional)</label>
                <textarea name="message" rows="3" placeholder="Anniversary, dietary restrictions, preferred seating..."></textarea>
            </div>

            <button type="submit" class="btn-book">Confirm Reservation</button>
        </form>
        
        <?php endif; ?>
    </div>
</div>

<?php include "../includes/footer.php"; ?>