<?php
/**
 * CV Generator
 * Generates a professional PDF CV from user data
 * Using FPDF library for PDF generation
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../fpdf/fpdf.php'; 


require_login();
$user_id = get_user_id();

// Check if user wants to download CV
if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
    // Include FPDF library (you need to download this)
    
    // Fetch user data
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    // Fetch activities
    $sql = "SELECT * FROM activities WHERE user_id = ? ORDER BY start_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $activities = $stmt->get_result();
    
    // Fetch skills
    $sql = "SELECT * FROM skills WHERE user_id = ? ORDER BY skill_level DESC, skill_name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $skills = $stmt->get_result();
    
    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Header - Name
    $pdf->SetFont('Arial', 'B', 24);
    $pdf->Cell(0, 10, $user['full_name'], 0, 1, 'C');
    $pdf->Ln(2);
    
    // Contact Information
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 6, 'Email: ' . $user['email'], 0, 1, 'C');
    if (!empty($user['phone'])) {
        $pdf->Cell(0, 6, 'Phone: ' . $user['phone'], 0, 1, 'C');
    }
    if (!empty($user['address'])) {
        $pdf->Cell(0, 6, 'Address: ' . $user['address'], 0, 1, 'C');
    }
    $pdf->Ln(5);
    
    // Profile Summary
    if (!empty($user['profile_summary'])) {
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'PROFILE SUMMARY', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 5, $user['profile_summary']);
        $pdf->Ln(3);
    }
    
    // Education
    if (!empty($user['education'])) {
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'EDUCATION', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 5, $user['education']);
        $pdf->Ln(3);
    }
    
    // Experience (Internships)
    $internships = [];
    $courses = [];
    $conferences = [];
    $workshops = [];
    
    $activities->data_seek(0);
    while ($activity = $activities->fetch_assoc()) {
        if ($activity['type'] == 'Internship') {
            $internships[] = $activity;
        } elseif ($activity['type'] == 'Course') {
            $courses[] = $activity;
        } elseif ($activity['type'] == 'Conference') {
            $conferences[] = $activity;
        } else {
            $workshops[] = $activity;
        }
    }
    
    // Internships/Experience
    if (count($internships) > 0) {
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'PROFESSIONAL EXPERIENCE', 0, 1);
        
        foreach ($internships as $internship) {
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 6, $internship['title'], 0, 1);
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->Cell(0, 5, $internship['organization'] . ' | ' . 
                       format_date($internship['start_date']) . ' - ' . 
                       ($internship['end_date'] ? format_date($internship['end_date']) : 'Present'), 0, 1);
            if (!empty($internship['description'])) {
                $pdf->SetFont('Arial', '', 10);
                $pdf->MultiCell(0, 5, $internship['description']);
            }
            $pdf->Ln(2);
        }
        $pdf->Ln(1);
    }
    
    // Courses & Certifications
    if (count($courses) > 0) {
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'COURSES & CERTIFICATIONS', 0, 1);
        
        foreach ($courses as $course) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(5, 5, chr(149), 0, 0);
            $pdf->Cell(0, 5, $course['title'] . ' - ' . $course['organization'] . 
                       ' (' . format_date($course['start_date']) . ')', 0, 1);
        }
        $pdf->Ln(3);
    }
    
    // Conferences & Workshops
    $combined = array_merge($conferences, $workshops);
    if (count($combined) > 0) {
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'CONFERENCES & WORKSHOPS', 0, 1);
        
        foreach ($combined as $item) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(5, 5, chr(149), 0, 0);
            $pdf->Cell(0, 5, $item['title'] . ' - ' . $item['organization'] . 
                       ' (' . format_date($item['start_date']) . ')', 0, 1);
        }
        $pdf->Ln(3);
    }
    
    // Skills
    if ($skills->num_rows > 0) {
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'SKILLS', 0, 1);
        
        $skills_by_level = ['Advanced' => [], 'Intermediate' => [], 'Beginner' => []];
        while ($skill = $skills->fetch_assoc()) {
            $skills_by_level[$skill['skill_level']][] = $skill['skill_name'];
        }
        
        foreach ($skills_by_level as $level => $skill_names) {
            if (count($skill_names) > 0) {
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 5, $level . ':', 0, 1);
                $pdf->SetFont('Arial', '', 10);
                $pdf->MultiCell(0, 5, implode(', ', $skill_names));
                $pdf->Ln(1);
            }
        }
    }
    
    // Output PDF
    $filename = str_replace(' ', '_', $user['full_name']) . '_CV_' . date('Y-m-d') . '.pdf';
    $pdf->Output('D', $filename);
    exit();
}

$page_title = 'Generate CV';

// Fetch user profile for editing
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $profile_summary = sanitize_input($_POST['profile_summary']);
    $education = sanitize_input($_POST['education']);
    
    $sql = "UPDATE users SET phone = ?, address = ?, profile_summary = ?, education = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $phone, $address, $profile_summary, $education, $user_id);
    
    if ($stmt->execute()) {
        $success_message = "Profile updated successfully!";
        // Refresh user data
        $user['phone'] = $phone;
        $user['address'] = $address;
        $user['profile_summary'] = $profile_summary;
        $user['education'] = $education;
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Generate CV</h2>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <p>Complete your profile information below, then generate your professional CV in PDF format.</p>

        <form method="POST" action="">
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" class="form-control" 
                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                       placeholder="+92 XXX XXXXXXX">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" class="form-control" 
                       value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" 
                       placeholder="Your city and country">
            </div>

            <div class="form-group">
                <label for="profile_summary">Profile Summary</label>
                <textarea id="profile_summary" name="profile_summary" class="form-control" rows="4" 
                          placeholder="Write a brief professional summary about yourself (2-3 sentences)"><?php echo htmlspecialchars($user['profile_summary'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="education">Education</label>
                <textarea id="education" name="education" class="form-control" rows="3" 
                          placeholder="e.g., Bachelor of Science in Computer Science, XYZ University (2020-2024)"><?php echo htmlspecialchars($user['education'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn btn-success">Update Profile</button>
        </form>
    </div>

    <div class="card">
        <h3>Ready to Generate Your CV?</h3>
        <p>Your CV will include:</p>
        <ul>
            <li>Personal Information & Contact Details</li>
            <li>Profile Summary</li>
            <li>Education</li>
            <li>Professional Experience (Internships)</li>
            <li>Courses & Certifications</li>
            <li>Conferences & Workshops</li>
            <li>Skills (organized by proficiency level)</li>
        </ul>

        <div style="margin-top: 1.5rem;">
    <a href="generate.php?download=pdf" class="btn btn-primary" style="font-size: 1.1rem;">
        <i class="fa-solid fa-file-pdf"></i> Download CV as PDF
    </a>
</div>

<?php include '../includes/footer.php'; ?>