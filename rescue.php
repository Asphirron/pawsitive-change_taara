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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rescue Report | TAARA</title>

  <!-- External Styles -->
  <link rel="stylesheet" href="CSS/globals.css">
  <link rel="stylesheet" href="CSS/map_modal.css">
  <link rel="stylesheet" href="CSS/essentials.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f7f8fc;
      color: #222;
    }

    main {
      display: flex;
      justify-content: center;
      padding: 50px 20px;
    }

    .container {
      width: 850px;
      background: white;
      padding: 40px 50px;
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }

    .banner {
      background: linear-gradient(135deg, #c9378a, #9b2780);
      color: white;
      text-align: center;
      border-radius: 10px;
      padding: 20px;
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 30px;
    }

    form {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    fieldset {
      border: none;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    label {
      margin-bottom: 6px;
      font-weight: 500;
    }

    input, select, textarea {
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 10px;
      font-size: 15px;
      width: 100%;
    }

    textarea {
      min-height: 90px;
      resize: none;
    }

    .two-cols {
      display: flex;
      gap: 20px;
      width: 100%;
    }

    .two-cols fieldset {
      flex: 1;
    }

    .location-controls {
      display: flex;
      gap: 10px;
      margin-top: 8px;
    }

    .location-controls button {
      flex: 1;
      background-color: #162070;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 10px;
      cursor: pointer;
    }

    .button-group {
      width: 100%;
      display: flex;
      justify-content: space-between;
      margin-top: 15px;
      gap: 10px;
    }

    .button-group button {
      flex: 1;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.2s;
      color: white;
    }

    .button-group button[type="reset"] {
      background: #444;
    }

    .button-group button.submit {
      background: #c9378a;
    }

    .button-group button:hover {
      opacity: 0.9;
    }

    footer {
      background: #222;
      color: #eee;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-around;
      padding: 20px;
      text-align: center;
    }

    .footer-content {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .footer-content-img {
      width: 24px;
      height: 24px;
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
    }

    .modal-content {
      background: #fff;
      margin: 5% auto;
      padding: 20px;
      width: 600px;
      height: 500px;
      border-radius: 10px;
      display: flex;
      flex-direction: column;
    }

    #map {
      flex: 1;
      border-radius: 8px;
    }

    .btn {
      margin-top: 10px;
      padding: 10px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
  </style>

  <!-- Google Maps API -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBEmB9mvLt7AsnqLewyGh9EOoZIsn6C0xA&libraries=places"></script>
</head>

<body>

  <!-- HEADER -->
   <header>
    <img src="Assets/UI/taaralogo.jpg" alt="TAARA Logo">
    <div class="nav-container">
      <nav>
        <ul>
          <li><a class='active' href="rescue.php">Rescue</a></li>
          <li><a href="adoption.php">Adopt</a></li>
          <li><a href="donation.php">Donation</a></li>
          <li><a href="volunteer.php">Volunteer</a></li>
          <li><a href="events.php">Events</a></li>
          <li><a href="index.php">About</a></li> <!-- About moved to last -->
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

  <!-- MAIN -->
  <main>
    <div class="container">
      <div class="banner">RESCUE REPORT</div>

      <form action="includes/post_report.php" method="post" enctype="multipart/form-data">
        <fieldset class="full">
          <p><strong>PLEASE SUBMIT ONLY VALID DATA</strong></p>
          <p>Providing false information is punishable under the Revised Penal Code (Act No. 3825).</p>
        </fieldset>

        <div class="two-cols">
          <fieldset>
            <label for="report_type">Report Type</label>
            <select name="report_type" id="report_type" required>
              <option value="rescue">Rescue</option>
              <option value="lost_and_found">Lost and Found</option>
            </select>
          </fieldset>

          <fieldset>
            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" id="full_name" placeholder="Enter your name" required>
          </fieldset>
        </div>

        <div class="two-cols">
          <fieldset>
            <label for="contact_num">Contact Number</label>
            <input type="number" name="contact_num" id="contact_num" placeholder="Enter contact number" required>
          </fieldset>

          <fieldset>
            <label for="location_container">Location</label>
            <input type="text" name="location" id="location_container" placeholder="Set your location" readonly required>
            <div class="location-controls">
              <button type="button" name="current_location">Use Current Location</button>
              <button type="button" id="open_map_btn">Use Map</button>
            </div>
          </fieldset>
        </div>

        <div class="two-cols">
        <fieldset class="full">
          <label for="description">Report Description</label>
          <textarea name="description" id="description" placeholder="Describe the situation..." required></textarea>
        </fieldset>

        <fieldset class="">
          <label>Photo for Documentation</label>
          <input type="file" name="image" accept="image/*" required>
        </fieldset>
        </div>

        <fieldset class="">
          <input type="checkbox" name="agreed" value="true" class="check-box">
          <label>Send me E-mail Updates</label>
        </fieldset>

        <div class="button-group">
          <button type="reset">Reset</button>
          <button type="submit" class="submit">Post Report</button>
        </div>
      </form>
    </div>
  </main>

  <!-- FOOTER -->
  <footer>
    <div class="footer-content">
      <img src="Assets/UI/facebook.png" class="footer-content-img" alt="">
      <h3>Facebook</h3>
    </div>
    <div class="footer-content">
      <img src="Assets/UI/phone.png" class="footer-content-img" alt="">
      <h3>09055238105</h3>
    </div>
    <div class="footer-content">
      <h3>Tabaco Animal Advocates and Rescuers Association — All rights reserved</h3>
    </div>
  </footer>

  <!-- MAP MODAL -->
  <div id="mapModal" class="modal">
    <div class="modal-content">
      <div id="map"></div>
      <button id="confirmLocation" class="btn">Confirm Location</button>
    </div>
  </div>

  <script>
  let map, marker, geocoder;
  let selectedCoords = null;

  function initMap() {
    geocoder = new google.maps.Geocoder();
    map = new google.maps.Map(document.getElementById("map"), {
      center: { lat: 14.5995, lng: 120.9842 }, // Manila default 13.33942847116026, 123.7302911488142
      zoom: 12,
    });

    map.addListener("click", (event) => {
      placeMarker(event.latLng);
    });
  }

  function placeMarker(location) {
    if (marker) marker.setMap(null);
    marker = new google.maps.Marker({
      position: location,
      map: map,
    });
    selectedCoords = location;

    geocoder.geocode({ location: location }, (results, status) => {
      let address = "";
      if (status === "OK" && results[0]) {
        address = results[0].formatted_address;
      } else {
        address = "Unknown Location";
      }

      document.getElementById("location_container").value =
        `${location.lat().toFixed(6)}, ${location.lng().toFixed(6)}`;
    });
  }

  // Elements
  const modal = document.getElementById("mapModal");
  const openMapBtn = document.getElementById("open_map_btn");
  const confirmBtn = document.getElementById("confirmLocation");
  const locationInput = document.getElementById("location_container");

  // Open modal when clicking "Use Map"
  openMapBtn.addEventListener("click", () => {
    modal.style.display = "block";
    google.maps.event.trigger(map, "resize");
    //map.setCenter({ lat: 14.5995, lng: 120.9842 });
    map.setCenter({ lat: 13.339672471273763, lng: 123.73034543896598 }); //13.339672471273763, 123.73034543896598
  });

  // Confirm location
  confirmBtn.addEventListener("click", () => {
    if (selectedCoords) {
      modal.style.display = "none";
    } else {
      alert("Please select a location on the map first.");
    }
  });

  // Close modal if clicking outside
  window.onclick = (e) => {
    if (e.target == modal) {
      modal.style.display = "none";
    }
  };

  // Use Current Location
  document.querySelector("button[name='current_location']").addEventListener("click", () => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          const lat = pos.coords.latitude;
          const lng = pos.coords.longitude;
          const position = { lat: lat, lng: lng };
          placeMarker(position);
          map.setCenter(position);
          map.setZoom(15);
          locationInput.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        },
        () => {
          alert("Unable to retrieve your location.");
        }
      );
    } else {
      alert("Geolocation is not supported by your browser.");
    }
  });

  window.onload = initMap;
</script>


</script>

</body>
</html>
