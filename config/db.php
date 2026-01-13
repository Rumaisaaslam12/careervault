<?php
session_start();

$host = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') ?: "";
$database = getenv('DB_NAME') ?: "careervault_db";
$port = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;

if (!function_exists('mysqli_connect')) {
    die("Missing mysqli extension. Ensure PHP MySQLi is installed/enabled.");
}

$conn = mysqli_connect($host, $user, $password, $database, $port);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
?>
