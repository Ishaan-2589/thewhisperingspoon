<?php
session_start();
require_once "../includes/db.php";

// Ensure user has a session
if (!isset($_SESSION["user_id"])) {
    header("Location: /auth/login.php");
    exit;
}

$userId = $_SESSION["user_id"];

// Fetch user data
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// CRITICAL FIX: If the user was deleted from the DB but the session remained, force logout.
if (!$user) {
    session_unset();
    session_destroy();
    header("Location: /auth/login.php");
    exit;
}

$initials = "U";
if (!empty($user['name'])) {
    $words = explode(" ", trim($user['name']));
    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ""));
}

include "../includes/header.php"; 
?>

<style>
.profile-wrapper {
    max-width: 480px;
    margin: 40px auto 80px;
    padding: 0 20px;
    font-family: 'Roboto', sans-serif;
}

.profile-header-app {
    text-align: center;
    margin-bottom: 40px;
}

.avatar-circle {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, gold, #d4af37);
    color: #000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 42px;
    font-family: 'Playfair Display', serif;
    font-weight: bold;
    margin: 0 auto 15px;
    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
    border: 4px solid #1a1a1a;
}

.profile-name {
    color: #fff;
    font-size: 26px;
    font-family: 'Playfair Display', serif;
    margin-bottom: 5px;
}

.profile-email {
    color: #888;
    font-size: 14px;
    letter-spacing: 0.5px;
}

.profile-menu {
    background: #111;
    border-radius: 16px;
    border: 1px solid #222;
    overflow: hidden;
    margin-bottom: 30px;
}

.profile-menu-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    color: #ddd;
    text-decoration: none;
    border-bottom: 1px solid #222;
    transition: background 0.3s ease, color 0.3s ease;
}

.profile-menu-item:last-child {
    border-bottom: none;
}

.profile-menu-item:hover {
    background: #1a1a1a;
    color: gold;
}

.menu-left {
    display: flex;
    align-items: center;
    font-size: 16px;
}

.menu-icon {
    width: 30px;
    color: gold;
    font-size: 18px;
}

.menu-arrow {
    color: #555;
    font-size: 14px;
}

.logout-btn-container {
    margin-top: 20px;
}

.btn-logout {
    display: block;
    width: 100%;
    text-align: center;
    padding: 16px;
    border-radius: 16px;
    background: rgba(255, 68, 68, 0.05);
    color: #ff4444;
    text-decoration: none;
    font-weight: bold;
    font-size: 16px;
    border: 1px solid rgba(255, 68, 68, 0.2);
    transition: all 0.3s ease;
}

.btn-logout:hover {
    background: #ff4444;
    color: #fff;
    box-shadow: 0 8px 20px rgba(255, 68, 68, 0.3);
}

</style>

<div class="profile-wrapper">
    
    <div class="profile-header-app">
        <div class="avatar-circle">
            <?php echo $initials; ?>
        </div>
        <h2 class="profile-name"><?php echo htmlspecialchars($user['name'] ?? 'Guest'); ?></h2>
        <p class="profile-email"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
        <p style="color: #555; font-size: 12px; margin-top: 5px;">Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
    </div>

    <div class="profile-menu">
        <a href="my-orders.php" class="profile-menu-item">
            <div class="menu-left">
                <i class="fas fa-receipt menu-icon"></i>
                <span>Order History</span>
            </div>
            <i class="fas fa-chevron-right menu-arrow"></i>
        </a>
        
        <a href="my-bookings.php" class="profile-menu-item">
            <div class="menu-left">
                <i class="fas fa-calendar-check menu-icon"></i>
                <span>Table Reservations</span>
            </div>
            <i class="fas fa-chevron-right menu-arrow"></i>
        </a>
        
        <a href="edit-profile.php" class="profile-menu-item">
            <div class="menu-left">
                <i class="fas fa-user-cog menu-icon"></i>
                <span>Account Settings & Addresses</span>
            </div>
            <i class="fas fa-chevron-right menu-arrow"></i>
        </a>
    </div>

    <div class="logout-btn-container">
        <a href="/auth/logout.php" class="btn-logout">
            Log Out
        </a>
    </div>

</div>

<?php include "../includes/footer.php"; ?>