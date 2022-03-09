<?php
	require_once "../utilities/app_config.php";
	require_once "../classes/webpage.class.php";
	
	// Check if session exists
	session_start();
	if (isset($_SESSION['username'])) {
		$username = $_SESSION['username'];
		$user_id = $_SESSION['user_id'];
	} else {
		//no session
		header("Location: ../user_login/login.php");
	}
	
	$webpage = new webpage();
	$webpage->createPage("Thank you");
	
	if (isset($_REQUEST['emailVerificationCode'])) {
		$code = $_REQUEST['emailVerificationCode'];
		
		$html = "";
		
		//check the database for the verification code validity date
		$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		$_qry = sprintf("SELECT verification_code, v_code_valid_date FROM users WHERE id = %d;",
						mysqli_real_escape_string($db_connection, $user_id));
		if ($_result = $db_connection->query($_qry)) {
			if ($_result->num_rows == 1) {
				$_row = $_result->fetch_array(MYSQLI_BOTH);
				if ($_row['v_code_valid_date'] == date('Y-m-d')) { //check the date
					if ($code == $_row['verification_code']) { //check the verification code
						//email verified
						$_qry = sprintf("UPDATE users SET email_verified = %d WHERE id = %d",
										mysqli_real_escape_string($db_connection, true),
										mysqli_real_escape_string($db_connection, $user_id));
						$db_connection->query($_qry);
						$html = "<p>Thank you for verifying your email!</p>";
					} else {
						$html = "<p>The verification code you entered is incorrect</p>";
					}
				}
			}
		}
		
		$webpage->inputHTML($html);
	}
	
	$webpage->printPage();
?>