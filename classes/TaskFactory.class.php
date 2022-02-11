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
	function FetchTasks($user_id, $lastUpdateDate) {
		//connect to the database
		$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		
		
		//if rollovers/recurrences haven't been updated yet
		if (strlen($lastUpdateDate) == 0 || $lastUpdateDate < date('Y-m-d')) {
			
			//update recurring tasks
			//daily
			$_qry = sprintf("UPDATE tasks " .
							"SET start_date = '%s', is_complete = %d " .
							"WHERE user_id = %d ".
							"AND recurrence_interval > 0 AND interval_unit = 0 ". 
							"AND CURDATE() = DATE_ADD(start_date, INTERVAL recurrence_interval DAY);",
							mysqli_real_escape_string($db_connection, date('Y-m-d')),
							mysqli_real_escape_string($db_connection, false),
							mysqli_real_escape_string($db_connection, $user_id));
			
			$db_connection->query($_qry);
			
			//weekly
			$_qry = sprintf("UPDATE tasks " .
							"SET start_date = '%s', is_complete = %d " .
							"WHERE user_id = %d ".
							"AND recurrence_interval > 0 AND interval_unit = 1 ". 
							"AND CURDATE() = DATE_ADD(start_date, INTERVAL recurrence_interval WEEK);",
							mysqli_real_escape_string($db_connection, date('Y-m-d')),
							mysqli_real_escape_string($db_connection, false),
							mysqli_real_escape_string($db_connection, $user_id));
			
			$db_connection->query($_qry);
			
			//monthly
			$_qry = sprintf("UPDATE tasks " .
							"SET start_date = '%s', is_complete = %d " .
							"WHERE user_id = %d ".
							"AND recurrence_interval > 0 AND interval_unit = 2 ". 
							"AND CURDATE() = DATE_ADD(start_date, INTERVAL recurrence_interval MONTH);",
							mysqli_real_escape_string($db_connection, date('Y-m-d')),
							mysqli_real_escape_string($db_connection, false),
							mysqli_real_escape_string($db_connection, $user_id));
			
			$db_connection->query($_qry);
			
			//yearly
			$_qry = sprintf("UPDATE tasks " .
							"SET start_date = '%s', is_complete = %d " .
							"WHERE user_id = %d ".
							"AND recurrence_interval > 0 AND interval_unit = 3 ". 
							"AND CURDATE() = DATE_ADD(start_date, INTERVAL recurrence_interval YEAR);",
							mysqli_real_escape_string($db_connection, date('Y-m-d')),
							mysqli_real_escape_string($db_connection, false),
							mysqli_real_escape_string($db_connection, $user_id));
			
			$db_connection->query($_qry);
			
			//rolls over all incomplete tasks from the previous day(s)
			$_qry = sprintf("UPDATE tasks SET start_date = '%s' WHERE user_id = %d AND start_date < '%s' AND rolls_over = %d and is_complete = %d;",
							mysqli_real_escape_string($db_connection, date('Y-m-d')),
							mysqli_real_escape_string($db_connection, $user_id),
							mysqli_real_escape_string($db_connection, date('Y-m-d')),
							mysqli_real_escape_string($db_connection, true),
							mysqli_real_escape_string($db_connection, false));
			
			$db_connection->query($_qry);
		}
		
		
		//now that everything is rolled over, we get the tasks with a start date of today
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
			}
		}
		
		//close and return
		$db_connection->close();
		return $tasks;
	}
	
	/***
	*	Create Task(...)
			Inputs: All the information for a task that belongs in the DB
			
			Description: Accepts all of the inputs and uses them to dynamically create an input query for the database.
				If any of the inputs are null, they aren't used in the query. This system seemed more efficient than a decision tree.
	*/
	
	function CreateTask($user_id, $title, $description, $start_date, $recurrence_interval, $recurrence_unit, $priority, $rolls_over) {
		//connect to database
		$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		
		//initialize strings for the query
		$insertString = "INSERT INTO tasks (";
		$valuesString = "VALUES (";
		
		//these arrays hold query values
		$insertVals = array();
		$valuesVals = array();
		//this array holds the input parameters
		$queryVals = array();
		
		//if any value is set
		if (isset($user_id)) {
			//push them into the string arrays for the query
			array_push($insertVals, "user_id");
			array_push($valuesVals, "%d");
			//escape the parameter and add its value to the insert values
			array_push($queryVals, mysqli_real_escape_string($db_connection, $user_id));
		}
		//do this for all of them
		if (isset($title)) {
			array_push($insertVals, "title");
			array_push($valuesVals, "'%s'");
			array_push($queryVals, mysqli_real_escape_string($db_connection, $title));
		}
		if (isset($description)) {
			array_push($insertVals, "description");
			array_push($valuesVals, "'%s'");
			array_push($queryVals, mysqli_real_escape_string($db_connection, $description));
		}
		if (isset($start_date)) {
			array_push($insertVals, "start_date");
			array_push($valuesVals, "'%s'");
			array_push($queryVals, mysqli_real_escape_string($db_connection, $start_date));
		}
		//these two only get inserted as a pair
		if (isset($recurrence_interval) && isset($recurrence_unit)) {
			array_push($insertVals, "recurrence_interval");
			array_push($valuesVals, "%d");
			array_push($queryVals, mysqli_real_escape_string($db_connection, $recurrence_interval));
			array_push($insertVals, "interval_unit");
			array_push($valuesVals, "%d");
			array_push($queryVals, mysqli_real_escape_string($db_connection, $recurrence_unit));
			
		}
		if (isset($priority)) {
			array_push($insertVals, "priority");
			array_push($valuesVals, "%d");
			array_push($queryVals, mysqli_real_escape_string($db_connection, $priority));
		}
		if (isset($rolls_over)) {
			array_push($insertVals, "rolls_over");
			array_push($valuesVals, "%d");
			array_push($queryVals, mysqli_real_escape_string($db_connection, $rolls_over));
		}
		
		//loop through the array values to dynamically create a query string
		for ($i = 0; $i < count($insertVals); $i++) {
			if ($i == count($insertVals) - 1) {
				//last iteration
				$insertString .= $insertVals[$i] . ") ";
				$valuesString .= $valuesVals[$i] . ");";
			} else {
				$insertString .= $insertVals[$i] . ", ";
				$valuesString .= $valuesVals[$i] . ", ";
			}
		}
		
		//use the strings and the insert values to concatenate the string
		$_qry = vsprintf($insertString . $valuesString, $queryVals);
		
		//perform the query and if good, create a task object
		if ($db_connection->query($_qry)) {
			$obj = new Task();
			$obj->id = $db_connection->insert_id;
			$obj->user_id = $user_id;
			$obj->title = $title;
			$obj->description = $description;
			$obj->start_date = $start_date;
			$obj->recurrence_interval = $recurrence_interval;
			$obj->interval_unit = $recurrence_unit;
			$obj->priority = $priority;
			$obj->is_complete = self::DEFAULT_IS_COMPLETE;
			$obj->rolls_over = $rolls_over;
			
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