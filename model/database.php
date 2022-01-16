<?php
// Class for connecting to a database using PDO
// Run setup_database script first!!!!
class Database {
	// Connects to shopping_db database with user admin (both created in setup_database script)
    private static $dsn = 'mysql:host=localhost;dbname=shopping_db';
    private static $username = 'admin';
    private static $password = 'admin_password';

    private static $db;

    private function __construct() {}

	// Returns PDO connection instance to database
    private static function getDB () {
        if (!isset(self::$db)) {
            try {
                self::$db = new PDO(
					self::$dsn,
					self::$username,
					self::$password
				);
            } catch (PDOException $e) {
                $error_message = $e->getMessage();
                include(dirname(__FILE__).'/../errors/database_error.php');
                exit();
            }
        }
        return self::$db;
    }
	
	/*
	 * Runs query against database
	 * Pass bind_values an associative array of key to value for it to bind those values in sql statement
	 * By default, function will return a list of rows that the sql statement returns
	 * Set multi to true if you want a list of rows back, false for only one row
	 * Set insert to true to return the id of the newly inserted row after an insert statement
	*/
	public static function runQuery($query, $bind_values=[], $multi=true, $insert=false) {
		$statement = self::getDB()->prepare($query);
		foreach ($bind_values as $bind=>$value) {
			$statement->bindValue($bind, $value);
		}
		$statement->execute();

		if ($insert) {
			return self::getDB()->lastInsertId();
		}
		else {
			if ($multi) {
				$rows = $statement->fetchAll();
			}
			else {
				$rows = $statement->fetch();
			}
			return $rows;
		}
	}
}
?>
