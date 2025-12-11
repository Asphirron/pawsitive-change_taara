<?php
include "includes/db_connection.php";
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
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="CSS/essentials.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Events - TAARA</title>

<style>
.site-footer {
  width: auto;
  height: auto;
  color: var(--color-text-primary);
  background-color: var(--color-fg);
  box-shadow: 3px 3px 3px 3px rgba(0, 0, 0, 0.275);
  padding-block: 20px;
}
.footer-content {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  gap: 10px;
}
.footer-content img {
  height: 20px;
  width: 20px;
}

/* ===== Event Cards ===== */
.card-container {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin-top: 1rem;
  justify-content: center;
}
.event-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  overflow: hidden;
  flex: 0 0 100%;
  display: flex;
  flex-direction: column;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.event-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 18px rgba(0,0,0,0.15);
}
.event-card img {
  width: 100%;
  height: 350px;
  object-fit: cover;
  display: block;
}
.card-details {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: .5rem;
  min-height: 260px;
}
.event-date { color: #777; font-size: 0.9rem; }
.card-title { font-size: 1.35rem; font-weight: 700; color: #222; }
.card-text { font-size: 0.95rem; color: #555; line-height: 1.5; flex-grow: 1; }
.event-info { font-size: 0.9rem; color: #444; }
.event-info strong { color: #e91e63; }
.event-card button {
  background-color: #e91e63;
  color: white;
  border: none;
  padding: 0.7rem;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  width: 100%;
  transition: background-color 0.25s ease;
}
.event-card button:hover { background-color: #c2185b; }

/* ===== Carousel ===== */
.carousel-wrapper {
  position: relative;
  width: 100%;
  max-width: 750px;
  margin: 2.5rem auto;
  overflow: hidden;
  border-radius: 12px;
}
.carousel-track { display: flex; transition: transform 0.6s ease-in-out; width: 100%; }
.carousel-track .event-card { flex: 0 0 100%; margin: 0; }
.carousel-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background-color: rgba(255,255,255,0.95);
  border: 2px solid #e91e63;
  color: #e91e63;
  border-radius: 50%;
  width: 42px;
  height: 42px;
  cursor: pointer;
  font-size: 1.25rem;
  font-weight: 700;
  transition: 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  z-index: 10;
}
.carousel-btn:hover { background-color: #e91e63; color: #fff; }
.carousel-btn.prev { left: 12px; }
.carousel-btn.next { right: 12px; }

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

/* ===== Calendar ===== */
.calendar-container {
  max-width: 700px;
  margin: 0 auto;
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* ===== Modal ===== */
.modal { z-index: 50; }

@media (max-width: 768px) {
  .carousel-wrapper { max-width: 95%; }
  .event-card img { height: 250px; }
  .card-title { font-size: 1.1rem; }
  .subsection-header-text { font-size: 1.6rem; }
}
</style>
</head>

<body>
<header>
  <img src="Assets/UI/taaralogo.jpg" alt="TAARA Logo">
  <div class="nav-container">
    <nav>
      <ul>
        <li><a href="rescue.php">Rescue</a></li>
        <li><a href="adoption.php">Adopt</a></li>
        <li><a href="donation.php">Donation</a></li>
        <li><a href="volunteer.php">Volunteer</a></li>
        <li><a class='active' href="events.php">Events</a></li>
        <li><a href="index.php">About</a></li>
      </ul>
    </nav>

    <?php
    if ($logged_in) {
      echo "<img src='Assets/Profile_Images/$user_img' class='profile-img' id='user_profile'>";
    } else {
      echo "<a href='register.php' class='bg-pink-600 text-white px-4 py-2 rounded-full font-bold hover:bg-pink-700 flex items-center gap-2'>
              <i class='fa-solid fa-user-plus'></i> Register
            </a>";
    }
    ?>
  </div>
</header>

<div class="content-area">
  <article class="feature-section">
    <div class="featured-content">
      <div class="collapsible-header">
        <h4 class="subsection-header-text">Events Calendar</h4>
      </div>

      <?php
      $conn = connect();
      $events = [];
      $now = date('Y-m-d H:i:s');
      $query = "SELECT event_id, title, description, img, location, event_date FROM event WHERE event_date >= $now ORDER BY event_date ASC";
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
      ?>

      <div id="calendarContainer" class="calendar-container mt-6"></div>

      <div class="collapsible-header mt-10">
        <h4 class="subsection-header-text">Upcoming Events</h4>
      </div>

      <div class="carousel-wrapper">
        <button class="carousel-btn prev" id="prevBtn">&#10094;</button>
        <div class="carousel-track" id="eventCarousel">
          <?php
          if (!empty($events)) {
            foreach ($events as $e) {
              if($e['date'] >= date('Y-m-d')){
                echo "
                      <div class='event-card'>
                        <img src='../Assets/UserGenerated/{$e['img']}' alt='{$e['title']}'>
                        <div class='card-details'>
                            <small class='event-date'>{$e['date']}</small>
                            <h4 class='card-title'>{$e['title']}</h4>
                            <p class='card-text'>{$e['desc']}</p>
                            <p class='event-info'><strong>Location:</strong> {$e['location']}</p>
                            <!--button id='notify-btn{$e['id']}' onclick='notify({$e['id']})'>Notify Me</button-->
                        </div>
                      </div>";
              }else{
                echo "<p>No events found.</p>";
              }
              
            }
          } else {
            echo "<p>No events found.</p>";
          }
          $conn->close();
          ?>
        </div>
        <button class="carousel-btn next" id="nextBtn">&#10095;</button>
      </div>
    </div>
  </article>
</div>

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

    <!-- FOOTER -->
  <footer>
    <p>TAARA located at P-3 Burac St., San Lorenzo, Tabaco, Philippines</p>
    <p>
      <a href="#"><i class="fa-brands fa-facebook"></i> Facebook</a> | 
      <a href="tel:09055238105"><i class="fa-solid fa-phone"></i> 0905 523 8105</a>
    </p>
  </footer>

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

<script>
const carouselTrack = document.getElementById('eventCarousel');
const nextBtn = document.getElementById('nextBtn');
const prevBtn = document.getElementById('prevBtn');
const slides = carouselTrack.querySelectorAll('.event-card');
let currentIndex = 0;

function showSlide(index) {
  if (index < 0) index = slides.length - 1;
  if (index >= slides.length) index = 0;
  carouselTrack.style.transform = `translateX(-${index * 100}%)`;
  currentIndex = index;
}
nextBtn.addEventListener('click', () => showSlide(currentIndex + 1));
prevBtn.addEventListener('click', () => showSlide(currentIndex - 1));

function notify(btn_id){
  const btn = document.getElementById('notify-btn'+btn_id);
  btn.innerText = 'Notified';
  btn.disabled = true;
  btn.style.backgroundColor = '#aaa';
}
</script>
</body>
</html>
