<?php
include "includes/db_connection.php";

 //echo $_SESSION['email'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - TAARA</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, Helvetica, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: #f0f2f5;
    }

    .login-container {
      position: relative;
      background: #fff;
      padding: 3rem 2.5rem;
      border-radius: 15px;
      width: 420px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      text-align: center;
    }

    /* Back button top-left */
    .back-btn {
      position: absolute;
      top: 15px;
      left: 15px;
      background: #e83e8c;
      border: none;
      padding: 0.4rem 0.9rem;
      border-radius: 20px;
      color: #fff;
      cursor: pointer;
      transition: 0.3s;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 0.85rem;
      font-weight: bold;
    }
    .back-btn:hover {
      background: #c92d74;
      transform: scale(1.05);
    }

    /* Logo */
    .login-container img {
      height: 115px;
      margin-top: 1.2rem;
      margin-bottom: 0.4rem;
    }

    /* Heading */
    h2 {
      margin-bottom: 1rem;
      font-weight: bold;
      font-size: 1.9rem;
      color: #2b2ba1;
    }

    p {
      margin-bottom: 1.8rem;
      color: #555;
      font-size: 0.95rem;
    }

    p a {
      color: #e83e8c;
      font-weight: bold;
      text-decoration: none;
      transition: 0.3s;
    }
    p a:hover {
      color: #c92d74;
    }

    .form-single {
      margin-bottom: 1.2rem;
      text-align: left;
    }

    .form-single label {
      font-size: 0.9rem;
      color: #333;
      margin-bottom: 0.4rem;
      display: block;
      font-weight: bold;
    }

    .form-single input {
      width: 100%;
      padding: 0.85rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
      outline: none;
      transition: 0.3s;
    }
    .form-single input:focus {
      border: 1px solid #2b2ba1;
      box-shadow: 0 0 5px rgba(43, 43, 161, 0.3);
    }

    /* Gradient Login Button with fixed size */
    .btn {
      display: inline-block;           /* shrink to content */
      width: 350px;                    /* ✅ fixed width */
      padding: 0.9rem;
      border: none;
      border-radius: 8px;
      background: linear-gradient(135deg, #2b2ba1, #1c1c7d);
      color: #fff;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
      margin-top: 0.8rem;
    }
    .btn:hover {
      background: linear-gradient(135deg, #4a6ed1, #2b2ba1);
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <div class="login-container">
    <!-- Back button -->
    <button class="back-btn" onclick="window.location.href='index.php'">
      <i class="fa-solid fa-arrow-left"></i> Back
    </button>

    <img src="Assets/UI/taaralogo-removebg-preview.png" alt="TAARA Logo">
    <h2>Login</h2>
    <p>Don’t have an account? <a href="register.php">Register</a></p>

    <form action="includes/login_verification.php" method="post">
      <div class="form-single">
        <label>Email</label>
        <input type="email" placeholder="Enter your email" name='email' required>
        <p class="text-red-500 text-sm mt-1">
          <?php if(isset($_GET['msg1'])) { echo htmlspecialchars($_GET['msg1']); } ?>
        </p>
      </div>
      <div class="form-single">
        <label>Password</label>
        <input type="password" placeholder="Enter your password" name='password'required>
        <p class="text-red-500 text-sm mt-1">
          <?php if(isset($_GET['msg2'])) { echo htmlspecialchars($_GET['msg2']); } ?>
        </p>
      </div>
      <button type="submit" class="btn">Login</button>
    </form>
  </div>
</body>
</html>