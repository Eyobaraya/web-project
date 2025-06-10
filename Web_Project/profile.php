<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = 0;
$user = null;

// Determine which user profile to show
if (isset($_GET['user'])) {
    $user_id = (int) $_GET['user'];
} elseif (isset($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];
} else {
    $page_title = 'Guest – Portfolio';
    require_once 'includes/header.php';
    echo '<div class="profile-page">';
    echo '<h2>Guest</h2><p>You are not logged in. Please <a href="login.php">log in</a> to view your profile.</p>';
    echo '</div>';
    require_once 'includes/footer.php';
    exit;
}

// Get user data
$stmt = $conn->prepare('SELECT id, name, email, role, profile_pic, bio, skills FROM users WHERE id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header('Location: index.php');
    exit();
}

// Check for additional columns
$additional_fields = ['education', 'telegram_username', 'linkedin_url'];
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

$page_title = $user['name'] . ' – Portfolio';
require_once 'includes/header.php';
?>

<div class="profile-page">
    <div class="profile-header">
        <?php if (is_logged_in() && $_SESSION['user_id'] == $user['id']): ?>
            <a href="edit_profile.php" class="edit-profile-btn">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
        <?php endif; ?>

        <div class="profile-basic">
       <?php if (!empty($user['profile_pic']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/Web_Project/' . $user['profile_pic'])): ?>
    <img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile Picture" class="profile-avatar">
        <?php else: ?>
    <img src="assets/img/default.jpg" alt="Default Avatar" class="profile-avatar">
        <?php endif; ?>

            <div class="name-role-container" style="text-align: center;">
                <h2 style="color: black;">
                    <?php if ($user['role'] === 'employer'): ?>
                        <?= htmlspecialchars($user['name']) ?> [Employer]
                    <?php elseif ($user['role'] === 'teacher'): ?>
                        <?= htmlspecialchars($user['name']) ?> [Teacher]
                    <?php else: ?>
                        <?= htmlspecialchars($user['name']) ?>
                    <?php endif; ?>
                </h2>
            </div>
        </div>
    </div>

    <div class="profile-sections">
        <?php if (!empty($user['bio'])): ?>
            <section class="profile-section">
                <h3>About Me</h3>
                <div class="section-content">
                    <?= nl2br(htmlspecialchars($user['bio'])) ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($user['education'])): ?>
            <section class="profile-section">
                <h3>Education</h3>
                <div class="section-content">
                    <?= nl2br(htmlspecialchars($user['education'])) ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($user['skills'])): ?>
            <section class="profile-section">
                <h3>Skills</h3>
                <div class="section-content">
                    <?= nl2br(htmlspecialchars($user['skills'])) ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="profile-section">
            <h3>Contact Information</h3>
            <div class="section-content contact-info">
                <?php
                // Debug information
                echo "<!-- Debug Info:\n";
                echo "Email: " . (isset($user['email']) ? $user['email'] : 'not set') . "\n";
                echo "Telegram: " . (isset($user['telegram_username']) ? $user['telegram_username'] : 'not set') . "\n";
                echo "LinkedIn: " . (isset($user['linkedin_url']) ? $user['linkedin_url'] : 'not set') . "\n";
                echo "-->";
                ?>

                <?php if (!empty($user['email'])): ?>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?></a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['telegram_username'])): ?>
                    <div class="contact-item">
                        <i class="fab fa-telegram"></i>
                        <a href="https://t.me/<?= htmlspecialchars($user['telegram_username']) ?>" target="_blank" rel="noopener noreferrer">
                            @<?= htmlspecialchars($user['telegram_username']) ?>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['linkedin_url'])): ?>
                    <div class="contact-item">
                        <i class="fab fa-linkedin"></i>
                        <a href="<?= htmlspecialchars($user['linkedin_url']) ?>" target="_blank" rel="noopener noreferrer">
                            <?= htmlspecialchars($user['linkedin_url']) ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php if ($user['role'] === 'student'): ?>
    <div class="profile-projects">
        <h3>Projects</h3>
        <?php
        $stmt = $conn->prepare('SELECT * FROM projects WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->bind_param('i', $user['id']);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            echo '<section class="grid">';
            while ($row = $res->fetch_assoc()) {
                $row['author'] = $user['name'];
                $project = (object) $row;
                include 'templates/project_card.php';
            }
            echo '</section>';
        } else {
            echo '<p>No projects yet.</p>';
        }
        ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
