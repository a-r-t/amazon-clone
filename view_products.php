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
		$customer_id = $_SESSION["customer_id"];
		$customer = CustomerDb::getCustomer($customer_id);
		
		// Get customer cart item number
		if (CartDb::isCustomerCartStarted($customer_id)) {
			$cart = CartDb::getCustomerCart($customer_id);
			$cartItemNumber = $cart->getCartItemNumber();
		}
		else {
			$cartItemNumber = 0;
		}
	?>

	<div class="container-fluid">
		<h2>Hello <?php echo $customer->getFullName() ?>!</h2>
		<a href="view_cart.php">
			<img name="view_cart" type="image" width="50px" src="images/icons/shopping-cart-icon.png">
		</a>
		<p>Items: <?php echo $cartItemNumber ?></p>
		<p style="font-size:20px;">Click <a href="view_orders.php">here</a> to view your past orders</p>
		<h3>Product List</h2>
		
		<!-- Filter Products Button -->
		<form action="view_products.php" method="get" id="filter_form" >
			<div class="form-group">
				<input style="width:auto;" type="text" class="form-control" id="filter" placeholder="Filter Products" name="filter" value="<?php if (isset($_GET['filter'])) { echo $_GET['filter']; } ?>" >
			</div>
			<div class="form-group">
				<input id="filter_button" class="btn btn-primary btn-sm" type="submit" value="Filter" style="width:200px;">
			</div>
		</form>
		<!-- End Filter Products Button -->
		
		<?php
			// Get all products from Products Table
			if (isset($_GET['filter']) && $_GET['filter'] !== "") {
				$products = StoreDb::filterProducts($_GET['filter']);
			}
			else {
				$products = StoreDb::getStoreProducts();
			}					
		?>
					
		<?php if ($products): ?>
		
			<!-- If add to cart error, display error message -->
			<?php if (isset($_SESSION['add_to_cart_error'])): ?>
				<p id="add_to_cart_error" style="color:red;" ><?php echo $_SESSION['add_to_cart_error'] ?></p>
				<?php unset($_SESSION['add_to_cart_error']); ?>
			<?php endif; ?>
			
			<!-- Product Info Table -->
			<table class="table table-bordered" style="width:auto;" id="product_table" >
				<tr>
					<th>Name</th>
					<th>Image</th>
					<th>Description</th>
					<th>Price</th>
					<th>Number In Stock</th>
					<th>Number In Cart</th>
				</tr>
				
				<!--
					For each product, add product's attributes to table row
				--->
				<?php foreach ($products as $product): ?>
					<tr>
						<td><?php echo $product->getName(); ?></td>
						<td><img width="100px" src=<?php echo 'images/products/'.$product->getImagePath(); ?>></td>
						<td><?php echo $product->getDescription(); ?></td>
						<td><?php echo format_money($product->getPrice()); ?></td>
						<td><?php echo $product->getQuantity(); ?></td>
						<td>
							<?php 
								$cart_quantity = 0;
								if (CartDb::isCustomerCartStarted($customer_id)) {
									$cart = CartDb::getCustomerCart($customer_id);
									$cart_product = $cart->getProduct($product->getId());
									if ($cart_product) {
										$cart_quantity = $cart_product->getQuantity();
									}
								}
								echo $cart_quantity;
							?>
						</td>
						<td>
							<form action="add_to_cart.php" method="post" id="add_to_cart_form" >
								<input type="hidden" name="product_id" value=<?php echo htmlspecialchars($product->getId()); ?> />
								<div style="padding-bottom:10px;">
									<input type="hidden" name="product_id" value=<?php echo $product->getId(); ?> />
									<input type="number" name="quantity" class="form-control" id="quantity" placeholder="Quantity" value=0 style="width:108px;" min="0" max="<?php echo getMaxQuantityAvailable($product->getQuantity(), $cart_quantity); ?>" step="1">
								</div>
								<input id="add_to_cart_button" class="btn btn-primary" type="submit" value="Add to Cart" name="add_to_cart">
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<!-- End Products Info Table -->
		<?php else: ?>
			<p>No items to show -- try changing the filter!</p>
		<?php endif; ?>
	</div>
</main>

<script>
</script>

<?php
	// gets max quantity that can be added of a product to an order based on how much is left in stock and how much is currently in order
	function getMaxQuantityAvailable($store_quantity, $order_quantity) {
		$max_quantity_available = $store_quantity - $order_quantity;
		if ($max_quantity_available < 0) {
			$max_quantity_available = 0;
		}
		return $max_quantity_available;
	}
?>

<?php include 'footer.php'; ?>