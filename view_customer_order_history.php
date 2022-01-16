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
	require_once('model/customer_db.php');
	require_once('model/order_db.php');
	require_once('model/shipping_information_db.php');
	require_once('model/billing_information_db.php');
	require_once('utils.php');
?>

<?php include 'header.php'; ?>

<main>
	<?php
		if (isset($_GET['customer_id'])) {
			$customer_id = $_GET['customer_id'];
			$customer = CustomerDb::getCustomer($customer_id);
		}
		else {
			header("Location: view_customers.php");
			die();
		}
	?>
	
	<div class="container-fluid">
		
		<?php if ($customer): ?>
			<h2>Customer <?php echo $customer->getFullName() ?>'s Order History (Customer Id: <?php echo $customer->getId() ?>)</h2>
			<p style="font-size:20px;">Click <a href="view_customers.php">here</a> to view customer list.</p>
			
			<h3>Orders</h3>
			<?php $orders = OrderDb::getCustomerOrders($customer_id); ?>
			
			<?php if ($orders): ?>
				
				<!--
					For each order, create div block with table of all products in order
				--->
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
						<?php $billing_information = BillingInformationDb::getCustomerBillingInformation($order->getCustomerId(), $order->getBillingInformationId()) ?>
						<p style="font-size:20px;"><b>Paid with:</b></p>
						<p style="font-size:14px;"> <?php echo $billing_information->getCardTitle(); ?></p>
						<?php $shipping_information = ShippingInformationDb::getCustomerShippingInformation($order->getCustomerId(), $order->getShippingInformationId()) ?>
						<p style="font-size:20px;"><b>Shipping Information:</b>
						<p style="font-size:14px;"><b>Country:</b> <?php echo $shipping_information->getCountry(); ?></p>
						<p style="font-size:14px;"><b>Address Line 1:</b> <?php echo $shipping_information->getAddress1(); ?></p>
						<p style="font-size:14px;"><b>Address Line 2:</b> <?php echo $shipping_information->getAddress2(); ?></p>
						<p style="font-size:14px;"><b>City:</b> <?php echo $shipping_information->getCity(); ?></p>
						<p style="font-size:14px;"><b>State:</b> <?php echo $shipping_information->getState(); ?></p>
						<p style="font-size:14px;"><b>Zip Code:</b> <?php echo $shipping_information->getZipCode(); ?></p>
						<div style="padding-bottom:10px;">
							<form action="edit_customer_order.php" method="get" id="edit_customer_order_form" >
								<input type="hidden" name="customer_id" value=<?php echo htmlspecialchars($customer->getId()); ?> />
								<input type="hidden" name="order_id" value=<?php echo htmlspecialchars($order->getId()); ?> />
								<input id="edit_order" class="btn btn-primary" style="width:150px;" type="submit" value="Edit Order">
							</form>
						</div>
						<div style="padding-bottom:10px;">
							<form action="delete_customer_order.php" method="post" id="delete_customer_order_form" >
								<input type="hidden" name="customer_id" value=<?php echo htmlspecialchars($customer->getId()); ?> />
								<input type="hidden" name="order_id" value=<?php echo htmlspecialchars($order->getId()); ?> />
								<input id="delete_order" class="btn btn-primary" style="width:150px;" type="submit" value="Delete Order", name="delete_order" >
							</form>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<p style="font-size:20px;">No order history!</p>
			<?php endif; ?>
		<?php else: ?>
			<p style="color:red;">Customer not found!</p>
		<?php endif; ?>
	</div>
</main>

<?php include 'footer.php'; ?>
