<?php
if (!isset($project)) return;

$image_path = $project->image_path ?? '';
$server_path = $_SERVER['DOCUMENT_ROOT'] . '/Web_Project/' . $image_path;
?>

<article class="project-card">
  <a href="project.php?id=<?= htmlspecialchars($project->id) ?>">

    <?php if (!empty($image_path) && file_exists($server_path)): ?>
      <img
        src="<?= htmlspecialchars($image_path) ?>"
        alt="Project image"
        class="project-image"
        style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 10px;">

    <?php else: ?>
      <div style="width: 100%; height: 200px; background: #eee; display: flex; align-items: center; justify-content: center; color: #999;">
        No image available
      </div>
    <?php endif; ?>

    <h3 class="project-title"><?= htmlspecialchars($project->title) ?></h3>
  </a>

  <?php if (!empty($project->description)): ?>
    <p class="project-description"><?= nl2br(htmlspecialchars($project->description)) ?></p>
  <?php endif; ?>

  <p class="byline">
    by&nbsp;
    <a href="profile.php?user=<?= htmlspecialchars($project->user_id) ?>" class="project-author">
      <?= htmlspecialchars($project->author) ?>
    </a>
  </p>
  <?php
$isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $project->user_id;
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>

<?php if ($isOwner || $isAdmin): ?>
  <form method="POST" action="delete_project.php" onsubmit="return confirm('Are you sure you want to delete this project?');" style="margin-top: 10px;">
    <input type="hidden" name="project_id" value="<?= htmlspecialchars($project->id) ?>">
    <button type="submit" style="background-color: #e74c3c; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer;">
      Delete Project
    </button>
  </form>
<?php endif; ?>
</article>
