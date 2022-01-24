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
			}
			
			$db_connection->close();
			header("Location: ../home/home.php");
		} else {
			//no password
		}
	} else {
		//no username
	}


// Output webpage
$webpage->printPage();

?>