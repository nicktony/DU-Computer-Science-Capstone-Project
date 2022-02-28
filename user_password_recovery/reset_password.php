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
	
	//check for the verification code
	if (isset($_REQUEST['v_code']) && isset($_SESSION['recovery_id'])) {
		$verificationCode = $_REQUEST['v_code'];
		$pageContent = "";
		
		//open up a database connection
		$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		$_qry = sprintf("SELECT verification_code, v_code_valid_date FROM users WHERE id = %d;",
						mysqli_real_escape_string($db_connection, $_SESSION['recovery_id']));
		if ($_result = $db_connection->query($_qry)) {
			if ($_result->num_rows == 1) {
				$_row = $_result->fetch_array(MYSQLI_BOTH);
					if ($_row['v_code_valid_date'] == date('Y-m-d')) { //check the validity date of the code
						if ($_row['verification_code'] == $verificationCode) { //check that the code the user entered matches
							//present the reset password form
							$pageContent = '<p>Please complete the form below to reset your password</p>
									<form action="finalize.php" method="POST">
										<label for="newpassword">New Password: </label>
										<input type="password" name="newpassword" pattern="^.{5,20}$"
												title="between 5 and 20 characters in length" required />
										<br>
										<label for="p_confirm">Confirm New Password: </label>
										<input type="password" name="p_confirm" pattern="^.{5,20}$"
												title="between 5 and 20 characters in length" required />
										<br>
										<input type="submit" value="Submit" />
									</form>';
							$_SESSION['v_code'] = $verificationCode;
						} else {
							//the code the user entered does not match
							$_SESSION['incorrect_input'] = true;
							$pageContent = '<p>The code that you entered does not match what we have sent.</p>
											<p>Please <a href="send_link.php">try again</a></p>';
						}
					} else {
						//the code the user entered is no longer valid
						$pageContent = '<p>There is no active verification code on your account</p>
										<a href="../home/home.php">Go Home</a>';
					}
			}
		}
		
		$webpage->convert('PAGE_CONTENT', $pageContent);
		$db_connection->close();
	} else {
		//the user shouldn't be here
	}
	
	$webpage->printPage();
?>
