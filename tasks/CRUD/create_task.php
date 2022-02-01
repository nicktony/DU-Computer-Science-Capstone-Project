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

if (isset($_REQUEST['title'])) {
	$title = $_REQUEST['title'];
}
if (isset($_REQUEST['description'])) {
	$description = $_REQUEST['description'];
}
if (isset($_REQUEST['start_date'])) {
	$start_date = $_REQUEST['start_date'];
}
if (isset($_REQUEST['rolls_over']) && $_REQUEST['rolls_over'] == "yes") {
	$rolls_over = true;
} else {
	$rolls_over = false;
}
if (isset($_REQUEST['priority'])) {
	$priority = $_REQUEST['priority'];
}

//get the tasks for this user_error
$factory = new TaskFactory();

$factory->CreateTaskNoDescription($user_id, $title);
header("Location: ../../home/home.php");
exit;
?>