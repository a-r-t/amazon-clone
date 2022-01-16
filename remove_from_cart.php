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
?>

<?php
	// remove product from cart by specified quantity
	if (isset($_POST['remove_from_cart'])) {
		$customer_id = $_SESSION["customer_id"];
		$cart = CartDb::getCustomerCart($customer_id);
		CartDb::updateCartProduct($cart->getId(), $_POST['product_id'], $_POST['quantity'] * -1);
	}
	
	header("Location: view_cart.php");
	die();
?>

