<?php
require_once('database.php');
require_once('shipping_information.php');


// Class for interacting with shipping information table in Database
class ShippingInformationDb {
	/*
		Shipping Information Schema:
		- id
		- country
		- address_1
		- address_2
		- city
		- state
		- zip_code
		- name
	*/
	
	// Get all shipping information associated with a customer
	public static function getCustomerShippingInformations($customer_id) {
		$query = 'SELECT * 
				  FROM shipping_information
				  WHERE customer_id = :customer_id
				  ORDER BY id';
		$bind_values = [
			":customer_id" => $customer_id
		];
		$rows = Database::runQuery($query, $bind_values=$bind_values);
		$shipping_informations = self::mapShippingInformations($rows);
		return $shipping_informations;
	}
	
	// Get specified customer shipping information
	public static function getCustomerShippingInformation($customer_id, $shipping_information_id) {
		$query = 'SELECT * 
				  FROM shipping_information
				  WHERE customer_id = :customer_id
				  AND id = :shipping_information_id
				  LIMIT 1';
		$bind_values = [
			":customer_id" => $customer_id,
			":shipping_information_id" => $shipping_information_id
		];
		$row = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		if ($row) {
			$shipping_information = self::mapShippingInformation($row);
		}
		else {
			$shipping_information = null;
		}
		return $shipping_information;
	}
	
	// Add shipping information to database
	public static function addShippingInformation($shipping_information) {
        $query = 'INSERT INTO shipping_information
					(customer_id, country, address_1, address_2, city, state, zip_code, name)
                  VALUES
					(:customer_id, :country, :address_1, :address_2, :city, :state, :zip_code, :name)';
		$bind_values = [
			":customer_id" => $shipping_information->getCustomerId(),
			":country" => $shipping_information->getCountry(),
			":address_1" => $shipping_information->getAddress1(),
			":address_2" => $shipping_information->getAddress2(),
			":city" => $shipping_information->getCity(),
			":state" => $shipping_information->getState(),
			":zip_code" => $shipping_information->getZipCode(),
			":name" => $shipping_information->getName()
		];
		$id_inserted = Database::runQuery($query, $bind_values=$bind_values, $multi=false, $insert=true)[0];
		return $id_inserted;
    }
	
	// Delete shipping information from database
    public static function deleteShippingInformation($id) {
        $query = 'DELETE FROM shipping_information
                  WHERE id = :id';
		$bind_values = [
			":id" => $id
		];        
		Database::runQuery($query, $bind_values=$bind_values);
	}
	
	// Update shipping information in database
	public static function updateShippingInformation($shipping_information) {
		$query = 'UPDATE
					shipping_information
				  SET
					customer_id = :customer_id,
					country = :country,
					address_1 = :address_1,
					address_2 = :address_2,
					city = :city,
					state = :state,
					zip_code = :zip_code,
					name = :name
                  WHERE
					id = :id';					
		$bind_values = [
			":id" => $shipping_information->getId(),
			":customer_id" => $shipping_information->getCustomerId(),
			":country" => $shipping_information->getCountry(),
			":address_1" => $shipping_information->getAddress1(),
			":address_2" => $shipping_information->getAddress2(),
			":city" => $shipping_information->getCity(),
			":state" => $shipping_information->getState(),
			":zip_code" => $shipping_information->getZipCode(),
			":name" => $shipping_information->getName()
		];
		Database::runQuery($query, $bind_values=$bind_values);
    }
	
	// maps rows from shipping information table to ShippingInformation objects
	public static function mapShippingInformations($rows) {
		$shipping_informations = array();
		foreach ($rows as $row) {
            $shipping_information = self::mapShippingInformation($row);
            $shipping_informations[] = $shipping_information;
        }
		return $shipping_informations;
	}
	
	// maps a row from shipping information table to a ShippingInformation object
	private static function mapShippingInformation($row) {
		$shipping_information = new ShippingInformation(
			$row['customer_id'],
			$row['country'],
			$row['address_1'],
			$row['address_2'],
			$row['city'],
			$row['state'],
			$row['zip_code'],
			$row['name']
		);
		$shipping_information->setId($row['id']);
		return $shipping_information;
	}
	
}
?>