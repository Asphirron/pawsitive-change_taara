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
  <link rel="stylesheet" href="CSS/essentials.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
 <style>
  :root {
    --accent: #e63946;
    --accent-dark: #d62828;
    --success: #06d6a0;
    --bg: #f8f9fa;
    --text-dark: #222;
    --text-light: #555;
    --border: #eaeaea;
  }

  body {
    background: var(--bg);
    font-family: 'Inter', system-ui, sans-serif;
    margin: 0;
  }

  .donation-wrapper {
    max-width: 1200px;
    margin: 40px auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    padding: 0 20px;
  }

  @media (max-width: 900px) {
    .donation-wrapper {
      grid-template-columns: 1fr;
    }
  }

  /* LEFT: Donation Details */
  .donation-details {
    background: #fff;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    transition: 0.3s;
  }

  .donation-details:hover {
    transform: translateY(-3px);
  }

  .donation-banner-img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 15px;
    margin-bottom: 15px;
  }

  .donation-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 10px;
  }

  .donation-description {
    color: var(--text-light);
    line-height: 1.6;
    margin-bottom: 20px;
  }

  .donate-actions {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
  }

  .donate-action-btn {
    background: var(--accent);
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
  }

  .donate-action-btn:hover {
    background: var(--accent-dark);
    transform: translateY(-2px);
  }

  .progress-bar {
    width: 100%;
    height: 12px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 10px;
  }

  .progress-level {
    height: 100%;
    background: var(--success);
    transition: width 0.5s ease;
  }

  /* RIGHT: Donation Form */
  .donation-form {
    background: #fff;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
  }

  .donation-form h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 8px;
  }

  .donation-form p {
    color: var(--text-light);
    font-size: 0.95rem;
    margin-bottom: 20px;
  }

  .form-group {
    margin-bottom: 15px;
    position: relative;
  }

  .form-group input, 
  .form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border-radius: 10px;
    border: 1px solid var(--border);
    font-size: 1rem;
    transition: 0.2s;
    background: #fff;
  }

  .form-group input:focus,
  .form-group textarea:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(230,57,70,0.15);
  }

  .submit-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px;
    font-weight: 600;
    color: #fff;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.3s;
  }

  .submit-btn img {
    height: 20px;
  }

  .submit-btn:hover {
    transform: translateY(-2px);
    opacity: 0.9;
  }

  .payment-buttons {
  display: flex;
  gap: 12px;
  justify-content: space-between;
  align-items: stretch; /* makes both buttons equal height */
  margin-top: 10px;
  margin-bottom: 0; /* remove unwanted bottom spacing */
}

.payment-buttons .submit-btn {
  flex: 1;
  min-width: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  height: 48px; /* consistent button height */
  padding: 0 12px;
  border-radius: 10px;
  border: none;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}

.payment-buttons .submit-btn img {
  height: 20px;
  width: auto;
  display: inline-block;
  vertical-align: middle;
}

.payment-buttons .paypal-btn {
  background: #0070BA;
}

.payment-buttons .gcash-btn {
  background: #0077FF;
}

.payment-buttons .submit-btn:hover {
  transform: translateY(-2px);
  opacity: 0.9;
}


.donation-form {
  background: #fff;
  border-radius: 20px;
  padding: 30px 30px 25px; 
  box-shadow: 0 8px 20px rgba(0,0,0,0.05);
  display: flex;
  flex-direction: column;
  gap: 12px;
}


@media (max-width: 600px) {
  .payment-buttons {
    flex-direction: column;
    gap: 10px;
  }
}

  .checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 20px;
  }

  .checkbox input {
    accent-color: var(--accent);
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
  <h2>Make a Donation</h2>
  <p>Your support helps us provide shelter, food, and care for animals in need.</p>

  <input type="hidden" name="dpost" value="<?php echo $id; ?>">

  <div class="form-group">
    <input type="text" name="fullname" placeholder="Full Name" required>
  </div>

  <div class="form-group">
    <input type="number" name="amount" placeholder="Amount (₱)" min="1" required>
  </div>

  <div class="form-group">
    <textarea name="message" rows="3" placeholder="Your message (optional)"></textarea>
  </div>

  <div class="form-group">
    <input type="text" name="contact" placeholder="Contact Number">
  </div>

 

 <div class="payment-buttons">
  <button type="submit" name="payment_method" value="paypal" class="submit-btn paypal-btn">
    <img src="Assets/UI/paypal.png" alt="PayPal"> Donate with PayPal
  </button>

  <button type="submit" name="payment_method" value="gcash" class="submit-btn gcash-btn">
    <img src="Assets/UI/gcash.png" alt="GCash"> Donate with GCash
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