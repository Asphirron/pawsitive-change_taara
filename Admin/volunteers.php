<?php
include '../includes/db_connection.php';
session_start();

$applicationDB = new DatabaseCRUD("volunteer_application");
$volunteerDB   = new DatabaseCRUD("volunteer");

// Handle Approve / Reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['v_application_id'])) {
        $id = intval($_POST['v_application_id']);
        $action = $_POST['action'];

        if ($action === 'approve') {
            $applicationDB->update($id, ['status' => 'approved'], 'v_application_id');

            // Fetch application details to insert into volunteers
            $app = $applicationDB->read($id, 'v_application_id');
            if ($app) {
                $volunteerDB->create([
                    'full_name' => $app['full_name'],
                    'role' => 'None',
                    'user_id' => $app['user_id']
                ]);
            }
        } elseif ($action === 'reject') {
            $applicationDB->update($id, ['status' => 'rejected'], 'v_application_id');
            
        } elseif ($action === 'set_role' && isset($_POST['volunteer_id']) && isset($_POST['role'])) {
            $volunteerDB->update($_POST['volunteer_id'], ['role' => $_POST['role']], 'volunteer_id');
            alert($_POST['role']);
        }
    }
}

$applications = $applicationDB->readAll();
$volunteers   = $volunteerDB->readAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAARA Admin - Volunteers</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

<div class="flex">
  <!-- Sidebar -->
  <aside class="w-64 bg-[#0b1d3a] text-white min-h-screen p-6">
    <div class="flex flex-col items-center mb-10">
      <img src="logo.png" alt="Logo" class="w-20 h-20 mb-4">
      <h1 class="text-lg font-bold">T.A.A.R.A</h1>
    </div>

    <nav>
      <ul class="space-y-4">
        <li onclick="window.location.href='index.php'" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer"><span class="material-icons">dashboard</span> Dashboard</li>
        <li onclick="window.location.href='adoptionrequest.php'" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer"><span class="material-icons">pets</span> Adoption Requests</li>
        <li onclick="window.location.href='animalprofile.php'" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer"><span class="material-icons">favorite</span> Animal Profiles</li>
        <li class="flex items-center gap-3 p-3 rounded-lg bg-gray-700 cursor-pointer"><span class="material-icons">groups</span> Volunteers</li>
        <li onclick="window.location.href='donation.php'" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer"><span class="material-icons">volunteer_activism</span> Donations</li>
        <li onclick="window.location.href='events.php'" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer"><span class="material-icons">event</span> Events</li>
        <li onclick="window.location.href='reports.php'" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer"><span class="material-icons">report</span> Rescue Reports</li>
        <li onclick="window.location.href='settings.php'" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer"><span class="material-icons">settings</span> Settings</li>
        <li onclick="logout()" class="flex items-center gap-3 p-3 rounded-lg hover:bg-red-600 cursor-pointer mt-10"><span class="material-icons">logout</span> Logout</li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-8 space-y-8">
    <h1 class="text-3xl font-bold mb-6">👥 Volunteer Management</h1>

    <!-- Volunteer Applications Table -->
    <div class="bg-white shadow rounded-xl p-6">
      <h2 class="text-xl font-semibold mb-4">Volunteer Applications</h2>
      <table class="w-full border rounded-lg overflow-hidden">
        <thead class="bg-gray-200">
          <tr>
            <th class="p-3 text-left">ID</th>
            <th class="p-3 text-left">Full Name</th>
            <th class="p-3 text-left">Classification</th>
            <th class="p-3 text-left">Age</th>
            <th class="p-3 text-left">Status</th>
            <th class="p-3 text-left">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($applications as $app): ?>
          <tr class="border-b hover:bg-gray-50">
            <td class="p-3"><?= $app['v_application_id'] ?></td>
            <td class="p-3"><?= htmlspecialchars($app['full_name']) ?></td>
            <td class="p-3"><?= htmlspecialchars($app['classification']) ?></td>
            <td class="p-3"><?= htmlspecialchars($app['age']) ?></td>
            <td class="p-3 font-semibold <?= $app['status']=='approved' ? 'text-green-600' : ($app['status']=='rejected'?'text-red-600':'text-yellow-600') ?>"><?= ucfirst($app['status']) ?></td>
            <td class="p-3">
              <button 
                onclick="openAppModal(<?= htmlspecialchars(json_encode($app)) ?>)" 
                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 w-full">View More</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Volunteers Table -->
    <div class="bg-white shadow rounded-xl p-6">
      <h2 class="text-xl font-semibold mb-4">Volunteers</h2>
      <table class="w-full border rounded-lg overflow-hidden">
        <thead class="bg-gray-200">
          <tr>
            <th class="p-3 text-left">ID</th>
            <th class="p-3 text-left">Full Name</th>
            <th class="p-3 text-left">Role</th>
            <th class="p-3 text-left">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($volunteers as $vol): ?>
          <tr class="border-b hover:bg-gray-50">
            <td class="p-3"><?= $vol['volunteer_id'] ?></td>
            <td class="p-3"><?= htmlspecialchars($vol['full_name']) ?></td>
            <td class="p-3"><?= htmlspecialchars($vol['role']) ?></td>
            <td class="p-3">
              <button 
                onclick="openRoleModal(<?= $vol['volunteer_id'] ?>, '<?= htmlspecialchars($vol['full_name']) ?>')" 
                class="bg-indigo-500 text-white px-3 py-1 rounded hover:bg-indigo-600 w-full">Set Role</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<!-- Application Modal -->
