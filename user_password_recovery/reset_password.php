<?php
	// Required files
	require_once '../classes/webpage.class.php';
	require_once '../utilities/app_config.php';

	// Redirect if session is already started
	session_start();
	if (isset($_SESSION['username'])) {
		header("Location: ../home/home.php");
		session_destroy();
	}
	
	$webpage = new webpage("reset_password.html");
	$webpage->createPage("Password Reset");
	
	if (isset($_SESSION['ERROR_MSG'])) {
		$err_msg = $_SESSION['ERROR_MSG'];
		echo "<p class='error_message'>{$err_msg}</p>";
	}
	
	//get the information from the recovery form
	if (isset($_REQUEST['file']) && isset($_REQUEST['reset'])) {
		$username = $_REQUEST['file'];
		$password = $_REQUEST['reset'];
		
		//update the page
		$webpage->convert("USERNAME_HASH", $username);
		$webpage->convert("PASSWORD_HASH", $password);
	}
	
	$webpage->printPage();
?>
