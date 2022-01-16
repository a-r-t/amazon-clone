<?php
require_once('database.php');
require_once('product_db.php');

// Class for interacting with Products in Store
class StoreDb {
	
	// Get all products in store
	public static function getStoreProducts() {
        $query = 'SELECT products.*, products_store.quantity 
				  FROM products, products_store
				  WHERE products.id = products_store.product_id
                  ORDER BY name';
        $rows = Database::runQuery($query);
        $products = ProductDb::mapProducts($rows);
        return $products;
	}
	
	// Get quantity of specified product in store
	public static function getStoreProductQuantity($product_id) {
		$query = 'SELECT products_store.quantity 
		          FROM products_store
		          WHERE products_store.id = :product_id
		          LIMIT 1';
		$bind_values = [
			":product_id" => $product_id,
		];
        $row = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		if ($row) {
			$quantity = $row['quantity'];
		}
		else {
			$quantity = null;
		}
        return $quantity;
	}
	
	// Filters all products in store to where name or description matches filter pattern
    public static function filterProducts($filter) {		
        $query = "SELECT products.*, products_store.quantity
				  FROM products, products_store
				  WHERE products.id = products_store.product_id
                  AND (products.name LIKE :filter
				  OR products.description LIKE :filter)
                  ORDER BY products.name";
		$bind_values = [
			":filter" => '%'.$filter.'%'
		];        
		$rows = Database::runQuery($query, $bind_values=$bind_values);
        $products = ProductDb::mapProducts($rows);
        return $products;
    }
	
	// Update quantity of specific product in store
	public static function updateProductQuantity($product_id, $quantity) {
		if ($quantity < 0) {
			$quantity = 0;
		}
		
		$query = 'UPDATE
				    products_store
				  SET
					quantity = :quantity
				  WHERE
					product_id = :product_id';
					
		$bind_values = [
			":product_id" => $product_id,
			":quantity" => $quantity
		];
		Database::runQuery($query, $bind_values=$bind_values);
	}
	
	// Apply modifier to quantity of item in store
	public static function modifyProductQuantity($product_id, $quantity_modifier) {
		$current_quantity = self::getStoreProductQuantity($product_id);
		if ($current_quantity) {
			$new_quantity = $current_quantity + $quantity_modifier;
			if ($new_quantity < 0) {
				$new_quantity = 0;
			}
		}
		else {
			return null;
		}

		$query = 'UPDATE
				    products_store
				  SET
					quantity = :quantity
				  WHERE
					product_id = :product_id';
					
		$bind_values = [
			":product_id" => $product_id,
			":quantity" => $new_quantity
		];
		Database::runQuery($query, $bind_values=$bind_values);
	}
}


?>