<?php
/**
 * Add Skill Page
 * Form to add new skills
 */
require_once '../config/db.php';
require_once '../includes/session.php';

require_login();
$user_id = get_user_id();
$page_title = 'Add Skill';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $skill_name = sanitize_input($_POST['skill_name']);
    $skill_level = sanitize_input($_POST['skill_level']);
    
    if (empty($skill_name) || empty($skill_level)) {
        $error_message = "All fields are required.";
    } else {
        // Check if skill already exists
        $check_sql = "SELECT skill_id FROM skills WHERE user_id = ? AND skill_name = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("is", $user_id, $skill_name);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error_message = "This skill already exists in your profile.";
        } else {
            // Insert skill
            $sql = "INSERT INTO skills (user_id, skill_name, skill_level) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $user_id, $skill_name, $skill_level);
            
            if ($stmt->execute()) {
                header("Location: index.php?added=1");
                exit();
            } else {
                $error_message = "Failed to add skill. Please try again.";
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Add New Skill</h2>
            <a href="index.php" class="btn btn-secondary">← Back to Skills</a>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="skill_name">Skill Name *</label>
                <input type="text" id="skill_name" name="skill_name" class="form-control" 
                       value="<?php echo isset($_POST['skill_name']) ? htmlspecialchars($_POST['skill_name']) : ''; ?>" 
                       placeholder="e.g., JavaScript, Python, Project Management" required>
            </div>

            <div class="form-group">
                <label for="skill_level">Proficiency Level *</label>
                <select id="skill_level" name="skill_level" class="form-control" required>
                    <option value="">Select Level</option>
                    <option value="Beginner" <?php echo (isset($_POST['skill_level']) && $_POST['skill_level'] == 'Beginner') ? 'selected' : ''; ?>>Beginner</option>
                    <option value="Intermediate" <?php echo (isset($_POST['skill_level']) && $_POST['skill_level'] == 'Intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                    <option value="Advanced" <?php echo (isset($_POST['skill_level']) && $_POST['skill_level'] == 'Advanced') ? 'selected' : ''; ?>>Advanced</option>
                </select>
                <small class="text-muted">
                    <strong>Beginner:</strong> Basic understanding<br>
                    <strong>Intermediate:</strong> Practical experience<br>
                    <strong>Advanced:</strong> Expert level proficiency
                </small>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-success">Add Skill</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>