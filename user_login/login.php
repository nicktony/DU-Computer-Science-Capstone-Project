<?php
	
// Required files
require_once '../classes/webpage.class.php';
require_once '../utilities/app_config.php';

// Create webpage
$webpage = new webpage('./login.html');

// Redirect if session is already started
session_start(); 
if (isset($S_SESSION['username'])) {
	header("Location: ../home/home.php");
}

//if the information is present
if (isset($_REQUEST['username'])) {
<<<<<<< HEAD
		if (isset($_REQUEST['password'])) {
			//get it
			$username = $_REQUEST['username'];
			$password = $_REQUEST['password'];
			
			//open a connection
			$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
			
			
			$_qry = sprintf("SELECT username, id " .
							"FROM users " .
							"WHERE username = '%s' AND password = '%s';",
							mysqli_real_escape_string($db_connection, $username),
							mysqli_real_escape_string($db_connection, crypt($password, $username)));
			if ($_result = $db_connection->query($_qry)) {//if query okay
				$number_of_results = $_result->num_rows; //get number of rows
				if ($number_of_results == 1) {
					//log user in
					$user_information = $_result->fetch_array(MYSQLI_BOTH);
					
					//set the session
					$_SESSION['user_id'] = $user_information['id'];
					$_SESSION['username'] = $user_information['username'];
					
				} else {
					//duplicate rows in the DB
				}
			} else {
				//query failed
=======
	if (isset($_REQUEST['password'])) {
		//get it
		$username = $_REQUEST['username'];
		$password = $_REQUEST['password'];
		
		//open a connection
		$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		
		$result_ok = true;
		
		$_qry = sprintf("SELECT username, id " .
						"FROM users " .
						"WHERE username = '%s' AND password = '%s';",
						mysqli_real_escape_string($db_connection, $username),
						mysqli_real_escape_string($db_connection, crypt($password, $username)));
		if ($_result = $db_connection->query($_qry)) {//if query okay
			$nor = $_result->num_rows; //get number of rows
			if ($nor == 0) {
				//incorrect username or password
				$result_ok = false;
			} elseif ($nor == 1) {
				//log user in
				$info = $_result->fetch_array(MYSQLI_BOTH);
				
				//set the session
				$_SESSION['user_id'] = $info['user_id'];
				$_SESSION['username'] = $info['username'];
				
			} elseif ($nor > 1) {
				//duplicate rows in the DB
				$result_ok = false; //possibly other error handling here
>>>>>>> 4fa9438d709b854d641be1feff6e968a6ffe6f9a
			}
		} else {
<<<<<<< HEAD
			//no password
=======
			//query failed
			$result_ok = false;
>>>>>>> 4fa9438d709b854d641be1feff6e968a6ffe6f9a
		}
		
		$db_connection->close();
		header("Location: ../home/home.php");
	} else {
<<<<<<< HEAD
		//no username
=======
		//no password
		$_SESSION['error_message'] = "no password sent to authorize_user.php";
		//header("Location: " . ERROR_PAGE);
>>>>>>> 4fa9438d709b854d641be1feff6e968a6ffe6f9a
	}
} else {
	//no username
	$_SESSION['error_message'] = "no username sent to authorize_user.php";
	//header("Location: " . ERROR_PAGE);
}


// Output webpage
$webpage->printPage();

?>