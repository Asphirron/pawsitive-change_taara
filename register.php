<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create an Account - TAARA</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #221f3b;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background: #1c1c3c;
      color: #fff;
      display: flex;
      width: 800px;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.5);
    }
    .left-panel {
      flex: 1;
      background: #ffff;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    .left-panel img {
      height: 120px; 
      margin-bottom: 1rem;
    }
    .left-panel p {
      font-size: 1rem;
      color: #0c0124;
      line-height: 1.5;
    }
    .right-panel {
      flex: 2;
      padding: 3rem;
      background: #221f3b;
    }
    .right-panel h2 {
      margin-bottom: 0.3rem;
    }
    .right-panel p {
      margin-bottom: 1.5rem;
      font-size: 0.95rem;
    }
    .right-panel p a {
      color: #9f8cff;
      font-weight: bold;
      text-decoration: none;
      transition: 0.3s;
    }
    .right-panel p a:hover {
      color: #e83e8c;
    }
    .form-group {
      display: flex;
      gap: 1rem;
      margin-bottom: 1rem;
    }
    .form-group input, 
    .form-single input {
      width: 100%;
      padding: 0.8rem;
      border: none;
      border-radius: 8px;
      background: #2d2a4f;
      color: #fff;
      outline: none;
    }
    .form-single {
      margin-bottom: 1rem;
    }
    .form-single input:focus, 
    .form-group input:focus {
      border: 1px solid #9f8cff;
    }
    .btn {
      width: 100%;
      padding: 1rem;
      border: none;
      border-radius: 8px;
      background: #e83e8c;
      color: #fff;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }
    .btn:hover {
      background: #c92d74;
      transform: scale(1.05);
    }
    .back-btn {
      margin-bottom: 1rem;
      background: #2b2ba1;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      color: #fff;
      cursor: pointer;
      transition: 0.3s;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    .back-btn:hover {
      background: #1c1c7d;
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- LEFT PANEL -->
    <div class="left-panel">
      <img src="Assets/UI/taaralogo-removebg-preview.png" alt="TAARA Logo">
      <p>Protecting Paws, <br> Giving Hope, <br> Building a Better Tomorrow.</p>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
      <button class="back-btn" onclick="window.location.href='index.php'">
        <i class="fa-solid fa-arrow-left"></i> Back
      </button>
      <h2>Create an account</h2>
      <p>Already have an account? <a href="login.php">Log in</a></p>

      <form action="includes/register_process.php" method="post">
         <div class="form-single">
          <input type="text" placeholder="Username" name='username' required>
        </div>
        <div class="form-single">
          <input type="email" placeholder="Email" name='email' required>
        </div>
        <div class="form-group">
          <input type="password" placeholder="Password" name='password' required>
          <input type="password" placeholder="Confirm Password" name='confirm_password' required>
        </div>
    
        <button type="submit" class="btn">Create Account</button>
      </form>
    </div>
  </div>
</body>
</html>