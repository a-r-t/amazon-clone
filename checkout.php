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
	require_once('model/store_db.php');
	require_once('model/customer_db.php');
	require_once('model/cart_db.php');
	require_once('model/billing_information_db.php');
	require_once('model/shipping_information_db.php');
	require_once('utils.php');
?>

<?php include 'header.php'; ?>

<main>
	<?php
		$customer_id = $_SESSION["customer_id"];
		$customer = CustomerDb::getCustomer($customer_id);
		$cart = CartDb::getCustomerCart($customer_id);
		
		// if cart has quantities that are more than store currently has in stock, redirect user back to cart page to fix
		if (!areCartQuantitiesValid($cart)) {
			$_SESSION["checkout_error"];
			header("Location: view_cart.php");
			die();
		}
	?>
	
	<div class="container-fluid">
		<h2>Checkout</h2>
		
		<!-- if process_checkout.php errors, display error message -->
		<?php if (isset($_SESSION['checkout_error'])): ?>
			<p id="checkout_error" style="color:red;" ><?php echo $_SESSION['checkout_error'] ?></p>
			<?php unset($_SESSION['checkout_error']); ?>
		<?php endif; ?>
		
		<p style="font-size:20px;">Click <a href="view_cart.php">here</a> to go back and edit your cart!</p>
		<h3>Review Cart Products</h3>
		<h4>Items: <?php echo $cart->getCartItemNumber() ?></h4>
		
		<!-- Product Info Table -->
		<table class="table table-bordered" style="width:auto;">
			<tr>
				<th>Name</th>
				<th>Image</th>
				<th>Description</th>
				<th>Number in Stock</th>
				<th>Desired Quantity</th>
				<th>Price Each</th>
				<th>Total Price</th>
			</tr>
			
			<?php
				// Get all products from Products Table
				$products = $cart->getProducts();			
			?>
			
			<!--
				For each product, add product's attributes to table row
			--->
			<?php foreach($products as $product): ?>
				<tr>
					<td><?php echo $product->getName(); ?></td>
					<td><img width="100px" src=<?php echo 'images/products/'.$product->getImagePath(); ?>></td>
					<td><?php echo $product->getDescription(); ?></td>
					<td><?php echo StoreDb::getStoreProductQuantity($product->getId()); ?></td>
					<td><?php echo $product->getQuantity(); ?></td>
					<td><?php echo format_money($product->getPrice()); ?></td>
					<td><?php echo format_money($product->getTotalPrice()); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
		<!-- End Products Info Table -->
		<p style="font-size:20px;">Total: <?php echo format_money($cart->getTotalCost()); ?></p>
		
		<!-- Payment Information Form -->
		<h3>Select Payment Information</h3>
		<?php
			$billing_informations = BillingInformationDb::getCustomerBillingInformations($customer_id);
		?>
		<div style="padding-bottom:10px;">
			<select class="form-control" name="paymentInfo" id="paymentInfo" style="width:auto;" onchange="selectPaymentChange()">
				<?php foreach ($billing_informations as $billing_information): ?>
					<option data-billing_information_id=<?php echo $billing_information->getId(); ?>>
						<?php echo $billing_information->getCardTitle() ?>
					</option>
				<?php endforeach; ?>
				<option value="new">Add New Payment Information</option>
			</select>
		</div>
		<div id="enterPaymentInfo" style="display:none;">
			<form>
				<p style="font-size:25px;">Card Information:</p>
				<?php include 'card_form.php'; ?>
				<p style="font-size:25px;">Billing Address:</p>
				<?php 
					$address_type = "billing";
					include 'address_form.php';
				?>
			</form>
		</div>
		<!-- End Payment Information Form -->
		
		<!-- Shippign Information Form -->
		<h3>Select Shipping Address</h3>
		<div style="padding-bottom:10px;">
			<?php
				$shipping_informations = ShippingInformationDb::getCustomerShippingInformations($customer_id);
			?>
			<div style="padding-bottom:10px;">
				<select class="form-control" name="shippingInfo" id="shippingInfo" style="width:auto;" onchange="selectShippingChange()">
					<?php foreach ($shipping_informations as $shipping_information): ?>
						<option 
							data-shipping_information_id="<?php echo $shipping_information->getId(); ?>"
							data-country="<?php echo $shipping_information->getCountry(); ?>"
							data-address_1="<?php echo $shipping_information->getAddress1(); ?>"
							data-address_2="<?php echo $shipping_information->getAddress2(); ?>"
							data-city="<?php echo $shipping_information->getCity(); ?>"
							data-state="<?php echo $shipping_information->getState(); ?>"
							data-zip_code="<?php echo $shipping_information->getZipCode(); ?>"
							data-name="<?php echo $shipping_information->getName(); ?>"
							value="<?php echo $shipping_information->getName(); ?>"
						>
							<?php echo $shipping_information->getName(); ?>
						</option>
					<?php endforeach; ?>
					<option value="new">Add New Shipping Address</option>
				</select>
			</div>
			<div id="enterShippingInfo">
				<form>
					<p style="font-size:25px;">Shipping Address:</p>
					<?php 
						$address_type = "shipping";
						include 'address_form.php';
					?>
					<div class="form-group">
						<div id="shipping_name" style="display:none;">
							<label for="shipping_name">Shipping Address Nickname *</label>
							<input style="width:auto;" type="text" class="form-control" id="shipping_name_input" placeholder="Shipping Address Name">
							<p id="shipping_name_error" style="display:none;color:red">Nickname for new shipping address is required and must be unique!</p>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- End Shipping Information Form -->
		
		<!-- Checkout Form -->
		<form action="process_checkout.php" method="post" id="process_checkout">
			<input type="hidden" name="billing_information_id" value="" id="input_billing_information_id" />
			<input type="hidden" name="card_type" value="" id="input_card_type" />
			<input type="hidden" name="card_name" value="" id="input_card_name" />
			<input type="hidden" name="card_last_four_digits" value="" id="input_card_last_four_digits" />
			<input type="hidden" name="card_hash" value="" id="input_card_hash" />
			<input type="hidden" name="billing_information_country" value="" id="input_billing_information_country" />
			<input type="hidden" name="billing_information_address_1" value="" id="input_billing_information_address_1" />
			<input type="hidden" name="billing_information_address_2" value="" id="input_billing_information_address_2" />
			<input type="hidden" name="billing_information_city" value="" id="input_billing_information_city" />
			<input type="hidden" name="billing_information_state" value="" id="input_billing_information_state" />
			<input type="hidden" name="billing_information_zip_code" value="" id="input_billing_information_zip_code" />
			<input type="hidden" name="shipping_information_id" value="" id="input_shipping_information_id" />
			<input type="hidden" name="shipping_information_country" value="" id="input_shipping_information_country" />
			<input type="hidden" name="shipping_information_address_1" value="" id="input_shipping_information_address_1" />
			<input type="hidden" name="shipping_information_address_2" value="" id="input_shipping_information_address_2" />
			<input type="hidden" name="shipping_information_city" value="" id="input_shipping_information_city" />
			<input type="hidden" name="shipping_information_state" value="" id="input_shipping_information_state" />
			<input type="hidden" name="shipping_information_zip_code" value="" id="input_shipping_information_zip_code" />
			<input type="hidden" name="shipping_information_name" value="" id="input_shipping_information_name" />
			
			<button id="process_checkout_submit" name="process_checkout_submit" type="submit" class="btn btn-primary">Submit</button>
		</form>
		<!-- End Checkout Form -->
	</div>
</main>

<script src="utils.js"></script>
<script src="checkout.js"></script>
<script>
	selectPaymentChange();
	selectShippingChange();
</script>

<?php include 'footer.php'; ?>