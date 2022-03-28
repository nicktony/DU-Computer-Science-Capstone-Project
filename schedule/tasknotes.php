<?php

// Check if session exists
session_start();
if (isset($_SESSION['username'])) {
	$username = $_SESSION['username'];
  //echo "<div style='margin-left: 5rem; padding: 1rem'>Session is active with $username</div>";
} else {
	header("Location: ../user_login/login.php");
}

// DB Information
require_once '../utilities/app_config.php';

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Required files
require '../classes/webpage.class.php';

// Update selected date
$day = isset($_POST['selected_day']) ? $_POST['selected_day'] : NULL;
$month = isset($_POST['selected_month']) ? $_POST['selected_month'] : date('m');
$year = isset($_POST['selected_year']) ? $_POST['selected_year'] : date('Y');

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
if (!empty($day) && 
	!empty($month) && 
	!empty($year)) $date = $year . '-' . $month . '-' . $day;
else $date = date('Y-m-d');

// Set date variables to int (remove error in leading 0's)
$day = (int)$day;
$month = (int)$month;
$year = (int)$year;

// Check for leap year
$leapYear = date('L', strtotime("$year-01-01"));

// Set default time zone
date_default_timezone_set('America/New_York');

// Query tasks for selected day
$html = "<table class='tasks'>";
$sql = "SELECT title, description FROM tasks INNER JOIN users ON tasks.user_id = users.id WHERE tasks.start_date = '$date' AND users.username = '$username' ORDER BY tasks.priority, tasks.start_date asc";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // Output data of each row
  while($row = $result->fetch_assoc()) {
  	$title = $row['title'];
  	$description = $row['description'];

    $html .= "<tr><td><b>$title</b>: $description</td></tr>";
  }
} else {
  //echo "0 results";
}

// Close DB connection, end table
$conn->close();
$html .= "</table></div>";

// Output results
echo $html;

exit;
?>