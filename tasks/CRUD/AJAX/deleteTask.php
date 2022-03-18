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
	require_once '../../../utilities/app_config.php';


	if (isset($_REQUEST['info'])) {
		//get all of the IDs that were selected to be deleted
		$tasksToBeDeleted = json_decode($_REQUEST['info']);
		
		//open up a connection to the database
		$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		
		//flag for all the query results
		$allGood = true;
		
		//foreach ID to be deleted
		for ($i = 0; $i < count($tasksToBeDeleted); $i += 1) {
			//construct a query and delete it
			$_qry = sprintf("DELETE FROM tasks WHERE id = %d;",
				mysqli_real_escape_string($db_connection, $tasksToBeDeleted[$i]));
				
			//if any of them go bad, report it to the frontend (this should honestly be more verbose)
			if (!($db_connection->query($_qry)))
				$allGood = false;
		}
		
		//close the connection
		$db_connection->close();
		
		//report back to the client the results
		if ($allGood)
			echo "1";
		else
			echo "0";
		
	} else {
		echo "0";
	}
?>