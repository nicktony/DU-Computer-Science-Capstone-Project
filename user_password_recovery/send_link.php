<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="recovery_form.css">
	</head>
	<body>
		<header>
			<h1>TASK<span>LESS</span></h1>
		</header>

<?php
	// Required files
	require_once '../classes/webpage.class.php';
	require_once '../utilities/app_config.php';

	// Redirect if session is already started
	session_start();
	if (isset($SESSION['username'])) {
		header("Location: ../home/home.php");
		session_destroy();
	}
	
	//get the information from the recovery form
	if (isset($_REQUEST['username'])) {
		$username = $_REQUEST['username'];
		//get their information from the DB
		$db_connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		$_qry = sprintf("SELECT id, username, password, email, email_verified FROM users WHERE username = '%s';",
			mysqli_real_escape_string($db_connection, $username));
		$_result = $db_connection->query($_qry);
		$db_connection->close();
		
		if ($_result->num_rows == 1) {
			//check that the email is verified
			$_row = $_result->fetch_array(MYSQLI_BOTH);
			if (!$_row['email_verified']) {
				echo "<p>The email address you have provided has not been verified.</p>";
			}
			
			//create the recovery link
			$recovery_link = "reset_password.php?file=".
				hash('sha256', $_row['username'], false).
				"&reset=".
				hash('sha256', $_row['password'], false);
			?>
			<div class="container">
				<p>Todo: Email this reset link to the user</p>
				<a href="<?php echo $recovery_link;?>">Recovery Link</a>
			</div>
			<?php
		} else {
			//query error, more than one user with the same username
		}
	}
?>
	</body>
</html>