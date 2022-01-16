<?php

// Represents a user in the users table
class User {
    private $id;
    private $role;

    public function __construct($role) {
        $this->role = $role;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($value) {
        $this->id = $value;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole($value) {
        $this->role = $value;
    }
}
?>