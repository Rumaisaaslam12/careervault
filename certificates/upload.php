<?php
/**
 * Certificate Upload Page
 * Upload and link certificates to activities
 */
require_once '../config/db.php';
require_once '../includes/session.php';

require_login();
$user_id = get_user_id();
$page_title = 'Upload Certificate';

$error_message = '';
$success_message = '';

// Get user's activities for dropdown
$sql = "SELECT activity_id, title, type FROM activities WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$activities = $stmt->get_result();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $certificate_name = sanitize_input($_POST['certificate_name']);
    $activity_id = !empty($_POST['activity_id']) ? intval($_POST['activity_id']) : null;
    
    if (empty($certificate_name)) {
        $error_message = "Certificate name is required.";
    } elseif (!isset($_FILES['certificate_file']) || $_FILES['certificate_file']['error'] == UPLOAD_ERR_NO_FILE) {
        $error_message = "Please select a file to upload.";
    } else {
        $file = $_FILES['certificate_file'];
        
        // Validate file
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error_message = "File upload error occurred.";
        } elseif ($file['size'] > $max_size) {
            $error_message = "File size must be less than 5MB.";
        } elseif (!in_array($file['type'], $allowed_types)) {
            $error_message = "Only PDF and image files (JPG, PNG) are allowed.";
        } else {
            $env_upload = getenv('UPLOAD_DIR');
            $upload_dir = $env_upload ? rtrim($env_upload, '/').'/' : __DIR__ . '/../uploads/certificates/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $unique_filename = $user_id . '_' . time() . '_' . uniqid() . '.' . $file_extension;
            $file_path = $upload_dir . $unique_filename;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                // Save to database
                $sql = "INSERT INTO certificates (user_id, activity_id, certificate_name, file_path, file_type) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iisss", $user_id, $activity_id, $certificate_name, $file_path, $file['type']);
                
                if ($stmt->execute()) {
                    header("Location: index.php?uploaded=1");
                    exit();
                } else {
                    $error_message = "Failed to save certificate information.";
                    unlink($file_path); // Delete uploaded file
                }
                $stmt->close();
            } else {
                $error_message = "Failed to upload file.";
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Upload Certificate</h2>
            <a href="index.php" class="btn btn-secondary">← Back to Certificates</a>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="certificate_name">Certificate Name *</label>
                <input type="text" id="certificate_name" name="certificate_name" class="form-control" 
                       value="<?php echo isset($_POST['certificate_name']) ? htmlspecialchars($_POST['certificate_name']) : ''; ?>" 
                       placeholder="e.g., Web Development Course Completion" required>
            </div>

            <div class="form-group">
                <label for="activity_id">Link to Activity (optional)</label>
                <select id="activity_id" name="activity_id" class="form-control">
                    <option value="">-- Select Activity --</option>
                    <?php 
                    $activities->data_seek(0); // Reset pointer
                    while ($activity = $activities->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $activity['activity_id']; ?>" 
                                <?php echo (isset($_POST['activity_id']) && $_POST['activity_id'] == $activity['activity_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($activity['title']) . ' (' . $activity['type'] . ')'; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <small class="text-muted">You can link this certificate to one of your activities</small>
            </div>

            <div class="form-group">
                <label for="certificate_file">Certificate File *</label>
                <input type="file" id="certificate_file" name="certificate_file" class="form-control" 
                       accept=".pdf,.jpg,.jpeg,.png" required>
                <small class="text-muted">Allowed formats: PDF, JPG, PNG (Max size: 5MB)</small>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-success">Upload Certificate</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
