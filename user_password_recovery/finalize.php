<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="recovery_form.css">
	</head>
	<body>
		<header>
			<h1>TASK<span>LESS</span></h1>
		</header>

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
	
	//get the information from the recovery form
	if (isset($_REQUEST['file']) && isset($_REQUEST['reset']) && isset($_REQUEST['newpassword']) && isset($_REQUEST['p_confirm'])) {
		$hashed_username = $_REQUEST['file'];
		$hashed_password = $_REQUEST['reset'];
		$new_password = $_REQUEST['newpassword'];
		$pass_confirm = $_REQUEST['p_confirm'];
		
		//check that the password and confirmation password match
		if ($new_password != $pass_confirm) {
			$_SESSION['ERROR_MSG'] = "Passwords do not match!";
			header("Location: reset_password.php?file={$hashed_username}&reset={$hashed_password}");
			die();
		} else {
			//check that they are the right format
			$pattern = "/^.{5,20}$/";
			if (!preg_match($pattern, $new_password)) {
				//if invalid, set an error message and return
				$err_msg = "Password must be between 5 and 20 characters";
				$_SESSION['ERROR_MSG'] = "Password invalid!";
				header("Location: reset_password.php?file={$hashed_username}&reset={$hashed_password}");
				die();
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
						?>
						<p>Password updated successfully!</p>
						<a href="..">Go Home</a>
						<?php
					}
				}
			}
		}
	}
	?>
	</body>
</html>