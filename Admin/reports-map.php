<?php
// admin_dynamic.php
include "../includes/db_connection.php"; //Establishes database connection
include "../Admin/admin_ui.php"; //Displays Navigation

session_start();

$location = '13.3583,123.7332';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['geolocation'])) {
    $location = urldecode($_GET['geolocation']);
}


include "../includes/post_handler.php"; //Handles POST (search, CRUD, etc)

$rescue_table = new DatabaseCRUD('rescue_report');
$poi_table = new DatabaseCRUD('point_of_interest');
$rescue_reports = $rescue_table->read();
$pois = $poi_table->read();

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Interactive Map</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../CSS/admin_style.css">
<link rel="icon" type="image/png" href="../Assets/UI/Taara_Logo.webp">

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
</head>

<body>
<?= displayNav('reports'); ?>

<main class="content flex-c">

    <!-- Map -->
    <div class="flex-c center">
        <h2 >Rescue & Community Map</h2>
        <div id="map" style='height: 500px; border-radius: 10px; width: 80%;'></div>
        <p>
            Legend: 
            <span style="color: red">■ Rescue Report</span> 
            <span style="color: blue">■ Lost/Found Report</span> 
            <span style="color: green">■ Partner</span>
            <span style="color: purple">■ Shelter</span>
            <span style="color: yellow">■ HQ</span>
        </p>
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

// Call goToLocation immediately if PHP passed a location
<?php if (!empty($location)): ?>
  goToLocation("<?= $location ?>");
<?php endif; ?>

setTimeout(() => map.invalidateSize(), 300);

</script>
</main>
</body>
</html>