<div id="appModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center">
  <div class="bg-white rounded-lg p-6 w-1/2">
    <h3 class="text-xl font-semibold mb-3">Volunteer Application Details</h3>
    <div id="appDetails" class="space-y-2 text-gray-700"></div>
    <form method="POST" class="flex justify-end gap-2 mt-6">
      <input type="hidden" name="v_application_id" id="appIdField">
      <button name="action" value="approve" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Approve</button>
      <button name="action" value="reject" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Reject</button>
      <button type="button" onclick="closeModal('appModal')" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Close</button>
    </form>
  </div>
</div>

<!-- Role Modal -->
<div id="roleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center">
  <div class="bg-white rounded-lg p-6 w-1/3">
    <h3 class="text-xl font-semibold mb-3">Set Volunteer Role</h3>
    <form method="POST" class="space-y-3">
      <input type="hidden" name="action" value="set_role">
      <input type="hidden" name="volunteer_id" id="volIdField">
      <p id="volName" class="font-semibold"></p>
      <select name="role" class="border p-2 rounded w-full" require >
        <option value="">Select Role</option>
        <option value="rescue volunteer">Rescue Volunteer</option>
        <option value="event volunteer">Event Volunteer</option>
        <option value="adoption volunteer">Adoption Volunteer</option>
      </select>
      <div class="flex justify-end gap-2">
        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save</button>
        <button type="button" onclick="closeModal('roleModal')" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Close</button>
      </div>
    </form>
  </div>
</div>

<script>
function logout() {
  alert("Logging out...");
  window.location.href = "../login.php";
}

function openAppModal(data) {
  const modal = document.getElementById('appModal');
  const details = document.getElementById('appDetails');
  const idField = document.getElementById('appIdField');
  idField.value = data.v_application_id;

  details.innerHTML = `
    <p><strong>Full Name:</strong> ${data.full_name}</p>
    <p><strong>Classification:</strong> ${data.classification}</p>
    <p><strong>First Committee:</strong> ${data.first_committee}</p>
    <p><strong>Second Committee:</strong> ${data.second_committee}</p>
    <p><strong>Contact:</strong> ${data.contact_num}</p>
    <p><strong>Address:</strong> ${data.address}</p>
    <p><strong>Reason for Joining:</strong> ${data.reason_for_joining}</p>
    <p><strong>Status:</strong> ${data.status}</p>
  `;

  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function openRoleModal(id, name) {
  document.getElementById('roleModal').classList.remove('hidden');
  document.getElementById('roleModal').classList.add('flex');
  document.getElementById('volIdField').value = id;
  document.getElementById('volName').innerText = "Assign role for: " + name;
}

function closeModal(id) {
  document.getElementById(id).classList.add('hidden');
  document.getElementById(id).classList.remove('flex');
}
</script>
</body>
</html>
