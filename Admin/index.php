<?php
// admin_dynamic.php
include "../includes/db_connection.php"; //Establishes database connection
include "../Admin/admin_ui.php"; //Displays Navigation



?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= ucwords($tableName) ?> Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../CSS/admin_style.css">
<link rel="icon" type="image/png" href="../Assets/UI/Taara_Logo.webp">

<script src="https://cdn.tailwindcss.com"></script>
  <!-- Material Icons -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
<?= displayNav('dashboard'); ?>

<!-- Main Content --->
 <div class='flex-c content'>
    <div class="flex-1">
      
      <!-- Dashboard Section ---->
      <main class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white shadow rounded-xl p-6">
          <h3 class="font-bold">Animal Profiles</h3>
          <p class="text-gray-600">Manage rescued and adoptable animals.</p>
        </div>
        <div class="bg-white shadow rounded-xl p-6">
          <h3 class="font-bold">Adoption</h3>
          <p class="text-blue-600 font-bold">26 Pending</p>
        </div>
        <div class="bg-white shadow rounded-xl p-6">
          <h3 class="font-bold">Volunteers</h3>
          <p class="text-green-600 font-bold">100 Registered</p>
        </div>
        <div class="bg-white shadow rounded-xl p-6">
          <h3 class="font-bold">Donations</h3>
          <p>₱10,000 • 30 Items</p>
        </div>
      </main>

      <!-- ADMIN PANEL SECTION -->
      <section class="p-8">
        <h2 class="text-2xl font-bold mb-6">Admin Panel</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

          <!-- Manage Users -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">User Management</h3>
            <p class="text-sm text-gray-600">Add, update, or remove volunteers & adopters.</p>
            <a href='animals-records.php'><button class="mt-3 px-4 py-2 bg-green-500 text-white rounded">Manage</button></a>
          </div>

          <!-- Manage Animals -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">Animal Management</h3>
            <p class="text-sm text-gray-600">Update profiles, track adoptions, mark rescued.</p>
            <a href='animals-records.php'><button class="mt-3 px-4 py-2 bg-green-500 text-white rounded">Manage</button></a>
          </div>

          <!-- Manage Donations -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">Donation Management</h3>
            <p class="text-sm text-gray-600">Approve donations and view history.</p>
            <a href='donations-topdonors.php'><button class="mt-3 px-4 py-2 bg-green-500 text-white rounded">Manage</button></a>
          </div>

          <!-- Manage Events -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">Event Management</h3>
            <p class="text-sm text-gray-600">Create, edit, or cancel adoption drives.</p>
            <a href='events-upcoming.php'><button class="mt-3 px-4 py-2 bg-green-500 text-white rounded">Manage</button></a>
          </div>

          <!-- Reports -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">Reports & Analytics</h3>
            <p class="text-sm text-gray-600">Generate and download reports in CSV/PDF.</p>
            <button class="mt-3 px-4 py-2 bg-red-500 text-white rounded">Generate</button>
          </div>

          <!-- Notifications -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">Admin Notifications</h3>
            <p class="text-sm text-gray-600">Approve requests & review pending alerts.</p>
            <button class="mt-3 px-4 py-2 bg-yellow-500 text-white rounded">View</button>
          </div>

        </div>
      </section>
    </div>
</div>
<!-- DYNAMIC MODAL -->
<?php 
include 'dynamic_modal.php'; 
//Includes Add/Edit/View, Delete, Message, and ImagePreview Modals
?> 

<?php if(!empty($message)): ?>
<script> showMessage("<?= e($message) ?>"); </script>
<?php endif; ?>
</body>
</html>
