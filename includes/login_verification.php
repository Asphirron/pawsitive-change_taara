<?php
include "db_connection.php";
session_start();

$user_table = new DatabaseCRUD("user");

// ✅ Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

// ✅ Collect and sanitize input9pi
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

$msg1 = $msg2 = '';

// ✅ Validate inputs
if (empty($email)) $msg1 = "Email must be filled";
if (empty($password)) $msg2 = "Password must be filled";

if ($msg1 || $msg2) {
    header("Location: ../login.php?msg1=" . urlencode($msg1) . "&msg2=" . urlencode($msg2));
    exit();
}

// ✅ Fetch user by email using select() (since read() expects numeric ID)
$user_data_list = $user_table->select(["*"], ["email" => $email], 1);

// ✅ Check if email exists
if (empty($user_data_list)) {
    $msg1 = 'Email does not exist';
    header("Location: ../login.php?msg1=" . urlencode($msg1));
    exit();
}

// ✅ Extract the first result (since select() returns an array)
$user_data = $user_data_list[0];

// ✅ Check password (plain-text check — matches your current setup)
// ⚠️ Consider using password_hash()/password_verify() for better security
if ($user_data["password"] !== $password) {
    $msg2 = 'Password does not match';
    header("Location: ../login.php?msg2=" . urlencode($msg2));
    exit();
}

// ✅ Set session variables
$_SESSION['email'] = $user_data['email'];
$_SESSION['name'] = $user_data['user_id'];
$_SESSION['user_id'] = $user_data['user_id'];
$_SESSION['username'] = $user_data['username'];
$_SESSION['user_type'] = $user_data['user_type'];

if($user_data['user_type'] === 'admin'){
    header("Location: Admin/index.php");
    exit();
}else{
    // ✅ Redirect to index page
    header("Location: ../index.php");
    exit();
}



?>
