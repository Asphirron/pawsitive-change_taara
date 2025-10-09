<?php
include "db_connection.php";
include "send_email.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../register.php");
    exit();
}

// Collect and sanitize input
$username        = htmlspecialchars(trim($_POST['username']));
$email           = htmlspecialchars(trim($_POST['email']));
$password        = $_POST['password'];
$confirm_password= $_POST['confirm_password'];
$user_type       = $_POST['user_type'] ?? 'client';

// Check required fields
if (!$username || !$email || !$password || !$confirm_password) {
    header("Location: ../register.php?error=Please fill all required fields");
    exit();
}

// Check password match
if ($password !== $confirm_password) {
    header("Location: ../register.php?error=Passwords do not match");
    exit();
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Handle profile image
$profile_img = 'user.png'; // default
if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if (in_array($_FILES['profile_img']['type'], $allowed_types)) {
        $ext = pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION);
        $profile_img = uniqid('profile_', true) . '.' . $ext;
        move_uploaded_file($_FILES['profile_img']['tmp_name'], "../Assets/UI/profiles/$profile_img");
    }
}

// Database connection
$conn = connect();

// Check if email already exists
$stmt = $conn->prepare("SELECT email FROM user WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    $conn->close();
    header("Location: ../register.php?error=Email already exists");
    exit();
}
$stmt->close();

// Insert new user
$stmt = $conn->prepare("INSERT INTO user (username, email, password, profile_img, user_type) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $email, $hashed_password, $profile_img, $user_type);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: ../login.php?success=Account created successfully. Please login.");
    exit();
} else {
    $stmt->close();
    $conn->close();
    header("Location: ../register.php?error=Failed to create account. Try again.");
    exit();
}
?>