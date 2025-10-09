<?php
include "includes/db_connection.php";
session_unset();
session_abort();


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TAARA Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-sm">
    
    <!-- Logo Container -->
    <div class="flex justify-center mb-4">
      <img src="Assets/UI/Taara_Logo.webp" alt="TAARA Logo" class="w-24 h-24 object-contain">
    </div>

    <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Welcome to TAARA</h1>

    <form class="space-y-5" action="includes/login_verification.php" method="post">

      <!-- Email Input -->
      <div>
        <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email"
               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
        <p class="text-red-500 text-sm mt-1">
          <?php if(isset($_GET['msg1'])) { echo htmlspecialchars($_GET['msg1']); } ?>
        </p>
      </div>

      <!-- Password Input -->
      <div>
        <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password"
               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
        <p class="text-red-500 text-sm mt-1">
          <?php if(isset($_GET['msg2'])) { echo htmlspecialchars($_GET['msg2']); } ?>
        </p>
      </div>

      <!-- Submit Button -->
      <div>
        <button type="submit" name="submit" class="w-full bg-pink-500 hover:bg-pink-600 text-white font-semibold py-2 rounded-lg transition">
          Login
        </button>
      </div>

    </form>

    <!-- Sign Up & Back to Home -->
    <div class="text-center mt-4 space-y-2">
      <p class="text-gray-600">Don't have an account? 
        <a href="signup.php" class="text-pink-500 hover:underline font-medium">Create Account</a>
      </p>
      <a href="index.php" class="text-pink-500 hover:underline">Back to Home</a>
    </div>
  </div>

</body>
</html>
