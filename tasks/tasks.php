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

<div class="tasks">
	<div id="createTaskButton" class="createTaskHeader">
		<span>Create Task</span>
		<div class="createTaskButton">
			<div class="option-linking">
				<svg
					aria-hidden='true'
					focusable='false'
					data-prefix='fad'
					data-icon='angle-double-right'
					role='img'
					xmlns='http://www.w3.org/2000/svg'
					viewBox='0 0 448 512'
					class='svg-inline--fa fa-angle-double-right fa-w-14 fa-5x'
					id='arrowSVG'
					style='transition: all .5s ease'>
					<g class='fa-group'>
						<path
							fill='currentColor'
							d='M224 273L88.37 409a23.78 23.78 0 0 1-33.8 0L32 386.36a23.94 23.94 0 0 1 0-33.89l96.13-96.37L32 159.73a23.94 23.94 0 0 1 0-33.89l22.44-22.79a23.78 23.78 0 0 1 33.8 0L223.88 239a23.94 23.94 0 0 1 .1 34z'
							class='fa-secondary'>
						</path>
						<path
							fill='currentColor'
							d='M415.89 273L280.34 409a23.77 23.77 0 0 1-33.79 0L224 386.26a23.94 23.94 0 0 1 0-33.89L320.11 256l-96-96.47a23.94 23.94 0 0 1 0-33.89l22.52-22.59a23.77 23.77 0 0 1 33.79 0L416 239a24 24 0 0 1-.11 34z'
							class='fa-third'>
						</path>
					</g>
				</svg>
			</div>
		</div>
	</div>
	<div id="createTaskFormContainer" class="createTaskFormContainer">
		<div class="filler"></div>
		<form name="taskCreateForm">
			<div class="createTask-left">
				<input name="title" type="text" placeholder="Add Title" maxlength="128" style="width: 20rem" required autofocus />
				<br>
				<textarea class="" name="description" placeholder="Add Description" rows="10" cols="70" maxlength="256"></textarea>
				<br>
			</div>
			<div class="createTask-right">
				<label for="start_date">Start Date:&nbsp&nbsp</label><input name="start_date" type="date" value="{$currentDate}" required />
				<br>
				<label for="priority">Priority:&nbsp&nbsp</label><input name="priority" type="number" value="1" style="width: 3rem" required />
				<br>
				<input name="rolls_over" type="checkbox" value="yes" checked /><label for="rolls_over">&nbsp&nbspRoll Over (Recreates next day if incomplete)</label>
				<br>
				<input id="recurrence_checkbox" name="recurrence_cb" type="checkbox" value="yes" unchecked /><label for="recurrence_cb">&nbsp&nbspMake this task repeat</label>
				<br>
				<div id="recurrence_container">
					Recurrence <input name="recurrence_interval" value="1" type="number" style="width: 3rem" />
					<select name="recurrence_unit">
						<option value="0">Days</option>
						<option value="1">Weeks</option>
						<option value="2">Months</option>
						<option value="3">Years</option>
					</select>
				</div>
				<button name="submit" type="submit" class="mainbutton">Create</button><br>
			</div>
		</form>
		<div class="filler"></div>
	</div>
EOD;

// Assign body contents
$html = $createNewTaskForm;

//this is the AJAX stuff
$html .= <<<EOD
	<div id="task-body" style="margin: 0px; padding: 0">
	</div>
	<script src="js/sorting.js"></script>
	<script src="js/taskret.js"></script>
	<script>
		window.onload = function() {
			getTasks({$user_id});
		}
	</script>
</div>
EOD;

// Input additional css
$webpage->inputCSS('./tasks.css');

// Input html body contents in template
$webpage->inputHTML($html);

// Output webpage
$webpage->printPage();

exit;

?>