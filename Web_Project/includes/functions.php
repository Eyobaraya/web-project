<?php
// See if someone is currently logged into their account
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Check if the current user is an administrator
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Check if the current user is an employer
function is_employer() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'employer';
}

// Check if the current user is a student
function is_student() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

// Check if the current user is a teacher
function is_teacher() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'teacher';
}

// See if the current user is allowed to upload new projects
// Only students and admins can upload projects
function can_upload_project() {
    return is_logged_in() && (is_admin() || is_student());
}

// Check if the current user is allowed to view a specific project
// Admins, teachers, employers, and the project owner can view projects
function can_view_project($project_user_id) {
    return is_logged_in() && (
        is_admin() || 
        is_teacher() || 
        is_employer() || 
        get_current_user_id() === $project_user_id
    );
}

// Check if the current user is allowed to download a project
// Only admins, teachers, and the project owner can download
function can_download_project($project_user_id) {
    return is_logged_in() && (
        is_admin() || 
        is_teacher() || 
        get_current_user_id() === $project_user_id
    );
}

// Check if the current user is allowed to comment on a project
// Teachers can comment on any project, and users can comment on their own projects
function can_comment_on_project($project_user_id) {
    if (!is_logged_in()) {
        return false;
    }
    
    // Teachers have permission to comment on any project
    if (is_teacher()) {
        return true;
    }
    
    // Users can comment on their own projects
    if ($_SESSION['user_id'] == $project_user_id) {
        return true;
    }
    
    return false;
}

// Check if the current user is allowed to view project comments
// Admins, teachers, and project owners can view comments
function can_view_comments($project_user_id) {
    return is_logged_in() && (
        is_admin() || 
        is_teacher() || 
        get_current_user_id() === $project_user_id
    );
}

// Get the ID of the currently logged-in user
function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

// Get the role of the currently logged-in user (admin, student, teacher, etc.)
function get_current_user_role() {
    return $_SESSION['role'] ?? null;
}

// Get the name of the currently logged-in user
function get_current_user_name() {
    return $_SESSION['name'] ?? null;
}

// Check if the current user is allowed to edit a project
// Only the project owner can edit their own projects
function can_edit_project($project_user_id) {
    if (!is_logged_in()) {
        return false;
    }
    
    // Users can only edit their own projects
    if ($_SESSION['user_id'] == $project_user_id) {
        return true;
    }
    
    return false;
}

// Check if the current user is allowed to delete a project
// Admins can delete any project, and users can delete their own projects
function can_delete_project($project_user_id) {
    return is_admin() || (is_logged_in() && get_current_user_id() === $project_user_id);
}

// Convert a date into a nice, readable format (e.g., "January 1, 2024")
function format_date($date) {
    return date('F j, Y', strtotime($date));
}

// Make text safe to display on a webpage by converting special characters
// This helps prevent XSS (Cross-Site Scripting) attacks
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Get the file extension from a filename (e.g., "jpg" from "photo.jpg")
function get_file_extension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Check if a file is an allowed image type (jpg, jpeg, png, or gif)
function is_image($filename) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    return in_array(get_file_extension($filename), $allowed);
}

// Check if a file is a ZIP archive
function is_zip($filename) {
    return get_file_extension($filename) === 'zip';
}

// Create a unique filename to prevent overwriting existing files
function generate_filename($original_name) {
    $ext = get_file_extension($original_name);
    return uniqid() . '.' . $ext;
}

// Make sure a folder exists, create it if it doesn't
function ensure_directory_exists($path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
}

// Safely remove a file if it exists
function delete_file_if_exists($path) {
    if (file_exists($path)) {
        unlink($path);
    }
}

// Check if a password meets our minimum requirements
// Currently, we only require at least 6 characters
function validate_password($password) {
    return strlen($password) >= 6;
}
?> 