<?php
/**
 * Delete Certificate Handler
 * Removes certificate file and database record
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

if ($result->num_rows > 0) {
    $certificate = $result->fetch_assoc();
    
    // Delete file from server
    if (file_exists($certificate['file_path'])) {
        unlink($certificate['file_path']);
    }
    
    // Delete from database
    $delete_sql = "DELETE FROM certificates WHERE certificate_id = ? AND user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $certificate_id, $user_id);
    $delete_stmt->execute();
    
    header("Location: index.php?deleted=1");
} else {
    header("Location: index.php?error=1");
}

exit();
?>