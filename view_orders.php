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
	require_once('model/order_db.php');
	require_once('model/billing_information_db.php');
	require_once('model/shipping_information_db.php');
	require_once('utils.php');
?>

<?php include 'header.php'; ?>

<main>
	<?php
		$customer_id = $_SESSION["customer_id"];
		$customer = CustomerDb::getCustomer($customer_id);
		$orders = OrderDb::getCustomerOrders($customer_id);
	?>

	<div class="container-fluid">
		<h2><?php echo $customer->getFullName() ?>'s Order History</h2>
		<p style="font-size:20px;">Go to our products page <a href="view_products.php">here</a> to create an order!</p>
		<h3>Orders</h3>
		<?php if ($orders): ?>	
			<?php foreach($orders as $order): ?>
				<div style="padding:10px;margin-top:10px;border: 2px solid black;border-radius: 5px;">
					<h4><?php echo $order->getOrderTimestamp() ?></h4>
					<h4>Items: <?php echo $order->getOrderItemNumber() ?></h4>		
					<!-- Product Info Table -->
					<table class="table table-bordered" style="width:auto;">
						<tr>
							<th>Name</th>
							<th>Image</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>Price Each</th>
							<th>Total Price</th>
						</tr>
						
						<?php
							// Get all products from order
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
								<td><?php echo $product->getQuantity(); ?></td>
								<td><?php echo format_money($product->getPrice()); ?></td>
								<td><?php echo format_money($product->getTotalPrice()); ?></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<!-- End Products Info Table -->
					
					<p style="font-size:20px;"><b>Total:</b> <?php echo format_money($order->getTotalCost()); ?></p>
					
					<!-- Display Billing Information -->
					<?php $billing_information = BillingInformationDb::getCustomerBillingInformation($order->getCustomerId(), $order->getBillingInformationId()) ?>
					<p style="font-size:20px;"><b>Paid with:</b></p>
					<p style="font-size:14px;"> <?php echo $billing_information->getCardTitle(); ?></p>
					<!-- End Display Billing Information -->
					
					<!-- Display Shipping Information -->
					<?php $shipping_information = ShippingInformationDb::getCustomerShippingInformation($order->getCustomerId(), $order->getShippingInformationId()) ?>
					<p style="font-size:20px;"><b>Shipping Information:</b>
					<p style="font-size:14px;"><b>Country:</b> <?php echo $shipping_information->getCountry(); ?></p>
					<p style="font-size:14px;"><b>Address Line 1:</b> <?php echo $shipping_information->getAddress1(); ?></p>
					<p style="font-size:14px;"><b>Address Line 2:</b> <?php echo $shipping_information->getAddress2(); ?></p>
					<p style="font-size:14px;"><b>City:</b> <?php echo $shipping_information->getCity(); ?></p>
					<p style="font-size:14px;"><b>State:</b> <?php echo $shipping_information->getState(); ?></p>
					<p style="font-size:14px;"><b>Zip Code:</b> <?php echo $shipping_information->getZipCode(); ?></p>
					<!-- End Display Shipping Information -->
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<p style="font-size:20px;">No order history!</p>
		<?php endif; ?>
	</div>
</main>

<script>
</script>

<?php include 'footer.php'; ?>