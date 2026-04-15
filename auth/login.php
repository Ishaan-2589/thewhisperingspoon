<?php
session_start();
require_once "/includes/db.php";

// If user already logged in, redirect
if (isset($_SESSION["user_id"])) {
    header("Location: /TheWhisperingSpoon/public/index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, name, password FROM users WHERE email = ?"
        );
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {

            if (password_verify($password, $user["password"])) {

                // Login success
                $_SESSION["user_id"]   = $user["id"];
                $_SESSION["user_name"] = $user["name"];

                header("Location: /TheWhisperingSpoon/public/index.php");
                exit;

            } else {
                $error = "Invalid email or password.";
            }

        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | The Whispering Spoon</title>

  <link rel="stylesheet" href="/TheWhisperingSpoon/assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Allura&family=Parisienne&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<header>
  <h1 class="logo-font">The Whispering Spoon</h1>
</header>

<section class="login-section">
  <div class="login-box">

    <h2 class="heading-font">Member Login</h2>
    <p class="login-tagline">Welcome back to fine dining</p>

    

    <?php if ($error): ?>
      <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">

      <div class="login-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>

      <div class="login-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <button type="submit" class="login-btn">Login</button>
    </form>

    <p class="login-footer-text">
      New here?
      <a href="register.php">Create an account</a>
    </p>

  </div>
</section>

<footer>
  <p>© 2025 The Whispering Spoon. All rights reserved.</p>
</footer>

</body>
</html>
