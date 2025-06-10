<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if (!validate_password($password)) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Email already registered';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = in_array($_POST['role'], ['student', 'employer', 'teacher']) ? $_POST['role'] : 'student';
            $stmt = $conn->prepare('INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)');
            $stmt->bind_param('ssss', $name, $email, $hash, $role);
            if ($stmt->execute()) {
                header('Location: login.php');
                exit;
            } else {
                $error = 'Registration failed';
            }
        }
    }
}
$page_title = 'Sign Up';
require_once 'includes/header.php';
?>
<div class="login-container">
  <div class="login-box">
    <h2>Sign Up</h2>
    
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="post" action="">
      <label for="name">Name</label>
      <input type="text" name="name" id="name" required>

      <label for="email">Email</label>
      <input type="email" name="email" id="email" required>

      <label for="password">Password (minimum 6 characters)</label>
      <input type="password" name="password" id="password" required minlength="6">

      <label for="confirm">Confirm Password</label>
      <input type="password" name="confirm" id="confirm" required minlength="6">

      <label for="role">Role</label>
      <select name="role" id="role" required>
        <option value="student">Student</option>
        <option value="employer">Employer</option>
        <option value="teacher">Teacher</option>
      </select>

      <button type="submit">Sign Up</button>
    </form>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
