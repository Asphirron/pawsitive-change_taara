<?php
include '../includes/db_connection.php';
session_start();

$rescue_table = new DatabaseCRUD('rescue_report');
$poi_table = new DatabaseCRUD('point_of_interest');

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action === 'resolve') {
    $rescue_table->update($id, ['status' => 'resolved'], 'report_id');
    } elseif ($action === 'cancel') {
        $rescue_table->update($id, ['status' => 'cancelled'], 'report_id');
    }


    header("Location: reports.php");
    exit;
}

// Fetch rescue reports and POIs
$rescue_reports = $rescue_table->read();
$pois = $poi_table->read();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAARA Admin - Rescue Reports</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <style>
    #map { height: 400px; border-radius: 10px; }
  </style>
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
          <li class="flex items-center gap-3 p-3 rounded-lg bg-gray-700 cursor-pointer" onclick="window.location.href='reports.php'">
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
  <main class="flex-1 p-8">
    <h1 class="text-3xl font-bold mb-6">🚨 Rescue Reports</h1>

    <!-- Rescue Reports Table -->
    <div class="bg-white shadow rounded-xl p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4">Reported Cases</h2>
      <table class="w-full border rounded-lg overflow-hidden">
        <thead class="bg-gray-200">
          <tr>
            <th class="p-3 text-left">Reporter</th>
            <th class="p-3 text-left">Description</th>
            <th class="p-3 text-left">Documentation</th>
            <th class="p-3 text-left">Date Posted</th>
            <th class="p-3 text-left">Status</th>
            <th class="p-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rescue_reports as $report): ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="p-3"><?= htmlspecialchars($report['full_name']) ?></td>
              <td class="p-3"><?= htmlspecialchars($report['description']) ?></td>
              <td class="p-3"><img src="<?= htmlspecialchars($report['img']) ?>" alt="Proof" class="w-20 h-20 object-cover rounded"></td>
              <td class="p-3"><?= htmlspecialchars($report['date_posted']) ?></td>
              <td class="p-3 font-semibold 
                <?= $report['status'] === 'Pending' ? 'text-yellow-600' : 
                   ($report['status'] === 'Resolved' ? 'text-green-600' : 'text-red-600') ?>">
                <?= htmlspecialchars($report['status']) ?>
              <td class="p-3 flex flex-col gap-2 w-48">
              <button 
                onclick="goToLocation('<?= $report['location'] ?>')" 
                class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 w-full text-center">
                Go to Location
              </button>

              <?php if (strtolower($report['status']) === 'pending'): ?>
              <form method="POST" action="reports.php" class="w-full">
                <input type="hidden" name="id" value="<?= $report['report_id'] ?>">
                <button 
                  name="action" 
                  value="resolve" 
                  class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600 w-full text-center">
                  Set as Resolved
                </button>
              </form>

              <form method="POST" action="reports.php" class="w-full">
                <input type="hidden" name="id" value="<?= $report['report_id'] ?>">
                <button 
                  name="action" 
                  value="cancel" 
                  class="bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600 w-full text-center">
                  Cancel
                </button>
              </form>
              <?php endif; ?>
            </td>

            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>


    <!-- Map -->
    <div class="bg-white shadow rounded-xl p-6">
      <h2 class="text-xl font-semibold mb-4">Rescue & Community Map</h2>
      <div id="map"></div>
      <p class="mt-4 text-sm text-gray-600">
        Legend: 
        <span class="text-red-600 font-bold">■ Rescue Report</span> 
        <span class="text-blue-600 font-bold">■ Lost/Found Report</span> 
        <span class="text-green-600 font-bold">■ Partner</span>
        <span class="text-purple-600 font-bold">■ Shelter</span>
        <span class="text-yellow-600 font-bold">■ HQ</span>
      </p>
    </div>

    <!-- Points of Interest Table -->
    <h1 class="text-2xl font-bold mb-4">Points of Interest</h1>
    <div class="overflow-x-auto bg-white shadow-md rounded-lg mb-6">
      <table class="min-w-full table-auto text-sm text-gray-700">
        <thead class="bg-gray-200 text-gray-700 uppercase text-xs">
          <tr>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Description</th>
            <th class="px-4 py-2">Coordinates</th>
            <th class="px-4 py-2">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pois as $p): ?>
            <?php 
              $coords = explode(',', $p['location']);
              $lat = isset($coords[0]) ? trim($coords[0]) : '';
              $lng = isset($coords[1]) ? trim($coords[1]) : '';
            ?>
            <tr class="border-b hover:bg-gray-100">
              <td class="px-4 py-2 capitalize"><?= htmlspecialchars($p['type']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($p['description']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($lat) ?>, <?= htmlspecialchars($lng) ?></td>
              <td class="px-4 py-2">
                <?php if ($lat && $lng): ?>
                <button onclick="focusOnLocation(<?= $lat ?>, <?= $lng ?>)" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Go to Location</button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    
  </main>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
const reports = <?= json_encode($rescue_reports) ?>;
const pois = <?= json_encode($pois) ?>;

// Find HQ for map center
let hq = pois.find(p => p.type === 'hq');
let mapCenter = [13.3583, 123.7332]; // default
if (hq && hq.location) {
  const coords = hq.location.split(',').map(s => parseFloat(s.trim()));
  if (coords.length === 2 && !isNaN(coords[0]) && !isNaN(coords[1])) {
    mapCenter = coords;
  }
}

// Initialize map
let map = L.map('map').setView(mapCenter, 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);

const iconBase = 'https://maps.google.com/mapfiles/ms/icons/';
const icons = {
  rescue: iconBase + 'red-dot.png',
  lostfound: iconBase + 'blue-dot.png',
  partner: iconBase + 'green-dot.png',
  shelter: iconBase + 'purple-dot.png',
  hq: iconBase + 'yellow-dot.png'
};

// Add Rescue Report Markers
reports.forEach(r => {
  if (r.status !== 'pending') return; // skip resolved/cancelled
  if (!r.location) return;

  const loc = r.location.split(',').map(s => parseFloat(s.trim()));
  if (loc.length === 2 && !isNaN(loc[0]) && !isNaN(loc[1])) {
    const color = r.type === 'rescue report' ? icons.rescue : icons.lostfound;
    L.marker(loc, { icon: L.icon({ iconUrl: color }) }).addTo(map)
      .bindPopup(`<b>${r.type.toUpperCase()}</b><br>${r.description}`);
  }
});

// Add POI Markers
pois.forEach(p => {
  if (!p.location) return;
  const loc = p.location.split(',').map(s => parseFloat(s.trim()));
  if (loc.length === 2 && !isNaN(loc[0]) && !isNaN(loc[1])) {
    const iconUrl = icons[p.type] || icons.partner;
    L.marker(loc, { icon: L.icon({ iconUrl }) }).addTo(map)
      .bindPopup(`<b>${p.type.toUpperCase()}</b><br>${p.description}`);
  }
});

// Go to location from report
function goToLocation(location) {
  if (!location) return alert("Invalid location.");
  const loc = location.split(',').map(s => parseFloat(s.trim()));
  if (loc.length === 2 && !isNaN(loc[0]) && !isNaN(loc[1])) {
    map.setView(loc, 16);
    L.popup().setLatLng(loc).setContent("Target Location").openOn(map);
  } else {
    alert("Invalid location format.");
  }
}

// Go to location from POI table
function focusOnLocation(lat, lng) {
  if (!isNaN(lat) && !isNaN(lng)) {
    map.setView([lat, lng], 16);
    L.popup().setLatLng([lat, lng]).setContent("Target Location").openOn(map);
  }
}

function logout() {
  alert("Logging out...");
  window.location.href = "../login.php";
}
</script>
</body>
</html>
