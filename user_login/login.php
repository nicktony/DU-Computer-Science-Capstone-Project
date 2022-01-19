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
				}
			} else {
				//query failed
				$result_ok = false;
			}
			
			$db_connection->close();
			header("Location: ../home/home.php");
		} else {
			//no password
			$_SESSION['error_message'] = "no password sent to authorize_user.php";
			//header("Location: " . ERROR_PAGE);
		}
	} else {
		//no username
		$_SESSION['error_message'] = "no username sent to authorize_user.php";
		//header("Location: " . ERROR_PAGE);
	}


// Output webpage
$webpage->printPage();

?>