<?php

if (isset($_POST['submit']))
{
    $name = $_POST['firstName'];
    $name =$_POST['lastName'];
    $phone =$_POST['phone'];
    $visitor_email = $_POST['email'];
    $message = $_POST['message'];
    $email_from = 'capstone@gmail.com';
    $email_subject = "New Form Submission";
    $email_body = "User Name: $name $name.\n".
              "User Email: $visitor_email.\n".
              "User Message: $message.\n";
    $to = "graazg7@gmail.com";

  $headers="From: $email_from\r\n";
  $headers .= "Reply-To: $visitor_email\r\n";
  if(mail($to, $email_subject,$email_body,$headers))
  {
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
$html = file_get_contents('./contact.html');

// Input additional css
$webpage->inputCSS('./support.css');

// Input html body contents in template
$webpage->inputHTML($html);

// Output webpage
$webpage->printPage();

exit;


 ?>
