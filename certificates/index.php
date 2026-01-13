<?php
/**
 * Certificates List Page
 * View and manage uploaded certificates
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';


require_login();
$user_id = get_user_id();
$page_title = 'My Certificates';

// Get all certificates with activity details
$sql = "SELECT c.*, a.title as activity_title FROM certificates c 
        LEFT JOIN activities a ON c.activity_id = a.activity_id 
        WHERE c.user_id = ? ORDER BY c.upload_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$certificates = $stmt->get_result();

include '../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">My Certificates</h2>
            <a href="upload.php" class="btn btn-primary">+ Upload Certificate</a>
        </div>

        <?php if (isset($_GET['uploaded'])): ?>
            <div class="alert alert-success">Certificate uploaded successfully!</div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Certificate deleted successfully!</div>
        <?php endif; ?>

        <?php if ($certificates->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Certificate Name</th>
                            <th>Related Activity</th>
                            <th>File Type</th>
                            <th>Upload Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($cert = $certificates->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($cert['certificate_name']); ?></strong></td>
                                <td>
                                    <?php 
                                    echo $cert['activity_title'] 
                                        ? '<span class="badge badge-info">' . htmlspecialchars($cert['activity_title']) . '</span>' 
                                        : '<span class="text-muted">No activity linked</span>';
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($cert['file_type']); ?></td>
                                <td><?php echo format_date($cert['upload_date']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="download.php?id=<?php echo $cert['certificate_id']; ?>" 
                                           class="btn btn-primary btn-small" target="_blank">View/Download</a>
                                        <a href="delete.php?id=<?php echo $cert['certificate_id']; ?>" 
                                           class="btn btn-danger btn-small"
                                           onclick="return confirm('Are you sure you want to delete this certificate?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No certificates uploaded yet. Upload your certificates to keep them safe!</p>
                <a href="upload.php" class="btn btn-primary mt-1">Upload Your First Certificate</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>