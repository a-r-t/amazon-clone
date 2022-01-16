<?php

// Represents a customer's cart in the carts table
class Cart {
    private $id;
	private $customer_id;
	private $products;
	
    public function __construct($customer_id) {
        $this->customer_id = $customer_id;
        $this->products = [];
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
	
	// below are functions to interact with the products currently in a customer's cart
	public function getProducts() {
        return $this->products;
    }
	
	public function getProduct($product_id) {
		foreach ($this->products as $product) {
			if ($product->getId() === $product_id) {
				return $product;
			}
		}
		return null;
	}
	
    public function addToProducts($product) {
		$cart_product = self::getProduct($product->getId());
		if ($cart_product) {
			$cart_product->addToQuantity($product->getQuantity());
		}
		else {
			$this->products[] = $product;
		}
    }
	
	public function removeFromProducts($product_id, $quantity) {
		for ($i = 0, $size = count($this->products); $i <= $size; ++$i) {
			$product = $this->products[$i];
			if ($product->id == $product_id) {
				$product->subtractFromQuantity($quantity);
				if (!$product->hasCount()) {
					unset($product);
				}
				break;
			}
		}
	}
	
	public function getTotalCost() {
		$total = 0;
		foreach ($this->products as $product) {
			$total += $product->getTotalPrice();
		}
		return $total;
	}
	
	public function getCartItemNumber() {
		$total = 0;
		foreach ($this->products as $product) {
			$total += $product->getQuantity();
		}
		return $total;
	}
}
?>