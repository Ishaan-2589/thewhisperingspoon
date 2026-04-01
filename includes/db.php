<?php
// Turn off error reporting for Production (Security Hardening)
error_reporting(0);
ini_set('display_errors', 0);

$host = "localhost";
$user = "root";
$password = "";
$dbname = "thewhisperingspoon";

$conn = mysqli_connect($host, $user, $password, $dbname);

// We also change the die() message so it doesn't show hackers exactly why it failed
if (!$conn) {
    die("System Error: Could not connect to the database.");
}
?>