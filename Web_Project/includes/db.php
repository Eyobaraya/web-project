<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if mysqli extension is loaded
if (!extension_loaded('mysqli')) {
    die('MySQLi extension is not enabled. Please enable it in your PHP configuration (php.ini).');
}

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'student_showcase';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");
?>
