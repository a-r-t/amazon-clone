<?php
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	if (!isset($_SESSION["admin_id"])) {
		header("Location: login.php");
		die();
	}
?>

<?php
	require_once('model/order_db.php');
	require_once('model/product_db.php');
	require_once('model/store_db.php');
?>

<?php
	/*
	 * Adds product to order if it does not already exist with posted quantity
	 * If product in order, apply posted quantity modifier to its current quantity
	*/
	if (isset($_POST['add_to_order'])) {
		$customer_id = $_POST["customer_id"];
		$order_id = $_POST["order_id"];
		$order = OrderDb::getCustomerOrder($customer_id, $order_id);
		$store_product_quantity = StoreDb::getStoreProductQuantity($_POST['product_id']);
		
		$order_product = $order->getProduct($_POST['product_id']);
		if ($order_product) {
			$order_product_quantity = $order_product->getQuantity();
		}
		else {
			$order_product_quantity = 0;
		}
		
		// If you try to add more products to the order than the store has in stock, you will get an error
		if ($store_product_quantity - $order_product_quantity < 0) {
			$product = ProductDb::getProduct($_POST['product_id']);
			$_SESSION["add_to_order_error"] = sprintf('Unable to add %s more %s to order because store only has %s in stock!', $_POST['quantity'], $product->getName(), $store_product_quantity);
		}
		// Add product to customer's cart
		else {
			OrderDb::updateOrderProduct($order->getId(), $_POST['product_id'], $_POST['quantity']);
			StoreDb::modifyProductQuantity($_POST['product_id'], $_POST['quantity'] * -1);
			unset($_SESSION["add_to_order_error"]);
		}
	}
	
	header(sprintf("Location: edit_customer_order.php?customer_id=%s&order_id=%s", $customer_id, $order_id));
	die();
?>


