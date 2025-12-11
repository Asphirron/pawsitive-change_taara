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



$application_table = new DatabaseCRUD('adoption_application');
$uid = $_SESSION['name'];
$animal_id = $_POST['animal_id'];
$full_name = $_POST['full_name'];
$address = $_POST['address'];
$classification = $_POST['classification'];
$comp_name = $_POST['comp_name'];

$id_img = "";
if (isset($_FILES['id_img'])) {
  $target_dir = '../Assets/UserGenerated/';
  $file_name = basename($_FILES['id_img']['name']);
  $target_file = $target_dir . $file_name;
  if(move_uploaded_file($_FILES["id_img"]['tmp_name'], $target_file)){
    $id_img = $target_file;
  }
}

date_default_timezone_set('Asia/Manila');
$date = date('Y-m-d');

$application_id = $application_table->create([
  "user_id" => $uid,
  "animal_id" => $animal_id,
  "full_name" => $full_name,
  "address" => $address,
  "classification" => $classification,
  "comp_name" => $comp_name,
  "id_img" => $id_img,
  "date_applied" => $date,
  "status" => "pending"
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adoption Screening</title>

  <link rel="stylesheet" href="CSS/globals.css">
  <link rel="stylesheet" href="CSS/index.css">
  <link rel="stylesheet" href="CSS/donation.css">
  <link rel="stylesheet" href="CSS/essentials.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="functions.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body {
      min-height: 100%;
      background-color: #f8f9fa;
    }

    main {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 50px 0;
    }

    .screening-container {
      width: 70%;
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .banner {
      width: 100%;
      height: 200px;
      background-image: url('Assets/Images/rescue_banner.jpg');
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 36px;
      font-weight: 800;
      text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
      letter-spacing: 1px;
    }

    form {
      width: 90%;
      display: flex;
      flex-direction: column;
      gap: 20px;
      padding: 40px 0;
    }

    .form-row {
      display: flex;
      gap: 20px;
      width: 100%;
    }

    fieldset {
      border: none;
      display: flex;
      flex-direction: column;
      gap: 8px;
      width: 100%;
    }

    label {
      font-weight: 600;
      color: #333;
    }

    input, select, textarea {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      box-sizing: border-box;
    }

    textarea {
      resize: vertical;
      height: 80px;
    }

    button {
      flex: 1;
      height: 45px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      font-size: 15px;
      transition: all 0.2s ease;
      color: white;
      padding: 25px;
    }

    button:hover {
      opacity: 0.9;
    }

    .submit {
      background-color: #c9378a;
      color: white;
      padding: 10px;
    }

    .info {
      text-align: justify;
      color: #444;
      line-height: 1.5;
    }

    @media screen and (max-width: 900px) {
      form {
        width: 90%;
      }

      .form-row {
        flex-direction: column;
      }
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

  <main>
    <div class="screening-container">
      <div class="banner">
        <h1>ADOPTION SCREENING</h1>
      </div>

      <form action="includes/send_application.php" method="post" enctype="multipart/form-data">
        <fieldset>
          <p><strong>SCREENING FOR ADOPTION</strong></p>
          <p class="info">
            Screening helps ensure that our animals are adopted into safe, caring, and responsible homes. 
            Please answer truthfully — your honesty helps us match you with the right companion.
          </p>
          <input type="hidden" name="a_application_id" value="<?php echo $application_id ?>">
        </fieldset>

        <div class="form-row">
          <fieldset>
            <label>1. What type of housing do you live in?</label>
            <select name="housing" required>
              <option value="apartment">Apartment</option>
              <option value="house">House</option>
              <option value="other">Other</option>
            </select>
          </fieldset>

          <fieldset>
            <label>2. Reason for adopting a pet?</label>
            <select name="reason" required>
              <option value="companion for child">Companion for child</option>
              <option value="companion for other pet">Companion for other pet</option>
              <option value="companion for self">Companion for self</option>
              <option value="service animal">Service animal</option>
              <option value="security">Security</option>
            </select>
          </fieldset>
        </div>

        <div class="form-row">
          <fieldset>
            <label>3. Have you previously owned pets?</label>
            <select name="own_pets" required>
              <option value="yes">Yes</option>
              <option value="no">No</option>
            </select>
          </fieldset>

          <fieldset>
            <label>4. How much time are you willing to dedicate to daily exercise and play time?</label>
            <select name="time_dedicated" required>
              <option value="1-2 hours">1-2 hours every day</option>
              <option value="free_time">Only during my free time</option>
              <option value="no_time">I have no time</option>
            </select>
          </fieldset>
        </div>

        <fieldset>
          <label>5. Do you have a house with children? If YES, state the age and your ability to manage. If NONE, put N/A</label>
          <input type="text" name="children_info" placeholder="Example: Yes, ages 5 and 8 - I can manage OR N/A" required>
        </fieldset>

        <div class="form-row">
          <fieldset>
            <label>6. Are you prepared for the financial responsibilities of owning a pet?</label>
            <select name="financial_ready" required>
              <option value="yes">Yes</option>
              <option value="no">No</option>
              <option value="family_provider">My family are my pet’s provider</option>
            </select>
          </fieldset>

          <fieldset>
            <label>7. What size and breed are you interested in and why?</label>
            <textarea name="breed_interest" placeholder="Describe your preference and reason" required></textarea>
          </fieldset>
        </div>

        <fieldset>
          <label>8. Does anyone in your family have a known allergy to animals? If YES, how will you manage it? If NONE, put N/A</label>
          <textarea name="allergy_info" placeholder="Explain or put N/A" required></textarea>
        </fieldset>

        <fieldset>
          <label>9. How long do you plan to leave your pet alone during the day? Do you have a plan for their care if you're away?</label>
          <textarea name="alone_time_plan" placeholder="Describe your plan" required></textarea>
        </fieldset>

        <fieldset>
          <label>10. Have you researched the needs and characteristics of the breed you are interested in adopting?</label>
          <select name="researched_breed" required>
            <option value="yes">Yes, I am well aware</option>
            <option value="no">No</option>
          </select>
        </fieldset>

        <button type="submit" class="submit">Submit Application</button>
      </form>
    </div>
  </main>

  <!-- USER OPTIONS MODAL -->
  <div id="user_options" class="user-options-modal">
    <div class="user-options-content">
      <img src="Assets/Profile_Images/<?php echo $user_img; ?>" alt="Profile Picture">
      <h4><?php echo $username; ?></h4>
      <h6><?php echo $email; ?></h6>
      <hr class="my-2">
      <button onclick="window.location.href='login.php'">Change Account</button>
      <button onclick="logout()">Logout</button>
      <?php if ($user_type === 'admin') { ?>
        <button onclick="window.location.href='Admin/index.php'">Go to Admin Dashboard</button>
      <?php } ?>
      <hr class="my-2">
      <button onclick="window.location.href='adoptions.php'">Your Adoptions</button>
      <button onclick="window.location.href='donations.php'">Your Donations</button>
      <button onclick="window.location.href='events.php'">Events</button>
      <button class="close-btn" onclick="closeModal()">Close</button>
    </div>
  </div>

  <script>
    const profileImg = document.getElementById("user_profile");
    const modal = document.getElementById("user_options");

    if (profileImg) {
      profileImg.addEventListener("click", () => {
        modal.style.display = "flex";
      });
    }

    function closeModal() {
      modal.style.display = "none";
    }

    function logout() {
      fetch('includes/logout.php').then(() => window.location.href = 'login.php');
    }

    window.addEventListener('click', e => {
      if (e.target === modal) closeModal();
    });
  </script>
</body>
</html>
