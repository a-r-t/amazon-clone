<?php
require_once('database.php');
require_once('customer.php');

// Class for interacting with customers table in Database
class CustomerDb {
	/*
		Customer Object Schema:
        - id
		- user_id
        - first_name
	    - last_name
	    - email
	*/

	// Get all customers
    public static function getCustomers() {
        $query = 'SELECT * FROM customers
                  ORDER BY last_name, first_name';
        $rows = Database::runQuery($query);
        $customers = self::mapCustomers($rows);
        return $customers;
    }

	// Get customer with matching id attribute
    public static function getCustomer($id) {
        $query = 'SELECT * FROM customers
                  WHERE id = :id
				  LIMIT 1';    
		$bind_values = [
			":id" => $id,
		];
        $row = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		if ($row) {
			$customer = self::mapCustomer($row);
		}
		else {
			$customer = null;
		}
        return $customer;
    }
	
	// Add customer to database
	public static function addCustomer($customer) {
        $query = 'INSERT INTO customers
					(user_id, first_name, last_name, email)
                  VALUES
					(:user_id, :first_name, :last_name, :email)';
		$bind_values = [
			":user_id" => $customer->getUserId(),
			":first_name" => $customer->getFirstName(),
			":last_name" => $customer->getLastName(),
			":email" => $customer->getEmail()
		];
		Database::runQuery($query, $bind_values=$bind_values);
    }
	
	// Delete customer from database
    public static function deleteCustomer($id) {
        $query = 'DELETE FROM customers
                  WHERE id = :id';
		$bind_values = [
			":id" => $id
		];        
		Database::runQuery($query, $bind_values=$bind_values);
	}
	
	// Update customer in database
	public static function updateCustomer($customer) {
		$query = 'UPDATE
					customers
				  SET
				    user_id = :user_id
					first_name = :first_name,
					last_name = :last_name,
					email = :email,
                  WHERE
					id = :id';					
		$bind_values = [
			":id" => $customer->id(),
			":user_id" => $customer->getUserId(),
			":first_name" => $customer->getFirstName(),
			":last_name" => $customer->getLastName(),
			":email" => $customer->getEmail()
		];
		Database::runQuery($query, $bind_values=$bind_values);
    }
	
	// Get the customer associated with a user
	public static function getCustomerFromUser($user_id) {
		$query = 'SELECT customers.* FROM customers, users
                  WHERE users.id = :user_id
				  AND customers.user_id = users.id
				  LIMIT 1';    
		$bind_values = [
			":user_id" => $user_id
		];
        $row = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		if ($row) {
			$customer = self::mapCustomer($row);
		}
		else {
			$customer = null;
		}
        return $customer;
	}
	
	// Get all customers where first name or last name match the specified filter
	public static function filterCustomers($filter) {		
        $query = "SELECT customers.*
				  FROM customers
                  WHERE (customers.last_name LIKE :filter
				  OR customers.first_name LIKE :filter)
                  ORDER BY customers.last_name, customers.first_name";
		$bind_values = [
			":filter" => '%'.$filter.'%'
		];        
		$rows = Database::runQuery($query, $bind_values=$bind_values);
        $customers = CustomerDb::mapCustomers($rows);
        return $customers;
    }
	
	// maps rows from customers table to an array of Customer objects
	private static function mapCustomers($rows) {
		$customers = array();
		foreach ($rows as $row) {
            $customer = self::mapCustomer($row);
            $customers[] = $customer;
        }
		return $customers;
	}

	// maps a row from customers table to a Customer object
	private static function mapCustomer($row) {
		$customer = new Customer(
			$row['user_id'],
			$row['first_name'],
			$row['last_name'],
			$row['email']
		);
		$customer->setId($row['id']);
		return $customer;
	}
}
?>