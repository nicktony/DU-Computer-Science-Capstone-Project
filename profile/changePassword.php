<?php

	// DB Information
	require_once '../utilities/app_config.php';
	
	//get session information
	session_start();
	if (isset($_SESSION['username'])) {
		$username = $_SESSION['username'];
		$user_id = $_SESSION['user_id'];
		
		//this page validates old password and new password information
		
		//for the old password
		if (isset($_REQUEST['old_password'])) {
			//open up database and see if that password is correct
			$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
			$_qry = sprintf("SELECT * FROM users WHERE id = %d AND username = '%s' AND password = '%s';",
							mysqli_real_escape_string($db_connection, $user_id),
							mysqli_real_escape_string($db_connection, $username),
							mysqli_real_escape_string($db_connection, crypt($_REQUEST['old_password'], $username)));
			
			if ($_result = $db_connection->query($_qry)) {
				if ($_result->num_rows == 1) {
					echo "1"; //if it is, return 1
				} else {
					echo "0"; //else 0
				}
				$db_connection->close();
				exit;
			}
		//if we're doing the new password
		} else if (isset($_REQUEST['new_password'])) {
			//validate it with a regular expression
			$password = $_REQUEST['new_password'];
			$pattern = "/^.{5,20}$/";
			if (preg_match($pattern, $password)) {
				//update it in the database
				$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
				$_qry = sprintf("UPDATE users SET password = '%s' WHERE id = %d;",
							mysqli_real_escape_string($db_connection, crypt($password, $username)),
							mysqli_real_escape_string($db_connection, $user_id));
				if ($db_connection->query($_qry)) {
					echo "1"; //all good
				} else {
					echo "0"; //query error
				}
				$db_connection->close();
			} else {
				//regex failed, return 0
				echo "0";
			}
		} else {
			//if session was there but no POST information, return 0
			echo "0";
		}
	} else { //if no session information, error code
		echo "0";
	}
?>