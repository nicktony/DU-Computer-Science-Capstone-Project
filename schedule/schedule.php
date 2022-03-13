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

// Set default time zone and current date variables
date_default_timezone_set('America/New_York');

$current_day = date('d');
$current_month = date('m');
$current_year = date('Y');

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

// Get month string and number of days for that month
$month_string = getMonthString($month);
$maxDate = getMaxDate($month, $leapYear);

// Create webpage
$webpage = new webpage();

// Assign title
$webpage->createPage('Schedule');

// JavaScript Ajax
$JsGetDay = '';
for ($i = 1; $i <= 31; $i++) {
	$JsGetDay .= "

		$('#day$i').click(function() {
			
			// Update day variable to reflect day clicked
			day = $i;

			// Refresh page and update day variable
			$('#tasknotes').load('./tasknotes.php', {
				selected_day: day,
				selected_month: month,
				selected_year: year
			});

			// Reset arrows
  		resetArrows($i);

  		// Rotate clicked arrow SVG
      $(svg$i).css({
        'transform': 'rotate(90deg)'
      });
			$('html,body').animate({scrollTop: document.body.scrollHeight},'slow');
		});
	";
}

$Js = "
	<script src='./js/schedule.js'></script>
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

			$JsGetDay
		});
	</script>
";

// Generate calendar headers
$html = "
$Js
<div class='calendar'>
	<table class='month'>
		<tr>
			<td class='prev'><div id='prev'><button>Previous Month</button></div><div id='reset'><button>Reset Calendar</button></div></td>
			<td class='monthandyear'>$month_string<br><span style='font-size:18px'>$year</span></td>
			<td class='next'><div id='next'><button>Next Month</button></div></td>
		</tr>
	</table>

	<table class='internalcalendar'>
		<tr class='weekday'>
			<th class='dayofweek'>Su</td>
		  <th class='dayofweek'>Mo</td>
		  <th class='dayofweek'>Tu</td>
		  <th class='dayofweek'>We</td>
		  <th class='dayofweek'>Th</td>
		  <th class='dayofweek'>Fr</td>
		  <th class='dayofweek'>Sa</td>
		</tr>

		<tr>
";

// Generate calendar days
$firstDayOfMonth = $year . '-' . $month . '-01';
$dayOfWeek = date('w', strtotime($firstDayOfMonth));
$dayOfWeekCounter = 0; // Set for calculating blank dates at end of month
for ($i = 1; $i <= $maxDate + $dayOfWeek; $i++) {
	$dayOfWeekCounter++;

	// Set html start
	$dayTasks = "<div class='embeddedtask'>";

	// Alter i using j depending on starting day of week
	$j = $i - $dayOfWeek;

	// Assign current date
	$tempDate = $year . '-' . $month . '-' . $j;

	// Query for tasks
	$sql = "SELECT tasks.title, tasks.description, tasks.start_date, tasks.priority, tasks.is_complete FROM tasks INNER JOIN users ON tasks.user_id = users.id WHERE start_date = '$tempDate' AND users.username = '$username' ORDER BY tasks.title asc";
	$result = $conn->query($sql);
	$numTasks = 0;

  // Grab each task asscoiated with the date
  while($row = $result->fetch_assoc()) {
  	$numTasks++;

  	// Limit task output to 5 for spacing
  	if ($numTasks <= 5) {
	  	$title = $row['title'];
	  	$description = $row['description'];
	  	$start_date = $row['start_date'];
	  	$priority = $row['priority'];
	  	$is_complete = $row['is_complete'];

	    $dayTasks .= "<div>&nbsp;$title</div>";
  	} else {
  		break;
  	}
  }
  if ($result->num_rows > 5) {
  	$dayTasks .= "<div>&nbsp;&nbsp;.&nbsp;.&nbsp;.&nbsp;.&nbsp;.</div>";
  }
  $dayTasks .= "</div>";

	// Set style of day
	if ($i > $dayOfWeek) {
		// If 'today' is selected for the current day
		if ($j == date('d') && $month == date('m') && $year == date('Y') && $j == $day) {
			$html .= "
			<td class='currentday'>
				<div class='dayiconactive'>
					<div class='daylogoactive'>
		        <div class='option-linking' id='day$j'>
		        	<span class='linking-text daylogo-text'>$j</span>
							<svg
		            aria-hidden='true'
		            focusable='false'
		            data-prefix='fad'
		            data-icon='angle-double-right'
		            role='img'
		            xmlns='http://www.w3.org/2000/svg'
		            viewBox='0 0 448 512'
		            class='svg-inline--fa fa-angle-double-right fa-w-14 fa-5x'
		            id='svg$j'
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
		  	$dayTasks
			</td>";
			// If 'today' isn't chosen for the current day
		} else if ($j == date('d') && $month == date('m') && $year == date('Y')) {
			$html .= "
			<td class='currentday'>
				<div class='dayicon'>
					<div class='daylogo'>
		        <div class='option-linking' id='day$j'>
		        	<span class='linking-text daylogo-text'>$j</span>
							<svg
		            aria-hidden='true'
		            focusable='false'
		            data-prefix='fad'
		            data-icon='angle-double-right'
		            role='img'
		            xmlns='http://www.w3.org/2000/svg'
		            viewBox='0 0 448 512'
		            class='svg-inline--fa fa-angle-double-right fa-w-14 fa-5x'
								id='svg$j'
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
		  	$dayTasks
			</td>";
			// If a day is selected that isn't the current day
		} else if ($j == $day) {
			$html .= "
			<td class='selectedday'>
				<div class='dayiconactive'>
					<div class='daylogoactive'>
		        <div class='option-linking' id='day$j'>
		        	<span class='linking-text daylogo-text'>$j</span>
							<svg
		            aria-hidden='true'
		            focusable='false'
		            data-prefix='fad'
		            data-icon='angle-double-right'
		            role='img'
		            xmlns='http://www.w3.org/2000/svg'
		            viewBox='0 0 448 512'
		            class='svg-inline--fa fa-angle-double-right fa-w-14 fa-5x'
		            id='svg$j'
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
		  	$dayTasks
			</td>";
			// Not a selected day
		} else {
			$html .= "
			<td class='day'>
				<div class='dayicon'>
					<div class='daylogo'>
		        <div class='option-linking' id='day$j'>
		        	<span class='linking-text daylogo-text'>$j</span>
							<svg
		            aria-hidden='true'
		            focusable='false'
		            data-prefix='fad'
		            data-icon='angle-double-right'
		            role='img'
		            xmlns='http://www.w3.org/2000/svg'
		            viewBox='0 0 448 512'
		            class='svg-inline--fa fa-angle-double-right fa-w-14 fa-5x'
		            id='svg$j'
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
		  	$dayTasks
			</td>";
		}

		if ($i % 7 == 0) {
			$html .= "</tr><tr>";
			$dayOfWeekCounter = 0;
		}
	} else {
		// Fill in blank spaces if month doesn't start on the 1st
		$html.= "<td class='day'></td>";
	}
}

// Fill in rest of calendar with blank dates
for ($i = 1; $i <= (7 - $dayOfWeekCounter) && $dayOfWeekCounter > 0; $i++) {
	$html.= "<td class='day'></td>";
}
$html .= "</tr></table>";

// Query tasks for selected day, this id id replaced by tasknotes.php when day is clicked
$html .= "<table class='tasks' id='tasknotes'>";

// Close DB connection, end table
$conn->close();
$html .= "</table></div>";

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

function getMaxDate($month, $leap) {
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

	// Change February for leap year, if applicable
	if ($leap == 1) $maxDates[2] = 29;

	return $maxDates[$month];
}
?>