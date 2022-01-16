<?php
require_once('database.php');
require_once('order.php');
require_once('product_db.php');


// Class for interacting with orders table in Database
class OrderDb {
	/*
		Order Object Schema:
		- id
		- customer_id
		- billing_information_id
		- shipping_information_id
		- order_timestamp
		- products
	*/
	
	// Get all of a customer's orders
	public static function getCustomerOrders($customer_id) {
        $order_infos = self::getOrderInfos($customer_id);
		if ($order_infos) {
			$orders = [];
			foreach ($order_infos as $order_info) {
				$query = 'SELECT products.*, orders_products.quantity
						  FROM orders_products, products, orders
						  WHERE orders.id = :order_id
						  AND orders_products.order_id = orders.id
						  AND products.id = orders_products.product_id';

				$bind_values = [
					":order_id" => $order_info['id']
				];
				$products = Database::runQuery($query, $bind_values=$bind_values);
				$order = self::mapOrder($order_info, $products);
				$orders[] = $order;
			}
			return $orders;
		}
		else {
			return null;
		}
    }
	
	// Get a specific order from customer
	public static function getCustomerOrder($customer_id, $order_id) {
        $order_info = self::getOrderInfo($customer_id, $order_id);
		if ($order_info) {
			$query = 'SELECT products.*, orders_products.quantity
					  FROM orders_products, products, orders
					  WHERE orders.id = :order_id
					  AND orders_products.order_id = orders.id
					  AND products.id = orders_products.product_id';

			$bind_values = [
				":order_id" => $order_id
			];
			$products = Database::runQuery($query, $bind_values=$bind_values);
			$order = self::mapOrder($order_info, $products);			
			return $order;
		}
		else {
			return null;
		}
    }
	
	// Get all order infos for a customer (infos contain everything but products)
	private static function getOrderInfos($customer_id) {
		$query = 'SELECT * FROM orders
				  WHERE customer_id = :customer_id
				  ORDER BY orders.order_timestamp DESC';
		$bind_values = [
			":customer_id" => $customer_id,
		];
        return Database::runQuery($query, $bind_values=$bind_values);
	}
	
	// Get order info for a specific order (info contains everything but products)
	private static function getOrderInfo($customer_id, $order_id) {
		$query = 'SELECT * FROM orders
				  WHERE customer_id = :customer_id
				  AND id = :order_id
				  LIMIT 1';
		$bind_values = [
			":customer_id" => $customer_id,
			":order_id" => $order_id
		];
        return Database::runQuery($query, $bind_values=$bind_values, $multi=false);
	}
	
	// Get all products in a specified order
	private static function getOrderProducts($order_id) {
		$query = 'SELECT products.*
				  FROM orders_products, products
				  WHERE orders_products.cart_id = :order_id
				  AND products.id = orders_products.product_id
				  ORDER BY products.name';
		$bind_values = [
			":order_id" => $order_id
		];
		$rows = Database::runQuery($query, $bind_values=$bind_values);
		$products = ProductDb::mapProducts($rows);
		return $products;
	}
	
	// Get number of products in an order
	private static function getOrderProductsCount($order_id) {
		$query = 'SELECT count(*)
				  FROM orders_products
				  WHERE orders_products.order_id = :order_id';
		$bind_values = [
			":order_id" => $order_id
		];
		$count = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		return $count[0];
	}
	
	// Get product from a specified order
	private static function getOrderProduct($cart_id, $product_id) {
		$query = 'SELECT products.*, orders_products.quantity
				  FROM orders_products, products
				  WHERE orders_products.order_id = :order_id
				  AND orders_products.product_id = :product_id
				  AND products.id = orders_products.product_id
				  LIMIT 1';
		$bind_values = [
			":order_id" => $cart_id,
			":product_id" => $product_id
		];
		$row = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		if ($row) {
			$product = ProductDb::mapProduct($row);
		}
		else {
			$product = null;
		}
		return $product;
	}

