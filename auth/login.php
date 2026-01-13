<?php
/**
 * User Login Page
 * Authenticates users and establishes session
 */
require_once '../config/db.php';
require_once '../includes/session.php';

// Redirect if already logged in
if (is_logged_in()) {
    header("Location: ../dashboard/index.php");
    exit();
}

$error_message = '';
$timeout_message = '';

// Check for timeout parameter
if (isset($_GET['timeout'])) {
    $timeout_message = "Your session has expired. Please login again.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error_message = "Email and password are required.";
    } else {
        // Query user from database
        $sql = "SELECT user_id, full_name, email, password_hash FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                // Set session
                set_user_session($user['user_id'], $user['full_name'], $user['email']);
                
                // Redirect to dashboard
                header("Location: ../dashboard/index.php");
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Invalid email or password.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CareerVault</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>📚 CareerVault</h1>
                <p>Login to your account</p>
            </div>

            <?php if ($timeout_message): ?>
                <div class="alert alert-warning"><?php echo $timeout_message; ?></div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>

            <div class="auth-links">
                <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
                <p><a href="../index.php">← Back to Home</a></p>
            </div>
        </div>
    </div>
</body>
</html>