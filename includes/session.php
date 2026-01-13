<?php
/**
 * Session Management File
 * Handles user authentication and session protection
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require login - redirect to login page if not authenticated
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: ../auth/login.php");
        exit();
    }
}

/**
 * Get current user ID
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user name
 */
function get_user_name() {
    return $_SESSION['user_name'] ?? 'User';
}

/**
 * Set user session after successful login
 */
function set_user_session($user_id, $user_name, $user_email) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $user_name;
    $_SESSION['user_email'] = $user_email;
    $_SESSION['last_activity'] = time();
}

/**
 * Destroy user session (logout)
 */
function destroy_user_session() {
    session_unset();
    session_destroy();
}

/**
 * Check session timeout (30 minutes of inactivity)
 */
function check_session_timeout() {
    $timeout_duration = 1800; // 30 minutes in seconds
    
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity']) > $timeout_duration) {
        destroy_user_session();
        header("Location: ../auth/login.php?timeout=1");
        exit();
    }
    
    $_SESSION['last_activity'] = time();
}
/**
 * Sanitize user input
 */
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Check timeout on every page load
if (is_logged_in()) {
    check_session_timeout();
}
?>