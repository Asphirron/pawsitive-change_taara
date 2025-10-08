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
  <link rel="stylesheet" href="CSS/globals.css">
  <link rel="stylesheet" href="CSS/index.css">
  <link rel="stylesheet" href="CSS/donation.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="CSS/essentials.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Events - TAARA</title>

  <style>
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
    width: 300px;
    display: flex;
    flex-direction: column;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
  }

  .event-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    display: block;
  }

  .event-card .card-details {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    width: 100%;
    padding: 1rem;
    flex-grow: 1;
    box-sizing: border-box;
  }

  .event-card .card-title {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    color: #222;
    width: 100%;
    text-align: left;
  }

  .event-card .card-text {
    font-size: 0.95rem;
    color: #555;
    margin-bottom: 0.5rem;
    overflow-y: auto;
    max-height: 100px; /* Scroll if too long */
    width: 100%;
    text-align: left;
    line-height: 1.4;
    scrollbar-width: thin;
    scrollbar-color: #ccc transparent;
  }

  .event-card .card-text::-webkit-scrollbar {
    width: 6px;
  }

  .event-card .card-text::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 3px;
  }

  .event-card button {
    background-color: #e91e63;
    color: white;
    border: none;
    padding: 0.6rem;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    width: 100%;
    align-self: center;
    margin-top: auto;
    transition: background-color 0.2s ease;
  }

  .event-card button:hover {
    background-color: #c2185b;
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
        <div class="card-container">
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
                      <img src='$img' alt='$img'>
                      <div class='event-date-badge'>$event_date</div>
                      <div class='card-details'>
                          <h4 class='card-title'>$title</h4>
                          <p class='card-text'>$desc</p>
                          <p class='event-info'><strong>Location:</strong> $location</p>
                          <button id='notify-btn$id' onclick=notify($id)>Notify Me</button>
                      </div>
                  </div>";
              }
          } else {
              echo "<p>No events found.</p>";
          }
          $conn->close();
          ?>
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
</body>
</html>
