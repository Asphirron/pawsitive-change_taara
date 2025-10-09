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
                header("Location: Admin/adoptionrequest.php");
                exit();
            case 'rescue':
                header("Location: Admin/reports.php");
                exit();
            case 'donation':
                header("Location: Admin/donation.php");
                exit();
            case 'event':
                header("Location: Admin/events.php");
                exit();
            default:
                header("Location: ../login.php?msg=Unauthorized access");
                exit();
        }
    }
  }
}


$user_table = new DatabaseCRUD('user');
$animal_table = new DatabaseCRUD('animal');
$application_table = new DatabaseCRUD('adoption_application');
$screening_table = new DatabaseCRUD('adoption_screening');
$adoption_table = new DatabaseCRUD('adoption');

error_reporting(E_ERROR | E_PARSE);

// Handle Accept/Deny Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $app_id = $_POST['a_application_id'];

    $application = $application_table->read($app_id, "a_application_id");
    $user = $user_table->read($application['user_id'], "user_id");
    $animal = $animal_table->read($application['animal_id'], "animal_id");
    $date_responded = date("Y-m-d");

    if ($action === "accept") {
      

        $application_table->update($app_id, ["status" => "Accepted"], "a_application_id");
        $application_table->update($app_id, ["date_responded" => "$date_responded"], "date_responded");
        $animal_table->update($application['animal_id'], ["status" => "Adopted"], "animal_id");

        $adopter_email = $user['email'];
        $adopter_name = $user['username'];
        $pet_name = $animal['name'];
        $subject = "🎉 Adoption Approved - $pet_name";
        $message = "Hi $adopter_name, your application to adopt $pet_name has been approved!";
    } elseif ($action === "deny") {
        $application_table->update($app_id, ["status" => "Rejected"], "a_application_id");

        $adopter_email = $user['email'];
        $adopter_name = $user['username'];
        $pet_name = $animal['name'];
        $subject = "Adoption Application Update - $pet_name";
        $message = "Hi $adopter_name, unfortunately your application to adopt $pet_name has been denied.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>TAARA Admin Dashboard - Adoption Requests</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='index.php'">
            <span class="material-icons">dashboard</span> Dashboard
          </li>
          <li class="flex items-center gap-3 p-3 rounded-lg bg-gray-700 cursor-pointer" onclick="window.location.href='adoptionrequest.php'">
            <span class="material-icons">pets</span> Adoption Requests
          </li>
          <li class="flex items-center gap-3 p-3 rounded-lg hiver:bg-gray-700 cursor-pointer" onclick="window.location.href='animalprofile.php'">
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

  <main class="flex-1 p-8">
    <h1 class="text-3xl font-bold mb-6">🐾 Adoption Requests</h1>

    <!-- Applications Table -->
    <div class="bg-white p-6 rounded-xl shadow mb-10">
      <h2 class="text-xl font-bold mb-4">Pending Applications</h2>
      <table class="w-full border rounded-lg">
        <thead class="bg-gray-200">
          <tr>
            <th class="p-3 text-left">Applicant</th>
            <th class="p-3 text-left">Animal</th>
            <th class="p-3 text-left">Date Applied</th>
            <th class="p-3 text-left">Status</th>
            <th class="p-3 text-left">Action</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $apps = $application_table->select(["*"], ["status" => "Pending"]);
        $appDetails = []; // preload app details for JSON

        foreach ($apps as $row) {
            $animal = $animal_table->read($row['animal_id'], "animal_id");
            $user = $user_table->read($row['user_id'], "user_id");
            $screening = $screening_table->read($row['a_application_id'], "a_application_id");

            $app_id = $row['a_application_id'];

            $appDetails[$app_id] = [
                "app_id" => $app_id,
                "full_name" => $row['full_name'],
                "address" => $row['address'],
                "animal" => $animal['name'],
                "date_applied" => $row['date_applied'],
                "housing" => $screening['housing'] ?? '',
                "reason" => $screening['reason'] ?? '',
                "own_pets" => $screening['own_pets'] ?? '',
                "financial_ready" => $screening['financial_ready'] ?? '',
                "status" => $row['status']
            ];

            echo "<tr class='border-b hover:bg-gray-50'>
                <td class='p-3'>{$row['full_name']}</td>
                <td class='p-3'>{$animal['name']}</td>
                <td class='p-3'>{$row['date_applied']}</td>
                <td class='p-3 text-blue-600 font-semibold'>{$row['status']}</td>
                <form method='post'>
                <td class='p-3'>
                  <button type='button' onclick=\"viewApplication($app_id)\" class='bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600'>More Details</button>
                </td>
                </form>
              </tr>";
        }
        ?>
        </tbody>
      </table>
    </div>

     <!-- Accepted Applications -->
    <div class="bg-white p-6 rounded-xl shadow mb-10">
      <h2 class="text-xl font-bold mb-4">Accepted Applications</h2>
      <table class="w-full border rounded-lg">
        <thead class="bg-gray-200">
          <tr>
            <th class="p-3 text-left">Applicant</th>
            <th class="p-3 text-left">Animal</th>
            <th class="p-3 text-left">Date Accepted</th>
            <!--th class="p-3 text-left"> Action</th -->
          </tr>
        </thead>
        <tbody>
        <?php
        $applications = $application_table->readAll();
        foreach ($applications as $app) {
          if($app['status'] === 'Accepted')
            $user = $user_table->read($app['user_id'], "user_id");
            $animal = $animal_table->read($app['animal_id'], "animal_id");
            echo "<tr class='border-b hover:bg-gray-50'>
                <td class='p-3'>{$user['username']}</td>
                <td class='p-3'>{$animal['name']}</td>
                <td class='p-3'>{$app['date_responded']}</td>
                <!-- td class='p-3'>{$app['date_responded']}</td -->

              </tr>";
        }
        ?>
        </tbody>
      </table>
    </div>

    <!-- Rejected Applications -->
    <div class="bg-white p-6 rounded-xl shadow">
      <h2 class="text-xl font-bold mb-4">Rejected Applications</h2>
      <table class="w-full border rounded-lg">
        <thead class="bg-gray-200">
          <tr>
            <th class="p-3 text-left">Applicant</th>
            <th class="p-3 text-left">Animal</th>
            <th class="p-3 text-left">Date Rejected</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $rejected = $application_table->select(["*"], ["status" => "Rejected"]);
        foreach ($rejected as $rej) {
            $animal = $animal_table->read($rej['animal_id'], "animal_id");
            echo "<tr class='border-b hover:bg-gray-50'>
                <td class='p-3'>{$rej['full_name']}</td>
                <td class='p-3'>{$animal['name']}</td>
                <td class='p-3'>{$rej['date_responded']}</td>
              </tr>";
        }

        ?>
  </main>
</div>

<!-- View Application Modal -->
<div id="applicationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center">
  <div class="bg-white rounded-lg p-6 w-1/2 shadow-lg">
    <h2 class="text-xl font-bold mb-4">Application Details</h2>
    <div id="applicationDetails" class="mb-4"></div>
    <div class="flex justify-end gap-3">
      <form method="POST">
        <input type="hidden" name="a_application_id" id="modal_app_id">
        <button type="submit" name="application_action" value="deny" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Deny</button>
        <button type="submit" name="application_action" value="accept" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Accept</button>
      </form>
      <button onclick="closeApplication()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Close</button>
    </div>
  </div>
</div>

<script>
// preload PHP application data into JS
const applicationData = <?php echo json_encode($appDetails); ?>;

function viewApplication(appId) {
  const data = applicationData[appId];
  if (!data) {
    alert("Application not found!");
    return;
  }

  let html = `
    <p><strong>Applicant:</strong> ${data.full_name}</p>
    <p><strong>Address:</strong> ${data.address}</p>
    <p><strong>Animal:</strong> ${data.animal}</p>
    <p><strong>Date Applied:</strong> ${data.date_applied}</p>
    <h3 class="mt-4 font-bold">Screening</h3>
    <ul>
      <li><strong>Housing:</strong> ${data.housing}</li>
      <li><strong>Reason for Adoption:</strong> ${data.reason}</li>
      <li><strong>Previously Owned Pets:</strong> ${data.own_pets}</li>
      <li><strong>Financial Ready:</strong> ${data.financial_ready}</li>
    </ul>
  `;

  document.getElementById("applicationDetails").innerHTML = html;
  document.getElementById("modal_app_id").value = appId;
  document.getElementById("applicationModal").classList.remove("hidden");
}

function closeApplication() {
  document.getElementById("applicationModal").classList.add("hidden");
}

function acceptApplication(id){
  
}

function logout() {
  alert("Logging out...");
  window.location.href = "../login.php";
}
</script>
</body>
</html>



 
