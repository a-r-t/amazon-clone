<?php
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	
	// if session with user id already started, redirect to verify_login and redirect to appropriate page
	if (isset($_SESSION["user_id"])) {
		header("Location: verify_login.php");
		die();
	}
?>

<?php
	require_once('model/store_db.php');
	require_once('model/customer_db.php');
	require_once('model/cart_db.php');
?>

<?php include 'header.php'; ?>

<main>
	<div class="container-fluid">
		<h2>Login to your account</h2>
		
		<!-- if verify_login error, display error message -->
		<?php if (isset($_SESSION['login_failed'])): ?>
			<p id="login_error" style="color:red;" >Username or Password incorrect!</p>
			<?php unset($_SESSION['login_failed']); ?>
		<?php endif; ?>
		
		<!-- Username/Password Form -->
		<form action="verify_login.php" method="post" id="login_form" >
			<div class="form-group">
				<label for="username">Username</label>
				<input type="text" class="form-control" id="username" placeholder="Username" style="width:auto;" >
			</div>
			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" class="form-control" id="password" placeholder="Password" style="width:auto;">
			</div>
			<input type="hidden" name="login_hash" value="" id="login_hash" />
			<button id="login_submit" name="login_submit" type="submit" class="btn btn-primary" style="width:200px;">Submit</button>
		</form>
		<!-- End Username/Password Form -->
	</div>
</main>

<script src="utils.js"></script>
<script>
	// On submit, send username and password hashed to backend for a little obfuscation
	$('#login_form').submit(() => {
		let password_hash = ($("#username").val() + $("#password").val()).hashCode();
		console.log(password_hash);
		$("#login_hash").attr('value', password_hash);
		return true;
	});	
</script>

<?php $no_logout = true; ?>
<?php include 'footer.php'; ?>