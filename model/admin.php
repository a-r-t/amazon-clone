<?php
// Represents an admin in the admins table
class Admin {
    private $id;
	private $user_id;
    private $first_name;
	private $last_name;
	private $email;
	
    public function __construct($user_id, $first_name, $last_name, $email) {
		$this->user_id = $user_id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
		$this->email = $email;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($value) {
        $this->id = $value;
    }
	
    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($value) {
        $this->user_id = $value;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function setFirstName($value) {
        $this->first_name = $value;
    }
	
    public function getLastName() {
        return $this->last_name;
    }

    public function setLastName($value) {
        $this->last_name = $value;
    }
	
    public function getFullName() {
        return sprintf('%s %s', $this->first_name, $this->last_name);
    }
	
    public function getEmail() {
        return $this->email;
    }

    public function setEmail($value) {
        $this->email = $value;
    }
}
?>