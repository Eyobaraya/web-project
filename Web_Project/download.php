<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Make sure the user is logged in before allowing download
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Get the project ID from the URL and make sure it's valid
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($project_id <= 0) {
    die('Invalid project ID');
}

// Get all the project information from the database
$stmt = $conn->prepare('SELECT * FROM projects WHERE id = ?');
$stmt->bind_param('i', $project_id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_object();

if (!$project) {
    die('Project not found');
}

// Check if this user is allowed to download this project
if (!can_download_project($project->user_id)) {
    die('You do not have permission to download this project');
}

// Use the file path that was saved when the project was uploaded
$file_path = $project->file_path;

// Make sure the file actually exists on the server
if (!file_exists($file_path)) {
    die('File not found');
}

// Get the file's name and size for the download
$file_name = basename($file_path);
$file_size = filesize($file_path);

// Tell the browser this is a ZIP file and should be downloaded
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Content-Length: ' . $file_size);
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Send the file to the user
readfile($file_path);
exit;
?> 