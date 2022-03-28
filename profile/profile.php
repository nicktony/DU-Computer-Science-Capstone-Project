<?php

// Check if session exists
session_start();
if (isset($_SESSION['username'])) {
	$username = $_SESSION['username'];
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

// Grab theme information
$theme = isset($_POST['theme']) ? $_POST['theme'] : NULL;

if (!empty($theme)) {
	// Update user prefered theme
	$sql = "UPDATE users SET theme='$theme' WHERE username = '$username'";
	$conn->query($sql);
}

// Create webpage
$webpage = new webpage();

// Assign title
$webpage->createPage('Profile');

// Html body contents in template
$html = file_get_contents('./profile.html');

// Jquery
$jQuery = "
	<script>
		$(document).ready(function() {

			$('#darkButton').click(function() {
				$('#page').load('profile.php', {
					theme: 'Dark'
				});
				setTheme('Dark');
			});

			$('#lightButton').click(function() {
				$('#page').load('profile.php', {
					theme: 'Light'
				});
				setTheme('Light');
			});
		});
	</script>";

// Insert html inside of page
$html = $jQuery . $html;
$webpage->inputHTML($html);

// Query for profile info
$sql = "SELECT username, password, email, email_verified, name, phone, bio FROM users WHERE username = '$username' LIMIT 1";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
	$username = $row['username'];
	$password = $row['password'];
	$email = $row['email'];
	$email_verified = $row['email_verified'];
	$name = $row['name'];
	$phone = $row['phone'];
	$bio = $row['bio'];
}

// Email verified symbol
$verifiedText = "";
if ($email_verified == true) {
	$verifiedText .= "<span style='color:green' class='material-icons'>
done
</span>";
} else {
	$verifiedText .= "(<a class='email_link' href='../email_verification/verifyEmail.php'>Click here to verify!</a>)";
}

// Profile pic
$webpage->convert('pic', '../images/test.jpg');

// Insert profile details
$webpage->convert('name', $name);
$webpage->convert('phone', $phone);
$webpage->convert('email', $email);
$webpage->convert('verified', $verifiedText);
$webpage->convert('bio', $bio);
if ($username == 'admin') {
	$webpage->convert('status', 'Administrator');
} else {
	$webpage->convert('status', 'User');
}

// Input additional css
$webpage->inputCSS('./profile.css');

// Output webpage
$webpage->printPage();

exit;

?>