<?php
/**
 * Add Activity Page
 * Form to add new activities
 */
require_once '../config/db.php';
require_once '../includes/session.php';

require_login();
$user_id = get_user_id();
$page_title = 'Add Activity';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize_input($_POST['title']);
    $type = sanitize_input($_POST['type']);
    $organization = sanitize_input($_POST['organization']);
    $start_date = sanitize_input($_POST['start_date']);
    $end_date = !empty($_POST['end_date']) ? sanitize_input($_POST['end_date']) : null;
    $status = sanitize_input($_POST['status']);
    $description = sanitize_input($_POST['description']);
    
    // Validation
    if (empty($title) || empty($type) || empty($organization) || empty($start_date) || empty($status)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Insert activity
        $sql = "INSERT INTO activities (user_id, title, type, organization, start_date, end_date, status, description) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssss", $user_id, $title, $type, $organization, $start_date, $end_date, $status, $description);
        
        if ($stmt->execute()) {
            $success_message = "Activity added successfully!";
            // Clear form
            $_POST = array();
        } else {
            $error_message = "Failed to add activity. Please try again.";
        }
        $stmt->close();
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Add New Activity</h2>
            <a href="index.php" class="btn btn-secondary">← Back to Activities</a>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
                <a href="index.php">View all activities</a>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Activity Title *</label>
                <input type="text" id="title" name="title" class="form-control" 
                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" 
                       placeholder="e.g., Web Development Internship" required>
            </div>

            <div class="form-group">
                <label for="type">Activity Type *</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="">Select Type</option>
                    <option value="Internship" <?php echo (isset($_POST['type']) && $_POST['type'] == 'Internship') ? 'selected' : ''; ?>>Internship</option>
                    <option value="Course" <?php echo (isset($_POST['type']) && $_POST['type'] == 'Course') ? 'selected' : ''; ?>>Course</option>
                    <option value="Conference" <?php echo (isset($_POST['type']) && $_POST['type'] == 'Conference') ? 'selected' : ''; ?>>Conference</option>
                    <option value="Workshop" <?php echo (isset($_POST['type']) && $_POST['type'] == 'Workshop') ? 'selected' : ''; ?>>Workshop</option>
                </select>
            </div>

            <div class="form-group">
                <label for="organization">Organization/Institution *</label>
                <input type="text" id="organization" name="organization" class="form-control" 
                       value="<?php echo isset($_POST['organization']) ? htmlspecialchars($_POST['organization']) : ''; ?>" 
                       placeholder="e.g., ABC Tech Company" required>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date *</label>
                <input type="date" id="start_date" name="start_date" class="form-control" 
                       value="<?php echo isset($_POST['start_date']) ? $_POST['start_date'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date (leave empty if ongoing)</label>
                <input type="date" id="end_date" name="end_date" class="form-control" 
                       value="<?php echo isset($_POST['end_date']) ? $_POST['end_date'] : ''; ?>">
            </div>

            <div class="form-group">
                <label for="status">Status *</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="">Select Status</option>
                    <option value="Ongoing" <?php echo (isset($_POST['status']) && $_POST['status'] == 'Ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="Completed" <?php echo (isset($_POST['status']) && $_POST['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="5" 
                          placeholder="Describe your role, responsibilities, and key achievements..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-success">Save Activity</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>