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
	require_once('model/store_db.php');
?>

<?php
	// Deletes a customer's order from database
	if (isset($_POST['delete_order'])) {
		$customer_id = $_POST["customer_id"];
		$order_id = $_POST["order_id"];
		$order = OrderDb::getCustomerOrder($customer_id, $order_id);
		if ($order) {
			// Before deleting order, adds back product quantities from order to product quantity in store
			foreach ($order->getProducts() as $order_product) {
				StoreDb::modifyProductQuantity($order_product->getId(), $order_product->getQuantity());
			}
			OrderDb::deleteOrder($order_id);
		}
	}
	
	header(sprintf("Location: view_customer_order_history.php?customer_id=%s", $customer_id));
	die();
?>


