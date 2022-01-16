<?php
require_once('database.php');
require_once('billing_information.php');


// Class for interacting with billing information table in Database
class BillingInformationDb {
	/*
		Billing Information Object Schema:
		- id
		- customer_id
		- country
		- address_1
		- address_2
		- city
		- state
	    - zip_code
	    - card_type
		- card_name
		- card_last_four_digits
		- card_hash
    */
	
	// Get all billing information with a matching customer_id
	public static function getCustomerBillingInformations($customer_id) {
		$query = 'SELECT * 
				  FROM billing_information
				  WHERE customer_id = :customer_id
				  ORDER BY id';
		$bind_values = [
			":customer_id" => $customer_id
		];
		$rows = Database::runQuery($query, $bind_values=$bind_values);
		$billing_informations = self::mapBillingInformations($rows);
		return $billing_informations;
	}
	
	// Get a specific billing information with a matching customer_id and billing_information_id
	public static function getCustomerBillingInformation($customer_id, $billing_information_id) {
		$query = 'SELECT * 
				  FROM billing_information
				  WHERE customer_id = :customer_id
				  AND id = :billing_information_id
				  LIMIT 1';
		$bind_values = [
			":customer_id" => $customer_id,
			":billing_information_id" => $billing_information_id
		];
		$row = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		if ($row) {
			$billing_information = self::mapBillingInformation($row);
		}
		else {
			$billing_information = null;
		}
		return $billing_information;
	}
	
	// Adds a row to billing_information table from a billing_information object
	public static function addBillingInformation($billing_information) {
        $query = 'INSERT INTO billing_information
					(customer_id, country, address_1, address_2, city, state, zip_code, card_type, card_name, card_last_four_digits, card_hash)
                  VALUES
					(:customer_id, :country, :address_1, :address_2, :city, :state, :zip_code, :card_type, :card_name, :card_last_four_digits, :card_hash)';
		$bind_values = [
			":customer_id" => $billing_information->getCustomerId(),
			":country" => $billing_information->getCountry(),
			":address_1" => $billing_information->getAddress1(),
			":address_2" => $billing_information->getAddress2(),
			":city" => $billing_information->getCity(),
			":state" => $billing_information->getState(),
			":zip_code" => $billing_information->getZipCode(),
			":card_type" => $billing_information->getCardType(),
			":card_name" => $billing_information->getCardName(),
			":card_last_four_digits" => $billing_information->getCardLastFourDigits(),
			":card_hash" => $billing_information->getCardHash()
		];
		$id_inserted = Database::runQuery($query, $bind_values=$bind_values, $multi=false, $insert=true)[0];
		return $id_inserted;
    }
	
	// Delete billing information from database with matching billing_information_id
    public static function deleteBillingInformation($id) {
        $query = 'DELETE FROM billing_information
                  WHERE id = :id';
		$bind_values = [
			":id" => $id
		];        
		Database::runQuery($query, $bind_values=$bind_values);
	}
	
	// Update billing_information in database from a billing_information object
	public static function updateBillingInformation($billing_information) {
		$query = 'UPDATE
					billing_information
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
			":id" => $billing_information->getId(),
			":customer_id" => $billing_information->getCustomerId(),
			":country" => $billing_information->getCountry(),
			":address_1" => $billing_information->getAddress1(),
			":address_2" => $billing_information->getAddress2(),
			":city" => $billing_information->getCity(),
			":state" => $billing_information->getState(),
			":zip_code" => $billing_information->getZipCode(),
			":name" => $billing_information->getName()
		];
		Database::runQuery($query, $bind_values=$bind_values);
    }
	
	// maps a list of rows from admins table to Admin objects
	public static function mapBillingInformations($rows) {
		$billing_informations = array();
		foreach ($rows as $row) {
            $billing_information = self::mapBillingInformation($row);
            $billing_informations[] = $billing_information;
        }
		return $billing_informations;
	}
	
	// maps a row from billing_information table to a billing_information object
	private static function mapBillingInformation($row) {
		$billing_information = new BillingInformation(
			$row['customer_id'],
			$row['country'],
			$row['address_1'],
			$row['address_2'],
			$row['city'],
			$row['state'],
			$row['zip_code'],
			$row['card_type'],
			$row['card_name'],
			$row['card_last_four_digits'],
			$row['card_hash']
		);
		$billing_information->setId($row['id']);
		return $billing_information;
	}
	
}
?>