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
  <!-- <link rel="stylesheet" href="CSS/globals.css"> -->
  <link rel="stylesheet" href="CSS/map_modal.css">
  <link rel="stylesheet" href="CSS/essentials.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    /* === GENERAL PAGE STYLING === */
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #f7f8fc, #ececf9);
  color: #222;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

main {
  display: flex;
  justify-content: center;
  padding: 50px 20px;
  flex: 1;
}

/* === CARD CONTAINER === */
.container {
  background: #fff;
  width: 500px;
  padding: 40px 35px;
  border-radius: 18px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}
.container:hover {
  transform: translateY(-4px);
}

/* === HEADER BANNER === */
.banner {
  background: linear-gradient(135deg, #c9378a, #7a2ca0);
  color: white;
  text-align: center;
  padding: 18px;
  border-radius: 10px;
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 25px;
}

/* === INSTRUCTIONS === */
.container {
  background: #fff;
  width: 750px; 
  padding: 50px 60px; 
  border-radius: 20px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.container p strong {
  display: block;
  font-size: 1rem;
  color: #222;
  margin-bottom: 5px;
}

/* === FORM === */
form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

fieldset {
  border: none;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

/* === LABELS === */
label {
  font-weight: 600;
  color: #2a2a2a;
  font-size: 0.9rem;
  margin-top:10px;
}

/* === INPUTS, SELECTS, TEXTAREA === */
input, select, textarea {
  border: 1.5px solid #ddd;
  border-radius: 10px;
  padding: 12px;
  font-size: 0.95rem;
  background: #fafafa;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
  width: 100%;
  outline: none;
}

input:focus, select:focus, textarea:focus {
  border-color: #7a2ca0;
  box-shadow: 0 0 6px rgba(122, 44, 160, 0.25);
  background: #fff;
}

/* === TEXTAREA === */
textarea {
  min-height: 100px;
  resize: vertical;
}

/* === LOCATION BUTTONS === */
.location-controls {
  display: flex;
  gap: 10px;
}
.location-controls button {
  flex: 1;
  background: linear-gradient(135deg, #2b2ba1, #1b1b7e);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 10px;
  font-size: 0.9rem;
  cursor: pointer;
  transition: 0.2s ease;
}
.location-controls button:hover {
  background: linear-gradient(135deg, #4a6ed1, #2b2ba1);
}

/* === FILE INPUT === */
input[type="file"] {
  padding: 4px;
  background: none;
  border: none;
  font-size: 0.9rem;
}

/* === CHECKBOX === */

fieldset.checkbox-group {
  flex-direction: row;
  align-items: center;
  gap: 10px;
}

fieldset.checkbox-group input[type="checkbox"] {
  width: auto;
  transform: scale(1.2);
  accent-color: #7a2ca0; 
}

fieldset.checkbox-group label {
  margin: 0;
  font-weight: 600;
  color: #333;
}


/* === BUTTON GROUP === */
.button-group {
  display: flex;
  gap: 12px;
  margin-top: 10px;
}
.button-group button {
  flex: 1;
  padding: 12px;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s ease;
  color: white;
}
.button-group button[type="reset"] {
  background: #555;
}
.button-group .submit {
  background: linear-gradient(135deg, #c9378a, #8e2bb5);
}
.button-group button:hover {
  transform: translateY(-2px);
  opacity: 0.9;
}

/* === FOOTER === */
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
/* === MAP MODAL === */
.modal {
  display: none;
  position: fixed;
  inset: 0;
  z-index: 1000;
  background: rgba(0, 0, 0, 0.5);
}
.modal-content {
  background: #fff;
  margin: 5% auto;
  padding: 20px;
  width: 90%;
  max-width: 600px;
  height: 480px;
  border-radius: 12px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.2);
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
  border-radius: 6px;
  cursor: pointer;
}
.btn:hover {
  background: #0056b3;
}

/* === RESPONSIVE === */
@media (max-width: 600px) {
  .container {
    width: 90%;
    padding: 25px;
  }
  .banner {
    font-size: 1.3rem;
  }
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

       <!--fieldset class="checkbox-group" hidden>
        <input type="checkbox" name="agreed" value="true" class="check-box">
        <label for="agreed">Send me E-mail Updates</label>
      </fieldset-->

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