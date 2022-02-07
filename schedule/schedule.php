<?php

// Set default time zone and current date variables
date_default_timezone_set('America/New_York');

$current_day = date('d');
$current_month = date('m');
$current_year = date('Y');

// Update selected date
$day = isset($_POST['selected_day']) ? $_POST['selected_day'] : NULL;
$month = isset($_POST['selected_month']) ? $_POST['selected_month'] : date('m');
$year = isset($_POST['selected_year']) ? $_POST['selected_year'] : date('Y');

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d h:i:s');
if (!empty($day) && 
	!empty($month) && 
	!empty($year)) $date = $year . '-' . $month . '-' . $day;
else $date = date('Y-m-d');

// Set date variables to int (remove error in leading 0's)
$day = (int)$day;
$month = (int)$month;
$year = (int)$year;

// Set default time zone
date_default_timezone_set('America/New_York');

// Get month string and number of days for that month
$month_string = getMonthString($month);
$maxDate = getMaxDate($month);

// Check if session exists
session_start();
if (isset($_SESSION['username'])) {
	$temp = $_SESSION['username'];
  //echo "<div style='margin-left: 5rem; padding: 1rem'>Session is active with $temp</div>";
} else {
	header("Location: ../user_login/login.php");
}

// Required files
require '../classes/webpage.class.php';

// Create webpage
$webpage = new webpage();

// Assign title
$webpage->createPage('Schedule');

// JavaScript Ajax
$ajaxGetDay = "";
for ($i = 1; $i <= 31; $i++) {
	$ajaxGetDay .= "
		$('#day$i').click(function() {
				day = $i;
				$('#page').load('schedule.php', {
					selected_day: day,
					selected_month: month,
					selected_year: year
				});
			});";
}

$ajax = "
	<script>
		$(document).ready(function() {

			var day;
			var month = $month;
			var year = $year;

			$('#prev').click(function() {
				month = month - 1;
				if (month == 0) {
					month = 12;
					year = year - 1;
				}
				$('#page').load('schedule.php', {
					selected_day: day,
					selected_month: month,
					selected_year: year
				});
			});

			$('#next').click(function() {
				month = month + 1;
				if (month == 13) {
					month = 1;
					year = year + 1;
				}
				$('#page').load('schedule.php', {
					selected_day: day,
					selected_month: month,
					selected_year: year
				});
			});

			$('#reset').click(function() {
				$('#page').load('schedule.php', {
					selected_day: $current_day,
					selected_month: $current_month,
					selected_year: $current_year
				});
			});

			$ajaxGetDay
		});
	</script>
";

// Generate calender
$html = 
"$ajax
<div style='overflow:auto'>
	<table class='month'>
		<tr>
			<td class='prev'><div id='prev'><button>Previous Month</button></div><div id='reset'><button>Reset Calendar</button></div></td>
			<td class='monthandyear'>$month_string<br><span style='font-size:18px'>$year</span></td>
			<td class='next'><div id='next'><button>Next Month</button></div></td>
		</tr>
	</table>

	<table class='calendar'>
		<tr class='weekday'>
		  <td class='dayofweek'>Mo</td>
		  <td class='dayofweek'>Tu</td>
		  <td class='dayofweek'>We</td>
		  <td class='dayofweek'>Th</td>
		  <td class='dayofweek'>Fr</td>
		  <td class='dayofweek'>Sa</td>
		  <td class='dayofweek'>Su</td>
		</tr>

		<tr>
";

for ($i = 1; $i <= $maxDate; $i++) {
	if ($i == date('d') && $month == date('m') && $year == date('Y')) {
		$html .= "<td class='currentday'><div id='day$i'><button>$i</button></div></td>";
	} else if ($i == $day) {
		$html .= "<td class='selectedday'><div id='day$i'><div><button>$i</button></div></td>";
	} else {
		$html .= "<td class='day'><div id='day$i'><button>$i</button></div></td>";
	}

	if ($i % 7 == 0) {
		$html .= "</tr><tr>";
	}
}
$html .= "</tr></table>";

// DB Information
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Taskless";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Query tasks for selected day
$html .= "<table class='tasks'>";
$sql = "SELECT name, description FROM tasks WHERE start_time = '$date'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // Output data of each row
  while($row = $result->fetch_assoc()) {
  	$task = $row['name'];
  	$desc = $row['description'];

    $html .= "<tr><td><b>$desc</b>: $task</td></tr>";
  }
} else {
  //echo "0 results";
}

// Close DB connection, end table
$conn->close();
$html .= "</table>
</div>";

// Input additional css
$webpage->inputCSS('./schedule.css');

// Input html body contents in template
$webpage->inputHTML($html);

// Output webpage
$webpage->printPage();

exit;


function getMonthString($month) {
	$monthStrings = array(
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December',
	);

	return $monthStrings[$month];
}

function getMaxDate($month) {
	$maxDates = array(
    1 => 31,
    2 => 28,
    3 => 31,
    4 => 30,
    5 => 31,
    6 => 30,
    7 => 31,
    8 => 31,
    9 => 30,
    10 => 31,
    11 => 30,
    12 => 31,
	);

	return $maxDates[$month];
}
?>