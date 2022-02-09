<?php
	// Check if session exists
	session_start();
	if (isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
		$temp = $_SESSION['username'];
		$user_id = $_SESSION['user_id'];
	  //echo "<div style='margin-left: 5rem; padding: 1rem'>Session is active with $temp</div>";
	} else {
		header("Location: ../user_login/login.php");
	}

	// Required files
	require_once '../../../classes/TaskFactory.class.php';
	require_once '../../../utilities/app_config.php';


	if (isset($_REQUEST['task_id'])) {
		//create a query to change that task
		$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		$_qry = sprintf("UPDATE tasks SET is_complete = NOT is_complete WHERE id = %d;",
						mysqli_real_escape_string($db_connection, $_REQUEST['task_id']));
		if ($db_connection->query($_qry)) {
			echo "1"; //everything went ok
		} else {
			echo "0";
		}
	} else {
		echo "0";
	}
?>