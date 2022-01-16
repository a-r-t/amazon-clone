<?php

require_once('database.php');
require_once('cart.php');
require_once('product_db.php');


// Class for interacting with carts table in Database
class CartDb {
	/*
		Cart Object Schema:
		- id
		- customer_id
		- products
	*/
	
	/*
	 * Get customer's cart with a matching customer_id
	 * If customer does not have a cart yet, add one to database for customer
	*/
	public static function getCustomerCart($customer_id) {
        $cart_info = self::getCartInfo($customer_id);
		if ($cart_info) {
			$query = 'SELECT products.*, carts_products.quantity 
					  FROM carts_products, products
					  WHERE carts_products.cart_id = :cart_id
					  AND products.id = carts_products.product_id
					  ORDER BY products.name';
			$bind_values = [
				":cart_id" => $cart_info['id']
			];
			$products = Database::runQuery($query, $bind_values=$bind_values);
		}
		else {
			self::addCart($customer_id);
			$cart_info = self::getCartInfo($customer_id);
			$products = [];
		}
		$cart = self::mapCart($cart_info, $products);
        return $cart;
    }
	
	// Gets all products in a cart with a matching cart_id
	private static function getCartProducts($cart_id) {
		$query = 'SELECT products.*
				  FROM carts_products, products
				  WHERE carts_products.cart_id = :cart_id
				  AND products.id = carts_products.product_id
				  ORDER BY products.name';
		$bind_values = [
			":cart_id" => $cart_id
		];
		$rows = Database::runQuery($query, $bind_values=$bind_values);
		$products = ProductDb::mapProducts($rows);
		return $products;
	}
	
	// Gets how many products are in a cart with a matching cart_id
	private static function getCartProductsCount($cart_id) {
		$query = 'SELECT count(*)
				  FROM carts_products
				  WHERE carts_products.cart_id = :cart_id';
		$bind_values = [
			":cart_id" => $cart_id
		];
		$count = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		return $count[0];
	}
	
	// Gets a specific product in a cart with matching cart_id and product_id
	public static function getCartProduct($cart_id, $product_id) {
		$query = 'SELECT products.*, carts_products.quantity
				  FROM carts_products, products
				  WHERE carts_products.cart_id = :cart_id
				  AND carts_products.product_id = :product_id
				  AND products.id = carts_products.product_id
				  LIMIT 1';
		$bind_values = [
			":cart_id" => $cart_id,
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
	
	// Get cart info with matching customer_id (info includes everything but products)
	private static function getCartInfo($customer_id) {
		$query = 'SELECT * FROM carts
				  WHERE customer_id = :customer_id';
		$bind_values = [
			":customer_id" => $customer_id,
		];
        return Database::runQuery($query, $bind_values=$bind_values, $multi=false);
	}

	// Check if a customer with matching customer_id currently has a cart started yet or not
	public static function isCustomerCartStarted($customer_id) {
		$query = 'SELECT * FROM carts
				  WHERE customer_id = :customer_id';
		$bind_values = [
			":customer_id" => $customer_id,
		];
        $cart = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		if ($cart) {
			return true;
		}
		else {
			return false;
		}
	}

	// Add cart to database
	private static function addCart($customer_id) {
        $query = 'INSERT INTO carts
					(customer_id)
                  VALUES
					(:customer_id)';
		$bind_values = [
			":customer_id" => $customer_id,
		];
		Database::runQuery($query, $bind_values=$bind_values);
    }
	
	// Delete cart from database
	public static function deleteCart($cart_id) {
		$query = 'DELETE FROM carts_products
                  WHERE cart_id = :cart_id';
		$bind_values = [
			":cart_id" => $cart_id
		];        
		Database::runQuery($query, $bind_values=$bind_values);
		
		$query = 'DELETE FROM carts
                  WHERE id = :cart_id';
		$bind_values = [
			":cart_id" => $cart_id
		];        
		Database::runQuery($query, $bind_values=$bind_values, $multi=false);
	}
	
	// Add a product with a specified quantity to cart
	private static function addCartProduct($cart_id, $product_id, $quantity) {
		$query = 'INSERT INTO carts_products
					(cart_id, product_id, quantity)
				  VALUES
					(:cart_id, :product_id, :quantity)';
		$bind_values = [
			":cart_id" => $cart_id,
			":product_id" => $product_id,
			":quantity" => $quantity
		];
		Database::runQuery($query, $bind_values=$bind_values);
	}
	
	// Set a product's quantity in a specified cart
	private static function setCartProductQuantity($cart_id, $product_id, $quantity) {
		$query = 'UPDATE
				    carts_products
				  SET
					quantity = :quantity
				  WHERE
					cart_id = :cart_id
				  AND
					product_id = :product_id';
		$bind_values = [
			":cart_id" => $cart_id,
			":product_id" => $product_id,
			":quantity" => $quantity
		];
		Database::runQuery($query, $bind_values=$bind_values);
	}
	
	/*
	 * Update a product's quantity in a specified cart
	 * If cart does not currently have specified product in cart, add it to cart with specified quantity (as long as quantity is more than 0)
	 * If cart already has product in cart, apply the quantity_modifier to the quantity currently in the cart (can either add or subtract)
	 * If the result of the cart_quantity with the applied quantity_modifier is 0 or less, delete the product from the cart
	 * If after a product is deleted a cart has no items in it, delete the cart
	*/
	public static function updateCartProduct($cart_id, $product_id, $quantity_modifier) {
		$product = self::getCartProduct($cart_id, $product_id);
		if ($product) {
			$new_quantity = $product->getQuantity() + $quantity_modifier;
			if ($new_quantity > 0) {
				self::setCartProductQuantity($cart_id, $product_id, $new_quantity);
			}
			else {
				self::deleteCartProduct($cart_id, $product_id);
				if (self::getCartProductsCount($cart_id) < 1) {
					self::deleteCart($cart_id);
				}
			}
		}
		else {
			if ($quantity_modifier > 0) {
				self::addCartProduct($cart_id, $product_id, $quantity_modifier);
			}
		}
	}
	
	// Delete a specified product from a cart
	public static function deleteCartProduct($cart_id, $product_id) {
		$query = 'DELETE FROM carts_products
                  WHERE cart_id = :cart_id
				  AND product_id = :product_id';
		$bind_values = [
			":cart_id" => $cart_id,
			":product_id" => $product_id
		];        
		Database::runQuery($query, $bind_values=$bind_values);
	}

	// Check if a specified product is currnetly in a cart
	private static function isProductInCart($cart_id, $product_id) {
		$query = 'SELECT * FROM carts_products
		          WHERE cart_id = :cart_id
				  AND product_id = :product_id';
		$bind_values = [
			":cart_id" => $cart_id,
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

	// maps customer cart info and product data to a Cart object
	private static function mapCart($cart_info, $products) {
		
		$cart = new Cart(
			$cart_info['customer_id'],
		);
		$cart->setId($cart_info['id']);
		foreach (ProductDb::mapProducts($products) as $product) {
			$cart->addToProducts($product);
		}
		return $cart;
	}
}
?>