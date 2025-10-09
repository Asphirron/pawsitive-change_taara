<?php
include '../includes/db_connection.php';
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
    $user_role = $user_data['role'];

    // ✅ Redirect non-directors to their corresponding admin pages
    if ($user_role !== 'director') {
        switch ($user_role) {
            case 'adoption':
                header("Location: adoptionrequest.php");
                exit();
            case 'rescue':
                header("Location: reports.php");
                exit();
            case 'donation':
                header("Location: donation.php");
                exit();
            case 'event':
                header("Location: events.php");
                exit();
            default:
                header("Location: ../login.php?msg=Unauthorized access");
                exit();
        }
    }
  }
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAARA Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <script>
    function showPage(page) {
      alert("You clicked " + page + " page. This will redirect to " + page + ".html");
      // Example: window.location.href = page + ".html";
    }

    function logout() {
      alert("Logging out...");
     window.location.href = "../login.php";
    }
  </script>
</head>
<body class="bg-gray-100 font-sans">

  <div class="flex">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-[#0b1d3a] text-white min-h-screen p-6">
      <!-- Logo -->
      <div class="flex flex-col items-center mb-10">
        <img src="logo.png" alt="Logo" class="w-20 h-20 mb-4">
        <h1 class="text-lg font-bold">T.A.A.R.A</h1>
      </div>

      <!-- Navigation -->
      <nav>
        <ul class="space-y-4">
          <li class="flex items-center gap-3 p-3 rounded-lg bg-gray-700 cursor-pointer" onclick="window.location.href='index.php'">
            <span class="material-icons">dashboard</span> Dashboard
          </li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='adoptionrequest.php'">
            <span class="material-icons">pets</span> Adoption Requests
          </li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='animalprofile.php'">
            <span class="material-icons">favorite</span> Animal Profiles
          </li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='volunteers.php'">
            <span class="material-icons">groups</span> Volunteers
          </li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='donation.php'">
            <span class="material-icons">volunteer_activism</span> Donations
          </li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='events.php'">
            <span class="material-icons">event</span> Events
          </li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='reports.php'">
            <span class="material-icons">report</span> Rescue Reports
          </li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='settings.php'">
            <span class="material-icons">settings</span> Settings
          </li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-red-600 cursor-pointer mt-10" onclick="logout()">
            <span class="material-icons">logout</span> Logout
          </li>
        </ul>
      </nav>
    </aside>


    <!-- Main Content -->
    <div class="flex-1">
      
      <!-- Dashboard Section -->
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
            <button class="mt-3 px-4 py-2 bg-blue-500 text-white rounded">Manage</button>
          </div>

          <!-- Manage Animals -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">Animal Management</h3>
            <p class="text-sm text-gray-600">Update profiles, track adoptions, mark rescued.</p>
            <button class="mt-3 px-4 py-2 bg-green-500 text-white rounded">Manage</button>
          </div>

          <!-- Manage Donations -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">Donation Management</h3>
            <p class="text-sm text-gray-600">Approve donations and view history.</p>
            <button class="mt-3 px-4 py-2 bg-purple-500 text-white rounded">Manage</button>
          </div>

          <!-- Manage Events -->
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg cursor-pointer">
            <h3 class="font-bold text-lg mb-3">Event Management</h3>
            <p class="text-sm text-gray-600">Create, edit, or cancel adoption drives.</p>
            <button class="mt-3 px-4 py-2 bg-orange-500 text-white rounded">Manage</button>
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

  <script>
  const userRole = "<?php echo $user_role; ?>";

  const navLinks = {
    dashboard: document.querySelector("li[onclick*='index.php']"),
    adoption: document.querySelector("li[onclick*='adoptionrequest.php']"),
    animals: document.querySelector("li[onclick*='animalprofile.php']"),
    volunteers: document.querySelector("li[onclick*='volunteers.php']"),
    donations: document.querySelector("li[onclick*='donation.php']"),
    events: document.querySelector("li[onclick*='events.php']"),
    reports: document.querySelector("li[onclick*='reports.php']")
  };

  // Lock everything first
  Object.values(navLinks).forEach(link => {
    if (link) link.classList.add("opacity-50", "pointer-events-none");
  });

  // Unlock based on role
  switch (userRole) {
    case "director":
      Object.values(navLinks).forEach(link => link?.classList.remove("opacity-50", "pointer-events-none"));
      break;
    case "adoption":
      navLinks.adoption?.classList.remove("opacity-50", "pointer-events-none");
      break;
    case "rescue":
      navLinks.reports?.classList.remove("opacity-50", "pointer-events-none");
      break;
    case "donation":
      navLinks.donations?.classList.remove("opacity-50", "pointer-events-none");
      break;
    case "event":
      navLinks.events?.classList.remove("opacity-50", "pointer-events-none");
      break;
  }
</script>

</body>
</html>
