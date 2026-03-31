<?php
session_start();
require_once "../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Simple admin check - in production, use proper authentication
    $adminEmails = ['admin@thewhisperingspoon.com', 'admin@example.com'];

    if (in_array($email, $adminEmails) && $password === 'admin123') {
        // For demo purposes - in production, check against database
        $_SESSION['user_id'] = 999; // Admin user ID
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = 'Admin';

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid admin credentials";
    }
}
?>

<?php include "../includes/header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - The Whispering Spoon</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .login-container h2 {
            text-align: center;
            color: #8B4513;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #8B4513;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background: #654321;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        .note {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>

    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="email">Admin Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">Login</button>
    </form>

    <div class="note">
        Demo credentials: admin@thewhisperingspoon.com / admin123
    </div>
</div>

</body>
</html>

<?php include "../includes/footer.php"; ?>