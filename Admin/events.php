<?php
include '../includes/db_connection.php';

$event_table = new DatabaseCRUD('event');

// --- Add New Event ---
if (isset($_POST['add_event'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $event_date = $_POST['event_date'];
    $date_posted = date('Y-m-d');
    $img_path = null;

    if (!empty($_FILES['img']['name'])) {
        $uploadDir = 'Assets/Images/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = basename($_FILES['img']['name']);
        $targetPath = $uploadDir . $fileName;
        move_uploaded_file($_FILES['img']['tmp_name'], $targetPath);
        $img_path = $targetPath;
    }

    $event_table->create([
        'title' => $title,
        'description' => $description,
        'img' => $img_path,
        'location' => $location,
        'event_date' => $event_date,
        'date_posted' => $date_posted
    ]);
}

// --- Edit Event ---
if (isset($_POST['edit_event'])) {
    $event_id = $_POST['event_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $event_date = $_POST['event_date'];

    $img_path = $_POST['current_img'];
    if (!empty($_FILES['img']['name'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time() . '_' . basename($_FILES['img']['name']);
        $targetPath = $uploadDir . $fileName;
        move_uploaded_file($_FILES['img']['tmp_name'], $targetPath);
        $img_path = $targetPath;
    }

    $event_table->update($event_id, [
        'title' => $title,
        'description' => $description,
        'img' => $img_path,
        'location' => $location,
        'event_date' => $event_date
    ]);
}

// --- Delete Event ---
if (isset($_POST['delete_event'])) {
    $event_table->delete($_POST['event_id']);
}

// Fetch all events
$events = $event_table->readAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAARA Admin - Events</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

  <div class="flex">

      <!-- Navigation -->
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
          <li class="flex items-center gap-3 p-3 rounded-lg bg-gray-700 cursor-pointer" onclick="window.location.href='events.php'">
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
    <main class="flex-1 p-8">
      <h1 class="text-3xl font-bold mb-6">📅 Events Management</h1>

      <!-- Event List Table -->
      <div class="bg-white shadow rounded-xl p-6">
        <h2 class="text-xl font-semibold mb-4">All Events</h2>
        <table class="w-full border rounded-lg overflow-hidden">
          <thead class="bg-gray-200">
            <tr>
              <th class="p-3 text-left">Image</th>
              <th class="p-3 text-left">Title</th>
              <th class="p-3 text-left">Date</th>
              <th class="p-3 text-left">Location</th>
              <th class="p-3 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($events as $event): ?>
              <tr class="border-b hover:bg-gray-50">
                <td class="p-3">
                  <?php if (!empty($event['img'])): ?>
                    <img src="<?= htmlspecialchars($event['img']) ?>" class="w-20 h-20 object-cover rounded">
                  <?php else: ?>
                    <span class="text-gray-400 italic">No image</span>
                  <?php endif; ?>
                </td>
                <td class="p-3"><?= htmlspecialchars($event['title']) ?></td>
                <td class="p-3"><?= date('M d, Y', strtotime($event['event_date'])) ?></td>
                <td class="p-3"><?= htmlspecialchars($event['location']) ?></td>
                <td class="p-3 flex gap-2">
                  <button onclick='openEditModal(<?= json_encode($event) ?>)' class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Edit</button>
                  <form method="POST" onsubmit="return confirm('Delete this event?')" style="display:inline;">
                    <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                    <button type="submit" name="delete_event" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Add New Event Form -->
      <div class="bg-white shadow rounded-xl p-6 mt-6">
        <h2 class="text-xl font-semibold mb-4">Add New Event</h2>
        <form method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-gray-700 mb-1">Event Title</label>
            <input type="text" name="title" class="border rounded p-2 w-full" required>
          </div>
          <div>
            <label class="block text-gray-700 mb-1">Date</label>
            <input type="date" name="event_date" class="border rounded p-2 w-full" required>
          </div>
          <div>
            <label class="block text-gray-700 mb-1">Location</label>
            <input type="text" name="location" class="border rounded p-2 w-full" required>
          </div>
          <div>
            <label class="block text-gray-700 mb-1">Image</label>
            <input type="file" name="img" accept="image/*" class="border rounded p-2 w-full">
          </div>
          <div class="col-span-2">
            <label class="block text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="4" class="border rounded p-2 w-full"></textarea>
          </div>
          <div class="col-span-2 flex justify-end">
            <button type="submit" name="add_event" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Event</button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50" onclick="closeOnOutsideClick(event)">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/2 max-h-[90vh] overflow-y-auto relative" onclick="event.stopPropagation()">
      <span class="absolute top-2 right-3 text-gray-600 cursor-pointer text-2xl" onclick="closeModal()">&times;</span>
      <h2 class="text-xl font-semibold mb-4">Edit Event</h2>
      <form method="POST" enctype="multipart/form-data" id="editForm" class="grid grid-cols-2 gap-4">
        <input type="hidden" name="event_id" id="edit_event_id">
        <input type="hidden" name="current_img" id="current_img">
        <div>
          <label class="block mb-1 text-gray-700">Title</label>
          <input type="text" name="title" id="edit_title" class="border rounded p-2 w-full">
        </div>
        <div>
          <label class="block mb-1 text-gray-700">Date</label>
          <input type="date" name="event_date" id="edit_date" class="border rounded p-2 w-full">
        </div>
        <div>
          <label class="block mb-1 text-gray-700">Location</label>
          <input type="text" name="location" id="edit_location" class="border rounded p-2 w-full">
        </div>
        <div>
          <label class="block mb-1 text-gray-700">Image</label>
          <input type="file" name="img" class="border rounded p-2 w-full">
        </div>
        <div class="col-span-2">
          <label class="block mb-1 text-gray-700">Description</label>
          <textarea name="description" id="edit_description" rows="4" class="border rounded p-2 w-full"></textarea>
        </div>
        <div class="col-span-2 flex justify-end">
          <button type="submit" name="edit_event" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openEditModal(event) {
      document.getElementById('editModal').classList.remove('hidden');
      document.getElementById('edit_event_id').value = event.event_id;
      document.getElementById('edit_title').value = event.title;
      document.getElementById('edit_date').value = event.event_date;
      document.getElementById('edit_location').value = event.location;
      document.getElementById('edit_description').value = event.description;
      document.getElementById('current_img').value = event.img || '';
    }

    function closeModal() {
      document.getElementById('editModal').classList.add('hidden');
    }

    function closeOnOutsideClick(e) {
      if (e.target.id === 'editModal') {
        closeModal();
      }
    }

    function logout() {
  alert("Logging out...");
  window.location.href = "../login.php";
}
  </script>

</body>
</html>
