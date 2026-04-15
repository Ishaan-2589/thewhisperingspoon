<?php
session_start();
require_once "/includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header('Location: /TheWhisperingSpoon/auth/login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'All fields are required.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New password and confirmation do not match.';
    } elseif (strlen($newPassword) < 8) {
        $error = 'New password must be at least 8 characters long.';
    } else {
        // Fetch current password hash from DB
        $sql = 'SELECT password FROM users WHERE id = ?';
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($row && password_verify($currentPassword, $row['password'])) {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = 'UPDATE users SET password = ? WHERE id = ?';
            $stmt = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($stmt, 'si', $newHash, $userId);
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Password updated successfully.';
            } else {
                $error = 'Unable to update password. Please try again later.';
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = 'Current password is incorrect.';
        }
    }
}

include '/includes/header.php';
?>

<main style="padding: 40px; max-width: 650px; margin: auto;">
    <h2 style="color: #8B4513; margin-bottom: 20px;">Change Password</h2>
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <button type="submit" class="btn">Update Password</button>
        <a href="/TheWhisperingSpoon/user/profile.php" class="btn btn-secondary" style="margin-left: 12px;">Back</a>
    </form>
</main>

<?php include '/includes/footer.php'; ?>