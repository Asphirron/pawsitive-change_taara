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
      font-family: 'Poppins', Arial, Helvetica, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: linear-gradient(135deg, #f7f8fc, #eaeefc);
      color: #333;
    }

    /* Container */
    .login-container {
      position: relative;
      background: #fff;
      padding: 3rem 2.8rem;
      border-radius: 20px;
      width: 420px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: all 0.3s ease;
    }

    .login-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    /* Back Button */
    .back-btn {
      position: absolute;
      top: 15px;
      left: 15px;
      background: linear-gradient(135deg, #e83e8c, #d52a78);
      border: none;
      padding: 0.45rem 1rem;
      border-radius: 30px;
      color: #fff;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.85rem;
      font-weight: 600;
      letter-spacing: 0.3px;
    }

    .back-btn:hover {
      background: linear-gradient(135deg, #ff5fa9, #e83e8c);
      transform: scale(1.07);
    }

    /* Logo */
    .login-container img {
      height: 110px;
      margin-top: 1.8rem;
      margin-bottom: 0.9rem;
      filter: drop-shadow(0 3px 6px rgba(0, 0, 0, 0.1));
    }

    /* Heading */
    h2 {
      margin-bottom: 0.8rem;
      font-weight: 700;
      font-size: 2rem;
      color: #2b2ba1;
      letter-spacing: 0.5px;
    }

    p {
      margin-bottom: 1.8rem;
      color: #666;
      font-size: 0.95rem;
    }

    p a {
      color: #e83e8c;
      font-weight: 600;
      text-decoration: none;
      transition: 0.3s;
    }

    p a:hover {
      color: #b81e66;
      text-decoration: underline;
    }

    /* Form */
    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      align-items: stretch;
    }

   .form-single {
  position: relative;
  margin-bottom: 1.3rem;
  text-align: left;
}

/* Labels */
.form-single label {
  display: block;
  font-weight: 600;
  margin-bottom: 0.4rem;
  color: #333;
  font-size: 0.9rem;
}

/* Input fields */
.form-single input {
  width: 100%;
  padding: 0.9rem 2.8rem 0.9rem 1rem;
  border: 1.5px solid #e0e0ff;
  border-radius: 12px;
  background-color: #fafaff;
  font-size: 1rem;
  outline: none;
  transition: all 0.3s ease;
  box-sizing: border-box;
}

.form-single input:focus {
  border-color: #2b2ba1;
  background: #fff;
  box-shadow: 0 0 8px rgba(43, 43, 161, 0.25);
}


/* 👁 Eye icon styling - aligned perfectly */
.form-single input[type="password"] {
  padding-right: 2.8rem; /* gives space for the icon */
}

.toggle-password {
  position: absolute;
  right: 14px;
  top: 46.5%;
  font-size: 1.1rem;
  color: #777;
  cursor: pointer;
  padding: 6px;
  border-radius: 50%;
  transition: background 0.2s ease, color 0.2s ease, transform 0.15s ease;
}

.toggle-password:hover {
  color: #2b2ba1;
  background: rgba(230, 230, 255, 0.5);
 
}


/* Error messages */
.text-red-500 {
  margin-top: 0.4rem;
  font-size: 0.85rem;
  color: #e63946;
}
    /* Button */
    .btn {
      width: 100%;
      padding: 1rem;
      border: none;
      border-radius: 10px;
      background: linear-gradient(135deg, #2b2ba1, #1c1c7d);
      color: #fff;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 0.5rem;
      box-shadow: 0 4px 12px rgba(43, 43, 161, 0.25);
    }

    .btn:hover {
      background: linear-gradient(135deg, #4a6ed1, #2b2ba1);
      transform: scale(1.04);
      box-shadow: 0 6px 18px rgba(43, 43, 161, 0.35);
    }

    @media (max-width: 480px) {
      .login-container {
        width: 90%;
        padding: 2.5rem 2rem;
      }

      h2 {
        font-size: 1.7rem;
      }

      .btn {
        font-size: 0.95rem;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <button class="back-btn" onclick="window.location.href='index.php'">
      <i class="fa-solid fa-arrow-left"></i> Back
    </button>

    <img src="Assets/UI/taaralogo-removebg-preview.png" alt="TAARA Logo">
    <h2>Login</h2>
    <p>Don’t have an account? <a href="register.php">Register</a></p>

   <form action="includes/login_verification.php" method="post">
  <div class="form-single">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" placeholder="Enter your email" required>
    <?php if(isset($_GET['msg1'])): ?>
      <p class="text-red-500"><?= htmlspecialchars($_GET['msg1']); ?></p>
    <?php endif; ?>
  </div>

  <div class="form-single">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" placeholder="Enter your password" required>
    <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
    <?php if(isset($_GET['msg2'])): ?>
      <p class="text-red-500"><?= htmlspecialchars($_GET['msg2']); ?></p>
    <?php endif; ?>
  </div>

  <button type="submit" class="btn">Login</button>
</form>
  </div>

  
<script>
  const togglePassword = document.getElementById("togglePassword");
  const passwordField = document.getElementById("password");

  togglePassword.addEventListener("click", () => {
    const isPassword = passwordField.type === "password";
    passwordField.type = isPassword ? "text" : "password";

    // Toggle icon between eye and eye-slash
    togglePassword.classList.toggle("fa-eye");
    togglePassword.classList.toggle("fa-eye-slash");

    // Optional: change tooltip or aria-label for accessibility
    togglePassword.title = isPassword ? "Hide password" : "Show password";
  });
</script>

</body>
</html>