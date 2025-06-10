<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get project details
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_object();

// Check if project exists and user has permission to edit
if (!$project || !can_edit_project($project->user_id)) {
    header('Location: index.php?error=You do not have permission to edit this project');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $imagePath = $project->image_path;
    $filePath = $project->file_path;

    // Handle new image upload if provided
    if (isset($_FILES['screenshots']) && $_FILES['screenshots']['error'][0] === 0) {
        $imgName = $_FILES['screenshots']['name'][0];
        $imgTmp = $_FILES['screenshots']['tmp_name'][0];
        $imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));

        if (in_array($imgExt, ['jpg', 'jpeg', 'png', 'gif'])) {
            $new_img = uniqid('img_') . '.' . $imgExt;
            $new_imagePath = 'uploads/image/' . $new_img;
            
            if (move_uploaded_file($imgTmp, $new_imagePath)) {
                // Delete old image if exists
                if (!empty($project->image_path) && file_exists($project->image_path)) {
                    unlink($project->image_path);
                }
                $imagePath = $new_imagePath;
            }
        }
    }

    // Handle new file upload if provided
    if (isset($_FILES['project_zip']) && $_FILES['project_zip']['error'] === 0) {
        $filename = $_FILES['project_zip']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed = ['zip', 'html', 'htm', 'css', 'js'];

        if (in_array($ext, $allowed)) {
            $new_filename = uniqid('zip_') . '.' . $ext;
            $new_filePath = 'uploads/web/' . $new_filename;
            
            if (move_uploaded_file($_FILES['project_zip']['tmp_name'], $new_filePath)) {
                // Delete old file if exists
                if (!empty($project->file_path) && file_exists($project->file_path)) {
                    unlink($project->file_path);
                }
                $filePath = $new_filePath;
            }
        }
    }

    // Update project in database
    $stmt = $conn->prepare("UPDATE projects SET title = ?, description = ?, image_path = ?, file_path = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $description, $imagePath, $filePath, $project_id);

    if ($stmt->execute()) {
        header('Location: project.php?id=' . $project_id . '&message=Project updated successfully');
        exit;
    } else {
        $error = 'Failed to update project: ' . $conn->error;
    }
}

$page_title = 'Edit Project';
require_once 'includes/header.php';
?>

<div class="form-container">
    <h2>Edit Project</h2>
    
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($project->title) ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($project->description) ?></textarea>

        <label>Current Screenshot:</label>
        <?php if (!empty($project->image_path) && file_exists($project->image_path)): ?>
            <img src="<?= htmlspecialchars($project->image_path) ?>" 
                 alt="Current screenshot" 
                 style="max-width: 200px; margin: 10px 0;">
        <?php endif; ?>
        <label>New Screenshot (optional):</label>
        <input type="file" name="screenshots[]" accept="image/*">

        <label>Current Project File:</label>
        <?php if (!empty($project->file_path) && file_exists($project->file_path)): ?>
            <p>Current file: <?= basename($project->file_path) ?></p>
        <?php endif; ?>
        <label>New Project File (optional):</label>
        <input type="file" name="project_zip" accept=".zip,.html,.htm,.css,.js">

        <button type="submit">Update Project</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?> 