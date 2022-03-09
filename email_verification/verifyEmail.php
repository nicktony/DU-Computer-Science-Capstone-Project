<?php
	require_once "../utilities/app_config.php";
	require_once "../classes/webpage.class.php";
	
	// Check if session exists
	session_start();
	if (isset($_SESSION['username'])) {
		$username = $_SESSION['username'];
		$user_id = $_SESSION['user_id'];
		
		//get their information from the DB
		$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		$_qry = sprintf("SELECT email FROM users WHERE id = %d;",
			mysqli_real_escape_string($db_connection, $user_id));
		$_result = $db_connection->query($_qry);
		
		if ($_result->num_rows == 1) {
			//create code
			$code = rand(100000, 999999);
			//put it in the database
			$_qry = sprintf("UPDATE users SET verification_code = %d, v_code_valid_date = '%s' WHERE id = %d;",
							mysqli_real_escape_string($db_connection, $code),
							mysqli_real_escape_string($db_connection, date('Y-m-d')),
							mysqli_real_escape_string($db_connection, $user_id));
			$db_connection->query($_qry);
			$db_connection->close();
			
			$pageMessage = "";
			
			//get the row
			$_row = $_result->fetch_array(MYSQLI_BOTH);
				
			
			//create the email
			$recipient = $_row['email'];
			$subject = "Verify Your Email";
			
			//add headers
			$headers = "To: {$username} <{$recipient}>\r\n";
			$headers .= 'From: Taskless Support <support@taskless.app>';
			
			//generate a code to include in the email
			$message = "{$username}, here is your Taskless verification code: " . $code;
			
			//wordwrap it
			$message = wordwrap($message, 70, '\r\n');
			
			$webpage = new webpage();
			$webpage->createPage("Verify Email");
			
			//send it, if everything works let the user know
			if (true /*mail($recipient, $subject, $message, $headers)*/) {
				$html = file_get_contents('./emailConfirmationCode.html');
			} else {
				$html = "<p>There was a problem sending you a verification email</p>";
			}
			
			$webpage->inputHTML($html);
			
		} else {
			//query error, more than one user with the same username
		}
	} else {
		//no session
		header("Location: ../user_login/login.php");
	}
	
	$webpage->printPage();
?>