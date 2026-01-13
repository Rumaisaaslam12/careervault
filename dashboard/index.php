<?php
/**
 * Dashboard Page
 * Displays overview statistics and recent activities
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
// Require authentication
require_login();

$user_id = get_user_id();
$page_title = 'Dashboard';

// Get statistics
$stats = [];

// Total activities
$sql = "SELECT COUNT(*) as total FROM activities WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['total_activities'] = $result->fetch_assoc()['total'];

// Ongoing internships
$sql = "SELECT COUNT(*) as total FROM activities WHERE user_id = ? AND type = 'Internship' AND status = 'Ongoing'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['ongoing_internships'] = $result->fetch_assoc()['total'];

// Completed courses
$sql = "SELECT COUNT(*) as total FROM activities WHERE user_id = ? AND type = 'Course' AND status = 'Completed'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['completed_courses'] = $result->fetch_assoc()['total'];

// Total certificates
$sql = "SELECT COUNT(*) as total FROM certificates WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['total_certificates'] = $result->fetch_assoc()['total'];

// Total skills
$sql = "SELECT COUNT(*) as total FROM skills WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats['total_skills'] = $result->fetch_assoc()['total'];

// Get recent activities
$sql = "SELECT * FROM activities WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_activities = $stmt->get_result();

include '../includes/header.php';
?>

<div class="container">
    <div class="card">
        <h2>Welcome back, <?php echo get_user_name(); ?>! 👋</h2>
        <p class="text-muted">Here's an overview of your career journey</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3><?php echo $stats['total_activities']; ?></h3>
            <p>Total Activities</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['ongoing_internships']; ?></h3>
            <p>Ongoing Internships</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['completed_courses']; ?></h3>
            <p>Completed Courses</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['total_certificates']; ?></h3>
            <p>Certificates Uploaded</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['total_skills']; ?></h3>
            <p>Skills Recorded</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Activities</h3>
            <a href="../activities/add.php" class="btn btn-primary">+ Add Activity</a>
        </div>

        <?php if ($recent_activities->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Organization</th>
                            <th>Status</th>
                            <th>Start Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($activity = $recent_activities->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($activity['title']); ?></td>
                                <td><span class="badge badge-primary"><?php echo $activity['type']; ?></span></td>
                                <td><?php echo htmlspecialchars($activity['organization']); ?></td>
                                <td>
                                    <span class="badge <?php echo $activity['status'] == 'Completed' ? 'badge-success' : 'badge-warning'; ?>">
                                        <?php echo $activity['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo format_date($activity['start_date']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="../activities/index.php" class="btn btn-secondary">View All Activities</a>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No activities recorded yet. Start building your career diary!</p>
                <a href="../activities/add.php" class="btn btn-primary mt-1">Add Your First Activity</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
    <h3>Quick Actions</h3>

    <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1rem;">

        <a href="../activities/add.php" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Add Activity
        </a>

        <a href="../certificates/upload.php" class="btn btn-success">
            <i class="fa-solid fa-upload"></i> Upload Certificate
        </a>

        <a href="../skills/add.php" class="btn btn-secondary">
            <i class="fa-solid fa-bullseye"></i> Add Skill
        </a>

        <a href="../cv/generate.php" class="btn btn-primary">
            <i class="fa-solid fa-file-lines"></i> Generate CV
        </a>

    </div>
</div>

</div>

<?php
include '../includes/footer.php';
?>
