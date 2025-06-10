<?php
class User {
    public $id;
    public $name;
    public $email;
    public $role;

    function __construct($id, $name, $email, $role) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
    }

    function isAdmin() {
        return $this->role === 'admin';
    }

    function isEmployer() {
        return $this->role === 'employer';
    }

    function isStudent() {
        return $this->role === 'student';
    }
}
?>
