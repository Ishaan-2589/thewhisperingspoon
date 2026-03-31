<?php
session_start();
require_once "../includes/db.php";

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: /TheWhisperingSpoon/auth/login.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];

// Get user information
$userQuery = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $userQuery);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$userResult = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($userResult);
mysqli_stmt_close($stmt);

// Get user's orders
$ordersQuery = "SELECT o.*, COUNT(oi.id) as item_count
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = ?
                GROUP BY o.id
                ORDER BY o.created_at DESC";
$stmt = mysqli_prepare($conn, $ordersQuery);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$ordersResult = mysqli_stmt_get_result($stmt);
$orders = [];
while ($row = mysqli_fetch_assoc($ordersResult)) {
    $orders[] = $row;
}
mysqli_stmt_close($stmt);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (!empty($name) && !empty($email)) {
        // Check if email is already taken by another user
        $emailCheckQuery = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = mysqli_prepare($conn, $emailCheckQuery);
        mysqli_stmt_bind_param($stmt, "si", $email, $userId);
        mysqli_stmt_execute($stmt);
        $emailResult = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($emailResult) == 0) {
            // Update user profile
            $updateQuery = "UPDATE users SET name = ?, email = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $success = "Profile updated successfully!";
            $user['name'] = $name;
            $user['email'] = $email;
        } else {
            $error = "Email is already taken by another user.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Name and email are required.";
    }
}

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $rating = (int) $_POST['rating'];
    $feedback = trim($_POST['feedback']);

    if ($rating >= 1 && $rating <= 5) {
        // For now, we'll just store this in session or you can create a feedback table
        $_SESSION['user_feedback'] = [
            'rating' => $rating,
            'feedback' => $feedback,
            'submitted_at' => date('Y-m-d H:i:s')
        ];
        $feedback_success = "Thank you for your feedback!";
    }
}
?>

