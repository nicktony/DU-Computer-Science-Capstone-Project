<?php

// Check if session exists
session_start();
if (isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
	$temp = $_SESSION['username'];
	$user_id = $_SESSION['user_id'];
} else {
	exit;
}

// Required files
require_once '../classes/TaskFactory.class.php';

if (isset($_REQUEST['start_date']) && isset($_REQUEST['end_date'])) {
	$factory = new Taskfactory();
	$tasks = $factory->FetchTasksFromDateRange($user_id, $_REQUEST['start_date'], $_REQUEST['end_date']);
	
	$organizedTasks = array();
	
	foreach ($tasks[1] as $task) {
		if (!array_key_exists($task->start_date, $organizedTasks))
			$organizedTasks[$task->start_date] = array();
		
		array_push($organizedTasks[$task->start_date], $task);
	}
	
	
	$endDate = new DateTime($_REQUEST['end_date']);
	
	foreach ($tasks[0] as $task) {
		$interval = $task->recurrence_interval;
		$unit = $task->interval_unit;
		$startDate = $task->start_date;
		
		$date = new DateTime($startDate);
		while ($date < $endDate) {
			if (!array_key_exists($date->format('Y-m-d'), $organizedTasks))
				$organizedTasks[$date->format('Y-m-d')] = array();
		
			array_push($organizedTasks[$date->format('Y-m-d')], $task);
			
			switch($unit) {
				case 0:
					$date->add(new DateInterval('P'.$interval.'D'));
				break;
				case 1:
					$date->add(new DateInterval('P'.$interval.'W'));
				break;
				case 2:
					$date->add(new DateInterval('P'.$interval.'M'));
				break;
				case 3:
					$date->add(new DateInterval('P'.$interval.'Y'));
				break;
			}
		}
		
	}
	
	echo json_encode($organizedTasks);
}




// if (!array_key_exists($_row['start_date'], $tasks)) {
	// $tasks[$_row['start_date']] = array();
// }

// array_push($tasks[$_row['start_date']], $obj);
?>