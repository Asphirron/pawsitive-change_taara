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
    // ✅ Extract first row from array
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
/* ===== General Card Styling ===== */
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
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  flex: 0 0 100%;
 
  display: flex;
  flex-direction: column;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.event-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
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
  justify-content: center;   /* vertical center */
  align-items: center;       /* horizontal center */
  text-align: center;        /* center text */
  gap: .5rem;
  min-height: 260px;         /* keeps a nice balanced height */
}


.event-date {
  color: #777;
  font-size: 0.9rem;
  margin-bottom: 0.4rem;
}

.card-title {
  font-size: 1.35rem;
  font-weight: 700;
  color: #222;
  margin-bottom: 0.5rem;
}

.card-text {
  font-size: 0.95rem;
  color: #555;
  line-height: 1.5;
  margin-bottom: 1rem;
  flex-grow: 1;
}

.event-info {
  font-size: 0.9rem;
  color: #444;
  margin-bottom: 1rem;
}

.event-info strong {
  color: #e91e63;
}

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

.event-card button:hover {
  background-color: #c2185b;
}

/* ===== Carousel Layout ===== */
.carousel-wrapper {
  position: relative;
  width: 100%;
  max-width: 750px;
  margin: 2.5rem auto;
  overflow: hidden;
  border-radius: 12px;
}

.carousel-track {
  display: flex;
  transition: transform 0.6s ease-in-out;
  width: 100%;
}

.carousel-track .event-card {
  flex: 0 0 100%;
  margin: 0;
}

/* Navigation Buttons */
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

.carousel-btn:hover {
  background-color: #e91e63;
  color: #fff;
}

.carousel-btn.prev { left: 12px; }
.carousel-btn.next { right: 12px; }

/* ===== Upcoming Events Title ===== */
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

/* ===== Responsive ===== */
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
        <h4 class="subsection-header-text">Upcoming Events</h4>
      </div>

      <!-- 🎠 Carousel Wrapper -->
      <div class="carousel-wrapper">
        <button class="carousel-btn prev" id="prevBtn">&#10094;</button>

        <div class="carousel-track" id="eventCarousel">
          <?php
          $conn = connect();
          $query = "SELECT event_id, title, description, img, location, event_date FROM event ORDER BY event_date ASC";
          $result = $conn->query($query);

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  $id = $row['event_id'];
                  $title = htmlspecialchars($row['title']);
                  $desc = htmlspecialchars($row['description']);
                  $img = $row['img'];
                  $location = htmlspecialchars($row['location']);
                  $event_date = date("F d, Y", strtotime($row['event_date']));

                  echo "
                  <div class='event-card'>
                      <img src='$img' alt='$title'>
                      <div class='card-details'>
                          <small class='event-date'>$event_date</small>
                          <h4 class='card-title'>$title</h4>
                          <p class='card-text'>$desc</p>
                          <p class='event-info'><strong>Location:</strong> $location</p>
                          <button id='notify-btn$id' onclick='notify($id)'>Notify Me</button>
                      </div>
                  </div>";
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
      notify_btn = document.getElementById('notify-btn'+btn_id);
      notify_btn.innerText = 'Notified';
      notify_btn.disabled = true;
      notify_btn.style.bgcolor = 'lightgray';

    }
  </script>

<script>
  const carouselTrack = document.getElementById('eventCarousel');
  const nextBtn = document.getElementById('nextBtn');
  const prevBtn = document.getElementById('prevBtn');
  const slides = carouselTrack.querySelectorAll('.event-card');
  let currentIndex = 0;

  // Function to display slides one at a time
  function showSlide(index) {
    if (index < 0) index = slides.length - 1;
    if (index >= slides.length) index = 0;
    carouselTrack.style.transform = `translateX(-${index * 100}%)`;
    currentIndex = index;
  }

  nextBtn.addEventListener('click', () => showSlide(currentIndex + 1));
  prevBtn.addEventListener('click', () => showSlide(currentIndex - 1));

  // Simple notify button behavior
  function notify(btn_id){
    const btn = document.getElementById('notify-btn'+btn_id);
    btn.innerText = 'Notified';
    btn.disabled = true;
    btn.style.backgroundColor = '#aaa';
  }
</script>


</body>
</html>