<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Make sure the user has permission to upload projects
if (!can_upload_project()) {
    header('Location: index.php');
    exit;
}

$message = '';

// Create the folders we need for storing files if they don't exist yet
if (!file_exists('uploads/web')) mkdir('uploads/web', 0755, true);
if (!file_exists('uploads/image')) mkdir('uploads/image', 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc  = trim($_POST['description']);
    $zipPath = '';
    $imagePath = '';

    // Handle the project file upload (ZIP or web files)
    if (isset($_FILES['project_zip']) && $_FILES['project_zip']['error'] === 0) {
        $filename = $_FILES['project_zip']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed = ['zip', 'html', 'htm', 'css', 'js'];

        if (in_array($ext, $allowed)) {
            $new_filename = uniqid('zip_') . '.' . $ext;
            $zipPath = 'uploads/web/' . $new_filename;
            move_uploaded_file($_FILES['project_zip']['tmp_name'], $zipPath);
        } else {
            $message = 'Invalid ZIP/web file type.';
        }
    } else {
        $message = 'Project ZIP file is required.';
    }

    // Handle the project screenshot upload
    if (isset($_FILES['screenshots']) && $_FILES['screenshots']['error'][0] === 0) {
        $imgName = $_FILES['screenshots']['name'][0];
        $imgTmp = $_FILES['screenshots']['tmp_name'][0];
        $imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));

        if (in_array($imgExt, ['jpg', 'jpeg', 'png', 'gif'])) {
            $new_img = uniqid('img_') . '.' . $imgExt;
            $imagePath = 'uploads/image/' . $new_img;
            move_uploaded_file($imgTmp, $imagePath);
        } else {
            $message = 'Invalid image file type.';
        }
    } else {
        $message = 'At least one screenshot is required.';
    }

    // Save all the project information to the database
    if (empty($message)) {
        $stmt = $conn->prepare('INSERT INTO projects (title, description, file_path, image_path, user_id, type, created_at) 
                                VALUES (?, ?, ?, ?, ?, "web", NOW())');
        $stmt->bind_param('ssssi', $title, $desc, $zipPath, $imagePath, $_SESSION['user_id']);

        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        } else {
            $message = 'Database error: ' . $conn->error;
        }
    }
}

$page_title = 'Upload Project';
require_once 'includes/header.php';
?>

<div class="form-container">
  <h2>Upload Project</h2>
  <?php if ($message): ?>
    <p class="error"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <label>Title:</label>
    <input type="text" name="title" required>

    <label>Description:</label>
    <textarea name="description" required></textarea>

    <label>Project ZIP File:</label>
    <input type="file" name="project_zip" accept=".zip,.html,.htm,.css,.js" required>

    <label>Screenshots (1 image required):</label>
    <input type="file" name="screenshots[]" accept="image/*" required>

    <button type="submit">Upload</button>
  </form>
</div>

<?php require_once 'includes/footer.php'; ?>
