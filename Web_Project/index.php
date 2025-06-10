<?php
require_once 'includes/db.php';
require_once 'includes/classes/Project.php';
require_once 'includes/header.php';

// Handle search
$searchQuery = $_GET['q'] ?? null;
if ($searchQuery) {
    $projects = Project::search($conn, $searchQuery);
} else {
    $projects = Project::fetchAllWithAuthor($conn);
}
?>

<!-- Search Bar -->
<div class="search-section">
  <form class="middle" action="index.php" method="get">
    <input type="text" name="q" placeholder="Search projects...">
    <button type="submit">Search</button>
  </form>
</div>

<?php
if (count($projects) > 0) {
    echo '<section class="grid">';
    foreach ($projects as $project) {
        include 'templates/project_card.php';
    }
    echo '</section>';
} else {
    echo '<p>No projects found.</p>';
}

require_once 'includes/footer.php';
?>
