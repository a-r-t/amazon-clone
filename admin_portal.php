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
?>

<?php include 'header.php'; ?>

<main>
	<?php
		$admin_id = $_SESSION["admin_id"];
		$admin = AdminDb::getAdmin($admin_id);
	?>
	
	<!-- Simple admin portal with links to other admin pages -->
	<div class="container-fluid">
		<h2>Hello Admin <?php echo $admin->getFullName() ?>!</h2>
		<p style="font-size:20px;">Click <a href="view_customers.php">here</a> to view customer list.</p>
		<p style="font-size:20px;">Click <a href="edit_products.php">here</a> to edit products.</p>
	</div>
</main>

<?php include 'footer.php'; ?>
