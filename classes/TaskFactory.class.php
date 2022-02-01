<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/taskless/utilities/app_config.php";
require_once "Task.class.php";


class TaskFactory {
	//constants for interval units in the database
	const INTERVAL_UNIT_DAILY = 0;
	const INTERVAL_UNIT_WEEKLY = 1;
	const INTERVAL_UNIT_MONTHLY = 2;
	const INTERVAL_UNIT_YEARLY = 3;
	
	//default values 
	const DEFAULT_RECURRENCE_INTERVAL = 0;
	const DEFAULT_PRIORITY = 1;
	const DEFAULT_DESCRIPTION = "";
	const DEFAULT_ROLLS_OVER = false;
	const DEFAULT_IS_COMPLETE = false;
	
	//retrieve tasks from the database
	function FetchTasks($user_id) {
		//connect to the database
		$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		
		$_qry = sprintf("SELECT * FROM tasks " .
						"WHERE user_id = %d " . 
						"AND start_date = '%s';", 
						mysqli_real_escape_string($db_connection, $user_id),
						mysqli_real_escape_string($db_connection, date("Y-m-d")));
		
		//holds the tasks fetched from the database
		$tasks = array();
		
		//make the query and get collection the tasks into objects
		if ($_result = $db_connection->query($_qry)) {
			if ($_result->num_rows > 0) {
				while ($_row = $_result->fetch_array(MYSQLI_BOTH)) {
					$obj = new Task();
					$obj->id = $_row['id'];
					$obj->user_id = $_row['user_id'];
					$obj->title = $_row['title'];;
					$obj->description = $_row['description'];;
					$obj->start_date = $_row['start_date'];;
					$obj->recurrence_interval = $_row['recurrence_interval'];;
					$obj->priority = $_row['priority'];;
					$obj->is_complete = $_row['is_complete'];;
					$obj->rolls_over = $_row['rolls_over'];;
					
					array_push($tasks, $obj);
				}
			} else { echo date('Y-m-d') . " 0 results"; }
		}
		
		//close and return
		$db_connection->close();
		return $tasks;
	}
	
	//The most basic creation of a new task
	function CreateTaskNoDescription($user_id, $title) : ?Task {
		//connect to the database
		$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		
		//construct the query
		$_qry = sprintf("INSERT INTO tasks " .
						"(user_id, title) " .
						"VALUES (%d, '%s');",
						mysqli_real_escape_string($db_connection, $user_id),
						mysqli_real_escape_string($db_connection, $title));
						
		//perform the query and if good, create a task object
		if ($db_connection->query($_qry)) {
			$obj = new Task();
			$obj->id = $db_connection->insert_id;
			$obj->user_id = $user_id;
			$obj->title = $title;
			$obj->description = self::DEFAULT_DESCRIPTION;
			$obj->start_date = date("Y-m-d");
			$obj->recurrence_interval = self::DEFAULT_RECURRENCE_INTERVAL;
			$obj->priority = self::DEFAULT_PRIORITY;
			$obj->is_complete = self::DEFAULT_IS_COMPLETE;
			$obj->rolls_over = self::DEFAULT_ROLLS_OVER;
			
			//close database and return
			$db_connection->close();
			
			return $obj;
		} else {
			$db_connection->close();
			return null;
		}
	}
}
?>