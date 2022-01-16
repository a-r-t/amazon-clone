<?php
require_once('database.php');
require_once('product.php');

// Class for interacting with products table in Database
class ProductDb {
	/*
		Product Object Schema:
		- id
		- name
		- description
		- price
		- image_path
		- quantity (Note, not a part of constructor -- data must come from elsewhere and be set)
	*/

	// Get all products
    public static function getProducts($order_by="name") {
        $query = 'SELECT * FROM products
                  ORDER BY :order_by';
		$bind_values = [
			":order_by" => $order_by
		];
        $rows = Database::runQuery($query, $bind_values=$bind_values);
        $products = self::mapProducts($rows);
        return $products;
    }

	// Get product with matching id attribute
    public static function getProduct($id) {
        $query = 'SELECT * FROM products
                  WHERE id = :id
				  LIMIT 1';    
		$bind_values = [
			":id" => $id
		];
        $row = Database::runQuery($query, $bind_values=$bind_values, $multi=false);
		if ($row) {
			$product = self::mapProduct($row);
		}
		else {
			$product = null;
		}
        return $product;
    }
	
	// Get all products with matching name attribute
    public static function getProductsByName($name, $order_by="name") {		
        $query = 'SELECT * FROM products
                  WHERE name = :name
                  ORDER BY :order_by';
		$bind_values = [
			":name" => $name,
			":order_by" => $order_by
		];        
		$rows = Database::runQuery($query, $bind_values=$bind_values);
        $products = self::mapProducts($rows);
        return $products;
    }
	
	// Get all products with matching description attribute
    public static function getProductsByDescription($description) {		
        $query = 'SELECT * FROM products
                  WHERE description = :description
                  ORDER BY name';
		$bind_values = [
			":description" => $description
		];        
		$rows = Database::runQuery($query, $bind_values=$bind_values);
        $products = self::mapProducts($rows);
        return $products;
	}
	
	// Get all products where price attribute is equal to or less than price maximum
    public static function getProductsByPriceRange($price_max, $order_by="name") {		
        $query = 'SELECT * FROM products
                  WHERE price <= :price_max
                  ORDER BY :order_by';
		$bind_values = [
			":price_max" => $price_max,
			":order_by" => $order_by
		];        
		$rows = Database::runQuery($query, $bind_values=$bind_values);
        $products = self::mapProducts($rows);
        return $products;
	}
	
	// Add product to database
	public static function addProduct($product) {
        $query = 'INSERT INTO products
					(name, description, price, image_path)
                  VALUES
					(:name, :description, :price, :image_path)';
		$bind_values = [
			":name" => $product->getName(),
			":description" => $product->getDescription(),
			":price" => $product->getPrice(),
			":image_path" => $product->getImagePath()
		];
		Database::runQuery($query, $bind_values=$bind_values);
    }
	
	// Delete product from database
    public static function deleteProduct($id) {
        $query = 'DELETE FROM product
                  WHERE id = :id';
		$bind_values = [
			":id" => $id
		];        
		Database::runQuery($query, $bind_values=$bind_values);
	}
	
	// Update product in database
	public static function updateProduct($product) {
		$query = 'UPDATE
					products
				  SET
					name = :name,
					description = :description,
					price = :price,
					image_path = :image_path
				  WHERE
					id = :id';					
		$bind_values = [
			":id" => $product->getId(),
			":name" => $product->getName(),
			":description" => $product->getDescription(),
			":price" => $product->getPrice(),
			":image_path" => $product->getImagePath()
		];
		Database::runQuery($query, $bind_values=$bind_values);
    }
	
	// maps rows from products table to an array of Product objects
	public static function mapProducts($rows) {
		$products = array();
		foreach ($rows as $row) {
            $product = self::mapProduct($row);
            $products[] = $product;
        }
		return $products;
	}

	// maps a row from products table to a Product object
	public static function mapProduct($row) {
		$product = new Product(
			$row['name'],
			$row['description'],
			$row['price'],
			$row['image_path']
		);
		$product->setId($row['id']);
		if (isset($row['quantity'])) {
			$product->setQuantity($row['quantity']);
		}
		return $product;
	}
}
?>