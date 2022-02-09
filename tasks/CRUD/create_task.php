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

//validation of the form elements
$invalid = false;
if (isset($_REQUEST['title'])) {
	$title = $_REQUEST['title'];
} else {
	$invalid = true;
}
if (isset($_REQUEST['description'])) {
	$description = $_REQUEST['description'];
} else {
	$description = null;
}
if (isset($_REQUEST['start_date'])) {
	$start_date = $_REQUEST['start_date'];
} else {
	$invalid = true;
}
if (isset($_REQUEST['rolls_over']) && $_REQUEST['rolls_over'] == "yes") {
	$rolls_over = true;
} else {
	$rolls_over = false;
}

if (isset($_REQUEST['recurrence_cb']) && $_REQUEST['recurrence_cb'] == "yes") {
	$recurrence_interval = $_REQUEST['recurrence_interval'];
	$recurrence_unit = $_REQUEST['recurrence_unit'];
} else {
	$recurrence_interval = null;
	$recurrence_unit = null;
}

if (isset($_REQUEST['priority'])) {
	$priority = $_REQUEST['priority'];
} else {
	$invalid = true;
}

if (!$invalid) {
	//get the tasks for this user_error
	$factory = new TaskFactory();
	//$factory->CreateTaskNoDescription($user_id, $title);
	$factory->CreateTask($user_id, $title, $description, $start_date, $recurrence_interval, $recurrence_unit, $priority, $rolls_over);
}


header("Location: ../../tasks/tasks.php");
exit;
?>