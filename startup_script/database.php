<?php
// Class for connecting to a database using PDO
class Database {
	// connect to mysql as root
    private static $dsn = 'mysql:host=localhost;';
    private static $username = 'root';
    private static $password = '';

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
	
	// Runs a query against a database
	public static function runQuery($query, $bind_values=[], $multi=true) {
		$statement = self::getDB()->prepare($query);
		foreach ($bind_values as $bind=>$value) {
			$statement->bindValue($bind, $value);
		}
		$statement->execute();
		$statement->closeCursor();
	}
}
?>
