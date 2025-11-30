<?php
include '../includes/db_connection.php';
session_start();

// Initialize CRUD handlers
$donationPostTable = new DatabaseCRUD('donation_post');
$monetaryTable = new DatabaseCRUD('monetary_donation');
$inkindTable = new DatabaseCRUD('inkind_donation');
$inventoryTable = new DatabaseCRUD('donation_inventory');

// --- Handle POST Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'approve_monetary') {
        $id = $_POST['id'];
        $donation = $monetaryTable->read($id, 'm_donation_id');
        $post = $donationPostTable->read($donation['dpost_id'], 'dpost_id');

        // Update status
        $monetaryTable->update($id, ['status' => 'verified'], 'm_donation_id');

        // Update donation_post current amount
        $newAmount = $post['current_amount'] + $donation['amount'];
        $donationPostTable->update($donation['dpost_id'], ['current_amount' => $newAmount], 'dpost_id');
    }

    if ($action === 'reject_monetary') {
        $id = $_POST['id'];
        $monetaryTable->update($id, ['status' => 'rejected'], 'm_donation_id');
    }

    if ($action === 'arrived_inkind') {
        $id = $_POST['id'];
        $donation = $inkindTable->read($id, 'i_donation_id');

        // Move to inventory
        $inventoryTable->create([
            'item_type' => $donation['donation_type'],
            'quantity' => 1,
            'date_stored' => date('Y-m-d'),
            'item_img' => $donation['img'],
            'donater_name' => $donation['full_name']
        ]);

        // Remove from inkind
        $inkindTable->delete($id, 'i_donation_id');
    }

    if ($action === 'cancel_inkind') {
        $id = $_POST['id'];
        $inkindTable->delete($id, 'i_donation_id');
    }

    header("Location: donation.php");
    exit;
}

// --- Fetch Data ---
$donationPosts = array_reverse($donationPostTable->readAll());
$monetaryDonations = array_reverse(array_filter($monetaryTable->readAll(), fn($row) => $row['status'] !== 'rejected'));
$inkindDonations = array_reverse($inkindTable->readAll());
$donationInventory = array_reverse($inventoryTable->readAll());

