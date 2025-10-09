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



if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET["donationId"]) || !is_numeric($_GET["donationId"])) {
  header("Location: donation.php");
  exit;
}

$id = intval($_GET["donationId"]);
$conn = connect();
$stmt = $conn->prepare("SELECT title, description, post_img, goal_amount, current_amount, date_posted, deadline FROM donation_post WHERE dpost_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
  $title = htmlspecialchars($row['title']);
  $desc = htmlspecialchars($row['description']);
  $img = htmlspecialchars($row['post_img']);
  $goal = floatval($row['goal_amount']);
  $progress = floatval($row['current_amount']);
  $date = $row['date_posted'];
  $deadline = $row['deadline'];
  $prog_lvl = getPercentage($progress, $goal);
} else {
  header("Location: donation.php");
  exit;
}
$stmt->close();
$conn->close();

$days_left = "No deadline";
if (!empty($deadline) && strtotime($deadline) !== false) {
  $dleft = ceil((strtotime($deadline) - time()) / 86400);
  $days_left = max(0, $dleft) . " days left";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Donation - <?php echo $title; ?></title>
  <link rel="stylesheet" href="CSS/globals.css">
  <link rel="stylesheet" href="CSS/essentials.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      background-color: #fafafa;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 50px;
      background: white;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    header img {
      height: 70px;
      width: auto;
      border-radius: 10px;
    }

    .nav-container ul {
      display: flex;
      gap: 20px;
      list-style: none;
    }

    .nav-container a {
      text-decoration: none;
      color: #333;
      font-weight: 600;
    }

    .nav-container a.active {
      color: #e63946;
      border-bottom: 2px solid #e63946;
    }

    /* Donation Layout */
    .donation-wrapper {
      max-width: 1200px;
      margin: 40px auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      align-items: start;
      padding: 0 20px;
    }

    @media (max-width: 900px) {
      .donation-wrapper {
        grid-template-columns: 1fr;
      }
    }

    .donation-banner-img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      border-radius: 15px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .donation-details {
      background: #fff;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .donation-title {
      font-size: 1.8rem;
      font-weight: 700;
      margin-top: 15px;
      color: #222;
    }

    .donation-description {
      color: #555;
      line-height: 1.5;
      margin: 10px 0 20px;
    }

    .donate-actions {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
    }

    .donate-action-btn {
      background-color: #e63946;
      border: none;
      color: white;
      padding: 10px 20px;
      border-radius: 25px;
      cursor: pointer;
      font-weight: 600;
      transition: 0.3s;
    }

    .donate-action-btn:hover {
      background-color: #d62828;
    }

    .progress-bar {
      width: 100%;
      height: 12px;
      background-color: #eee;
      border-radius: 8px;
      overflow: hidden;
      margin-bottom: 8px;
    }

    .progress-level {
      height: 100%;
      background-color: #06d6a0;
      border-radius: 8px;
    }

    .donation-form {
      background: #fff;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .donate-input, textarea {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-bottom: 15px;
      font-size: 1rem;
    }

    .donate-input:focus, textarea:focus {
      outline: none;
      border-color: #e63946;
      box-shadow: 0 0 0 2px rgba(230,57,70,0.1);
    }

    .submit-btn {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      color: white;
      font-weight: 600;
      padding: 12px;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      transition: 0.3s;
    }

    .submit-btn:hover {
      transform: scale(1.02);
      opacity: 0.9;
    }

    footer {
      background-color: #333;
      color: white;
      text-align: center;
      padding: 20px;
      font-size: 0.9rem;
      margin-top: 40px;
    }

    footer a {
      color: #ffd166;
      text-decoration: none;
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
          <li><a class="active" href="donation.php">Donation</a></li>
          <li><a href="volunteer.php">Volunteer</a></li>
          <li><a href="events.php">Events</a></li>
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

  <div class="donation-wrapper">
    <!-- LEFT: Donation Details -->
    <div class="donation-details">
      <img src="Assets/Images/<?php echo $img; ?>" class="donation-banner-img" alt="Donation Image">
      <h1 class="donation-title"><?php echo $title; ?></h1>
      <p class="donation-description"><?php echo $desc; ?></p>

      <div class="donate-actions">
        <button class="donate-action-btn"><i class="fa-solid fa-bookmark"></i> Bookmark</button>
        <button class="donate-action-btn"><i class="fa-solid fa-share-nodes"></i> Share</button>
      </div>

      <div class="progress-bar">
        <div class="progress-level" style="width: <?php echo $prog_lvl; ?>%"></div>
      </div>
      <p><strong>Goal:</strong> ₱<?php echo number_format($goal, 2); ?> | <strong>Raised:</strong> ₱<?php echo number_format($progress, 2); ?> (<?php echo $prog_lvl; ?>%)</p>
      <p><i class="fa-regular fa-clock"></i> <?php echo $days_left; ?></p>
    </div>

    <!-- RIGHT: Donation Form -->
    <form class="donation-form" action="includes/donate_money.php" method="post">
      <h2 class="text-xl font-bold mb-3 text-gray-800">Make a Donation</h2>
      <p class="text-sm text-gray-500 mb-4">Your support helps us provide shelter, food, and care for animals in need.</p>

      <input type="hidden" name="dpost" value="<?php echo $id; ?>">

      <label class="font-semibold text-gray-700">Full Name</label>
      <input type="text" name="fullname" class="donate-input" placeholder="Enter your full name" required>

      <label class="font-semibold text-gray-700">Amount (₱)</label>
      <input type="number" name="amount" class="donate-input" placeholder="Enter amount" min="1" required>

      <label class="font-semibold text-gray-700">Message (optional)</label>
      <textarea name="message" rows="3" class="donate-input" placeholder="What do you want to say to us?"></textarea>

      <label class="font-semibold text-gray-700">Contact Number</label>
      <input type="text" name="contact" class="donate-input" placeholder="Enter your contact number">

      <div class="flex items-center gap-2 mb-4">
        <input type="checkbox" id="agreed" name="agreed" value="true">
        <label for="agreed" class="text-sm text-gray-600">Send me email updates</label>
      </div>

      <div class="space-y-3">
        <button type="submit" name="payment_method" value="paypal" class="submit-btn" style="background:#0070BA;">
          <img src="Assets/UI/paypal.png" alt="PayPal" style="height:20px;"> Donate with PayPal
        </button>
        <button type="submit" name="payment_method" value="gcash" class="submit-btn" style="background:#0077FF;">
          <img src="Assets/UI/gcash.png" alt="GCash" style="height:20px;"> Donate with GCash
        </button>
      </div>
    </form>
  </div>

  <footer>
    <p>TAARA located at P-3 Burac St., San Lorenzo, Tabaco, Philippines</p>
    <p><a href="#"><i class="fa-brands fa-facebook"></i> Facebook</a> | <a href="tel:09055238105"><i class="fa-solid fa-phone"></i> 0905 523 8105</a></p>
  </footer>

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
      profileImg.addEventListener("click", () => modal.style.display = "flex");
    }

    function closeModal() { modal.style.display = "none"; }
    function logout() { fetch('includes/logout.php').then(() => window.location.href = 'login.php'); }
    window.addEventListener('click', e => { if (e.target === modal) closeModal(); });
  </script>
</body>
</html>
