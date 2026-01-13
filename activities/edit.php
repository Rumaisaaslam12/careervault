<?php
/**
 * Edit Activity Page
 * Update existing activity
 */
require_once '../config/db.php';
require_once '../includes/session.php';

require_login();
$user_id = get_user_id();
$page_title = 'Edit Activity';

$error_message = '';
$success_message = '';

// Get activity ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$activity_id = intval($_GET['id']);

// Fetch activity data
$sql = "SELECT * FROM activities WHERE activity_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $activity_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$activity = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize_input($_POST['title']);
    $type = sanitize_input($_POST['type']);
    $organization = sanitize_input($_POST['organization']);
    $start_date = sanitize_input($_POST['start_date']);
    $end_date = !empty($_POST['end_date']) ? sanitize_input($_POST['end_date']) : null;
    $status = sanitize_input($_POST['status']);
    $description = sanitize_input($_POST['description']);
    
    if (empty($title) || empty($type) || empty($organization) || empty($start_date) || empty($status)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Update activity
        $sql = "UPDATE activities SET title = ?, type = ?, organization = ?, start_date = ?, 
                end_date = ?, status = ?, description = ? WHERE activity_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssii", $title, $type, $organization, $start_date, $end_date, $status, $description, $activity_id, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "Activity updated successfully!";
            // Refresh activity data
            $activity['title'] = $title;
            $activity['type'] = $type;
            $activity['organization'] = $organization;
            $activity['start_date'] = $start_date;
            $activity['end_date'] = $end_date;
            $activity['status'] = $status;
            $activity['description'] = $description;
        } else {
            $error_message = "Failed to update activity. Please try again.";
        }
        $stmt->close();
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Edit Activity</h2>
            <a href="index.php" class="btn btn-secondary">← Back to Activities</a>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Activity Title *</label>
                <input type="text" id="title" name="title" class="form-control" 
                       value="<?php echo htmlspecialchars($activity['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="type">Activity Type *</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="Internship" <?php echo $activity['type'] == 'Internship' ? 'selected' : ''; ?>>Internship</option>
                    <option value="Course" <?php echo $activity['type'] == 'Course' ? 'selected' : ''; ?>>Course</option>
                    <option value="Conference" <?php echo $activity['type'] == 'Conference' ? 'selected' : ''; ?>>Conference</option>
                    <option value="Workshop" <?php echo $activity['type'] == 'Workshop' ? 'selected' : ''; ?>>Workshop</option>
                </select>
            </div>

            <div class="form-group">
                <label for="organization">Organization/Institution *</label>
                <input type="text" id="organization" name="organization" class="form-control" 
                       value="<?php echo htmlspecialchars($activity['organization']); ?>" required>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date *</label>
                <input type="date" id="start_date" name="start_date" class="form-control" 
                       value="<?php echo $activity['start_date']; ?>" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" class="form-control" 
                       value="<?php echo $activity['end_date'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label for="status">Status *</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="Ongoing" <?php echo $activity['status'] == 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="Completed" <?php echo $activity['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="5"><?php echo htmlspecialchars($activity['description']); ?></textarea>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-success">Update Activity</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>