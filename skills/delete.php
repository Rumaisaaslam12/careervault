<?php
/**
 * Delete Skill Handler
 * Removes skill from database
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

$skill_id = intval($_GET['id']);

// Delete skill
$sql = "DELETE FROM skills WHERE skill_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $skill_id, $user_id);

if ($stmt->execute()) {
    header("Location: index.php?deleted=1");
} else {
    header("Location: index.php?error=1");
}

exit();
?>