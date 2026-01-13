<?php
/**
 * Logout Handler
 * Destroys user session and redirects to login
 */
require_once '../includes/session.php';

// Destroy session
destroy_user_session();

// Redirect to login page
header("Location: login.php");
exit();
?>