<?php include "../includes/header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - The Whispering Spoon</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .profile-header {
            background: linear-gradient(135deg, #8B4513, #654321);
            color: white;
            padding: 40px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: #8B4513;
            border: 4px solid rgba(255,255,255,0.3);
        }

        .profile-tabs {
            display: flex;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .tab-btn {
            flex: 1;
            padding: 15px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
        }

        .tab-btn.active {
            background: #8B4513;
            color: white;
        }

        .tab-btn:hover:not(.active) {
            background: #f8f8f8;
        }

        .tab-content {
            display: none;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 20px;
        }

        .tab-content.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #8B4513;
        }

        .btn {
            background: #8B4513;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            background: #654321;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .btn-success {
            background: #28a745;
        }

        .btn-success:hover {
            background: #218838;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .orders-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }

        .order-card {
            border: 1px solid #e1e1e1;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s;
        }

        .order-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .order-id {
            font-weight: bold;
            color: #8B4513;
        }

        .order-date {
            color: #666;
            font-size: 14px;
        }

        .order-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-preparing { background: #d1ecf1; color: #0c5460; }
        .status-ready { background: #d4edda; color: #155724; }
        .status-delivered { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .order-total {
            font-size: 18px;
            font-weight: bold;
            color: #8B4513;
            margin-bottom: 10px;
        }

        .order-items {
            color: #666;
            font-size: 14px;
        }

        .settings-section {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .setting-card {
            border: 1px solid #e1e1e1;
            border-radius: 12px;
            padding: 20px;
        }

        .setting-card h3 {
            margin-top: 0;
            color: #8B4513;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rating-stars {
            display: flex;
            gap: 5px;
            margin-bottom: 15px;
        }

        .star {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.3s;
        }

        .star.active {
            color: #ffc107;
        }

        .feedback-section {
            max-width: 600px;
            margin: 0 auto;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .action-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .action-card i {
            font-size: 32px;
            color: #8B4513;
            margin-bottom: 10px;
        }

        .action-card h3 {
            margin: 0 0 5px 0;
            color: #333;
        }

        .action-card p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .profile-tabs {
                flex-direction: column;
            }

            .orders-grid, .settings-section {
                grid-template-columns: 1fr;
            }

            .quick-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="fas fa-user"></i>
        </div>
        <h1>Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</h1>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <div class="action-card" onclick="switchTab('orders')">
            <i class="fas fa-shopping-bag"></i>
            <h3>My Orders</h3>
            <p>View your order history</p>
        </div>
        <div class="action-card" onclick="switchTab('settings')">
            <i class="fas fa-cog"></i>
            <h3>Settings</h3>
            <p>Manage your account</p>
        </div>
        <div class="action-card" onclick="switchTab('feedback')">
            <i class="fas fa-star"></i>
            <h3>Feedback</h3>
            <p>Rate your experience</p>
        </div>
        <div class="action-card" onclick="window.location.href='/TheWhisperingSpoon/user/cart.php'">
            <i class="fas fa-shopping-cart"></i>
            <h3>My Cart</h3>
            <p>Continue shopping</p>
        </div>
    </div>

    <!-- Profile Tabs -->
    <div class="profile-tabs">
        <button class="tab-btn active" onclick="switchTab('overview')">Overview</button>
        <button class="tab-btn" onclick="switchTab('orders')">My Orders</button>
        <button class="tab-btn" onclick="switchTab('settings')">Settings</button>
        <button class="tab-btn" onclick="switchTab('feedback')">Feedback</button>
    </div>

    <!-- Tab Contents -->

    <!-- Overview Tab -->
    <div id="overview" class="tab-content active">
        <h2 style="color: #8B4513; margin-bottom: 20px;">Account Overview</h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                <i class="fas fa-shopping-bag" style="font-size: 32px; color: #8B4513; margin-bottom: 10px;"></i>
                <h3 style="margin: 0 0 5px 0;"><?php echo count($orders); ?></h3>
                <p style="margin: 0; color: #666;">Total Orders</p>
            </div>
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                <i class="fas fa-calendar-alt" style="font-size: 32px; color: #8B4513; margin-bottom: 10px;"></i>
                <h3 style="margin: 0 0 5px 0;"><?php echo date('M Y', strtotime($user['created_at'])); ?></h3>
                <p style="margin: 0; color: #666;">Member Since</p>
            </div>
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                <i class="fas fa-star" style="font-size: 32px; color: #8B4513; margin-bottom: 10px;"></i>
                <h3 style="margin: 0 0 5px 0;"><?php echo isset($_SESSION['user_feedback']) ? $_SESSION['user_feedback']['rating'] : 'Not rated'; ?></h3>
                <p style="margin: 0; color: #666;">Your Rating</p>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h3 style="color: #8B4513; margin-top: 0;">Recent Activity</h3>
            <?php if (count($orders) > 0): ?>
                <p>Your last order was on <?php echo date('M d, Y', strtotime($orders[0]['created_at'])); ?> for ₹<?php echo number_format($orders[0]['total_amount'], 2); ?>.</p>
            <?php else: ?>
                <p>You haven't placed any orders yet. <a href="/TheWhisperingSpoon/public/menu.php" style="color: #8B4513;">Start ordering now!</a></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Orders Tab -->
    <div id="orders" class="tab-content">
        <h2 style="color: #8B4513; margin-bottom: 20px;">My Orders</h2>

        <?php if (count($orders) > 0): ?>
            <div class="orders-grid">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <span class="order-id">#<?php echo $order['id']; ?></span>
                            <span class="order-date"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="order-total">₹<?php echo number_format($order['total_amount'], 2); ?></div>
                        <div class="order-items"><?php echo $order['item_count']; ?> items</div>
                        <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                            <?php echo $order['status']; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <i class="fas fa-shopping-bag" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                <h3 style="color: #666; margin-bottom: 10px;">No orders yet</h3>
                <p style="color: #999; margin-bottom: 20px;">Start your culinary journey with us!</p>
                <a href="/TheWhisperingSpoon/public/menu.php" class="btn">Browse Menu</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Settings Tab -->
    <div id="settings" class="tab-content">
        <h2 style="color: #8B4513; margin-bottom: 20px;">Account Settings</h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="settings-section">
            <div class="setting-card">
                <h3><i class="fas fa-user"></i> Profile Information</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>

            <div class="setting-card">
                <h3><i class="fas fa-shield-alt"></i> Account Security</h3>
                <p style="color: #666; margin-bottom: 15px;">Manage your password and security settings.</p>
                <a href="/TheWhisperingSpoon/user/change-password.php" class="btn btn-secondary">
                    <i class="fas fa-key"></i> Change Password
                </a>
            </div>

            <div class="setting-card">
                <h3><i class="fas fa-bell"></i> Notifications</h3>
                <p style="color: #666; margin-bottom: 15px;">Manage your notification preferences.</p>
                <a href="/TheWhisperingSpoon/user/notification-settings.php" class="btn btn-secondary">
                    <i class="fas fa-cog"></i> Notification Settings
                </a>
            </div>

            <div class="setting-card">
                <h3><i class="fas fa-sign-out-alt"></i> Account Actions</h3>
                <p style="color: #666; margin-bottom: 15px;">Other account-related actions.</p>
                <a href="/TheWhisperingSpoon/auth/logout.php" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Feedback Tab -->
    <div id="feedback" class="tab-content">
        <div class="feedback-section">
            <h2 style="color: #8B4513; margin-bottom: 20px; text-align: center;">Share Your Feedback</h2>

            <?php if (isset($feedback_success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $feedback_success; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_feedback'])): ?>
                <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <h3 style="color: #8B4513; margin-top: 0;">Your Previous Feedback</h3>
                    <div class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?php echo $i <= $_SESSION['user_feedback']['rating'] ? 'active' : ''; ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <p style="color: #666; font-style: italic;">"<?php echo htmlspecialchars($_SESSION['user_feedback']['feedback']); ?>"</p>
                    <small style="color: #999;">Submitted on <?php echo date('M d, Y', strtotime($_SESSION['user_feedback']['submitted_at'])); ?></small>
                </div>
            <?php endif; ?>

            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="color: #8B4513; margin-top: 0; text-align: center;">Rate Your Experience</h3>

                <form method="POST">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 10px; font-weight: 600;">How would you rate us?</label>
                        <div class="rating-stars" id="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star" data-rating="<?php echo $i; ?>" onclick="setRating(<?php echo $i; ?>)">★</span>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="rating-input" required>
                    </div>

                    <div class="form-group">
                        <label for="feedback">Your Feedback</label>
                        <textarea id="feedback" name="feedback" rows="5" placeholder="Tell us about your experience..."></textarea>
                    </div>

                    <button type="submit" name="submit_feedback" class="btn btn-success" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Submit Feedback
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));

    // Remove active class from all buttons
    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => btn.classList.remove('active'));

    // Show selected tab
    document.getElementById(tabName).classList.add('active');

    // Add active class to clicked button
    event.target.classList.add('active');
}

function setRating(rating) {
    const stars = document.querySelectorAll('#rating-stars .star');
    const ratingInput = document.getElementById('rating-input');

    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });

    ratingInput.value = rating;
}

// Set default tab based on URL hash
window.addEventListener('load', function() {
    const hash = window.location.hash.substring(1);
    if (hash) {
        switchTab(hash);
    }
});
</script>

</body>
</html>

<?php include "../includes/footer.php"; ?>