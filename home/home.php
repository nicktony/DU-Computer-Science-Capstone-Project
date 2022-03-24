<?php

// Check if session exists
session_start();
if (isset($_SESSION['username'])) {
	$username = $_SESSION['username'];
  //echo "<div style='margin-left: 5rem; padding: 1rem'>Session is active with $temp</div>";
} else {
	header("Location: ../user_login/login.php");
}

// DB Information
require_once '../utilities/app_config.php';

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Required files
require '../classes/webpage.class.php';

// Create webpage
$webpage = new webpage();

// Assign title
$webpage->createPage('Home');

/********************************************************************************************************
 * RIGHT SIDE 
 *******************************************************************************************************/

// Assign right side body contents
$html = "
<div class='infodiv'>
	<div class='infobox-right'>
		<div class='header'>
			<span>Friends</span>
		</div>
		<div class='searchbar'>
			<input type='text' placeholder='Search...'></input>
		</div>
		<div class='infobox-inside-right'>
			<table>
				<tr>
					<td>
						<div>Coming Soon</div><button>Group+</button>
					</td>
				</tr>
			</table>
		</div>
	</div>
";

/********************************************************************************************************
 * MIDDLE SIDE 
 *******************************************************************************************************/

// Query for all tasks
$sql = "SELECT is_complete FROM tasks INNER JOIN users ON tasks.user_id = users.id WHERE users.username = '$username' AND is_complete = 1";
$result = $conn->query($sql);
$total_tasks_complete = $result->num_rows;

$sql = "SELECT is_complete FROM tasks INNER JOIN users ON tasks.user_id = users.id WHERE users.username = '$username' AND is_complete = 0";
$result = $conn->query($sql);
$total_tasks_incomplete = $result->num_rows;

// Pie chart javascript
$html .= "
    <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
    <script type='text/javascript'>
      google.charts.load('current', {packages:['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Total Individual Tasks'],
          ['Complete', $total_tasks_complete],
          ['Incomplete', $total_tasks_incomplete]
        ]);

        var options = {
          title: '',
          titleTextStyle: {
            color: 'white',              
            fontName: 'Open Sans',    
            fontSize: 25,               
            bold: true,                              
          },
          is3D: true,
          backgroundColor: 'transparent',
          legend: {textStyle: {color: 'white'}},
          'width': 400,
          'height': 300,
          'chartArea': {'width': '100%', 'height': '80%'},
          colors: ['#0C3FB7', '#3366cc']
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d_1'));
        chart.draw(data, options);
      }
    </script>
    <script type='text/javascript'>
      google.charts.load('current', {packages:['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Total Individual Tasks'],
          ['Complete', 1],
          ['Incomplete', 1]
        ]);

        var options = {
          title: '',
          titleTextStyle: {
            color: 'white',              
            fontName: 'Open Sans',    
            fontSize: 25,               
            bold: true,                              
          },
          is3D: true,
          backgroundColor: 'transparent',
          legend: {textStyle: {color: 'white'}},
          'width': 400,
          'height': 300,
          'chartArea': {'width': '100%', 'height': '80%'},
          colors: ['#0C3FB7', '#3366cc']
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d_2'));
        chart.draw(data, options);
      }
    </script>
";

// Assign center body contents
$html .= "
	<div class='infobox-center'>
		<div class='header'>
			<span>This Week</span>
		</div>
		<div class='infobox-inside-center'>
			<div class='header2'>
				<span>Individual Tasks</span>
			</div>
			<div class='piechart1' id='piechart_3d_1'></div>
			<div class='header2'>
				<span>Group Tasks</span>
			</div>
			<div class='piechart2' id='piechart_3d_2'></div>
		</div>
	</div>
";


/********************************************************************************************************
 * LEFT SIDE 
 *******************************************************************************************************/

// Query for current day tasks
$current_date = date('Y-m-d', strtotime('-7 days'));
$sql = "SELECT tasks.title FROM tasks INNER JOIN users ON tasks.user_id = users.id WHERE start_date > '$current_date' AND is_complete = 0 AND users.username = '$username' ORDER BY tasks.priority, tasks.start_date desc";
$result = $conn->query($sql);
$tasks = "";

// Grab each task asscoiated with the date
while($row = $result->fetch_assoc()) {
  	$title = $row['title'];
  	if (strlen($title) > 35) {
  		$title = substr($title, 0, 27) . ". . .";
  	} else {
  		$title = substr($title, 0, 27);
  	}

  	$tasks .= "
  	<tr>
		<td>
			<div>$title</div><button>Complete+</button>
		</td>
	</tr>"
	;
}

// Assign left side body contents
$html .= "
	<div class='infobox-left'>
		<div class='header'>
			<span>Today</span>
		</div>
		<div class='header2'>
			<span>Individual Tasks</span>
		</div>
		<div class='searchbar'>
			<input type='text' placeholder='Search...'></input>
		</div>
		<div class='infobox-inside-left'>
			<table>
				$tasks
			</table>
		</div>
		<div class='header2'>
			<span>Group Tasks</span>
		</div>
		<div class='searchbar'>
			<input type='text' placeholder='Search...'></input>
		</div>
		<div class='infobox-inside-left'>
			<table>
				<tr>
					<td>
						<div>Coming Soon</div><button>Complete+</button>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
";

// Input additional css
$webpage->inputCSS('./home.css');

// Input html body contents in template
$webpage->inputHTML($html);

// Output webpage
$webpage->printPage();

exit;

?>