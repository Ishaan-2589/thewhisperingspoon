<?php
// Simple admin authentication
// In production, implement proper role-based authentication

function isAdmin() {
    // For now, check if user is logged in and has admin email
    // You can modify this logic as needed
    return isset($_SESSION['user_id']) &&
           isset($_SESSION['user_email']) &&
           in_array($_SESSION['user_email'], ['admin@thewhisperingspoon.com', 'admin@example.com']);
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: /TheWhisperingSpoon/auth/login.php");
        exit;
    }
}

// Check admin access
if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
    requireAdmin();
}
?>