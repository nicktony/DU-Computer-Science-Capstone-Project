<?php

// Check if session exists
session_start();
if (isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
	$temp = $_SESSION['username'];
	$user_id = $_SESSION['user_id'];
} else {
	header("Location: ../../user_login/login.php");
}

// Required files
require_once '../../classes/TaskFactory.class.php';

if (isset($_REQUEST['input'])) {
	$formValues = json_decode($_REQUEST['input'], true);
	
	//validation of the form elements
	$invalid = false;
	if (isset($formValues['title'])) {
		$title = $formValues['title'];
	} else {
		$invalid = true;
	}
	if (isset($formValues['description'])) {
		$description = $formValues['description'];
	} else {
		$description = null;
	}
	if (isset($formValues['start_date'])) {
		$start_date = $formValues['start_date'];
	} else {
		$invalid = true;
	}
	if (isset($formValues['rolls_over']) && $formValues['rolls_over'] == "yes") {
		$rolls_over = true;
	} else {
		$rolls_over = false;
	}

	if (isset($formValues['recurrence_cb']) && $formValues['recurrence_cb'] == "yes") {
		$recurrence_interval = $formValues['recurrence_interval'];
		$recurrence_unit = $formValues['recurrence_unit'];
	} else {
		$recurrence_interval = null;
		$recurrence_unit = null;
	}

	if (isset($formValues['priority'])) {
		$priority = $formValues['priority'];
	} else {
		$invalid = true;
	}

	if (!$invalid) {
		//get the tasks for this user_error
		$factory = new TaskFactory();
		
		$newTask = $factory->CreateTask($user_id, $title, $description, $start_date, $recurrence_interval, $recurrence_unit, $priority, $rolls_over);
		
		echo json_encode($newTask);
	}
}
?>