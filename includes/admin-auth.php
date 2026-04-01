<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// STRICT CHECK: Does the 'is_admin' flag exist and is it true?
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // They are not an admin. Kick them to the admin login page.
    header("Location: ../admin/login.php");
    exit;
}
?>