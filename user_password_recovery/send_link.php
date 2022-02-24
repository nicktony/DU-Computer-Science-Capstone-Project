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
			$pageMessage = "";
			
			//check that the email is verified
			$_row = $_result->fetch_array(MYSQLI_BOTH);
			if (!$_row['email_verified']) {
				$pageMessage .= "The email address you have provided has not been verified.<br>";
			}
			
			$username = $_row['username'];
			
			//create the recovery link
			$recovery_link = "https://www.taskless.app/user_password_recovery/reset_password.php?file=".
				hash('sha256', $_row['username'], false).
				"&reset=".
				hash('sha256', $_row['password'], false);
				
			
			//create the email
			$recipient = $_row['email'];
			$subject = "Recover your Taskless password";
			
			//add headers
			$headers = 'MIME-Version 1.0\r\n';
			$headers .= 'Content-type: text/html; charset=iso-8859-1\r\n';
			$headers .= "To: {$username} <{$recipient}>\r\n";
			$headers .= 'From: Taskless Support <support@taskless.app>';
			
			//use the webpage class and an html doc to create the email body
			$emailPage = new webpage("emailhtml.html");
			$emailPage->createPage("Password Recovery");
			$emailPage->convert("RECOVERY_LINK", $recovery_link);
			
			$message = $emailPage->html;
			
			//send it, if everything works let the user know
			if (mail($recipient, $subject, $message, $headers)) {
				$pageMessage .= "An email has been sent to the email address of the user you entered.";
			} else {
				$pageMessage .= "An error has occured, the email was not sent.";
			}
			
			$webpage->convert("MESSAGE", $pageMessage);
		} else {
			//query error, more than one user with the same username
			$webpage->convert("MESSAGE", "Could not find the username you specified");
		}
	}
	$webpage->printPage();
?>