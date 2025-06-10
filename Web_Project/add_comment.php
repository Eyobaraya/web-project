<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is logged in and is a teacher
if (!is_logged_in() || !is_teacher()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = $_POST['project_id'] ?? null;
    $comment = trim($_POST['comment'] ?? '');

    if ($project_id && !empty($comment)) {
        // Verify the project exists
        $stmt = $conn->prepare('SELECT id FROM projects WHERE id = ?');
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Add the comment
            $stmt = $conn->prepare('INSERT INTO comments (project_id, teacher_id, comment) VALUES (?, ?, ?)');
            $teacher_id = get_current_user_id();
            $stmt->bind_param('iis', $project_id, $teacher_id, $comment);

            if ($stmt->execute()) {
                header('Location: project.php?id=' . $project_id . '&message=Comment added successfully');
            } else {
                header('Location: project.php?id=' . $project_id . '&error=Failed to add comment');
            }
        } else {
            header('Location: index.php?error=Project not found');
        }
    } else {
        header('Location: index.php?error=Invalid comment data');
    }
} else {
    header('Location: index.php');
}
exit;
?> 