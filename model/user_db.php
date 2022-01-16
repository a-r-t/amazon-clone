<?php

require_once('user.php');
require_once('database.php');


// Class for interacting with users table in Database
class UserDb {
	
	/*
		User Object Schema:
		- id
		- role
	*/
	
	/*
	 * Called when attempting to login
	 * If username/password is valid, gets user's assigned role from users_roles table
	*/
	public static function getUserFromLoginHash($login_hash) {
		$query = 'SELECT id FROM users
				  WHERE login_hash = :login_hash
				  LIMIT 1';
		  
		$bind_values = [
			":login_hash" => $login_hash,
		];
        $user_id = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		if ($user_id) {
			$role = self::getUserRole($user_id[0]);
			$user = self::mapUser($user_id[0], $role);
			return $user;
		}
		else {
			return null;
		}
	}
	
	// Get specified user's role
	public static function getUserRole($user_id) {
		$query = 'SELECT roles.* 
				  FROM users_roles, roles
				  WHERE users_roles.user_id = :user_id
				  AND roles.id = users_roles.role_id
				  LIMIT 1';
		  
		$bind_values = [
			":user_id" => $user_id,
		];
        $row = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		return $row['role'];
	}
	
	// map row from users table to a User object
	public static function mapUser($user_id, $role) {
		$user = new User(
			$role
		);
		$user->setId($user_id);
		return $user;
	}
	
}
?>