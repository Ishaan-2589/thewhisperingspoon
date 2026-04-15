<?php
require_once "/includes/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm  = $_POST["confirm_password"];

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error = "All fields are required.";
    }
    elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    }
    else {
        // Check if email already exists
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "Email already registered.";
        } else {

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPassword);

            if (mysqli_stmt_execute($stmt)) {

              session_start();
              $_SESSION["user_id"] = mysqli_insert_id($conn);
              $_SESSION["user_name"] = $name;

              header("Location: /TheWhisperingSpoon/public/index.php");
              exit;
          }
           else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | The Whispering Spoon</title>

  <link rel="stylesheet" href="/assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Allura&family=Parisienne&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

<header>
  <h1 class="logo-font">The Whispering Spoon</h1>
</header>

<section class="login-section">
  <div class="login-box">

    <h2 class="heading-font">Create Account</h2>
    <p class="login-tagline">Join us for a fine dining experience</p>

    <?php if ($error): ?>
      <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">

      <div class="login-group">
        <label>Full Name</label>
        <input type="text" name="name" required>
      </div>

      <div class="login-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>

      <div class="login-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <div class="login-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>
      </div>

      <button type="submit" class="login-btn">Register</button>
    </form>

    <p class="login-footer-text">
      Already have an account?
      <a href="login.php">Login here</a>
    </p>

  </div>
</section>

<footer>
  <p>© 2025 The Whispering Spoon. All rights reserved.</p>
</footer>

</body>
</html>
