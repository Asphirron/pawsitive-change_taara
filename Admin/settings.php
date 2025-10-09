<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAARA Admin - Settings</title>
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
          <li class="flex items-center gap-3 p-3 rounded-lg bg-gray-700 cursor-pointer" onclick="window.location.href='settings.php'">
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
    <h1 class="text-3xl font-bold mb-6">⚙️ Admin Settings</h1>

    <!-- Profile Settings -->
    <div class="bg-white shadow rounded-xl p-6 mb-8">
      <h2 class="text-xl font-semibold mb-4">Profile Information</h2>
      <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="text" placeholder="Full Name" value="Admin User" class="border rounded p-2">
        <input type="email" placeholder="Email Address" value="admin@taara.org" class="border rounded p-2">
        <input type="text" placeholder="Phone Number" value="09123456789" class="border rounded p-2">
        <input type="text" placeholder="Role" value="System Administrator" class="border rounded p-2" disabled>
        <div class="col-span-2 flex justify-end">
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save Changes</button>
        </div>
      </form>
    </div>

    <!-- Security Settings -->
    <div class="bg-white shadow rounded-xl p-6 mb-8">
      <h2 class="text-xl font-semibold mb-4">Account Security</h2>
      <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="password" placeholder="Current Password" class="border rounded p-2">
        <input type="password" placeholder="New Password" class="border rounded p-2">
        <input type="password" placeholder="Confirm New Password" class="border rounded p-2">
        <div class="col-span-2 flex justify-end">
          <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Update Password</button>
        </div>
      </form>
    </div>

    <!-- System Preferences -->
    <div class="bg-white shadow rounded-xl p-6">
      <h2 class="text-xl font-semibold mb-4">System Preferences</h2>
      <div class="flex items-center justify-between mb-4">
        <label class="font-medium">Enable Notifications</label>
        <input type="checkbox" checked class="w-5 h-5">
      </div>
      <div class="flex items-center justify-between mb-4">
        <label class="font-medium">Dark Mode</label>
        <input type="checkbox" class="w-5 h-5" onclick="toggleTheme()">
      </div>
      <div class="flex items-center justify-between">
        <label class="font-medium">Backup Data</label>
        <button class="bg-purple-500 text-white px-3 py-1 rounded hover:bg-purple-600">Backup Now</button>
      </div>
    </div>
  </main>
</div>

<script>
 function logout() {
  alert("Logging out...");
  window.location.href = "../login.php";
}
</script>

</body>
</html>
