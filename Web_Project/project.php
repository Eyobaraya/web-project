<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page_title = 'Project View';
require_once 'includes/header.php';

if (!is_logged_in()) {
    echo '<div style="text-align: center; padding: 50px;">
            <h2 style="color: #dc3545;">Can\'t open if not logged in</h2>
            <p style="margin-top: 20px;">Please <a href="login.php">log in</a> to view this project.</p>
          </div>';
    require_once 'includes/footer.php';
    exit;
}

$user_id = isset($_GET['user']) ? (int) $_GET['user'] : $_SESSION['user_id'];

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT p.*, u.name AS author 
                        FROM projects p 
                        JOIN users u ON p.user_id = u.id 
                        WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Project not found.');
}

$project = $result->fetch_object();
$page_title = htmlspecialchars($project->title);

// Paths for image
$image_relative = $project->image_path ?? '';
$image_absolute = $_SERVER['DOCUMENT_ROOT'] . '/Web_Project/' . $image_relative;

// Get comments if user can view them
$comments = [];
if (can_view_comments($project->user_id)) {
    // Check if comments table exists
    $table_exists = $conn->query("SHOW TABLES LIKE 'comments'");
    if ($table_exists->num_rows > 0) {
        $stmt = $conn->prepare("SELECT c.*, u.name as teacher_name 
                               FROM comments c 
                               JOIN users u ON c.teacher_id = u.id 
                               WHERE c.project_id = ? 
                               ORDER BY c.created_at DESC");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $comments_result = $stmt->get_result();
        while ($comment = $comments_result->fetch_object()) {
            $comments[] = $comment;
        }
    } else {
        // Create comments table if it doesn't exist
        $sql = "CREATE TABLE IF NOT EXISTS comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT NOT NULL,
            teacher_id INT NOT NULL,
            comment TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        
        if ($conn->query($sql)) {
            // Table created successfully, but no comments yet
            $comments = [];
        }
    }
}
?>

<style>
.action-buttons {
    margin: 20px 0;
    display: flex;
    gap: 16px;
}
.action-buttons .custom-btn {
    width: 150px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.05em;
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
    cursor: pointer;
    outline: none;
    text-decoration: none;
}
.action-buttons .custom-btn:link,
.action-buttons .custom-btn:visited,
.action-buttons .custom-btn:hover,
.action-buttons .custom-btn:active,
.action-buttons .custom-btn:focus {
    text-decoration: none !important;
}
.action-buttons .download-btn {
    background: linear-gradient(90deg, #007bff 60%, #0056b3 100%);
    color: #fff;
}
.action-buttons .download-btn:hover {
    background: linear-gradient(90deg, #0056b3 60%, #007bff 100%);
    box-shadow: 0 4px 16px rgba(0,123,255,0.15);
    transform: translateY(-2px) scale(1.03);
}
.action-buttons .comment-btn {
    background: linear-gradient(90deg, #28a745 60%, #218838 100%);
    color: #fff;
}
.action-buttons .comment-btn:hover {
    background: linear-gradient(90deg, #218838 60%, #28a745 100%);
    box-shadow: 0 4px 16px rgba(40,167,69,0.15);
    transform: translateY(-2px) scale(1.03);
}
</style>

<?php if (!is_logged_in()): ?>
    <div class="project-guest-view">
        <?php if (!empty($project->image_path) && file_exists($project->image_path)): ?>
            <img src="<?= htmlspecialchars($project->image_path) ?>" alt="Project Screenshot" style="max-width: 350px; display: block; margin-bottom: 16px;">
        <?php endif; ?>
        <p style="font-size: 1.1em; color: #333; margin-bottom: 8px;">
            <?= nl2br(htmlspecialchars($project->description)) ?>
        </p>
        <div style="margin-top: 24px; color: #888; font-size: 1em;">You are not logged in. Viewing as Guest.</div>
    </div>
<?php else: ?>
<div class="form-container">
  <h2><?= htmlspecialchars($project->title) ?></h2>

  <?php if (!empty($image_relative) && file_exists($image_absolute)): ?>
    <div style="margin: 20px 0;">
      <img src="<?= htmlspecialchars($image_relative) ?>" 
           alt="Screenshot of <?= htmlspecialchars($project->title) ?>" 
           style="max-width: 100%; border: 1px solid #ccc; border-radius: 6px;">
    </div>
  <?php endif; ?>

  <p><strong>Author:</strong> 
    <a href="profile.php?user=<?= htmlspecialchars($project->user_id) ?>">
      <?= htmlspecialchars($project->author) ?>
    </a>
  </p>

  <p><strong>Uploaded on:</strong> <?= date('F j, Y', strtotime($project->created_at)) ?></p>

  <p><strong>Description:</strong></p>
  <p><?= nl2br(htmlspecialchars($project->description)) ?></p>

        <div class="action-buttons">
            <?php if (can_download_project($project->user_id)): ?>
                <a href="download.php?id=<?= $project->id ?>"
                   class="custom-btn download-btn">
                    Download File
                </a>
            <?php endif; ?>
            <?php if (can_edit_project($project->user_id)): ?>
                <a href="edit_project.php?id=<?= $project->id ?>"
                   class="custom-btn"
                   style="background: #6c757d; color: #fff;">
                    Edit Project
      </a>
            <?php endif; ?>
            <?php if (is_teacher()): ?>
                <button onclick="showCommentForm()"
                        class="custom-btn comment-btn">
                    Add Comment
                </button>
  <?php endif; ?>
</div>

        <?php if (is_teacher()): ?>
            <div id="commentForm" style="display: none; margin: 20px 0;">
                <h3>Add a Comment</h3>
                <form action="add_comment.php" method="POST">
                    <input type="hidden" name="project_id" value="<?= $id ?>">
                    <div style="margin-bottom: 15px;">
                        <textarea name="comment" rows="4" required 
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                                  placeholder="Write your comment here..."></textarea>
                    </div>
                    <button type="submit" 
                            style="padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
                        Submit Comment
                    </button>
                    <button type="button" onclick="hideCommentForm()"
                            style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; margin-left: 10px;">
                        Cancel
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <?php if (can_view_comments($project->user_id) && !empty($comments)): ?>
            <div class="comments-section" style="margin-top: 30px;">
                <h3>Comments</h3>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px; background-color: white;">
                        <p><strong><?= htmlspecialchars($comment->teacher_name) ?></strong> 
                           <small style="color: #666;"><?= format_date($comment->created_at) ?></small>
                        </p>
                        <p><?= nl2br(htmlspecialchars($comment->comment)) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
function showCommentForm() {
    document.getElementById('commentForm').style.display = 'block';
}

function hideCommentForm() {
    document.getElementById('commentForm').style.display = 'none';
}
</script>

<?php require_once 'includes/footer.php'; ?>
