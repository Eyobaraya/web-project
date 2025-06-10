<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $bio = $_POST['bio'] ?? '';
    $education = $_POST['education'] ?? '';
    $skills = $_POST['skills'] ?? '';
    $telegram_username = $_POST['telegram_username'] ?? '';
    $linkedin_url = $_POST['linkedin_url'] ?? '';
    $email = $_POST['email'] ?? '';
    $profile_pic = '';

    // Handle file upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_pic']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $upload_dir = 'uploads/profile_pics/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $new_filename = uniqid() . '.' . $ext;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $destination)) {
                $profile_pic = $destination;
            }
        }
    }

    try {
        // First, let's verify the columns exist
        $check_columns = $conn->query("SHOW COLUMNS FROM users");
        $existing_columns = [];
        while ($row = $check_columns->fetch_assoc()) {
            $existing_columns[] = $row['Field'];
        }

        // Build the SQL query based on existing columns
        $updates = [];
        $params = [];
        $types = '';

        if (in_array('bio', $existing_columns)) {
            $updates[] = "bio = ?";
            $params[] = $bio;
            $types .= 's';
        }

        if (in_array('education', $existing_columns)) {
            $updates[] = "education = ?";
            $params[] = $education;
            $types .= 's';
        }

        if (in_array('skills', $existing_columns)) {
            $updates[] = "skills = ?";
            $params[] = $skills;
            $types .= 's';
        }

        if (in_array('telegram_username', $existing_columns)) {
            $updates[] = "telegram_username = ?";
            $params[] = $telegram_username;
            $types .= 's';
        }

        if (in_array('linkedin_url', $existing_columns)) {
            $updates[] = "linkedin_url = ?";
            $params[] = $linkedin_url;
            $types .= 's';
        }

        if (in_array('email', $existing_columns)) {
            $updates[] = "email = ?";
            $params[] = $email;
            $types .= 's';
        }

        if ($profile_pic && in_array('profile_pic', $existing_columns)) {
            $updates[] = "profile_pic = ?";
            $params[] = $profile_pic;
            $types .= 's';
        }

        if (!empty($updates)) {
            $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
            $params[] = $user_id;
            $types .= 'i';

            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $success_message = "Profile updated successfully!";
            } else {
                $error_message = "Error updating profile: " . $conn->error;
            }
        } else {
            $error_message = "No valid fields to update";
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

$stmt = $conn->prepare('SELECT id, name, email, role FROM users WHERE id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$additional_fields = ['profile_pic', 'bio', 'skills', 'education', 'telegram_username', 'linkedin_url'];
foreach ($additional_fields as $field) {
    try {
        $check_stmt = $conn->prepare("SELECT $field FROM users WHERE id = ?");
        $check_stmt->bind_param('i', $user_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        if ($result) {
            $user[$field] = $result->fetch_assoc()[$field] ?? null;
        }
    } catch (Exception $e) {
        $user[$field] = null;
    }
}

$page_title = 'Edit Profile';
require_once 'includes/header.php';
?>

<div class="edit-profile-page">
    <h2>Edit Profile</h2>

    <?php if (isset($success_message)): ?>
        <div class="alert success"><?= $success_message ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert error"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="edit-profile-form">
        <div class="form-group">
            <label for="profile_pic">Profile Picture:</label>
            <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
            <?php if (!empty($user['profile_pic'])): ?>
                <div class="current-pic">
                    <p>Current picture:</p>
                    <img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="Current profile picture" style="max-width: 200px;">
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio" rows="4"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="education">Education:</label>
            <textarea id="education" name="education" rows="4"><?= htmlspecialchars($user['education'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="skills">Skills:</label>
            <textarea id="skills" name="skills" rows="4"><?= htmlspecialchars($user['skills'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="telegram_username">Telegram Username (without @):</label>
            <input type="text" id="telegram_username" name="telegram_username" value="<?= htmlspecialchars($user['telegram_username'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="linkedin_url">LinkedIn URL:</label>
            <input type="url" id="linkedin_url" name="linkedin_url" value="<?= htmlspecialchars($user['linkedin_url'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="profile.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?> 