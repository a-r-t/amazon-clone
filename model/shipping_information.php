<?php

// Represents a customer's shipping information in the shipping_information table
class ShippingInformation {
    private $id;
	private $customer_id;
	private $country;
    private $address_1;
	private $address_2;
	private $city;
	private $state;
	private $zip_code;
	private $name;
	
    public function __construct($customer_id, $country, $address_1, $address_2, $city, $state, $zip_code, $name) {
        $this->customer_id = $customer_id;
		$this->country = $country;
        $this->address_1 = $address_1;
		$this->address_2 = $address_2;
		$this->city = $city;
		$this->state = $state;
		$this->zip_code = $zip_code;
		$this->name = $name;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($value) {
        $this->id = $value;
    }
	
    public function getCustomerId() {
        return $this->customer_id;
    }

    public function setCustomerId($value) {
        $this->customer_id = $value;
    }

    public function getCountry() {
        return $this->country;
    }

    public function setCountry($value) {
        $this->country = $value;
    }
	
    public function getAddress1() {
        return $this->address_1;
    }

    public function setAddress1($value) {
        $this->address_1 = $value;
    }
	
    public function getAddress2() {
        return $this->address_2;
    }

    public function setAddress2($value) {
        $this->address_2 = $value;
    }
	
    public function getCity() {
        return $this->city;
    }

    public function setCity($value) {
        $this->city = $value;
    }
	
    public function getState() {
        return $this->state;
    }

    public function setState($value) {
        $this->state = $value;
    }
	
    public function getZipCode() {
        return $this->zip_code;
    }

    public function setZipCode($value) {
        $this->zip_code = $value;
    }
	
    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;
    }
}
?>