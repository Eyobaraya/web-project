<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// If user is already logged in, send them to the home page
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

// Handle the login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Look up the user in the database
    $stmt = $conn->prepare('SELECT id, name, password, role FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_object();

    // Check if the user exists and the password is correct
    if ($user && password_verify($password, $user->password)) {
        // Start a new session and store user information
        session_start();
        $_SESSION['user_id'] = $user->id;
        $_SESSION['name'] = $user->name;
        $_SESSION['role'] = $user->role;
        
        // Send the user to the home page
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid email or password';
    }
}

$page_title = 'Login';
require_once 'includes/header.php';
?>

<div class="login-container">
  <div class="login-box">
    <h2>Login</h2>
    
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="post" action="">
      <label for="email">Email</label>
      <input type="email" name="email" id="email" required>

      <label for="password">Password</label>
      <input type="password" name="password" id="password" required>

      <button type="submit">Login</button>
    </form>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>