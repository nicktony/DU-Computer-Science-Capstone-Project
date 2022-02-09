<?php
	
// Required files
require_once '../classes/webpage.class.php';
require_once '../utilities/app_config.php';

// Create webpage
$webpage = new webpage('./new_user.html');
$webpage->createPage("New User");

// Redirect if session is already started
session_start();
if (isset($_SESSION['username'])) {
	header("Location: ../home/home.php");
	session_destroy();
}

//error message
$err_msg = "";

//submit form by default
$submit_form = true;

$username = "";
$email_address = "";

//begin serverside validation of inputs
if (isset($_REQUEST['newusername'])) {
	//get values from the form and check against regex
	$username = $_REQUEST['newusername'];
	$pattern = "/^[A-Za-z0-9_]{5,20}$/";
	if (!preg_match($pattern, $username)) {
		$err_msg = "Username must be alphanumeric characters and underscores betwen 5 and 20" .
			" characters.";
		$username = "";
		$submit_form = false; //if they don't match, don't submit the form
	}
	
	//make sure username is unique
	$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	$_qry = sprintf("SELECT %s FROM users WHERE %s = '%s';",
			mysqli_real_escape_string($db_connection, 'username'),
			mysqli_real_escape_string($db_connection, 'username'),
			mysqli_real_escape_string($db_connection, $username));
	$_result = $db_connection->query($_qry);
	$db_connection->close();
	if ($_result->num_rows > 0) {
		$err_msg = $username . " is already taken.";
		$username = "";
		$submit_form = false;
	}
	
	//populate form fields with values that were still valid
} else { $submit_form = false; }

//check password for proper format
if (isset($_REQUEST['newpassword'])) {
	$password = $_REQUEST['newpassword'];
	$pattern = "/^.{5,20}$/";
	if (!preg_match($pattern, $password)) {
		$err_msg = "Password must be between 5 and 20 characters";
		$password = "";
		$submit_form = false;
	}
} else { $submit_form = false; }

//make sure the user's confirmed password matches the original input
if (isset($_REQUEST['p_confirm'])) {
	$p_confirm = $_REQUEST['p_confirm'];
	if ($password != $p_confirm) {
		$err_msg = "Passwords do not match!";
	}
} else { $submit_form = false; }

//validate email address
if (isset($_REQUEST['email_address'])) {
	if (!filter_var($_REQUEST['email_address'], FILTER_VALIDATE_EMAIL)) {
		$submit_form = false;
		$err_msg = "Invalid Email Address";
	} else {
		$email_address = $_REQUEST['email_address'];
		
		
	}
} else { $submit_form = false; }

//if everything is alright then create the profile
if (($submit_form) && ($password == $p_confirm)) {
	//submit form
	$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	$err_msg = "Success!";
	$_qry = sprintf("INSERT INTO users " .
				"(username, password, email) " .
				"VALUES ('%s', '%s', '%s');",
				mysqli_real_escape_string($db_connection, $username),
				mysqli_real_escape_string($db_connection, crypt($password, $username)), //always make sure to encrypt passwords
				mysqli_real_escape_string($db_connection, $email_address));
	
	if ($db_connection->query($_qry)) {
		$db_connection->close();
		//automatically log the user in if the query went okay
		$url = "/taskless/user_login/login.php?" .
			"username={$username}&password={$password}";
		header("Location: {$url}");
	} else {
		//old version of the application had a default error page, should probably do this here too
		$db_connection->close();
		$_SESSION['error_message'] = "Failed to create user";
		//header("Location: " . ERROR_PAGE);
	}
} else {
	//give feedback to the user about what went wrong
	$webpage->convert("ERR_MSG", "<font style='color:red; font-size: 1em; padding-left: 1em;'>{$err_msg}</font>");
	//populate form fields with values that were still valid
	$webpage->convert("EMAIL_ADDRESS", $email_address);
	$webpage->convert("USERNAME", $username);
}

// Output webpage
$webpage->printPage();

?>