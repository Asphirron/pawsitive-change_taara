<?php
// admin_dynamic.php
include "../includes/db_connection.php"; //Establishes database connection
include "../Admin/admin_ui.php"; //Displays Navigation

$animals = new DatabaseCRUD('animal');
$adoptions = new DatabaseCRUD('adoption');
$volunteers = new DatabaseCRUD('volunteer');
$mDonations = new DatabaseCRUD('monetary_donation');
$iDonations = new DatabaseCRUD('inkind_donation');

$animalN = $adoptionN = $volunteerN = $mDonationN = $iDonationN = 0;

foreach($animals->readAll() as $a){
  if($a['status'] == 'At a Shelter'){ $animalN++;}
}
foreach($adoptions->readAll() as $ad){
  if($ad['status'] == 'pending'){ $adoptionN++;}
}
foreach($volunteers->readAll() as $v){
  $volunteerN++;
}
foreach($mDonations->readAll() as $m){
  if($m['status'] == 'verified'){ $mDonationN += $m['amount'];}
}
foreach($iDonations->readAll() as $i){
  if($i['status'] == 'received'){ $iDonationN += $i['quantity'];}
}





?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= ucwords($tableName) ?> Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../CSS/admin_style.css">
<link rel="icon" type="image/png" href="../Assets/UI/Taara_Logo.webp">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> <!-- for icons -->

<script src="https://cdn.tailwindcss.com"></script>
  <!-- Material Icons -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
<?= displayNav('index'); ?>

<!-- Main Content --->
 <div class='flex-c content'>
    <div class="flex-1">
      
      <!-- Dashboard Section -->
      <main class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white shadow rounded-xl p-6">
          <h3 class="font-bold">Animals</h3>
          <p class="text-blue-600 font-bold"><?php echo $animalN; ?> At a Shelter</p>
        </div>
        <div class="bg-white shadow rounded-xl p-6">
          <h3 class="font-bold">Adoption</h3>
          <p class="text-blue-600 font-bold"><?php echo $adoptionN; ?> Pending</p>
        </div>
        <div class="bg-white shadow rounded-xl p-6">
          <h3 class="font-bold">Volunteers</h3>
          <p class="text-blue-600 font-bold"><?php echo $volunteerN; ?> Registered</p>
        </div>
        <div class="bg-white shadow rounded-xl p-6">
          <h3 class="font-bold">Donations</h3>
          <p class="text-blue-600 font-bold">₱<?php echo $mDonationN; ?> • <?php echo $iDonationN; ?> Items</p>
        </div>
      </main>

      <!-- ADMIN PANEL SECTION -->
      <section class="p-8">
        <h2 class="text-2xl font-bold mb-6">Admin Panel</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">


          <!-- Manage Animals -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">Animal Management</h3>
            <p class="text-sm text-gray-600">Update profiles, track adoptions, mark rescued.</p>
            <a href='animals-records.php'><button class="mt-3 px-4 py-2 bg-blue-500 text-white rounded" style="height: 40px;">Manage</button></a>
          </div>

          <!-- Manage Donations -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">Donation Management</h3>
            <p class="text-sm text-gray-600">Approve donations and view history.</p>
            <a href='donations-topdonors.php'><button class="mt-3 px-4 py-2 bg-blue-500 text-white rounded" style="height: 40px;">Manage</button></a>
          </div>

          <!-- Manage Events -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">Event Management</h3>
            <p class="text-sm text-gray-600">Create, edit, or cancel adoption drives.</p>
            <a href='events-upcoming.php'><button class="mt-3 px-4 py-2 bg-blue-500 text-white rounded" style="height: 40px;">Manage</button></a>
          </div>

          <!-- Manage Rescues -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">Rescue Management</h3>
            <p class="text-sm text-gray-600">Resolve or reject rescue reports and view map</p>
            <a href='reports-rescue.php'><button class="mt-3 px-4 py-2 bg-blue-500 text-white rounded" style="height: 40px;">Manage</button></a>
          </div>

          <!-- Manage Inventory -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">Inventory Management</h3>
            <p class="text-sm text-gray-600">Create, insert, or take out inventory records</p>
            <a href='inventory.php'><button class="mt-3 px-4 py-2 bg-blue-500 text-white rounded" style="height: 40px;">Manage</button></a>
          </div>

          <!-- Manage Inventory -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">Volunteer Management</h3>
            <p class="text-sm text-gray-600">Accept or reject applications and set roles</p>
            <a href='volunteers-records.php'><button class="mt-3 px-4 py-2 bg-blue-500 text-white rounded" style="height: 40px;">Manage</button></a>
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
