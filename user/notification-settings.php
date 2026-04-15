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

// Determine if notification columns exist
$columnCheckSql = "SHOW COLUMNS FROM users LIKE 'email_notifications'";
$columnCheck = mysqli_query($conn, $columnCheckSql);
$notificationColumnsExist = mysqli_num_rows($columnCheck) > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailNotifications = isset($_POST['email_notifications']) ? 1 : 0;
    $smsNotifications = isset($_POST['sms_notifications']) ? 1 : 0;
    $pushNotifications = isset($_POST['push_notifications']) ? 1 : 0;

    if ($notificationColumnsExist) {
        $sql = "UPDATE users SET email_notifications = ?, sms_notifications = ?, push_notifications = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'iiii', $emailNotifications, $smsNotifications, $pushNotifications, $userId);

        if (mysqli_stmt_execute($stmt)) {
            $message = 'Notification settings updated successfully.';
        } else {
            $error = 'Unable to save notification settings. Please try again.';
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['notification_settings'] = [
            'email_notifications' => $emailNotifications,
            'sms_notifications' => $smsNotifications,
            'push_notifications' => $pushNotifications,
        ];
        $message = 'Notification settings were saved locally. Add columns in DB for persistence.';
    }
}

// Load current settings
if ($notificationColumnsExist) {
    $userSql = "SELECT email_notifications, sms_notifications, push_notifications FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $userSql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $userSettings = mysqli_fetch_assoc($result) ?: [];
    mysqli_stmt_close($stmt);
} elseif (!empty($_SESSION['notification_settings'])) {
    $userSettings = $_SESSION['notification_settings'];
} else {
    $userSettings = [
        'email_notifications' => 0,
        'sms_notifications' => 0,
        'push_notifications' => 0,
    ];
}

include '/includes/header.php';
?>

<main style="padding: 40px; max-width: 650px; margin: auto;">
    <h2 style="color: #8B4513; margin-bottom: 20px;">Notification Settings</h2>
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label><input type="checkbox" name="email_notifications" <?php echo !empty($userSettings['email_notifications']) ? 'checked' : ''; ?>> Email Updates</label>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="sms_notifications" <?php echo !empty($userSettings['sms_notifications']) ? 'checked' : ''; ?>> SMS Alerts</label>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="push_notifications" <?php echo !empty($userSettings['push_notifications']) ? 'checked' : ''; ?>> Push Notifications</label>
        </div>

        <button type="submit" class="btn">Save Settings</button>
        <a href="/TheWhisperingSpoon/user/profile.php" class="btn btn-secondary" style="margin-left: 12px;">Back</a>
    </form>
</main>

<?php include '/includes/footer.php'; ?>