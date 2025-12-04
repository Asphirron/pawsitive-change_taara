<?php
// admin_dynamic.php
include "../includes/db_connection.php"; //Establishes database connection
include "../Admin/admin_ui.php"; //Displays Navigation

session_start();

// -------------------- CONFIG --------------------
$tableName = 'event';   // Change this to your table
$pk = 'event_id';       // Primary key column of the table

$conn = connect();
$events = [];
$query = "SELECT event_id, title, description, img, location, event_date FROM event ORDER BY event_date ASC";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $dateKey = date("Y-m-d", strtotime($row['event_date']));
    $events[$dateKey] = [
        "id" => $row['event_id'],
        "title" => htmlspecialchars($row['title']),
        "desc" => htmlspecialchars($row['description']),
        "img" => $row['img'],
        "location" => htmlspecialchars($row['location']),
        "date" => date("F d, Y", strtotime($row['event_date']))
    ];
}
$conn->close();

$message = "";

// -------------------- HELPER --------------------
function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES); }

include "../includes/post_handler.php"; //Handles POST (search, CRUD, etc)
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
<style>
/* ===== Calendar ===== */
.calendar-container {
  max-width: 700px;
  margin: 0 auto;
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.subsection-header-text {
  font-size: 2rem;
  font-weight: 800;
  text-align: center;
  margin-top: 2rem;
  color: #e91e63;
  position: relative;
}
.subsection-header-text::after {
  content: "";
  display: block;
  width: 60px;
  height: 3px;
  background-color: #e91e63;
  margin: 0.5rem auto 0;
  border-radius: 2px;
}
/* ===== Modal ===== */
.modal { z-index: 50; }
</style>
</head>

<body>
<?= displayNav('events'); ?>

<main class="content flex-c">

    <div style='padding-inline:10px;'>
        <h2 class="subsection-header-text">Events Calendar</h2>

        <!-- Calendar -->
        <div id="calendarContainer" class="calendar-container mt-6"></div>
    </div>
</main>

<!-- 🪩 Modal -->
<div id="eventModal" class="modal hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
  <div class="bg-white rounded-xl p-6 w-[90%] max-w-md shadow-lg relative">
    <button id="closeModal" class="absolute top-2 right-4 text-gray-600 text-xl font-bold">&times;</button>
    <h2 id="modalTitle" class="text-2xl font-bold text-pink-600 mb-2"></h2>
    <p id="modalDate" class="text-gray-500 mb-3"></p>
    <p id="modalDesc" class="mb-3 text-gray-700"></p>
    <p id="modalLoc" class="font-semibold text-gray-800"></p>
  </div>
</div>

<?php if(!empty($message)): ?>
<script> showMessage("<?= e($message) ?>"); </script>
<?php endif; ?>

<script>
const events = <?php echo json_encode($events); ?>;
const calendarContainer = document.getElementById("calendarContainer");
let currentDate = new Date();

function renderCalendar(date) {
  const year = date.getFullYear();
  const month = date.getMonth();
  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);
  const startDay = firstDay.getDay();
  const daysInMonth = lastDay.getDate();

  let html = `
    <div class="flex justify-between items-center mb-4">
      <button onclick="changeMonth(-1)" class="bg-pink-600 text-white px-3 py-1 rounded-lg font-bold hover:bg-pink-700">&lt;</button>
      <h3 class="text-xl font-bold text-pink-600">${date.toLocaleString('default', { month: 'long' })} ${year}</h3>
      <button onclick="changeMonth(1)" class="bg-pink-600 text-white px-3 py-1 rounded-lg font-bold hover:bg-pink-700">&gt;</button>
    </div>
    <div class="grid grid-cols-7 text-center font-semibold mb-2">
      <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
    </div>
    <div class="grid grid-cols-7 gap-1 text-center">`;

  for (let i = 0; i < startDay; i++) html += `<div class="p-2"></div>`;

  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    const event = events[dateStr];
    if (event) {
      html += `
        <div class="relative border rounded-lg p-2 bg-pink-50 hover:bg-pink-100 cursor-pointer"
             onclick="showEventModal('${dateStr}')">
          <div class="font-bold text-pink-600 text-sm">${d}</div>
          <div class="text-xs truncate text-gray-700">${event.title}</div>
        </div>`;
    } else {
      html += `<div class="border rounded-lg p-2 text-gray-500">${d}</div>`;
    }
  }
  html += `</div>`;
  calendarContainer.innerHTML = html;
}

function changeMonth(delta) {
  currentDate.setMonth(currentDate.getMonth() + delta);
  renderCalendar(currentDate);
}

const modal = document.getElementById('eventModal');
const closeModalBtn = document.getElementById('closeModal');

function showEventModal(dateStr) {
  const e = events[dateStr];
  if (!e) return;
  document.getElementById('modalTitle').innerText = e.title;
  document.getElementById('modalDate').innerText = e.date;
  document.getElementById('modalDesc').innerText = e.desc;
  document.getElementById('modalLoc').innerText = "📍 " + e.location;
  modal.classList.remove('hidden');
}
closeModalBtn.addEventListener('click', () => modal.classList.add('hidden'));
renderCalendar(currentDate);
</script>
</body>
</html>
