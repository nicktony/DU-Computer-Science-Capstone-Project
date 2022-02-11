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
$webpage->inputCSS('tasks.css');

// Assign title
$webpage->createPage('Tasks');
$currentDate = date('Y-m-d');
$createNewTaskForm = <<<EOD
<form name="taskCreateForm">
	<input name="title" type="text" placeholder="title" maxlength="128" required autofocus /><br>
	<input name="description" type="text" placeholder="description" maxlength="256" /><br>
	<label for="start_date">Start Date: </label><input name="start_date" type="date" value="{$currentDate}" required /><br>
	<label for="priority">Priority: </label><input name="priority" type="number" value="1" required /><br>
	<input name="rolls_over" type="checkbox" value="yes" checked /><label for="rolls_over">Make this task roll over to the next day if incomplete</label><br>
	<input id="recurrence_checkbox" name="recurrence_cb" type="checkbox" value="yes" unchecked /><label for="recurrence_cb">Make this task repeat</label><br>
	<div id="recurrence_container">
		Make this task repeat every <input name="recurrence_interval" value="1" type="number" />
		<select name="recurrence_unit">
			<option value="0">Days</option>
			<option value="1">Weeks</option>
			<option value="2">Months</option>
			<option value="3">Years</option>
		</select>
		.
	</div>
	<input name="submit" type="submit" value="Create!" /><br>
</form>
EOD;

// Assign body contents
$html = $createNewTaskForm;

//this is the AJAX stuff
$html .= <<<EOD
<div id="task-body">
</div>
<script src="js/taskret.js"></script>
<script>
window.onload = function() {
	getTasks({$user_id});
}
</script>
EOD;


// Input additional css
$webpage->inputCSS('./tasks.css');

// Input html body contents in template
$webpage->inputHTML($html);

// Output webpage
$webpage->printPage();

exit;

?>