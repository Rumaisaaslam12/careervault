<?php
/**
 * Edit Skill Page
 * Update existing skill
 */
require_once '../config/db.php';
require_once '../includes/session.php';

require_login();
$user_id = get_user_id();
$page_title = 'Edit Skill';

$error_message = '';

// Get skill ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$skill_id = intval($_GET['id']);

// Fetch skill data
$sql = "SELECT * FROM skills WHERE skill_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $skill_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$skill = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $skill_name = sanitize_input($_POST['skill_name']);
    $skill_level = sanitize_input($_POST['skill_level']);
    
    if (empty($skill_name) || empty($skill_level)) {
        $error_message = "All fields are required.";
    } else {
        // Update skill
        $sql = "UPDATE skills SET skill_name = ?, skill_level = ? WHERE skill_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $skill_name, $skill_level, $skill_id, $user_id);
        
        if ($stmt->execute()) {
            header("Location: index.php?updated=1");
            exit();
        } else {
            $error_message = "Failed to update skill. Please try again.";
        }
        $stmt->close();
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Edit Skill</h2>
            <a href="index.php" class="btn btn-secondary">← Back to Skills</a>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="skill_name">Skill Name *</label>
                <input type="text" id="skill_name" name="skill_name" class="form-control" 
                       value="<?php echo htmlspecialchars($skill['skill_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="skill_level">Proficiency Level *</label>
                <select id="skill_level" name="skill_level" class="form-control" required>
                    <option value="Beginner" <?php echo $skill['skill_level'] == 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                    <option value="Intermediate" <?php echo $skill['skill_level'] == 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                    <option value="Advanced" <?php echo $skill['skill_level'] == 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-success">Update Skill</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>