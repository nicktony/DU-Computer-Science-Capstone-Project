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
	if (isset($_SESSION['username'])) {
		header("Location: ../home/home.php");
		session_destroy();
	}
	
	if (isset($_SESSION['ERROR_MSG'])) {
		$err_msg = $_SESSION['ERROR_MSG'];
		echo "<p class='error_message'>{$err_msg}</p>";
	}
	
	//get the information from the recovery form
	if (isset($_REQUEST['file']) && isset($_REQUEST['reset'])) {
		$username = $_REQUEST['file'];
		$password = $_REQUEST['reset'];
		
		?>
		<form action="finalize.php" method="POST">
			<input type="hidden" name="file" value="<?php echo $username; ?>" />
			<input type="hidden" name="reset" value="<?php echo $password; ?>" />
			<b>New Password</b>
		    <br>
		    <input type="password" name="newpassword" pattern="^.{5,20}$"
					title="between 5 and 20 characters in length" required />
			<br>
			<b>Confirm Password</b>
			<br>
			<input type="password" name="p_confirm" pattern="^.{5,20}$"
					title="between 5 and 20 characters in length" required />
			<br>

		    <button type="submit">Create Account</button>
		</form>
		<?php
	}
	?>
	</body>
</html>