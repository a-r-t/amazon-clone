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
	require_once('model/admin_db.php');
	require_once('model/customer_db.php');
?>

<?php include 'header.php'; ?>

<main>
	<div class="container-fluid">
		<h2>Customers List</h2>
		<p style="font-size:20px;">Click <a href="admin_portal.php">here</a> to go back to the admin portal.</p>
		
		<!-- Filter Customers Button -->
		<form action="view_customers.php" method="post" id="filter_form" >
			<div class="form-group">
				<input style="width:auto;" type="text" class="form-control" id="filter" placeholder="Filter Customers" name="filter" value="<?php if (isset($_POST['filter'])) { echo $_POST['filter']; } ?>" >
			</div>
			<div class="form-group">
				<input id="filter_button" class="btn btn-primary btn-sm" type="submit" value="Filter" name="filter_button" style="width:200px;">
			</div>
		</form>
		<!-- End Filter Customers Button -->
		
		<?php
			// Get all customers from Customers Table
			if (isset($_POST['filter']) && $_POST['filter'] !== "") {
				$customers = CustomerDb::filterCustomers($_POST['filter']);
			}
			else {
				$customers = CustomerDb::getCustomers();
			}					
		?>
					
		<?php if ($customers): ?>
		
			<!-- Customers Info Table -->
			<table class="table table-bordered" style="width:auto;" id="product_table" >
				<tr>
					<th>Last Name</th>
					<th>First Name</th>
					<th>Email</th>
					<th>Id</th>
				</tr>
				
				<!--
					For each customer, add customer's attributes to table row
				--->
				<?php foreach ($customers as $customer): ?>
					<tr>
						<td><?php echo $customer->getLastName(); ?></td>
						<td><?php echo $customer->getFirstName(); ?></td>
						<td><?php echo $customer->getEmail(); ?></td>
						<td><?php echo $customer->getId(); ?></td>
						<td>
							<form action="view_customer_order_history.php" method="get" id="add_to_cart_form" >
								<input type="hidden" name="customer_id" value=<?php echo htmlspecialchars($customer->getId()); ?> />
								<input id="order_history" class="btn btn-primary" type="submit" value="Order History">
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<!-- End Customers Info Table -->
		<?php else: ?>
			<p>No customers to show -- try changing the filter!</p>
		<?php endif; ?>
	</div>
</main>

