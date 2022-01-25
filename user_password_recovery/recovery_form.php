<?php
	// Required files
	require_once '../classes/webpage.class.php';
	require_once '../utilities/app_config.php';

	// Create webpage
	$webpage = new webpage('recovery_form.html');

	// Redirect if session is already started
	session_start();
	if (isset($S_SESSION['username'])) {
		header("Location: ../home/home.php");
		session_destroy();
	}
	
	$webpage->printPage();
?>