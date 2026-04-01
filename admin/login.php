<?php
session_start();
require_once "../includes/db.php";

// If already logged in as admin, send them straight to dashboard
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Hardcoded Admin check (You can move this to a database later if you want)
    $adminEmails = ['admin@thewhisperingspoon.com', 'admin@example.com'];

    if (in_array($email, $adminEmails) && $password === 'admin123') {
        
        // SECURE ADMIN SESSION VARIABLES
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_email'] = $email;
        $_SESSION['admin_name'] = 'System Administrator';

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid admin credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - The Whispering Spoon</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #050505;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('../assets/images/others/login-bg.png');
            background-size: cover;
            background-position: center;
        }

        .login-container {
            background: #111;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.1);
            width: 100%;
            max-width: 400px;
            border: 1px solid #333;
        }

        .login-container h2 {
            text-align: center;
            color: gold;
            margin-bottom: 30px;
            font-family: 'Playfair Display', serif;
            font-size: 28px;
        }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #aaa; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;}
        .form-group input { width: 100%; padding: 12px; background: #000; border: 1px solid #444; color: #fff; border-radius: 4px; box-sizing: border-box;}
        .form-group input:focus { outline: none; border-color: gold; }

        .btn { width: 100%; padding: 14px; background: gold; color: #000; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 10px;}
        .btn:hover { background: #ffcc00; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255, 215, 0, 0.2);}

        .error { background: rgba(255, 68, 68, 0.1); border-left: 4px solid #ff4444; color: #ff4444; padding: 10px; margin-bottom: 20px; font-size: 14px;}
        .back-link { display: block; text-align: center; margin-top: 20px; color: #888; text-decoration: none; font-size: 14px;}
        .back-link:hover { color: gold; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Management Login</h2>

    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="email">Admin Email</label>
            <input type="email" id="email" name="email" required placeholder="admin@thewhisperingspoon.com">
        </div>

        <div class="form-group">
            <label for="password">Access Code</label>
            <input type="password" id="password" name="password" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn">Authorize Access</button>
    </form>
    
    <a href="../public/index.php" class="back-link">← Return to Public Site</a>
</div>

</body>
</html>