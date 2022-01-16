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
	// removes product from order by specified quantity
	if (isset($_POST['remove_from_order'])) {
		$customer_id = $_POST["customer_id"];
		$order_id = $_POST["order_id"];
		$order = OrderDb::getCustomerOrder($customer_id, $order_id);
		OrderDb::updateOrderProduct($order->getId(), $_POST['product_id'], $_POST['quantity'] * -1);
		StoreDb::modifyProductQuantity($_POST['product_id'], $_POST['quantity']);
	}
	
	header(sprintf("Location: edit_customer_order.php?customer_id=%s&order_id=%s", $customer_id, $order_id));
	die();
?>