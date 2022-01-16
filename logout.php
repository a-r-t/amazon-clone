<?php
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	
	// end session, redirect to login page
	session_start();
	$_SESSION = array();
	
	session_destroy();
	header("Location: login.php");
	die();
?>