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

// Check for difference in post and db values
if ($post_name != NULL) $name = $post_name;
if ($post_phone != NULL) $phone = $post_phone;
if ($post_email != NULL) $email = $post_email;
if ($post_verified != NULL) $verfied = $post_verified;
if ($post_bio != NULL) $bio = $post_bio;
if ($post_theme != NULL) $theme = 'Dark';
else $theme = 'Light';

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
if ($email_verified > 0) {

} else {

}

// Create webpage
$webpage = new webpage();

// Assign title
$webpage->createPage('Profile');

// Html body contents in template
$html = file_get_contents('./profile_edit.html');

// Jquery
$jQuery = "<script src='js/profile.js'></script>";

// Insert html inside of page
$html = $jQuery . $html;
$webpage->inputHTML($html);

// Profile pic
$webpage->convert('pic', '../images/test.jpg');

// Insert profile details
$webpage->convert('name', $name);
$webpage->convert('phone', $phone);
$webpage->convert('email', $email);
$webpage->convert('verified', $email_verified);
$webpage->convert('bio', $bio);
$webpage->convert('checked', $checked);

$webpage->convert('status', 'Administrator'); //temporary

// Input additional css
$webpage->inputCSS('./profile_edit.css');

// Output webpage
$webpage->printPage();

exit;

?>