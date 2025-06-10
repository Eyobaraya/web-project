<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit();
}

// Get project ID from either POST or GET
$project_id = $_POST['project_id'] ?? $_GET['id'] ?? null;

if (!$project_id) {
    header('Location: index.php?error=No project specified');
    exit();
}

// Get project details
$stmt = $conn->prepare('SELECT user_id, file_path, image_path FROM projects WHERE id = ?');
$stmt->bind_param('i', $project_id);
    $stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

if (!$project) {
    header('Location: index.php?error=Project not found');
    exit();
}

// Check if user has permission to delete
if (can_delete_project($project['user_id'])) {
    // Delete associated files
    if (!empty($project['file_path'])) {
        delete_file_if_exists($project['file_path']);
            }
    if (!empty($project['image_path'])) {
        delete_file_if_exists($project['image_path']);
            }

    // Delete the project
    $stmt = $conn->prepare('DELETE FROM projects WHERE id = ?');
    $stmt->bind_param('i', $project_id);
    
    if ($stmt->execute()) {
        header('Location: index.php?message=Project deleted successfully');
    } else {
        header('Location: index.php?error=Failed to delete project: ' . $conn->error);
    }
} else {
    header('Location: index.php?error=You do not have permission to delete this project');
}
exit();
?>
