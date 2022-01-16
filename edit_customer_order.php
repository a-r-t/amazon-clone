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
	require_once('model/customer_db.php');
	require_once('model/order_db.php');
	require_once('utils.php');
?>

<?php include 'header.php'; ?>

<main>
	<?php
		if (isset($_GET['customer_id']) && isset($_GET['order_id'])) {
			$customer = CustomerDb::getCustomer($_GET['customer_id']);
			if ($customer) {
				$order = OrderDb::getCustomerOrder($customer->getId(), $_GET['order_id']);
			}
		}
		else {
			header("Location: view_customers.php");
			die();
		}
	?>

	<div class="container-fluid">
		<!-- Done Editing Button -->
		<div style="padding-bottom:10px;">
			<form action="view_customer_order_history.php" method="get" id="done_editing_form" >
				<input type="hidden" name="customer_id" value=<?php echo htmlspecialchars($customer->getId()); ?> />
				<input id="done" class="btn btn-success" style="width:150px;" type="submit" value="Done Editing">
			</form>
		</div>
		<!-- End Done Editing Button -->

		<?php if (!$customer): ?>
			<p style="color:red">Customer Id not found!</p>
		<?php elseif (!$order): ?>
			<p style="color:red">Order Id not found!</p>
		<?php else: ?>
			<!-- Display Customer and Order information -->
			<h2><?php echo $customer->getFullName() ?>'s Order (Order Id: <?php echo $order->getId(); ?>) -- <?php echo $order->getOrderTimestamp(); ?></h2>
			
			<h3>Order Items</h3>
			
			<!-- If add to order error, display error message -->
			<?php if (isset($_SESSION['add_to_order_error'])): ?>
				<p id="add_to_cart_error" style="color:red;" ><?php echo $_SESSION['add_to_order_error']; ?></p>
				<?php unset($_SESSION['add_to_order_error']); ?>
			<?php endif; ?>
			
			<?php if ($order->getOrderItemNumber() > 0): ?>	
				<h4>Items: <?php echo $order->getOrderItemNumber() ?></h4>		
				
				<!-- Order Product Info Table -->
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
						$products = $order->getProducts();			
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
								<form action="remove_from_order.php" method="post">
									<div style="padding-bottom:10px;">
										<input type="hidden" name="product_id" value=<?php echo $product->getId(); ?> />
										<input type="number" name="quantity" class="form-control" id="quantity" placeholder="Quantity", value=0 style="width:153px;" min="0" max=<?php echo $product->getQuantity(); ?> step="1">
										<input type="hidden" name="customer_id" value=<?php echo htmlspecialchars($customer->getId()); ?> />
										<input type="hidden" name="order_id" value=<?php echo htmlspecialchars($order->getId()); ?> />
									</div>
									<input class="btn btn-danger" type="submit" value="Remove from Order" name="remove_from_order">
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<!-- End Order Products Info Table -->
				
				<p style="font-size:20px;">Total: <?php echo format_money($order->getTotalCost()); ?></p>
			<?php else: ?>
				<p style="font-size:20px;">This order has no items!</p>
			<?php endif; ?>	
			
			<h3>Store Products List</h3>
			
			<!-- Filter Products Button -->
			<form action="edit_customer_order.php" method="get" id="filter_form" >
				<div class="form-group">
					<input type="hidden" name="customer_id" value=<?php echo htmlspecialchars($customer->getId()); ?> />
					<input type="hidden" name="order_id" value=<?php echo htmlspecialchars($order->getId()); ?> />
					<input style="width:auto;" type="text" class="form-control" id="filter" placeholder="Filter Products" name="filter" value="<?php if (isset($_GET['filter'])) { echo $_GET['filter']; } ?>" >
				</div>
				<div class="form-group">
					<input id="filter_button" class="btn btn-primary btn-sm" type="submit" value="Filter" style="width:200px;">
				</div>
			</form>
			<!-- End Filter Products Button -->
			
			<?php
				// Get products from Products Table
				if (isset($_GET['filter']) && $_GET['filter'] !== "") {
					$store_products = StoreDb::filterProducts($_GET['filter']);
				}
				else {
					$store_products = StoreDb::getStoreProducts();
				}					
			?>
						
			<?php if ($store_products): ?>
				
				<!-- Store Products Info Table -->
				<table class="table table-bordered" style="width:auto;" id="product_table" >
					<tr>
						<th>Name</th>
						<th>Image</th>
						<th>Description</th>
						<th>Price</th>
						<th>Number In Stock</th>
					</tr>
					
					<!--
						For each product, add product's attributes to table row
					--->
					<?php foreach ($store_products as $store_product): ?>
						<tr>
							<td><?php echo $store_product->getName(); ?></td>
							<td><img width="100px" src=<?php echo 'images/products/'.$store_product->getImagePath(); ?>></td>
							<td><?php echo $store_product->getDescription(); ?></td>
							<td><?php echo format_money($store_product->getPrice()); ?></td>
							<td><?php echo $store_product->getQuantity(); ?></td>
							<td>
								<form action="add_to_order.php" method="post" id="add_to_order_form" >
									<div style="padding-bottom:10px;">
										<input type="hidden" name="customer_id" value=<?php echo htmlspecialchars($customer->getId()); ?> />
										<input type="hidden" name="order_id" value=<?php echo htmlspecialchars($order->getId()); ?> />
										<input type="hidden" name="product_id" value=<?php echo $store_product->getId(); ?> />
										<input type="number" name="quantity" class="form-control" id="quantity" placeholder="Quantity" value=0 style="width:108px;" min="0" max="<?php echo getMaxQuantityAvailable($store_product->getQuantity(), $order->getProductQuantity($store_product->getId())); ?>" step="1">
									</div>
									<input id="add_to_order_button" class="btn btn-primary" type="submit" value="Add to Order" name="add_to_order">
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<!-- End Store Products Info Table -->
			<?php else: ?>
				<p>No store products to show -- try changing the filter!</p>
			<?php endif; ?>
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