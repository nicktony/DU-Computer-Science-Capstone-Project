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

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Required files
require '../classes/webpage.class.php';

<<<<<<< HEAD
// Grab POST information
$post_name = isset($_POST['name']) ? $_POST['name'] : NULL;
$post_phone = isset($_POST['phone']) ? $_POST['phone'] : NULL;
$post_email = isset($_POST['email']) ? $_POST['email'] : NULL;
$post_verified = isset($_POST['verified']) ? $_POST['verified'] : NULL;
$post_bio = isset($_POST['bio']) ? $_POST['bio'] : NULL;
$post_theme = isset($_POST['theme']) ? $_POST['theme'] : NULL;

// Query for current profile info
$sql = "SELECT username, password, email, email_verified, name, phone, bio, theme FROM users WHERE username = '$username' LIMIT 1";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
	$username = $row['username'];
	$password = $row['password'];
	$email = $row['email'];
	$email_verified = $row['email_verified'];
	$name = $row['name'];
	$phone = $row['phone'];
	$bio = $row['bio'];
	$theme = $row['theme'];
}

// Check for difference in POST and DB values
if ($post_name != NULL) $name = $post_name;
if ($post_phone != NULL) $phone = $post_phone;
if ($post_email != NULL) $email = $post_email;
if ($post_verified != NULL) $verfied = $post_verified;
if ($post_bio != NULL) $bio = $post_bio;
if (!isset($_POST['theme'])) {
	// Don't change, use DB value for theme when loading in the first time
} else {
	$theme = $post_theme;
}

// Update DB values from user input
try {
	// Update DB
	$sql = "UPDATE users SET name='$name', phone='$phone', bio='$bio', theme='$theme' WHERE username = '$username'";
	$conn->query($sql);
} catch (mysqli_sql_exception $e) {
	var_dump($e);
	exit;
}

// Determine if theme checkbox is checked
if ($theme == 'Dark') {
	$checked = 'checked';
} else if ($theme = 'Light') {
	$checked = '';
}

// Email verified symbol
$verifiedText = "";
if ($email_verified == true) {
	$verifiedText .= "<span style='color:green' class='material-icons'>
done
</span>";
} else {
	$verifiedText .= "(<a class='email_link' href='../email_verification/verifyEmail.php'>Click here to verify!</a>)";
=======
// Grab theme information
$theme = isset($_POST['theme']) ? $_POST['theme'] : NULL;

if (!empty($theme)) {
	// Update user prefered theme
	$sql = "UPDATE users SET theme='$theme' WHERE username = '$username'";
	$conn->query($sql);
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
}

// Create webpage
$webpage = new webpage();

// Assign title
$webpage->createPage('Profile');

// Html body contents in template
$html = file_get_contents('./profile_edit.html');

// Jquery
<<<<<<< HEAD
$jQuery = "<script src='js/profile.js'></script>";
=======
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
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69

// Insert html inside of page
$html = $jQuery . $html;
$webpage->inputHTML($html);

<<<<<<< HEAD
=======
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
if ($email_verified > 0) {

} else {

}

>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
// Profile pic
$webpage->convert('pic', '../images/test.jpg');

// Insert profile details
$webpage->convert('name', $name);
$webpage->convert('phone', $phone);
$webpage->convert('email', $email);
<<<<<<< HEAD
$webpage->convert('verified', $verifiedText);
$webpage->convert('bio', $bio);
$webpage->convert('checked', $checked);

$webpage->convert('status', 'Administrator'); //temporary

// Input additional css
$webpage->inputCSS('./profile_edit.css');
=======
$webpage->convert('verified', $email_verified);
$webpage->convert('bio', $bio);

$webpage->convert('status', 'Administrator');













// Input additional css
$webpage->inputCSS('./profile.css');
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69

// Output webpage
$webpage->printPage();

exit;

?>