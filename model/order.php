<?php

// Represents a order in the orders table
class Order {
    private $id;
    private $customer_id;
	private $billing_information_id;
	private $shipping_information_id;
	private $order_timestamp;
	private $products;

    public function __construct($customer_id, $billing_information_id, $shipping_information_id, $order_timestamp) {
        $this->customer_id = $customer_id;
		$this->billing_information_id = $billing_information_id;
		$this->shipping_information_id = $shipping_information_id;
        $this->order_timestamp = $order_timestamp;
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
	
    public function getBillingInformationId() {
        return $this->billing_information_id;
    }

    public function setBillingInformationId($value) {
        $this->billing_information_id = $value;
    }
	
    public function getShippingInformationId() {
        return $this->shipping_information_id;
    }

    public function setShippingInformationId($value) {
        $this->shipping_information_id = $value;
    }
	
    public function getOrderTimestamp() {
        return $this->order_timestamp;
    }

    public function setOrderTimestamp($value) {
        $this->order_timestamp = $value;
    }
	
	// Below functions are for interacting with the products in an order
    public function getProducts() {
        return $this->products;
    }

    public function setProducts($value) {
        $this->products = $value;
    }
	
	public function getProduct($product_id) {
		foreach ($this->products as $product) {
			if ($product->getId() == $product_id) {
				return $product;
			}
		}
		return null;
	}
	
	public function getProductQuantity($product_id) {
		foreach ($this->products as $product) {
			if ($product->getId() == $product_id) {
				return $product->getQuantity();
			}
		}
		return 0;
	}
	
    public function addToProducts($product) {
		$order_product = self::getProduct($product->getId());
		if ($order_product) {
			$order_product->addToQuantity($product->getQuantity());
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
	
	public function getOrderItemNumber() {
		$total = 0;
		foreach ($this->products as $product) {
			$total += $product->getQuantity();
		}
		return $total;
	}
}
?>