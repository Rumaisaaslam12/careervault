<?php
/**
 * Skills List Page
 * View and manage skills
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';

require_login();
$user_id = get_user_id();
$page_title = 'My Skills';

// Get all skills
$sql = "SELECT * FROM skills WHERE user_id = ? ORDER BY skill_level DESC, skill_name ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$skills = $stmt->get_result();

include '../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">My Skills</h2>
            <a href="add.php" class="btn btn-primary">+ Add Skill</a>
        </div>

        <?php if (isset($_GET['added'])): ?>
            <div class="alert alert-success">Skill added successfully!</div>
        <?php endif; ?>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">Skill updated successfully!</div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Skill deleted successfully!</div>
        <?php endif; ?>

        <?php if ($skills->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Skill Name</th>
                            <th>Proficiency Level</th>
                            <th>Added On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($skill = $skills->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($skill['skill_name']); ?></strong></td>
                                <td>
                                    <?php
                                    $badge_class = 'badge-info';
                                    if ($skill['skill_level'] == 'Advanced') $badge_class = 'badge-success';
                                    elseif ($skill['skill_level'] == 'Intermediate') $badge_class = 'badge-primary';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo $skill['skill_level']; ?>
                                    </span>
                                </td>
                                <td><?php echo format_date($skill['created_at']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit.php?id=<?php echo $skill['skill_id']; ?>" 
                                           class="btn btn-secondary btn-small">Edit</a>
                                        <a href="delete.php?id=<?php echo $skill['skill_id']; ?>" 
                                           class="btn btn-danger btn-small"
                                           onclick="return confirm('Are you sure you want to delete this skill?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 2rem; padding: 1.5rem; background-color: #f8f9fa; border-radius: 5px;">
                <h3 style="margin-bottom: 1rem;">Skills Summary</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <?php
                    $skills->data_seek(0);
                    $levels = ['Advanced' => 0, 'Intermediate' => 0, 'Beginner' => 0];
                    while ($skill = $skills->fetch_assoc()) {
                        $levels[$skill['skill_level']]++;
                    }
                    ?>
                    <div>
                        <strong>Advanced:</strong> <?php echo $levels['Advanced']; ?> skills
                    </div>
                    <div>
                        <strong>Intermediate:</strong> <?php echo $levels['Intermediate']; ?> skills
                    </div>
                    <div>
                        <strong>Beginner:</strong> <?php echo $levels['Beginner']; ?> skills
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No skills added yet. Start building your skillset profile!</p>
                <a href="add.php" class="btn btn-primary mt-1">Add Your First Skill</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>