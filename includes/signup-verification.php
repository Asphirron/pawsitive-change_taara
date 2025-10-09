<?php

include "../includes/db_connection.php";


if(!$_SERVER['REQUEST_METHOD'] == 'POST'){
    header('Location: ../register.php?msg=invalid operation');
    exit();
}

if(!isset($_POST['username'])){
    header('Location: ../register.php?msg=Username must be filled');
    exit();
}

if(!isset($_POST['email'])){
    header('Location: ../register.php?msg=Email must be filled');
    exit();
}

if(!isset($_POST['password'])){
    header('Location: ../register.php?msg=Password must be filled');
    exit();
}

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

$user_table = new DatabaseCRUD('user');

$data_email = $user_table->read($email, 'email');

if($data_email['email'] == $email){
    header('Location: ../register.php?msg=Email already exists');
    exit();
}

$data = [
    'username'=> $username,
    'email'=> $email,
    'password'=> $password
];


//Creating Account

    if ($user_table->create($data)) {
        // ✅ SUCCESS MESSAGE DISPLAY
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Account Created Successfully</title>
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
          <script src="https://cdn.tailwindcss.com"></script>
          <style>
            body {
              font-family: "Poppins", sans-serif;
              background: #fdfdfd;
              display: flex;
              justify-content: center;
              align-items: center;
              height: 100vh;
              margin: 0;
            }
            .success-container {
              background: #fff;
              padding: 40px 60px;
              border-radius: 16px;
              box-shadow: 0 8px 25px rgba(0,0,0,0.08);
              text-align: center;
              max-width: 480px;
              width: 90%;
              animation: fadeIn 0.5s ease-in-out;
            }
            .success-icon {
              background: #22c55e;
              color: #fff;
              font-size: 48px;
              width: 90px;
              height: 90px;
              line-height: 90px;
              border-radius: 50%;
              margin: 0 auto 20px;
              display: flex;
              justify-content: center;
              align-items: center;
              animation: pop 0.6s ease-in-out;
            }
            .success-container h2 {
              font-size: 26px;
              color: #333;
              margin-bottom: 10px;
              font-weight: 700;
            }
            .success-container p {
              color: #666;
              font-size: 15px;
              margin-bottom: 30px;
            }
            .buttons {
              display: flex;
              justify-content: center;
              gap: 20px;
            }
            .buttons a {
              text-decoration: none;
              color: white;
              background: #c9378a;
              padding: 10px 20px;
              border-radius: 8px;
              font-weight: 600;
              transition: 0.3s;
            }
            .buttons a.secondary {
              background: #555;
            }
            .buttons a:hover {
              opacity: 0.9;
              transform: translateY(-2px);
            }
            @keyframes pop {
              0% { transform: scale(0); opacity: 0; }
              80% { transform: scale(1.1); opacity: 1; }
              100% { transform: scale(1); }
            }
            @keyframes fadeIn {
              from { opacity: 0; transform: translateY(20px); }
              to { opacity: 1; transform: translateY(0); }
            }
          </style>
        </head>
        <body>
          <div class="success-container">
            <div class="success-icon">
              <i class="fa-solid fa-check"></i>
            </div>
            <h2>Account Created Successfully!</h2>
            <p>Welcome to TAARA! Your account has been created successfully. You can now log in to start exploring.</p>

            <div class="buttons">
              <a href="../login.php">Go to Login</a>
              <a href="../index.php" class="secondary">Return Home</a>
            </div>
          </div>

          <script>
            setTimeout(() => {
              window.location.href = "../login.php";
            }, 8000);
          </script>
        </body>
        </html>';
        exit;
    } else {
        echo "<script>alert('Error: Unable to create account. Please try again.'); window.history.back();</script>";
    }
