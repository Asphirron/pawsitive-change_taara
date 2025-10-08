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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['name'] ?? null;
    $first_committee = $_POST['first_com'] ?? '';
    $second_committee = $_POST['second_com'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $classification = $_POST['classification'] ?? '';
    $age = $_POST['age'] ?? '';
    $birth_date = $_POST['birth_date'] ?? '';
    $contact_num = $_POST['contact_num'] ?? '';
    $address = $_POST['address'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $date_applied = date('Y-m-d');

    $id_img = '';
    if (isset($_FILES['id_img']) && $_FILES['id_img']['error'] === 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_name = time() . "_" . basename($_FILES['id_img']['name']);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['id_img']['tmp_name'], $target_file)) {
            $id_img = $target_file;
        }
    }

    $conn = connect();
    $query = "INSERT INTO volunteer_application 
        (user_id, first_committee, second_committee, full_name, classification, age, birth_date, contact_num, address, id_img, reason_for_joining, date_appied)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssssisssss", $user_id, $first_committee, $second_committee, $full_name, $classification, $age, $birth_date, $contact_num, $address, $id_img, $reason, $date_applied);
    if ($stmt->execute()) {
        header("Location: includes/transaction_confirmation.php?type=volunteer&title=VOLUNTEER APPLICATION SUCCESSFUL&message=Please wait for our response in your email.");
        exit;
    } else {
        echo "Error submitting application: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Volunteer Application - TAARA</title>
  <link rel="stylesheet" href="CSS/globals.css">
  <link rel="stylesheet" href="CSS/index.css">
  <link rel="stylesheet" href="CSS/essentials.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    .volunteer-container {
      max-width: 800px;
      margin: 2rem auto;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      padding: 2.5rem;
      animation: fadeIn 0.6s ease;
    }

    .volunteer-banner {
      text-align: center;
      margin-bottom: 2rem;
    }

    .volunteer-banner img {
      width: 120px;
      margin-bottom: 1rem;
    }

    .volunteer-banner h1 {
      font-size: 1.8rem;
      color: #d63384;
      font-weight: 700;
    }

    .volunteer-banner p {
      color: #666;
      font-size: 1rem;
    }

    form {
      display: flex;
      flex-wrap: wrap;
      gap: 1.5rem;
      justify-content: space-between;
    }

    .labeled-input {
      flex: 1 1 48%;
      display: flex;
      flex-direction: column;
    }

    .labeled-input.full-width {
      flex: 1 1 100%;
    }

    label {
      font-weight: 600;
      color: #444;
      margin-bottom: 0.5rem;
    }

    input, select, textarea {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 0.7rem 1rem;
      font-size: 1rem;
      transition: all 0.2s ease;
    }

    input:focus, select:focus, textarea:focus {
      border-color: #d63384;
      outline: none;
      box-shadow: 0 0 4px rgba(214, 51, 132, 0.3);
    }

    textarea {
      resize: none;
      height: 120px;
    }

    .submit-btn {
      width: 100%;
      height: 45px;
      background-color: #d63384;
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: 1rem;
      font-weight: 600;
      font-size: 1.1rem;
      margin-top: 1rem;
      transition: all 0.3s ease;
    }

    .submit-btn:hover {
      background-color: #b82b6e;
      transform: translateY(-2px);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header>
    <img src="Assets/UI/taaralogo.jpg" alt="TAARA Logo">
    <div class="nav-container">
      <nav>
        <ul>
          <li><a href="rescue.php">Rescue</a></li>
          <li><a href="adoption.php">Adopt</a></li>
          <li><a href="donation.php">Donation</a></li>
          <li><a href="volunteer.php">Volunteer</a></li>
          <li><a href="events.php">Events</a></li>
          <li><a class='active' href="index.php">About</a></li>
        </ul>
      </nav>

      <?php
        if ($logged_in) {
          echo "<img src='Assets/Profile_Images/$user_img' class='profile-img' id='user_profile'>";
        } else {
          echo "<a href='register.html' class='bg-pink-600 text-white px-4 py-2 rounded-full font-bold hover:bg-pink-700 flex items-center gap-2'>
                  <i class='fa-solid fa-user-plus'></i> Register
                </a>";
        }
      ?>
    </div>
  </header>

  <!-- CONTENT -->
  <div class="volunteer-container">
    <div class="volunteer-banner">
      <h1>Volunteer Application</h1>
      <p>Join us and help make a difference for our animal friends.</p>
    </div>

    <form action="volunteer_application.php" method="post" enctype="multipart/form-data">
      <div class="labeled-input">
        <label for="first_com">First Committee</label>
        <select name="first_com" required>
          <option value="">-- Select Committee --</option>
          <option value="Secretariat">Secretariat</option>
          <option value="Logistics">Logistics</option>
          <option value="PR and Research">Public Relations & Research</option>
          <option value="Adoption and Foster">Adoption and Foster</option>
          <option value="Rescue Initiatives">Rescue Initiatives</option>
          <option value="Multimedia/Creatives">Multimedia/Creatives</option>
          <option value="Documentation">Documentation</option>
        </select>
      </div>

      <div class="labeled-input">
        <label for="second_com">Second Committee</label>
        <select name="second_com">
          <option value="">-- Select Committee --</option>
          <option value="Secretariat">Secretariat</option>
          <option value="Logistics">Logistics</option>
          <option value="PR and Research">Public Relations & Research</option>
          <option value="Adoption and Foster">Adoption and Foster</option>
          <option value="Rescue Initiatives">Rescue Initiatives</option>
          <option value="Multimedia/Creatives">Multimedia/Creatives</option>
          <option value="Documentation">Documentation</option>
        </select>
      </div>

      <div class="labeled-input full-width">
        <label for="full_name">Full Name</label>
        <input type="text" name="full_name" required>
      </div>

      <div class="labeled-input">
        <label for="classification">Classification</label>
        <input type="text" name="classification" required>
      </div>

      <div class="labeled-input">
        <label for="age">Age</label>
        <input type="number" name="age" required>
      </div>

      <div class="labeled-input">
        <label for="birth_date">Birth Date</label>
        <input type="date" name="birth_date" required>
      </div>

      <div class="labeled-input">
        <label for="contact_num">Contact Number</label>
        <input type="text" name="contact_num" required>
      </div>

      <div class="labeled-input full-width">
        <label for="address">Address</label>
        <input type="text" name="address" required>
      </div>

      <div class="labeled-input full-width">
        <label for="id_img">Upload ID</label>
        <input type="file" name="id_img" accept="image/*" required>
      </div>

      <div class="labeled-input full-width">
        <label for="reason">Reason for Joining</label>
        <textarea name="reason" required></textarea>
      </div>

      <button type="submit" class="submit-btn">Submit Application</button>
    </form>
  </div>

  <!-- FOOTER -->
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
</body>
</html>
