<?php
/**
 * Landing Page - CareerVault
 * Main entry point for the application
 */
require_once 'config/db.php';
require_once 'includes/session.php';

// Redirect if already logged in
if (is_logged_in()) {
    header("Location: dashboard/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to CareerVault</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/svg+xml" href="assets/icons/favicon.svg">
    <style>
        .hero-section {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            min-height: 70vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .hero-section p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
        }
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .hero-btn {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s;
        }
        .hero-btn:hover {
            transform: scale(1.05);
        }
        .features {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 2rem;
        }
        .features h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 2rem;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            text-align: center;
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .feature-card h3 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <h1>Welcome to CareerVault</h1>
        <p>Your Personal Career Diary & Portfolio System</p>
        <p style="font-size: 1rem; margin-bottom: 2rem;">Track internships, courses, conferences, and skills. Generate professional CVs instantly.</p>
        <div class="hero-buttons">
            <a href="auth/login.php" class="hero-btn btn-primary">Login</a>
            <a href="auth/signup.php" class="hero-btn btn-success">Sign Up</a>
        </div>
    </div>

    <div class="features">
    <h2>Key Features</h2>
    <div class="feature-grid">

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <h3>Activity Diary</h3>
            <p>Track all your internships, courses, conferences, and workshops in one place.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h3>Certificate Storage</h3>
            <p>Upload and manage all your certificates securely with easy access.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fa-solid fa-briefcase"></i>
            </div>
            <h3>Skills Management</h3>
            <p>Maintain a comprehensive list of your skills with proficiency levels.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fa-solid fa-file-lines"></i>
            </div>
            <h3>CV Generator</h3>
            <p>Generate professional CVs in PDF format using your stored data instantly.</p>
        </div>

    </div>
</div>


    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> CareerVault - Student Career Diary System</p>
    </footer>
</body>
</html>
