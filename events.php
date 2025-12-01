<?php
include "includes/db_connection.php";
session_start();

// -------------------- USER SESSION --------------------
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

// -------------------- EVENTS --------------------
$conn = connect();
$today = date("Y-m-d");
$ongoingEvents = [];
$upcomingEvents = [];
$pastEvents = [];

$query = "SELECT event_id, title, description, img, location, event_date FROM event ORDER BY event_date ASC";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
  $dateKey = date("Y-m-d", strtotime($row['event_date']));
  $eventData = [
    "id" => $row['event_id'],
    "title" => htmlspecialchars($row['title']),
    "desc" => htmlspecialchars($row['description']),
    "img" => $row['img'],
    "location" => htmlspecialchars($row['location']),
    "date" => date("F d, Y", strtotime($row['event_date'])),
    "raw_date" => $dateKey
  ];

  if ($dateKey === $today) {
    $ongoingEvents[] = $eventData;
  } elseif ($dateKey > $today) {
    $upcomingEvents[] = $eventData;
  } else {
    $pastEvents[] = $eventData;
  }
}
$conn->close();
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
  width: auto; height: auto;
  color: var(--color-text-primary);
  background-color: var(--color-fg);
  box-shadow: 3px 3px 3px rgba(0,0,0,0.275);
  padding-block: 20px;
}
.footer-content { display:flex; flex-direction:row; align-items:center; justify-content:center; gap:10px; }
.footer-content img { height:20px; width:20px; }

/* ===== Event Cards ===== */
.event-card {
  background:#fff; border-radius:12px;
  box-shadow:0 4px 10px rgba(0,0,0,0.1);
  overflow:hidden; flex:0 0 100%;
  display:flex; flex-direction:column;
  transition:transform 0.25s ease, box-shadow 0.25s ease;
}
.event-card:hover { transform:translateY(-5px); box-shadow:0 8px 18px rgba(0,0,0,0.15); }
.event-card img { width:100%; height:250px; object-fit:cover; }
.card-details { padding:1rem; display:flex; flex-direction:column; gap:.5rem; text-align:center; }
.event-date { color:#777; font-size:0.9rem; }
.card-title { font-size:1.2rem; font-weight:700; color:#222; }
.card-text { font-size:0.95rem; color:#555; line-height:1.5; }
.event-info { font-size:0.9rem; color:#444; }
.event-info strong { color:#e91e63; }
.event-card button {
  background-color:#e91e63; color:white; border:none;
  padding:0.6rem; border-radius:10px; font-weight:600;
  cursor:pointer; transition:background-color 0.25s ease;
}
.event-card button:hover { background-color:#c2185b; }

/* ===== Carousels ===== */
.carousel-wrapper {
  flex:1; min-width:300px; max-width:350px;
  margin:1rem; border-radius:12px;
}
.carousel-track { display:flex; flex-direction:column; gap:1rem; }

/* ===== Headers ===== */
.subsection-header-text {
  font-size:1.4rem; font-weight:800; text-align:center;
  margin-bottom:1rem; color:#fff; padding:8px; border-radius:6px;
}
.ongoing { background:#28a745; }
.upcoming { background:#007bff; }
.past { background:#6c757d; }

.flex-row { display:flex; flex-wrap:wrap; justify-content:center; gap:1rem; }
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

<main class="content-area">
  <article class="feature-section">
    <div class="featured-content flex-row">

      <!-- Ongoing -->
      <div class="carousel-wrapper">
        <h4 class="subsection-header-text ongoing">📌 Ongoing Events</h4>
        <div class="carousel-track">
          <?php if (!empty($ongoingEvents)): ?>
            <?php foreach ($ongoingEvents as $e): ?>
              <div class="event-card">
                <img src="<?= $e['img'] ?>" alt="<?= $e['title'] ?>">
                <div class="card-details">
                  <small class="event-date"><?= $e['date'] ?></small>
                  <h4 class="card-title"><?= $e['title'] ?></h4>
                  <p class="card-text"><?= $e['desc'] ?></p>
                  <p class="event-info"><strong>Location:</strong> <?= $e['location'] ?></p>
                  <button id="notify-btn<?= $e['id'] ?>" onclick="notify(<?= $e['id'] ?>)">Notify Me</button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?><p>No ongoing events.</p><?php endif; ?>
        </div>
      </div>

      <!-- Upcoming -->
      <div class="carousel-wrapper">
        <h4 class="subsection-header-text upcoming">📅 Upcoming Events</h4>
        <div class="carousel-track">
          <?php if (!empty($upcomingEvents)): ?>
            <?php foreach ($upcomingEvents as $e): ?>
              <div class="event-card">
                <img src="<?= $e['img'] ?>" alt="<?= $e['title'] ?>">
                <div class="card-details">
                  <small class="event-date"><?= $e['date'] ?></small>
                  <h4 class="card-title"><?= $e['title'] ?></h4>
                  <p class="card-text"><?= $e['desc'] ?></p>
                  <p class="event-info"><strong>Location:</strong> <?= $e['location'] ?></p>
                  <!-- No notify button -->
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?><p>No upcoming events.</p><?php endif; ?>
        </div>
      </div>

      <!-- Past -->
      <div class="carousel-wrapper">
        <h4 class="subsection-header-text past">🕑 Past Events</h4>
        <div class="carousel-track">
          <?php if (!empty($pastEvents)): ?>
            <?php foreach ($pastEvents as $e): ?>
              <div class="event-card">
                <img src="<?= $e['img'] ?>" alt="<?= $e['title'] ?>">
                <div class="card-details">
                  <small class="event-date"><?= $e['date'] ?></small>
                  <small class="event-date"><?= $e['date'] ?></small>
                  <h4 class="card-title"><?= $e['title'] ?></h4>
                  <p class="card-text"><?= $e['desc'] ?></p>
                  <p class="event-info"><strong>Location:</strong> <?= $e['location'] ?></p>
                  <!-- No notify button for past events -->
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p>No past events.</p>
          <?php endif; ?>
        </div>
      </div>

    </div> <!-- end flex-row -->
  </article>
</main>

<footer class="site-footer">
  <div class="footer-content">
    <img src="Assets/UI/facebook.png" class="footer-content-img">
    <h3 class="footer-content-text">Facebook</h3>
  </div>
  <div class="footer-content">
    <img src="Assets/UI/phone.png" class="footer-content-img">
    <h3 class="footer-content-text">09055238105</h3>
  </div>
  <div class="footer-content">
    <h3 class="footer-content-text">Tabaco Animal Advocates and Rescuers Association - All rights reserved</h3>
  </div>
</footer>

<script>
function notify(btn_id){
  const btn = document.getElementById('notify-btn'+btn_id);
  btn.innerText = 'Notified';
  btn.disabled = true;
  btn.style.backgroundColor = '#aaa';
}
</script>
</body>
</html>
