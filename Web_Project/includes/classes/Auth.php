<?php
require_once 'User.php';

class Auth {
    public static function login($conn, $email, $password) {
        $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($conn, $query);
        if ($user = mysqli_fetch_assoc($result)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            return new User($user['id'], $user['name'], $user['email'], $user['role']);
        }
        return null;
    }

    public static function logout() {
        session_destroy();
    }

    public static function currentUser() {
        if (isset($_SESSION['user_id'])) {
            return new User($_SESSION['user_id'], $_SESSION['name'], '', $_SESSION['role']);
        }
        return null;
    }
}
?>
