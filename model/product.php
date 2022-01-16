<?php

// Represents a product in the products table
class Product {
    private $id;
    private $name;
	private $description;
	private $price;
	private $image_path;
	private $quantity;

    public function __construct($name, $description, $price, $image_path) {
        $this->name = $name;
        $this->description = $description;
		$this->price = $price;
		$this->image_path = $image_path;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($value) {
        $this->id = $value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;
    }
	
    public function getDescription() {
        return $this->description;
    }

    public function setDescription($value) {
        $this->description = $value;
    }
	
    public function getPrice() {
        return $this->price;
    }

    public function setPrice($value) {
        $this->price = $value;
    }
	
	public function getImagePath() {
        return $this->image_path;
    }

    public function setImagePath($value) {
        $this->image_path = $value;
	}
	
	public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($value) {
        $this->quantity = $value;
    }
	
	public function addToQuantity($number) {
		$this->quantity += $number;
	}
	
	public function subtractFromQuantity($number) {
		$this->quantity -= $number;
		if ($this->quantity < 0) {
			$this->quantity = 0;
		}
	}
	
	public function hasCount() {
		return $this->quantity > 0;
	}
	
	public function getTotalPrice() {
		if ($this->quantity) {
			return $this->price * $this->quantity;
		}
		else {
			return $this->price;
		}
	}
	
	// Used for serialization to JSON/XML for rest api
	public function toDict() {
		return array(
			"id" => $this->id,
			"name" => $this->name,
			"description" => $this->description,
			"price" => $this->price
		);
	}
}
?>