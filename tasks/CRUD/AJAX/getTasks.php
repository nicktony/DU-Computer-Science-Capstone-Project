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


	if (isset($_REQUEST['user_id'])) {
		//get the tasks for this user_error
		$factory = new TaskFactory();
		if (isset($_SESSION['last_update_date'])) {
			$lastUpdateDate = $_SESSION['last_update_date'];
		} else {
			$lastUpdateDate = "";
		}
		
		$tasks = $factory->FetchTasks($_REQUEST['user_id'], $lastUpdateDate);
		
		$_SESSION['last_update_date'] = date('Y-m-d');
		
		echo json_encode($tasks);
	}
?>