// --- Helper ---
function daysRemaining($deadline) {
    $today = new DateTime();
    $end = new DateTime($deadline);
    $diff = $today->diff($end);
    return $diff->invert ? "Expired" : $diff->days . " days left";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAARA Admin Dashboard - Donations</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
<div class="flex">
  <!-- Sidebar -->
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
          <li class="flex items-center gap-3 p-3 rounded-lg bg-gray-700 cursor-pointer" onclick="window.location.href='donation.php'">
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
    <main class="flex-1 p-8 space-y-6">
  <h1 class="text-3xl font-bold mb-6">💝 Donations Overview</h1>

    <!-- Top Donors -->
  <div class="bg-white rounded-xl shadow overflow-hidden">
    <button onclick="toggleSection('topDonors')" class="w-full flex justify-between items-center p-6 text-left text-2xl font-bold hover:bg-gray-100">
      📢 TOP DONORS
      <span id="icon-topDonors" class="material-icons transform transition-transform">expand_more</span>
    </button>
    <div id="section-topDonors" class="p-6 border-t">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200 text-left">
            <th class="p-3">Donor Name</th>
            <th class="p-3">Total Donation Amount</th>
            <th class="p-3">Ranking</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Example Month/Year
          $year = 2025;
          $month = 11; 

          // Database Query
          $sql = "
              SELECT full_name, SUM(amount) AS total_donated
              FROM monetary_donation
              WHERE YEAR(date_donated) = ? 
                AND MONTH(date_donated) = ?
              GROUP BY full_name
              ORDER BY total_donated DESC
              LIMIT 5
          ";

          /*$stmt = $pdo->prepare($sql);
          $stmt->execute([$year, $month]);
          $topDonors = $stmt->fetchAll(PDO::FETCH_ASSOC);*/

          $conn = connect();
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("ii", $year, $month);
          $stmt->execute();
          $result = $stmt->get_result();
          $topDonors = $result;
          $stmt->close();

          if (empty($topDonors)) {
            echo "<tr><td colspan='3' style='text-align:center;'>No donations found for this month.</td></tr>";

          } else {
              $rank = 1;
              while ($donor = $topDonors->fetch_assoc()) {
                  echo "<tr class='border-b'>";
                  echo "<td class='p-3 font-medium'>" . $donor['full_name'] . "</td>";
                  echo "<td class='p-3'>₱" . number_format($donor['total_donated'], 2) . "</td>";
                  echo "<td class='p-3 '>" . $rank++ . "</td>";
                  echo "</tr>";
              }
          }
          $conn->close();
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Donation Allocations -->
  <div class="bg-white rounded-xl shadow overflow-hidden">
    <button onclick="toggleSection('donationPosts')" class="w-full flex justify-between items-center p-6 text-left text-2xl font-bold hover:bg-gray-100">
      📢 Monthly Donation Allocations
      <span id="icon-donationPosts" class="material-icons transform transition-transform">expand_more</span>
    </button>
    <div id="section-donationPosts" class="p-6 border-t hidden">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200 text-left">
            <th class="p-3">Allocation Name</th>
            <th class="p-3">Donation Milestone</th>
            <th class="p-3">Days Remaining</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($donationPosts as $post): ?>
          <tr class="border-b">
            <td class="p-3 font-medium"><?= htmlspecialchars($post['title']) ?></td>
            <td class="p-3">₱<?= number_format($post['current_amount'], 2) ?> / ₱<?= number_format($post['goal_amount'], 2) ?></td>
            <td class="p-3 text-gray-600"><?= daysRemaining($post['deadline']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Monetary Donations -->
  <div class="bg-white rounded-xl shadow overflow-hidden">
    <button onclick="toggleSection('monetaryDonations')" class="w-full flex justify-between items-center p-6 text-left text-2xl font-bold hover:bg-gray-100">
      💰 Monetary Donations
      <span id="icon-monetaryDonations" class="material-icons transform transition-transform">expand_more</span>
    </button>
    <div id="section-monetaryDonations" class="p-6 border-t hidden">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200 text-left">
            <th class="p-3">Donor</th>
            <th class="p-3">Allocation</th>
            <th class="p-3">Amount</th>
            <th class="p-3">Payment Option</th>
            <th class="p-3">Status</th>
            <th class="p-3">Date Donated</th>
            <th class="p-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($monetaryDonations as $d): ?>
          <tr class="border-b">
            <td class="p-3"><?= htmlspecialchars($d['full_name']) ?></td>
            <td class="p-3"><?= htmlspecialchars($donationPostTable->read($d['dpost_id'], 'dpost_id')['title'] ?? 'N/A') ?></td>
            <td class="p-3">₱<?= number_format($d['amount'], 2) ?></td>
            <td class="p-3"><?= htmlspecialchars($d['payment_option']) ?></td>
            <td class="p-3"><?= htmlspecialchars($d['status']) ?></td>
            <td class="p-3"><?= htmlspecialchars($d['date_donated']) ?></td>
            <td class="p-3 flex gap-2">
              <form method="POST">
                <input type="hidden" name="id" value="<?= $d['m_donation_id'] ?>">
                <button name="action" value="approve_monetary" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Verify</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- In-Kind Donations -->
  <div class="bg-white rounded-xl shadow overflow-hidden">
    <button onclick="toggleSection('inkindDonations')" class="w-full flex justify-between items-center p-6 text-left text-2xl font-bold hover:bg-gray-100">
      📦 In-Kind Donations
      <span id="icon-inkindDonations" class="material-icons transform transition-transform">expand_more</span>
    </button>
    <div id="section-inkindDonations" class="p-6 border-t hidden">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200 text-left">
            <th class="p-3">Image</th>
            <th class="p-3">Donor</th>
            <th class="p-3">Type</th>
            <th class="p-3">Drop-off Location</th>
            <th class="p-3">Date Donated</th>
            <th class="p-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($inkindDonations as $i): ?>
          <tr class="border-b">
            <td class="p-3"><img src='<?= htmlspecialchars($i['img']) ?>' class="w-10 h-10 rounded"></td>
            <td class="p-3"><?= htmlspecialchars($i['full_name']) ?></td>
            <td class="p-3"><?= htmlspecialchars($i['donation_type']) ?></td>
            <td class="p-3"><?= htmlspecialchars($i['location']) ?></td>
            <td class="p-3"><?= htmlspecialchars($i['date']) ?></td>
            <td class="p-3 flex gap-2">
              <form method="POST">
                <input type="hidden" name="id" value="<?= $i['i_donation_id'] ?>">
                <button name="action" value="arrived_inkind" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Set as Received</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Donation Inventory -->
  <div class="bg-white rounded-xl shadow overflow-hidden">
    <button onclick="toggleSection('donationInventory')" class="w-full flex justify-between items-center p-6 text-left text-2xl font-bold hover:bg-gray-100">
      🏷 Donation Inventory
      <span id="icon-donationInventory" class="material-icons transform transition-transform">expand_more</span>
    </button>
    <div id="section-donationInventory" class="p-6 border-t hidden">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200 text-left">
            <th class="p-3">Image</th>
            <th class="p-3">Item Type</th>
            <th class="p-3">Quantity</th>
            <th class="p-3">Date Received</th>
            <th class="p-3">Donor</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($donationInventory as $inv): ?>
          <tr class="border-b">
            <td class="p-3"><img src='<?= htmlspecialchars($inv['img']) ?>' class="w-10 h-10 rounded"></td>
            <td class="p-3"><?= htmlspecialchars($inv['item_type']) ?></td>
            <td class="p-3"><?= htmlspecialchars($inv['quantity']) ?></td>
            <td class="p-3"><?= htmlspecialchars($inv['date_stored']) ?></td>
            <td class="p-3"><?= htmlspecialchars($inv['donater_name']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- VERIFY DONATION MODAL -->
<div id="donationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-2xl relative shadow-lg">
    <h2 id="modalTitle" class="text-2xl font-bold mb-4">Add Animal</h2>
    <form id="animalForm" enctype="multipart/form-data">
      <input type="hidden" id="animal_id" name="animal_id">


      <div class="mt-4 flex items-center gap-4">
        <div>
          <label class="block text-gray-700">Proof of Payment</label> </div>
        <img id="previewImg" src="" alt="Preview" class="w-24 h-24 object-cover rounded-lg border hidden">
      </div>

      <div class="flex justify-end gap-3 mt-6">
        <button type="button" onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">Cancel</button>
        <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600">Verify</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleSection(id) {
  const section = document.getElementById(`section-${id}`);
  const icon = document.getElementById(`icon-${id}`);
  section.classList.toggle('hidden');
  icon.classList.toggle('rotate-180');
}

function logout() {
  alert("Logging out...");
  window.location.href = "../login.php";
}
</script>
</div>

</body>
</html>
