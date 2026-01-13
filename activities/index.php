<?php
/**
 * Activities List Page
 * View all activities with filtering options
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';


require_login();
$user_id = get_user_id();
$page_title = 'My Activities';

// Get filter parameters
$filter_type = isset($_GET['type']) ? sanitize_input($_GET['type']) : '';
$filter_status = isset($_GET['status']) ? sanitize_input($_GET['status']) : '';

// Build query with filters
$sql = "SELECT * FROM activities WHERE user_id = ?";
$params = [$user_id];
$types = "i";

if (!empty($filter_type)) {
    $sql .= " AND type = ?";
    $params[] = $filter_type;
    $types .= "s";
}

if (!empty($filter_status)) {
    $sql .= " AND status = ?";
    $params[] = $filter_status;
    $types .= "s";
}

$sql .= " ORDER BY start_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$activities = $stmt->get_result();

include '../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">My Activities</h2>
            <a href="add.php" class="btn btn-primary">+ Add New Activity</a>
        </div>

        <!-- Filters -->
        <form method="GET" style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <label for="type">Filter by Type:</label>
                <select name="type" id="type" class="form-control" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="Internship" <?php echo $filter_type == 'Internship' ? 'selected' : ''; ?>>Internship</option>
                    <option value="Course" <?php echo $filter_type == 'Course' ? 'selected' : ''; ?>>Course</option>
                    <option value="Conference" <?php echo $filter_type == 'Conference' ? 'selected' : ''; ?>>Conference</option>
                    <option value="Workshop" <?php echo $filter_type == 'Workshop' ? 'selected' : ''; ?>>Workshop</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label for="status">Filter by Status:</label>
                <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="Ongoing" <?php echo $filter_status == 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="Completed" <?php echo $filter_status == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
            <?php if ($filter_type || $filter_status): ?>
                <div style="align-self: flex-end;">
                    <a href="index.php" class="btn btn-secondary">Clear Filters</a>
                </div>
            <?php endif; ?>
        </form>

        <?php if ($activities->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Organization</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($activity = $activities->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($activity['title']); ?></strong></td>
                                <td><span class="badge badge-info"><?php echo $activity['type']; ?></span></td>
                                <td><?php echo htmlspecialchars($activity['organization']); ?></td>
                                <td><?php echo format_date($activity['start_date']); ?></td>
                                <td><?php echo $activity['end_date'] ? format_date($activity['end_date']) : 'Present'; ?></td>
                                <td>
                                    <span class="badge <?php echo $activity['status'] == 'Completed' ? 'badge-success' : 'badge-warning'; ?>">
                                        <?php echo $activity['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit.php?id=<?php echo $activity['activity_id']; ?>" class="btn btn-secondary btn-small">Edit</a>
                                        <a href="delete.php?id=<?php echo $activity['activity_id']; ?>" class="btn btn-danger btn-small" 
                                           onclick="return confirm('Are you sure you want to delete this activity?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            <?php if (!empty($activity['description'])): ?>
                                <tr>
                                    <td colspan="7" style="background-color: #f8f9fa; padding: 0.5rem 1rem;">
                                        <small><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($activity['description'])); ?></small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No activities found. Start adding your career activities!</p>
                <a href="add.php" class="btn btn-primary mt-1">Add Your First Activity</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>