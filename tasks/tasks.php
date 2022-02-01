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
require '../classes/webpage.class.php';
require_once '../classes/TaskFactory.class.php';

//get the tasks for this user_error
$factory = new TaskFactory();
$tasks = $factory->FetchTasks($user_id);

// Create webpage
$webpage = new webpage();
$webpage->inputCSS('tasks.css');

// Assign title
$webpage->createPage('Tasks');
$currentDate = date('Y-m-d');
$createNewTaskForm = <<<EOD
<form action="CRUD/create_task.php" method="POST">
	<input name="title" type="text" placeholder="title" maxlength="128" required /><br>
	<input name="description" type="text" placeholder="description" maxlength="256" /><br>
	<label for="start_date">Start Date: </label><input name="start_date" type="date" value="{$currentDate}" /><br>
	<label for="priority">Priority: </label><input name="priority" type="number" value="1" /><br>
	<label for="rolls_over">Roll Over?: </label><input name="rolls_over" type="checkbox" value="yes" checked /><br>
	<input name="submit" type="submit" value="Create!" /><br>
</form>
EOD;

// Assign body contents
$html = $createNewTaskForm;

foreach ($tasks as $t) {
	$html .= "<br>". $t->getTaskHTML();
}




// Input additional css
$webpage->inputCSS('./tasks.css');

// Input html body contents in template
$webpage->inputHTML($html);

// Output webpage
$webpage->printPage();

exit;

?>