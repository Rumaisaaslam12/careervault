<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug Info</h1>";

echo "<h2>PHP Version</h2>";
echo phpversion();

echo "<h2>Environment Variables</h2>";
echo "<pre>";
print_r([
    'DB_HOST' => getenv('DB_HOST'),
    'DB_USER' => getenv('DB_USER'),
    'DB_NAME' => getenv('DB_NAME'),
    'DB_PORT' => getenv('DB_PORT'),
    // Don't print password for security, just check length
    'DB_PASS_LEN' => strlen(getenv('DB_PASS') ?: '')
]);
echo "</pre>";

echo "<h2>Database Connection Test</h2>";
require_once 'config/db.php';

if (isset($conn) && $conn instanceof mysqli) {
    echo "<p style='color:green'><strong>Success!</strong> Connected to database.</p>";
    echo "Host info: " . $conn->host_info;
} else {
    echo "<p style='color:red'><strong>Failed!</strong> Connection variable not set.</p>";
}

echo "<h2>MySQL Extension Check</h2>";
if (extension_loaded('mysqli')) {
    echo "<p style='color:green'>mysqli extension is loaded.</p>";
} else {
    echo "<p style='color:red'>mysqli extension is NOT loaded.</p>";
}
?>
