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
	require_once('utils.php');
?>

<?php include 'header.php'; ?>

<main>
	<?php
		// get customer's cart
		$customer_id = $_SESSION["customer_id"];
		$customer = CustomerDb::getCustomer($customer_id);
		if (CartDb::isCustomerCartStarted($customer_id)) {
			$cart = CartDb::getCustomerCart($customer_id);
		}
		else {
			$cart = null;
		}
	?>

	<div class="container-fluid">
	
		<!-- if checkout error, display error message -->
		<?php if (isset($_SESSION["checkout_error"])): ?>
			<p id="checkout_error" style="color:red;" >Checkout failed -- please review your order and fix any cart issues.</p>
			<?php unset($_SESSION["checkout_error"]); ?>
		<?php endif; ?>
	
		<h2><?php echo $customer->getFullName() ?>'s Cart</h2>
		<p style="font-size:20px;">Go to our products page <a href="view_products.php">here</a> to add items to your cart!</p>
		
		<h3>Cart Products</h3>
		<?php if ($cart): ?>	
			<h4>Items: <?php echo $cart->getCartItemNumber() ?></h4>		
			<!-- Product Info Table -->
			<table class="table table-bordered" style="width:auto;">
				<tr>
					<th>Name</th>
					<th>Image</th>
					<th>Description</th>
					<th>Number in Stock</th>
					<th>Quantity</th>
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
						<td>
							<form action="remove_from_cart.php" method="post">
								<div style="padding-bottom:10px;">
									<input type="hidden" name="product_id" value=<?php echo $product->getId(); ?> />
									<input type="number" name="quantity" class="form-control" id="quantity" placeholder="Quantity", value=0 style="width:153px;" min="0" max=<?php echo $product->getQuantity(); ?> step="1">
								</div>
								<input class="btn btn-danger" type="submit" value="Remove from Cart" name="remove_from_cart">
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<!-- End Products Info Table -->
			<p style="font-size:20px;">Total: <?php echo format_money($cart->getTotalCost()); ?></p>
			
			<!-- If products in cart have more quantity than store has in stock, display error mesage and disable checkout button -->
			<?php 
				if (!areCartQuantitiesValid($cart)) {
					$valid_cart = false;
				}
				else {
					$valid_cart = true;
				}
			?>
			
			<?php if (!$valid_cart): ?>
				<p id="cart_error" style="color:red;" >There are item quantities in your cart that are invalid and must be fixed prior to checkout.</p>
			<?php endif; ?>
			
			<a class="btn btn-primary btn-lg<?php if (!$valid_cart) { echo " disabled"; } ?>" href="checkout.php" role="button" >Checkout</a>
		
		<?php else: ?>
			<p style="font-size:20px;">No items have been added to your cart yet!</p>
		<?php endif; ?>
	</div>
</main>

<script>
</script>

<?php include 'footer.php'; ?>