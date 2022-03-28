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
	
	$formContent = "";
	$pageMessage = "";
	$verificationMessage = "";
	$firstAttempt = true;
	
	//if this is a second attempt
	if (isset($_SESSION['incorrect_input'])) {
		$pageMessage = "An email has been sent to the email address of the user you entered."
								. " Please enter the code contained within the email in the textbox below.";
		
<<<<<<< HEAD
		$formContent = "<form method='POST' action='reset_password.php' name='recoveryForm'>
									<div class='text_area'>
										<input type='text' name='v_code' />
										<label>Verification Code</label>
									</div>
=======
		$formContent = "<form method='POST' action='reset_password.php'>
									<input type='' name='v_code' /><br>
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
									<input type='submit' value='Submit' pattern='^[0-9]{6}$' title='Please enter a 6 digit number' required />
								</form>";
		$webpage->convert("FORM_CONTENT", $formContent);
		//unset the variable
		unset($_SESSION['incorrect_input']);
		
		//clear the tag in the template
		$webpage->convert("VERIFICATION_MESSAGE", $verificationMessage);
<<<<<<< HEAD
		$webpage->convert("MESSAGE", $pageMessage);
=======
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
		
		//skip the next part of the script and jump to displaying the page
		$firstAttempt = false;
	}
	
	//get the information from the recovery form
	if (isset($_REQUEST['username']) && $firstAttempt) {
		$username = $_REQUEST['username'];
		
		//get their information from the DB
		$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		$_qry = sprintf("SELECT id, password, email, email_verified FROM users WHERE username = '%s';",
			mysqli_real_escape_string($db_connection, $username));
		$_result = $db_connection->query($_qry);
		
		
		if ($_result->num_rows == 1) {
			//check that the email is verified
			$_row = $_result->fetch_array(MYSQLI_BOTH);
			
			
			if (!$_row['email_verified']) {
				$verificationMessage = "The email address you have provided has not been verified.";
			} else {
				$verificationMessage = "Thank you for having a verified email address!";
			}
			$webpage->convert("VERIFICATION_MESSAGE", $verificationMessage);
			
			//create a verification code
			$verificationCode = rand(100000, 999999);
			$user_id = $_row['id'];
			
			//store it in the database along with the date of creation
			$_qry = sprintf("UPDATE users SET verification_code = %d, v_code_valid_date = '%s' WHERE id = %d;",
							mysqli_real_escape_string($db_connection, $verificationCode),
							mysqli_real_escape_string($db_connection, date('Y-m-d')),
							mysqli_real_escape_string($db_connection, $user_id));
			$db_connection->query($_qry);
			
			//create the email
			$recipient = $_row['email'];
			$subject = "Recover your Taskless password";
			
			$message = "Hello {$username}, we are sorry that you have lost your Taskless password.\r\n"
						. "We have provided a verification code below, enter it into our password recovery page "
						. "and you will be able to reset your password.\r\n"
						. "Verification Code: {$verificationCode}\r\n";
			
			//send it, if everything works let the user know
			if (true /*mail($recipient, $subject, $message, $headers)*/) {
				$pageMessage = "An email has been sent to the email address of the user you entered."
								. " Please enter the code contained within the email in the textbox below.";
								
<<<<<<< HEAD
				$formContent = "<form method='POST' action='reset_password.php' name='recoveryForm'>
									<div class='text_area'>
										<input type='text' name='v_code' />
										<label>Verification Code</label>
									</div>
=======
				$formContent = "<form method='POST' action='reset_password.php'>
									<input type='' name='v_code' /><br>
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
									<input type='submit' value='Submit' pattern='^[0-9]{6}$' title='Please enter a 6 digit number' required />
								</form>";
				$webpage->convert("FORM_CONTENT", $formContent);
				
				$_SESSION['recovery_id'] = $user_id;
			} else {
				$pageMessage = "An error has occured, the email was not sent."
								. " Please try again later or contact Taskless support.";
			}
			
			$webpage->convert("MESSAGE", $pageMessage);
		} else {
			//query error, more than one user with the same username
			$webpage->convert("MESSAGE", "Could not find the username you specified");
			$webpage->convert("FORM_CONTENT", $formContent);
			$webpage->convert("VERIFICATION_MESSAGE", $verificationMessage);
		}
		
		//close the connection to the database
		$db_connection->close();
	}
	$webpage->printPage();
?>