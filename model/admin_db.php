<?php
require_once('database.php');
require_once('admin.php');

// Class for interacting with admins table in Database
class AdminDb {
	
	/*
		Admin Object Schema:
		- id
		- user_id
		- first_name
		- last_name
		- email
	*/

	// get admin with matching id attribute
    public static function getAdmin($id) {
        $query = 'SELECT * FROM admins
                  WHERE id = :id
				  LIMIT 1';    
		$bind_values = [
			":id" => $id,
		];
        $row = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		if ($row) {
			$admin = self::mapAdmin($row);
		}
		else {
			$admin = null;
		}
        return $admin;
    }
	
	// get admin object from a user_id
	public static function getAdminFromUser($user_id) {
		$query = 'SELECT admins.* FROM admins, users
                  WHERE users.id = :user_id
				  AND admins.user_id = users.id
				  LIMIT 1';    
		$bind_values = [
			":user_id" => $user_id
		];
        $row = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		if ($row) {
			$admin = self::mapAdmin($row);
		}
		else {
			$admin = null;
		}
        return $admin;
	}
	
	// maps a row from admins table to an Admin object
	public static function mapAdmin($row) {
		$admin = new Admin(
			$row['user_id'],
			$row['first_name'],
			$row['last_name'],
			$row['email']
		);
		$admin->setId($row['id']);
		return $admin;
	}
}
?>