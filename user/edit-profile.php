<?php
session_start();
require_once "/includes/db.php";

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: /auth/login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$success = $error = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Update Basic Info & Address
    $updateQuery = "UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "sssi", $name, $phone, $address, $userId);
    
    if (mysqli_stmt_execute($stmt)) {
        $success = "Profile details updated successfully.";
    } else {
        $error = "Something went wrong updating your details.";
    }
    mysqli_stmt_close($stmt);

    // Handle Password Change (Only if the user typed something into the new password field)
    if (!empty($_POST['new_password'])) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $passQuery = "UPDATE users SET password = ? WHERE id = ?";
            $passStmt = mysqli_prepare($conn, $passQuery);
            mysqli_stmt_bind_param($passStmt, "si", $hashedPassword, $userId);
            mysqli_stmt_execute($passStmt);
            mysqli_stmt_close($passStmt);
            $success = "Profile and password updated successfully.";
        } else {
            $error = "Your new passwords did not match. Profile details were saved, but password was not changed.";
        }
    }
}

// Fetch current user data to pre-fill the form
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

include "/includes/header.php"; 
?>

<style>
.edit-wrapper { max-width: 600px; margin: 40px auto 80px; padding: 0 20px; font-family: 'Roboto', sans-serif; }
.edit-header { display: flex; align-items: center; gap: 15px; margin-bottom: 30px; border-bottom: 1px solid #222; padding-bottom: 20px;}
.edit-header h2 { color: gold; font-family: 'Playfair Display', serif; font-size: 28px; margin: 0; }
.btn-back { color: #888; text-decoration: none; font-size: 20px; transition: 0.3s; }
.btn-back:hover { color: gold; }

.edit-card { background: #111; border-radius: 16px; border: 1px solid #222; padding: 30px; margin-bottom: 30px;}
.edit-card h3 { color: #fff; margin-top: 0; margin-bottom: 20px; font-size: 18px; border-bottom: 1px dashed #333; padding-bottom: 10px;}

.form-group { margin-bottom: 20px; }
.form-group label { display: block; color: #aaa; margin-bottom: 8px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;}
.form-group input, .form-group textarea { width: 100%; padding: 14px; background: #000; border: 1px solid #333; color: #fff; border-radius: 6px; font-family: 'Roboto', sans-serif; transition: border-color 0.3s; box-sizing: border-box;}
.form-group input:focus, .form-group textarea:focus { outline: none; border-color: gold; }
.form-group input:disabled { background: #1a1a1a; color: #666; cursor: not-allowed; border-color: #222;}

.btn-save { display: block; width: 100%; padding: 16px; background: gold; color: #000; border: none; border-radius: 30px; font-size: 16px; font-weight: bold; cursor: pointer; transition: all 0.3s; text-transform: uppercase; letter-spacing: 1px; margin-top: 10px;}
.btn-save:hover { background: #ffcc00; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(255, 215, 0, 0.2); }

.alert { padding: 15px; border-radius: 8px; margin-bottom: 25px; font-weight: bold; text-align: center; }
.alert-error { background: rgba(255, 68, 68, 0.1); color: #ff4444; border: 1px solid #ff4444; }
.alert-success { background: rgba(0, 255, 136, 0.1); color: #00ff88; border: 1px solid #00ff88; }
</style>

<div class="edit-wrapper">
    <div class="edit-header">
        <a href="profile.php" class="btn-back"><i class="fas fa-arrow-left"></i></a>
        <h2>Account Settings</h2>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="edit-card">
            <h3>Personal Information</h3>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                <small style="color: #666; font-size: 11px; margin-top: 4px; display: block;">Contact support to change your email address.</small>
            </div>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+91 9876543210">
            </div>
        </div>

        <div class="edit-card">
            <h3>Delivery Preferences</h3>
            <div class="form-group">
                <label>Default Delivery Address</label>
                <textarea name="address" rows="3" placeholder="Enter your full street address for faster checkout..."><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="edit-card">
            <h3>Security</h3>
            <p style="color: #888; font-size: 13px; margin-bottom: 20px;">Leave these fields blank if you do not wish to change your password.</p>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="••••••••">
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="••••••••">
            </div>
        </div>

        <button type="submit" class="btn-save">Save Changes</button>
    </form>
</div>

<?php include "/includes/footer.php"; ?>