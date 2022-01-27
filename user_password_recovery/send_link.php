<?php
	// Required files
	require_once '../classes/webpage.class.php';
	require_once '../utilities/app_config.php';

	// Redirect if session is already started
	session_start();
	if (isset($SESSION['username'])) {
		header("Location: ../home/home.php");
		session_destroy();
	}
	
	//create the web page
	$webpage = new webpage("send_link.html");
	$webpage->createPage("Password Recovery");
	
	//get the information from the recovery form
	if (isset($_REQUEST['username'])) {
		$username = $_REQUEST['username'];
		//get their information from the DB
		$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		$_qry = sprintf("SELECT id, username, password, email, email_verified FROM users WHERE username = '%s';",
			mysqli_real_escape_string($db_connection, $username));
		$_result = $db_connection->query($_qry);
		$db_connection->close();
		
		if ($_result->num_rows == 1) {
			//check that the email is verified
			$_row = $_result->fetch_array(MYSQLI_BOTH);
			if (!$_row['email_verified']) {
				echo "<p>The email address you have provided has not been verified.</p>";
			}
			
			//create the recovery link
			$recovery_link = "reset_password.php?file=".
				hash('sha256', $_row['username'], false).
				"&reset=".
				hash('sha256', $_row['password'], false);
			//set the recovery link in the page
			$webpage->convert("RECOVERY_LINK", $recovery_link);
		} else {
			//query error, more than one user with the same username
		}
	}
	$webpage->printPage();
?>