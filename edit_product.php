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
	require_once('model/store_db.php');
	require_once('model/product_db.php');
	require_once('utils.php');
?>

<?php
	// edits information of a product in database
	if (isset($_POST['product_id'])) {
		$product = ProductDb::getProduct($_POST['product_id']);
		if ($product) {
			
			// update product name
			if (isset($_POST['update_product_name'])) {
				if ($_POST['productName'] !== "") {
					$product->setName($_POST['productName']);
					ProductDb::updateProduct($product);
				}
				else {
					setSessionErrorMessage("Product name cannot be empty.");

				}
			}
			
			// update product description
			else if (isset($_POST['update_product_description'])) {
				if ($_POST['productDescription'] !== "") {
					$product->setDescription($_POST['productDescription']);
					ProductDb::updateProduct($product);
				}
				else {
					setSessionErrorMessage("Product description cannot be empty.");
						
				}				
			}
			
			// update product price
			else if (isset($_POST['update_product_price'])) {
				if (is_numeric($_POST['productPrice'])) {
					$product->setPrice($_POST['productPrice']);
					ProductDb::updateProduct($product);
				}
				else {
					setSessionErrorMessage("Product price must be a valid decimal value.");

				}					
			}
			
			// update product quantity in store
			else if (isset($_POST['update_product_quantity'])) {
				if (is_numeric($_POST['productQuantity'])) {
					$product_quantity = $_POST['productQuantity'];
					if ($_POST['productQuantity'] < 0) {
						$product_quantity = 0;
					}
					StoreDb::updateProductQuantity($product->getId(), $product_quantity);
				}
				else {
					setSessionErrorMessage("Product quantity must be a valid integer.");
				}							
			}
		}
		else {
			setSessionErrorMessage(sprintf("Unable to locate product in database with id of %s -- please try again.", $_POST['product_id']));
		}
	}
	else {
		setSessionErrorMessage("An unknown error ocurred -- please try again.");
	}
	
	header("Location: edit_products.php");
	die();
	
	function setSessionErrorMessage($error_message) {
		$_SESSION['edit_product_error'] = $error_message;
	}
?>