	// Add order to database
	public static function addOrder($order) {
        $query = 'INSERT INTO orders
					(customer_id, billing_information_id, shipping_information_id, order_timestamp)
                  VALUES
					(:customer_id, :billing_information_id, :shipping_information_id, :order_timestamp)';
		
		$bind_values = [
			":customer_id" => $order->getCustomerId(),
			":billing_information_id" => $order->getBillingInformationId(),
			":shipping_information_id" => $order->getShippingInformationId(),
			":order_timestamp" => $order->getOrderTimestamp()
		];
		$id_inserted = Database::runQuery($query, $bind_values=$bind_values, $multi=false, $insert=true)[0];
		
		foreach ($order->getProducts() as $product) {
			self::addOrderProduct($id_inserted, $product->getId(), $product->getQuantity());
		}
    }
	
	// Delete order from database
	public static function deleteOrder($order_id) {
		$query = 'DELETE FROM orders_products
                  WHERE order_id = :order_id';
		$bind_values = [
			":order_id" => $order_id
		];        
		Database::runQuery($query, $bind_values=$bind_values);
		
		$query = 'DELETE FROM orders
                  WHERE id = :order_id';
		$bind_values = [
			":order_id" => $order_id
		];        
		Database::runQuery($query, $bind_values=$bind_values, $multi=false);
	}
	
	// Add a product to an order in database
	private static function addOrderProduct($order_id, $product_id, $quantity) {
		$query = 'INSERT INTO orders_products
					(order_id, product_id, quantity)
				  VALUES
					(:order_id, :product_id, :quantity)';
		$bind_values = [
			":order_id" => $order_id,
			":product_id" => $product_id,
			":quantity" => $quantity
		];
		Database::runQuery($query, $bind_values=$bind_values);
	}
	
	// Update a product in an order in database
	private static function setOrderProductQuantity($order_id, $product_id, $quantity) {
		$query = 'UPDATE
				    orders_products
				  SET
					quantity = :quantity
				  WHERE
					order_id = :order_id
				  AND
					product_id = :product_id';
		$bind_values = [
			":order_id" => $order_id,
			":product_id" => $product_id,
			":quantity" => $quantity
		];
		Database::runQuery($query, $bind_values=$bind_values);
	}
	
	/*
	 * Update an product's quantity in a specified order
	 * If order does not currently have specified product in order, add it to order with specified quantity (as long as quantity is more than 0)
	 * If order already has product in cart, apply the quantity_modifier to the quantity currently in the order (can either add or subtract)
	 * If the result of the order_quantity with the applied quantity_modifier is 0 or less, delete the product from the order
	*/
	public static function updateOrderProduct($order_id, $product_id, $quantity_modifier) {
		$product = self::getOrderProduct($order_id, $product_id);
		if ($product) {
			$new_quantity = $product->getQuantity() + $quantity_modifier;
			if ($new_quantity > 0) {
				self::setOrderProductQuantity($order_id, $product_id, $new_quantity);
			}
			else {
				self::deleteOrderProduct($order_id, $product_id);
			}
		}
		else {
			if ($quantity_modifier > 0) {
				self::addOrderProduct($order_id, $product_id, $quantity_modifier);
			}
		}
	}
	
	// Delete a specified product currently in an order
	public static function deleteOrderProduct($order_id, $product_id) {
		$query = 'DELETE FROM orders_products
                  WHERE order_id = :order_id
				  AND product_id = :product_id';
		$bind_values = [
			":order_id" => $order_id,
			":product_id" => $product_id
		];
		Database::runQuery($query, $bind_values=$bind_values);
	}

	// Check if product is currently in order
	private static function isProductInOrder($order_id, $product_id) {
		$query = 'SELECT * FROM orders_products
		          WHERE order_id = :order_id
				  AND product_id = :product_id';
		$bind_values = [
			":order_id" => $order_id,
			":product_id" => $product_id
		];
		$exists = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		if ($exists) {
			return true;
		}
		else {
			return false;
		}
	}

	// maps customer order info and product data to an Order object
	private static function mapOrder($order_info, $products) {
		$order = new Order(
			$order_info['customer_id'],
			$order_info['billing_information_id'],
			$order_info['shipping_information_id'],
			$order_info['order_timestamp']
		);
		$order->setId($order_info['id']);
		foreach (ProductDb::mapProducts($products) as $product) {
			$order->addToProducts($product);
		}
		return $order;
	}
}
?>