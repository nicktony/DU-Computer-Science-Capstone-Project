<?php

// Check if session exists
session_start();
if (isset($_SESSION['username'])) {
	$temp = $_SESSION['username'];
  	//echo "<div style='margin-left: 5rem; padding: 1rem'>Session is active with $temp</div>";
} else {
	header("Location: ../user_login/login.php");
}

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

// Required files
require '../classes/webpage.class.php';

// Create webpage
$webpage = new webpage();

// Assign title
$webpage->createPage('Profile');

// Input html body contents in template
$html = file_get_contents('./profile.html');
$webpage->inputHTML($html);

// Query for profile info
$sql = "SELECT username, password, email, email_verified, name, phone, bio FROM users WHERE username = '$temp' LIMIT 1";
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

// Profile pic
$webpage->convert('pic', '../images/test.jpg');

// Insert profile details
$webpage->convert('name', $name);
$webpage->convert('phone', $phone);
$webpage->convert('email', $email);
$webpage->convert('verified', $email_verified);
$webpage->convert('bio', $bio);

$webpage->convert('status', 'Administrator');

















// Input additional css
$webpage->inputCSS('./profile.css');

// Output webpage
$webpage->printPage();

exit;

?>