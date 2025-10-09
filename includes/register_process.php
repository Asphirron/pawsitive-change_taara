<?php
include "../includes/db_connection.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php?msg=Invalid operation');
    exit();
}

if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
    header('Location: ../register.php?msg=All fields are required');
    exit();
}

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

$user_table = new DatabaseCRUD('user');
$data_email = $user_table->read($email, 'email');

if ($data_email && $data_email['email'] == $email) {
    header('Location: ../register.php?msg=Email already exists');
    exit();
}

// Generate 6-digit verification code
$verification_code = random_int(100000, 999999);

// Store in session temporarily
$_SESSION['pending_user'] = [
    'username' => $username,
    'email' => $email,
    'password' => $password,
    'verification_code' => $verification_code
];

// Simulate email sending (replace with PHPMailer or SMTP in production)
//mail($email, "Your Verification Code", "Your verification code is: $verification_code", "From: no-reply@yourdomain.com");

// Redirect to verification page
header('Location: verify_email.php');
exit();
?>
