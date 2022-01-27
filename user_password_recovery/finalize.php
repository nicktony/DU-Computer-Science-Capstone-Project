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
	
	//create webpage
	$webpage = new webpage("finalize.html");
	$webpage->createPage("Password Reset");
	
	//get the information from the recovery form
	if (isset($_REQUEST['file']) && isset($_REQUEST['reset']) && isset($_REQUEST['newpassword']) && isset($_REQUEST['p_confirm'])) {
		$hashed_username = $_REQUEST['file'];
		$hashed_password = $_REQUEST['reset'];
		$new_password = $_REQUEST['newpassword'];
		$pass_confirm = $_REQUEST['p_confirm'];
		
		$try_again_link = "<a href='reset_password.php?file={$hashed_username}&reset={$hashed_password}'>Try Again</a>";
		//check that the password and confirmation password match
		if ($new_password != $pass_confirm) {
			$webpage->convert("RESET_MESSAGE", "Passwords do not match!");
			$webpage->convert("RESET_LINK", $try_again_link);
		} else {
			//check that they are the right format
			$pattern = "/^.{5,20}$/";
			if (!preg_match($pattern, $new_password)) {
				//if invalid, set an error message and return
				$webpage->convert("RESET_MESSAGE", "Password invalid!");
				$webpage->convert("RESET_LINK", $try_again_link);
			} else {
				//if everything is good, then reset the password
				//create database connection
				$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
				//find the username for the user
				$_qry = sprintf("SELECT id, username FROM users WHERE SHA2(username, 256)='%s' AND SHA2(password, 256)='%s';",
					mysqli_real_escape_string($db_connection, $hashed_username),
					mysqli_real_escape_string($db_connection, $hashed_password));
				$_result = $db_connection->query($_qry);
				if ($_result->num_rows == 1) {
					$_row = $_result->fetch_array(MYSQLI_BOTH);
					$username = $_row['username'];
					$user_id = $_row['id'];
					
					//now with the username we can salt the new password
					$_qry = sprintf("UPDATE users SET password='%s' WHERE id='%d';",
						mysqli_real_escape_string($db_connection, crypt($new_password, $username)),
						mysqli_real_escape_string($db_connection, $user_id));
					if ($db_connection->query($_qry)) {
						//if the password updates successfully
						$webpage->convert("RESET_MESSAGE", "Password changed successfully!");
						$webpage->convert("RESET_LINK", "<a href='..'>Go Home</a>");
					}
				}
			}
		}
	}
	
	$webpage->printPage();
?>