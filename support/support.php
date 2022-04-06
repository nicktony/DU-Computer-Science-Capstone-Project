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

// Check post variables after user submits webpage
if (isset($_POST['submit'])) {
  $name = $_POST['fullname'];
  $phone = $_POST['phone'];
  $visitor_email = $_POST['email'];
  $message = $_POST['message'];
  $email_from = 'capstone@gmail.com';
  $email_subject = "New Form Submission";
  $email_body = "User Name: $name $name.\n".
            "User Email: $visitor_email.\n".
            "User Message: $message.\n";
  $to = "graazg7@gmail.com";

  $headers = "From: $email_from\r\n";
  $headers .= "Reply-To: $visitor_email\r\n";

  // Send support email if information is typed in correctly
  if (mail($to, $email_subject, $email_body, $headers)) {
    header("Location: support.php? mailsend");
  }
}

// Required files
require '../classes/webpage.class.php';

// Create webpage
$webpage = new webpage();

// Assign title
$webpage->createPage('Support');

// Assign body contents
$html = file_get_contents('./support.html');

// Input additional css
$webpage->inputCSS('./support.css');

// Input html body contents in template
$webpage->inputHTML($html);


$webpage->convert("FULL_NAME", "");
$webpage->convert("PHONE_NUMBER", "");
$webpage->convert("EMAIL_ADDRESS", "");

// Output webpage
$webpage->printPage();

exit;

?>