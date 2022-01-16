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
	require_once('model/order_db.php');
	require_once('model/order.php');
	require_once('model/shipping_information_db.php');
	require_once('model/billing_information_db.php');
	require_once('model/billing_information.php');
	require_once('model/shipping_information.php');
	require_once('utils.php');
?>

<?php
	/*
	 * Processes checkout of customer cart
	 * If successful, order is created
	 * It's a lot of code but 99.9% of it is input validation from checkout page...
	*/
	if (isset($_POST['process_checkout_submit'])) {
		$customer_id = $_SESSION["customer_id"];
		
		// Validate Billing Information input data
		// If existing billing information was selected on form, use it
		if ($_POST['billing_information_id']) {
			if (BillingInformationDb::getCustomerBillingInformation($customer_id, $_POST['billing_information_id'])) {
				$billing_information_id = $_POST['billing_information_id'];
			}
			else {
				$_SESSION['checkout_error'] = "An error occurred with billing verification -- please try again";
				header("Location: checkout.php");
				die();
			}
		}
		// if new billing information was created, validate and add new billing information to database
		else {
			$country = $_POST['billing_information_country'];
			$address_1 = $_POST['billing_information_address_1'];
			$address_2 = $_POST['billing_information_address_2'];
			$city = $_POST['billing_information_city'];
			$state = $_POST['billing_information_state'];
			$zip_code = $_POST['billing_information_zip_code'];
			$card_type = $_POST['card_type'];
			$card_name = $_POST['card_name'];
			$card_last_four_digits = $_POST['card_last_four_digits'];
			$card_hash = md5($_POST['card_hash']);
			
			// Error if any required fields are empty
			if (!$country || !$address_1 || !$city || !$state || !$zip_code || !$card_type || !$card_name || !$card_last_four_digits || !$card_hash) {
				$_SESSION['checkout_error'] = "An error occurred in billing address -- please make sure all required fields are filled out";
				redirectToCheckout();
			}
			
			validateCountry($country, "billing");
			validateState($state, "billing");	
			validateZipCode($zip_code, "billing");
			
			validateCardType($card_type);
			validateCardLastFourDigits($card_last_four_digits);
			validateCardHash($card_hash);
			
			// Add new billing information to database
			$billing_information = new BillingInformation(
				$customer_id, $country, $address_1, $address_2, $city, $state, $zip_code,
				$card_type, $card_name, $card_last_four_digits, $card_hash
			);
			$billing_information_id = BillingInformationDb::addBillingInformation($billing_information);
		}
		
		// Validate Billing Information input data
		// If existing billing information was selected on form, use it
		if ($_POST['shipping_information_id']) {
			if (ShippingInformationDb::getCustomerShippingInformation($customer_id, $_POST['shipping_information_id'])) {
				$shipping_information_id = $_POST['shipping_information_id'];
			}
			else {
				$_SESSION['checkout_error'] = "An error occurred with shipping verification -- please try again";
				header("Location: checkout.php");
				die();
			}
		}
		// if new shipping information was created, validate and add new shipping information to database
		else {
			$country = $_POST['shipping_information_country'];
			$address_1 = $_POST['shipping_information_address_1'];
			$address_2 = $_POST['shipping_information_address_2'];
			$city = $_POST['shipping_information_city'];
			$state = $_POST['shipping_information_state'];
			$zip_code = $_POST['shipping_information_zip_code'];
			$name = $_POST['shipping_information_name'];
			
			// Error if any required fields are empty
			if (!$country || !$address_1 || !$city || !$state || !$zip_code || !$name) {
				$_SESSION['checkout_error'] = "An error occurred in shipping address -- please make sure all required fields are filled out";
				redirectToCheckout();
			}
			
			validateCountry($country, "shipping");
			validateState($state, "shipping");	
			validateZipCode($zip_code, "shipping");
			
			validateShippingName($customer_id, $name);

			// Add new shipping information to database
			$shipping_information = new ShippingInformation(
				$customer_id, $country, $address_1, $address_2, $city, $state, $zip_code, $name
			);
			$shipping_information_id = ShippingInformationDb::addShippingInformation($shipping_information);
		}
		
		// gets current timestamp in mysql format
		$current_timestamp = date("Y-m-d H:i:s");
		
		// double checks customer cart
		// if cart has product quantities that are more than store has in stock, redirect to view_cart page with error
		$cart = CartDb::getCustomerCart($customer_id);
		if (!areCartQuantitiesValid($cart)) {
			$_SESSION["checkout_error"] = "true";
			header("Location: view_cart.php");
			die();
		}
		
		// Add new order to database
		$order = new Order($customer_id, $billing_information_id, $shipping_information_id, $current_timestamp);
		$cart_products = $cart->getProducts();
		$order->setProducts($cart_products);
		OrderDb::addOrder($order);
		
		// for each product in cart, subtract quantity ordered from store quantity
		foreach ($cart_products as $cart_product) {
			$store_quantity = StoreDb::getStoreProductQuantity($cart_product->getId());
			$new_quantity = $store_quantity - $cart_product->getQuantity();
			StoreDb::updateProductQuantity($cart_product->getId(), $new_quantity);
		}
		
		// delete customer cart since it is now empty
		CartDb::deleteCart($cart->getId());
		
		header("Location: view_orders.php");
		die();
	}
	
	// redirects to checkout page
	function redirectToCheckout() {
		header("Location: checkout.php");
		die();
	}
	
	// validates country input
	function validateCountry($country, $address_type) {
		if ($country !== "United States") {
			$_SESSION['checkout_error'] = sprintf("An error occurred in %s address -- we only service United States residents at this time", $address_type);
			redirectToCheckout();
		}
	}
	
	// validates state input
	function validateState($state, $address_type) {
		$states = "|AL|AK|AZ|AR|CA|CO|CT|DE|DC|FL|GA|HI|ID|IL|IN|IA|KS|KY|LA|ME|MD|MA|MI|MN|MS|MO|MT|NE|NV|NH|NJ|NM|NY|NC|ND|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VT|VA|WA|WV|WI|WY|";
		if (strpos($states, $state) === false) {
			$_SESSION['checkout_error'] = sprintf("An error occurred in %s address -- state %s is invalid", $address_type, $state);
			redirectToCheckout();
		}
	}	
	
	// validates zipcode input
	function validateZipCode($zip_code, $address_type) {
		if (!preg_match("/[0-9]{5}/", $zip_code))  {
			$_SESSION['checkout_error'] = sprintf("An error occurred in %s address -- zip code %s is invalid", $address_type, $zip_code);
			redirectToCheckout();
		}		
	}

	// validates card type input
	function validateCardType($card_type) {
	$card_types = "|Visa|MasterCard|American Express|Discover|";
		if (strpos($card_types, $card_type) === false)  {
			$_SESSION['checkout_error'] = sprintf("An error occurred in shipping address -- card type %s is invalid", $card_type);
			redirectToCheckout();
		}
	}
	
	// validates card last four digits input
	function validateCardLastFourDigits($card_last_four_digits) {
		if (!preg_match("/[0-9]{4}/", $card_last_four_digits))  {
			$_SESSION['checkout_error'] = "An error occurred -- please double check card information";
			redirectToCheckout();
		}
	}
	
	// validates card hash
	// intentionally left blank because I don't know the process that goes into identifying if a credit card is valid or not
	// I think it's a good thing that the general public doesn't know the math behind credit card number validity
	function validateCardHash($card_hash) {
		
	}
	
	// validates shipping nickname (ensures it is unique and no other shipping informations have the same nickname)
	function validateShippingName($customer_id, $shipping_name) {
		$customer_shipping_informations = ShippingInformationDb::getCustomerShippingInformations($customer_id);
		foreach ($customer_shipping_informations as $customer_shipping_information) {
			if ($customer_shipping_information->getName() === $name) {
				$_SESSION['checkout_error'] = "An error occurred in shipping address -- name is already in use";
				redirectToCheckout();
			}
		}
	}
?>
