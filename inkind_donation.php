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
        <li><a class='active' href="donation.php">Donation</a></li>
        <li><a href="volunteer.php">Volunteer</a></li>
        <li><a href="events.php">Events</a></li>
        <li><a href="index.php">About</a></li>
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
    
    <!-- Two Columns: Type & Image -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


          <!-- Full Name -->
      <div>
        <label for="fullname" class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
        <input type="text" id="fullname" name="fullname" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Enter your full name">
      </div>

      <div>
        <label for="contact" class="block text-sm font-semibold text-gray-700 mb-1">Contact Number</label>
        <input type="number" id="contact" name="contact" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Enter your contact number">
      </div>

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
        <label for="item_name" class="block text-sm font-semibold text-gray-700 mb-1">Item Name</label>
        <input type="text" id="fullname" name="item_name" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Enter the item name e.g. Dog Food">
      </div>

      <div>
        <label for="image" class="block text-sm font-semibold text-gray-700 mb-1">Picture of Donation Item</label>
        <input type="file" id="image" name="image" accept="image/*" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 bg-white">
      </div>
      
      <div>
        <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-1">Quantity</label>
        <input type="number" id="contact" name="quantity" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Enter the item quantity">
      </div>

        <!-- Message -->
      <div>
        <label for="message" class="block text-sm font-semibold text-gray-700 mb-1">Message</label>
        <textarea id="message" name="message" rows="4" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="What do you want to say to us?"></textarea>
      </div>


      <div>
      <div>
        <label for="location" class="block text-sm font-semibold text-gray-700 mb-1">Drop-off Location</label>
        <input type="text" id="location" name="location" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500" placeholder="Where should we expect the item?">
      </div>

      <!-- Date -->
      <div>
        <label for="date" class="block text-sm font-semibold text-gray-700 mb-1">Expected Drop-off Date</label>
        <input type="date" id="date" name="date" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
      </div>
      </div>

    </div>

    <!-- Buttons -->
    <div class="flex justify-end gap-4 pt-4">
      <button type="button" onclick="window.location.href='index.php'" class="px-6 py-2 bg-gray-600 text-white border border-gray-400 text-gray-700 rounded-lg hover:bg-gray-500 transition">Cancel</button>
      <button type="submit" class="px-6 py-2 bg-pink-600 text-white rounded-lg font-semibold hover:bg-pink-700 transition">Donate</button>
    </div>
  </form>
</div>

  <!-- FOOTER -->
  <footer>
    <p>TAARA located at P-3 Burac St., San Lorenzo, Tabaco, Philippines</p>
    <p><a href="#"><i class="fa-brands fa-facebook"></i> Facebook</a> | <a href="tel:09055238105"><i class="fa-solid fa-phone"></i> 0905 523 8105</a></p>
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
