<?php
include "includes/db_connection.php";
session_start();

$logged_in = false;
$user_data = $username = $user_img = $uid = $user_type = $email = "";

if (isset($_SESSION['email'])) {
  $logged_in = true;
  $email = $_SESSION['email'];
  $user_table = new DatabaseCRUD('user');
  $user_result = $user_table->select(["*"], ["email" => $email], 1);

  if (!empty($user_result)) {
    $user_data = $user_result[0];
    $uid = $user_data['user_id'];
    $user_img = $user_data['profile_img'];
    $username = $user_data['username'];
    $user_type = $user_data['user_type'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="CSS/globals.css">
  <link rel="stylesheet" href="CSS/index.css">
  <link rel="stylesheet" href="CSS/donation.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="CSS/essentials.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>In-Kind Donation - TAARA</title>
</head>
<body class="bg-gray-50">

<!-- HEADER -->
<header>
  <img src="Assets/UI/taaralogo.jpg" alt="TAARA Logo">
  <div class="nav-container">
    <nav>
      <ul>
        <li><a href="rescue.php">Rescue</a></li>
        <li><a href="adoption.php">Adopt</a></li>
        <li><a href="donation.php">Donation</a></li>
        <li><a href="volunteer.php">Volunteer</a></li>
        <li><a href="events.php">Events</a></li>
        <li><a class='active' href="index.php">About</a></li>
      </ul>
    </nav>

    <?php
      if ($logged_in) {
        echo "<img src='Assets/Profile_Images/$user_img' class='profile-img' id='user_profile'>";
      } else {
        echo "<a href='register.html' class='bg-pink-600 text-white px-4 py-2 rounded-full font-bold hover:bg-pink-700 flex items-center gap-2'>
                <i class='fa-solid fa-user-plus'></i> Register
              </a>";
      }
    ?>
  </div>
</header>

<!-- BANNER SECTION -->
<section class="relative w-full h-72 bg-gray-200">
  <img src="Assets/Images/Donation_Banner.jpg" alt="Donation Banner" class="absolute inset-0 w-full h-full object-cover opacity-80">
  <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center text-center text-white">
    <h1 class="text-4xl md:text-5xl font-bold drop-shadow-lg">In-Kind Donation</h1>
    <p class="mt-2 text-lg md:text-xl text-gray-100 max-w-2xl">Help us by donating essential items — every contribution makes a difference.</p>
  </div>
</section>

<!-- MAIN CONTENT -->
<div class="content-area max-w-5xl mx-auto mt-10 bg-white shadow-md rounded-2xl p-8 mb-10">

  <form action="includes/donate_item.php" method="post" enctype="multipart/form-data" class="space-y-6">
    
    <!-- Full Name -->
    <div>
      <label for="fullname" class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
      <input type="text" id="fullname" name="fullname" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Enter your full name">
    </div>

    <!-- Two Columns: Type & Image -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label for="type" class="block text-sm font-semibold text-gray-700 mb-1">Donation Type</label>
        <select id="type" name="type" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
          <option value="food">Food</option>
          <option value="medicine">Medicine</option>
          <option value="toys">Toys</option>
          <option value="supplies">Supplies</option>
          <option value="equipment">Equipment</option>
          <option value="other">Other</option>
        </select>
      </div>

      <div>
        <label for="image" class="block text-sm font-semibold text-gray-700 mb-1">Picture of Donation Item</label>
        <input type="file" id="image" name="image" accept="image/*" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 bg-white">
      </div>
    </div>

    <!-- Message -->
    <div>
      <label for="message" class="block text-sm font-semibold text-gray-700 mb-1">Message</label>
      <textarea id="message" name="message" rows="4" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="What do you want to say to us?"></textarea>
    </div>

    <!-- Two Columns: Contact & Location -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label for="contact" class="block text-sm font-semibold text-gray-700 mb-1">Contact Number</label>
        <input type="number" id="contact" name="contact" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Enter your contact number">
      </div>

      <div>
        <label for="location" class="block text-sm font-semibold text-gray-700 mb-1">Drop-off Location</label>
        <input type="text" id="location" name="location" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Where should we expect the item?">
      </div>
    </div>

    <!-- Date -->
    <div>
      <label for="date" class="block text-sm font-semibold text-gray-700 mb-1">Expected Drop-off Date</label>
      <input type="date" id="date" name="date" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
    </div>

    <!-- Checkbox -->
    <div class="flex items-center gap-3">
      <input type="checkbox" id="agreed" name="agreed" value="true" class="w-5 h-5 text-pink-600 border-gray-300 rounded focus:ring-pink-500">
      <label for="agreed" class="text-gray-700">Send me E-mail updates</label>
    </div>

    <!-- Buttons -->
    <div class="flex justify-end gap-4 pt-4">
      <button type="button" onclick="window.location.href='index.php'" class="px-6 py-2 border border-gray-400 text-gray-700 rounded-lg hover:bg-gray-100 transition">Cancel</button>
      <button type="submit" class="px-6 py-2 bg-pink-600 text-white rounded-lg font-semibold hover:bg-pink-700 transition">Donate</button>
    </div>
  </form>
</div>

<!-- FOOTER -->
<footer class="site-footer">
  <div class="footer-content">
    <img src="Assets/UI/facebook.png" class="footer-content-img">
    <h3 class="footer-content-text">Facebook</h3>
  </div>
  <div class="footer-content">
    <img src="Assets/UI/phone.png" class="footer-content-img">
    <h3 class="footer-content-text">09055238105</h3>
  </div>
  <div class="footer-content">
    <h3 class="footer-content-text">Tabaco Animal Advocates and Rescuers Association - All rights reserved</h3>
  </div>
</footer>

<script>
  const profileImg = document.getElementById("user_profile");
  const modal = document.getElementById("user_options");

  if (profileImg) {
    profileImg.addEventListener("click", () => {
      modal.style.display = "flex";
    });
  }

  function closeModal() { modal.style.display = "none"; }
  function logout() {
    fetch('includes/logout.php')
      .then(() => window.location.href = 'login.php');
  }
  window.addEventListener('click', function(e) {
    if (e.target === modal) closeModal();
  });
</script>
</body>
</html>
