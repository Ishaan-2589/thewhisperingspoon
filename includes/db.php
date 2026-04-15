<?php
error_reporting(0);
ini_set('display_errors', 0);

$host = getenv("DB_HOST") ?: "localhost";
$user = getenv("DB_USER") ?: "root";
$password = getenv("DB_PASS") ?: "";
$dbname = getenv("DB_NAME") ?: "thewhisperingspoon";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("System Error: Could not connect to the database.");
}
?>