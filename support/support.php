<?php
if (isset($_POST['submit'])){
    $name = $_POST['firstName'];
    $name =$_POST['lastName'];
    $phone =$_POST['phone'];
    $visitor_email = $_POST['email'];
    $message = $_POST['message'];

    $email_form = 'capstone@gmail.com';
    $email_subject = "New Form Submission";
    $email_body = "User Name: $name.\n".
              "User Email: $visitor_email.\n".
              "User Message: $message.\n";
              $to = "graazg7@gmail.com";

  $headers="From: $email_from\r\n";
  $headers .= "Reply-To: $visitor_email\r\n";
  mail($to, $email_subject,$email_body,$headers);
  header("location: contact.html");


 ?>
