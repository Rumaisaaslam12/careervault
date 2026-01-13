<?php
// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

// Environment variables for flexibility (local vs production)
$host = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') ?: "";
$database = getenv('DB_NAME') ?: "careervault_db";
$port = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;

try {
    // Establish connection
    $conn = mysqli_connect($host, $user, $password, $database, $port);
    
    // Set charset to utf8mb4
    mysqli_set_charset($conn, "utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Log error and show a user-friendly message (or debug message if needed)
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please check your configuration. " . $e->getMessage());
}
?>
