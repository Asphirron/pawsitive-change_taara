<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
function sendEmail($email, $subject, $message){
    $mail = new PHPMailer(true);
  
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'cbuenconsejobusacc@gmail.com';
    $mail->Password = 'bptu grun kwbo xhhy';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
  
    $mail->setFrom('cbuenconsejobusacc@gmail.com');
  
    $mail->addAddress($email);
  
    $mail->isHTML(true);
  
    $mail->Subject = $subject;
  
    $mail->Body = $message;
  
    //echo "<script>alert('Email sent successfully');</script>";
  
    $mail->send();
  }