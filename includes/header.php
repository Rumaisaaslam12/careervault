<?php
/**
 * Header Template
 * Included in all pages for consistent navigation
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - CareerVault' : 'CareerVault - Student Career Diary'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" type="image/svg+xml" href="../assets/icons/favicon.svg">
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="<?php echo is_logged_in() ? '../dashboard/index.php' : '../index.php'; ?>" style="text-decoration:none;color:inherit;">
                    <h1>📚 CareerVault</h1>
                </a>
            </div>
            <?php if (is_logged_in()): ?>
            <nav class="nav-menu">
                <ul>
                    <li><a href="../dashboard/index.php">Dashboard</a></li>
                    <li><a href="../activities/index.php">Activities</a></li>
                    <li><a href="../certificates/index.php">Certificates</a></li>
                    <li><a href="../skills/index.php">Skills</a></li>
                    <li><a href="../cv/generate.php">Generate CV</a></li>
                    <li><a href="../auth/logout.php">Logout (<?php echo get_user_name(); ?>)</a></li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </header>
