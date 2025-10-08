<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TAARA - Create Account</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-sm">

    <!-- Logo Container -->
    <div class="flex justify-center mb-4">
      <img src="Assets/UI/Taara_Logo.webp" alt="TAARA Logo" class="w-24 h-24 object-contain">
    </div>

    <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Create Your Account</h1>

    <form class="space-y-5" action="includes/signup_process.php" method="post" enctype="multipart/form-data">

      <!-- Username -->
      <div>
        <label for="username" class="block text-gray-700 font-medium mb-1">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter your username"
               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500" required>
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email"
               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500" required>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password"
               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500" required>
      </div>

      <!-- Confirm Password -->
      <div>
        <label for="confirm_password" class="block text-gray-700 font-medium mb-1">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password"
               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500" required>
      </div>

      <!-- Profile Image -->
      <div>
        <label for="profile_img" class="block text-gray-700 font-medium mb-1">Profile Image</label>
        <input type="file" id="profile_img" name="profile_img" accept="image/*"
               class="w-full text-gray-600 py-2">
      </div>

     

      <!-- Submit Button -->
      <div>
        <button type="submit" name="submit" class="w-full bg-pink-500 hover:bg-pink-600 text-white font-semibold py-2 rounded-lg transition">
          Create Account
        </button>
      </div>

    </form>

    <!-- Back to Login -->
    <div class="text-center mt-4 space-y-2">
      <p class="text-gray-600">Already have an account? 
        <a href="login.php" class="text-pink-500 hover:underline font-medium">Login</a>
      </p>
      <a href="index.php" class="text-pink-500 hover:underline">Back to Home</a>
    </div>

  </div>

</body>
</html>
