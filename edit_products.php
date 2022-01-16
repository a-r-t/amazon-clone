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
	require_once('utils.php');
?>

<?php include 'header.php'; ?>

<main>
	<div class="container-fluid">
		<h2>Store Products</h2>
		<p style="font-size:20px;">Click <a href="admin_portal.php">here</a> to go back to the admin portal.</p>

		<!-- Filter Products Button -->
		<form action="edit_products.php" method="get" id="filter_form" >
			<div class="form-group">
				<input style="width:auto;" type="text" class="form-control" id="filter" placeholder="Filter Products" name="filter" value="<?php if (isset($_GET['filter'])) { echo $_GET['filter']; } ?>" >
			</div>
			<div class="form-group">
				<input id="filter_button" class="btn btn-primary btn-sm" type="submit" value="Filter" style="width:200px;">
			</div>
		</form>
		<!-- End Filter Products Button -->
		
		<?php
			// Get all products from Store Products
			if (isset($_GET['filter']) && $_GET['filter'] !== "") {
				$store_products = StoreDb::filterProducts($_GET['filter']);
			}
			else {
				$store_products = StoreDb::getStoreProducts();
			}					
		?>
			
		<?php if ($store_products): ?>
			
			<!-- if edit product error, show error message -->
			<?php if (isset($_SESSION['edit_product_error'])): ?>
				<p id="add_to_product_error" style="color:red;" ><?php echo $_SESSION['edit_product_error'] ?></p>
				<?php unset($_SESSION['edit_product_error']); ?>
			<?php endif; ?>
			
			<!-- Store Product Info Table -->
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
						<!-- Product Name -->
						<td>
							<div style="padding-bottom:10px;">
								<?php echo $store_product->getName(); ?>
							</div>
							<form action="edit_product.php" method="post" id="edit_product_name_form">
								<input type="hidden" name="product_id" value=<?php echo $store_product->getId(); ?> />
								<div class="form-group">
									<input style="width:auto;" type="text" class="form-control" name="productName" placeholder="New Name">
								</div>
								<div class="form-group">
									<input style="width:200px;" id="update_product_name_button" class="btn btn-primary" type="submit" value="Update Name" name="update_product_name">
								</div>
							</form>
						</td>
						<!-- End Product Name -->
						
						<!-- Product Image -->
						<td><img width="100px" src=<?php echo 'images/products/'.$store_product->getImagePath(); ?>></td>
						<!-- End Product Image -->
						
						<!-- Product Description -->
						<td>
							<div style="padding-bottom:10px;">
								<?php echo $store_product->getDescription(); ?>
							</div>
							<form action="edit_product.php" method="post" id="edit_product_description_form">
								<input type="hidden" name="product_id" value=<?php echo $store_product->getId(); ?> />
								<div class="form-group">
									<input style="width:auto;" type="text" class="form-control" name="productDescription" placeholder="New Description">
								</div>
								<div class="form-group">
									<input style="width:200px;" id="update_product_description_button" class="btn btn-primary" type="submit" value="Update Description" name="update_product_description">
								</div>
							</form>
						</td>
						<!-- End Product Description -->
						
						<!-- Product Price -->
						<td>
							<div style="padding-bottom:10px;">
								<?php echo format_money($store_product->getPrice()); ?>
							</div>
							<form action="edit_product.php" method="post" id="edit_product_price_form">
								<input type="hidden" name="product_id" value=<?php echo $store_product->getId(); ?> />
								<div class="form-group">
									<input style="width:auto;" type="text" class="form-control" name="productPrice" placeholder="New Price">
								</div>
								<div class="form-group">
									<input style="width:200px;" id="update_product_price_button" class="btn btn-primary" type="submit" value="Update Price" name="update_product_price">
								</div>
							</form>
						</td>
						<!-- End Product Price -->
						
						<!-- Product Quantity -->
						<td>
							<div style="padding-bottom:10px;">
								<?php echo $store_product->getQuantity(); ?>
							</div>
							<form action="edit_product.php" method="post" id="edit_product_quantity_form" >
								<input type="hidden" name="product_id" value=<?php echo $store_product->getId(); ?> />
								<div class="form-group">
									<input type="number" class="form-control" name="productQuantity" placeholder="Quantity" value="<?php echo $store_product->getQuantity(); ?>" style="width:142px;" min="0" step="1">
								</div>
								<div class="form-group">
									<input id="update_product_quantity_button" class="btn btn-primary" type="submit" value="Update Quantity" name="update_product_quantity">
								</div>
							</form>
						</td>
						<!-- End Product Quantity -->
					</tr>
				<?php endforeach; ?>
			</table>
			<!-- End Store Products Info Table -->
		<?php else: ?>
			<p>No items to show -- try changing the filter!</p>
		<?php endif; ?>
	</div>
</main>

<script>
</script>

<?php include 'footer.php'; ?>