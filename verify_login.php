<?php
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	
	// redirects to appropriate page if session with user login is already established
	if (!isset($_SESSION['user_id']) && !isset($_POST['login_submit'])) {
		header("Location: login.php");
		die();
	}
	
	if (isset($_SESSION["customer_id"])) {
		header("Location: view_products.php");
		die();
	}
	else if (isset($_SESSION["admin_id"])) {
		header("Location: admin_portal.php");
		die();
	}
?>

<?php
	require_once('model/user_db.php');
	require_once('model/customer_db.php');
	require_once('model/admin_db.php');
?>

<?php
	// check username and password to see if there is a matching user
	// if login successful, redirect user appropriately based on their role
	if (isset($_POST['login_submit'])) {
		$login_hash = md5($_POST['login_hash']);
		$user = UserDb::getUserFromLoginHash($login_hash);
		
		// if user matches username/password hash, redirect to appropriate page
		if ($user) {
			$_SESSION["user_id"] = $user->getId();
			
			// if user role is customer...
			if ($user->getRole() === "customer") {
				$customer = CustomerDb::getCustomerFromUser($user->getId());
				if ($customer) {
					$_SESSION["customer_id"] = $customer->getId();	
					header("Location: view_products.php");
					die();
				}
			}
			// if user role is admin...
			else if ($user->getRole() === "admin") {
				$admin = AdminDb::getAdminFromUser($user->getId());
				if ($admin) {
					$_SESSION["admin_id"] = $admin->getId();	
					header("Location: admin_portal.php");
					die();
				}
			}
		}
		// if no match, redirect back to login page
		$_SESSION["login_failed"] = 'true';
		header("Location: login.php");
		die();
	}
?>
