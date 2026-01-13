<?php
/**
 * Delete Activity Handler
 * Removes activity from database
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

$activity_id = intval($_GET['id']);

// Delete activity (certificates will be set to NULL due to foreign key constraint)
$sql = "DELETE FROM activities WHERE activity_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $activity_id, $user_id);

if ($stmt->execute()) {
    header("Location: index.php?deleted=1");
} else {
    header("Location: index.php?error=1");
}

exit();
?>