<?php

// Check if session exists
session_start();
if (isset($_SESSION['username'])) {
	$username = $_SESSION['username'];
  //echo "<div style='margin-left: 5rem; padding: 1rem'>Session is active with $temp</div>";
} else {
	header("Location: ../../user_login/login.php");
}

// Grab post variables
$name = isset($_POST['name']) ? $_POST['name'] : NULL;

// DB Information
require_once '../../utilities/app_config.php';

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Start of html
$tasks = "<table>";

// Query for current day tasks
$current_date = date('Y-m-d', strtotime('-7 days'));
$sql = "SELECT tasks.title FROM tasks INNER JOIN users ON tasks.user_id = users.id WHERE start_date > '$current_date' AND title LIKE '%$name%' AND is_complete = 0 AND users.username = '$username' ORDER BY tasks.priority, tasks.start_date desc";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	// Grab each task associated with the date
	while($row = $result->fetch_assoc()) {
	  	$title = $row['title'];
	  	if (strlen($title) > 35) {
	  		$title = substr($title, 0, 27) . ". . .";
	  	} else {
	  		$title = substr($title, 0, 27);
	  	}

	  	/*$tasks .= "
	  	<tr>
			<td>
				<div>$title</div><button>Complete+</button>
			</td>
		</tr>"
		;*/
	  	
	  	$tasks .= "
	  	<tr>
			<td>
				<div>$title</div>
			</td>
		</tr>"
		;
	}
} else {
	// Query for current day tasks
	$current_date = date('Y-m-d', strtotime('-7 days'));
	$sql = "SELECT tasks.title FROM tasks INNER JOIN users ON tasks.user_id = users.id WHERE start_date > '$current_date' AND is_complete = 0 AND users.username = '$username' ORDER BY tasks.priority, tasks.start_date desc";
	$result = $conn->query($sql);

	// Grab each task associated with the date
	while($row = $result->fetch_assoc()) {
	  	$title = $row['title'];
	  	if (strlen($title) > 35) {
	  		$title = substr($title, 0, 27) . ". . .";
	  	} else {
	  		$title = substr($title, 0, 27);
	  	}

	  	/*$tasks .= "
	  	<tr>
			<td>
				<div>$title</div><button>Complete+</button>
			</td>
		</tr>"
		;*/
	  	
	  	$tasks .= "
	  	<tr>
			<td>
				<div>$title</div>
			</td>
		</tr>"
		;
	}
}

echo $tasks . "</table>";

exit;

?>