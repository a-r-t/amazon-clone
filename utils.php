<?php
require_once('model/store_db.php');

// formats a number as money (dollar sign, two decimal places)
function format_money($number) {
	return sprintf('$%01.2f', $number);
}

// checks cart to ensure all product quantities are valid and not more than the store currently has in stock
function areCartQuantitiesValid($cart) {
	foreach ($cart->getProducts() as $product) {
		$store_quantity = StoreDb::getStoreProductQuantity($product->getId());
		if ($store_quantity - $product->getQuantity() < 0) {
			return false;
		}
	}
	return true;
}


?>