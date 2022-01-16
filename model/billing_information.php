<?php
// Represents a customer's billing information in the billing_information table
class BillingInformation {
    private $id;
	private $customer_id;
	private $country;
    private $address_1;
	private $address_2;
	private $city;
	private $state;
	private $zip_code;
	private $card_type;
	private $card_name;
    private $card_last_four_digits;
	
	// this is the credit card number, expiration month and year, and cvv all hashed together
	private $card_hash;

    public function __construct($customer_id, $country, $address_1, $address_2, $city, $state, $zip_code, $card_type, $card_name, $card_last_four_digits, $card_hash) {
        $this->customer_id = $customer_id;
		$this->country = $country;
        $this->address_1 = $address_1;
		$this->address_2 = $address_2;
		$this->city = $city;
		$this->state = $state;
		$this->zip_code = $zip_code;
		$this->card_type = $card_type;
		$this->card_name = $card_name;
		$this->card_last_four_digits = $card_last_four_digits;
		$this->card_hash = $card_hash;
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
	
    public function getCardType() {
        return $this->card_type;
    }

    public function setCardType($value) {
        $this->card_type = $value;
    }
	
	public function getCardName() {
        return $this->card_name;
    }

    public function setCardName($value) {
        $this->card_name = $value;
    }
	
	public function getCardLastFourDigits() {
        return $this->card_last_four_digits;
    }

    public function setCardLastFourDigits($value) {
        $this->card_last_four_digits = $value;
    }
	
	public function getCardHash() {
        return $this->card_hash;
    }

    public function setCardHash($value) {
        $this->card_hash = $value;
    }
	
	// returns "card_type - card_name - ****-****-****-card_last_four_digits"
	public function getCardTitle() {
		return sprintf("%s - %s - ****-****-****-%s", $this->card_type, $this->card_name, $this->card_last_four_digits);
	}
}
?>