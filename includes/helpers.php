<?php
/**
 * Common helper functions
 */

/**
 * Format date for display
 * Example: 2024-01-15 → 15 Jan 2024
 */
function format_date($date) {
    if (!$date) {
        return '-';
    }

    return date('d M Y', strtotime($date));
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
