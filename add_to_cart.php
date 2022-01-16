<?php
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	if (!isset($_SESSION["customer_id"])) {
		header("Location: login.php");
		die();
	}
?>

<?php
	require_once('model/cart_db.php');
	require_once('model/product_db.php');
	require_once('model/store_db.php');
?>

<?php
	/*
	 * Adds product to cart if it does not already exist with posted quantity
	 * If product in cart, apply posted quantity modifier to its current quantity
	*/
	if (isset($_POST['add_to_cart'])) {
		$customer_id = $_SESSION["customer_id"];
		$store_product_quantity = StoreDb::getStoreProductQuantity($_POST['product_id']);
		
		// This just checks if customer already has product in cart yet or not
		// If customer already has product in cart, it calculates its quantity plus the posted quantity modifier
		$total_quantity = $_POST['quantity'];
		if (CartDb::isCustomerCartStarted($customer_id)) {
			$cart = CartDb::getCustomerCart($customer_id);
			$cart_product = $cart->getProduct($_POST['product_id']);
			if ($cart_product) {
				$cart_product_quantity = $cart_product->getQuantity();
				$total_quantity = $cart_product_quantity + $_POST['quantity'];
			}
		}
		
		// If you try to add more products to your cart than the store has in stock, you will get an error
		if ($store_product_quantity - $total_quantity < 0) {
			$product = ProductDb::getProduct($_POST['product_id']);
			$_SESSION["add_to_cart_error"] = sprintf('Unable to add %s %s because store only has %s in stock!', $_POST['quantity'], $product->getName(), $store_product_quantity);;
		}
		// Adds product to customer's cart
		else {
			$cart = CartDb::getCustomerCart($customer_id);
			CartDb::updateCartProduct($cart->getId(), $_POST['product_id'], $_POST['quantity']);
			unset($_SESSION["add_to_cart_error"]);
		}
	}
	
	header("Location: view_products.php");
	die();
?>


