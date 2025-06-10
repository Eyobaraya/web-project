<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}
?>
