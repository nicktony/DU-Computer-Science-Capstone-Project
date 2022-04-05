<?php

// Check if session exists
session_start();
if (isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
	$temp = $_SESSION['username'];
	$user_id = $_SESSION['user_id'];
} else {
	header("Location: ../user_login/login.php");
}

// Required files
require '../classes/webpage.class.php';
require_once '../classes/TaskFactory.class.php';

// Create webpage
$webpage = new webpage();
$webpage->inputCSS('schedule.css');

// Assign title
$webpage->createPage('Schedule');

$html = file_get_contents('schedule.html');

// Input html body contents in template
$webpage->inputHTML($html);

// Output webpage
$webpage->printPage();

exit;

?>