<?php
/**
 * Certificate Download Handler
 * Serves certificate files securely
 */
require_once '../config/db.php';
require_once '../includes/session.php';

require_login();
$user_id = get_user_id();

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$certificate_id = intval($_GET['id']);

// Fetch certificate
$sql = "SELECT * FROM certificates WHERE certificate_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $certificate_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Certificate not found or access denied.");
}

$certificate = $result->fetch_assoc();
$file_path = $certificate['file_path'];

// Check if file exists
if (!file_exists($file_path)) {
    die("Certificate file not found on server.");
}

// Set headers for file download
header('Content-Type: ' . $certificate['file_type']);
header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
header('Content-Length: ' . filesize($file_path));

// Output file
readfile($file_path);
exit();
?>