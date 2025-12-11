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


$conn = connect();

// Query Top Donors
$year  = date("Y");
$month = date("n");
$limit = 5;
$sql = "
    SELECT full_name, SUM(amount) AS total_donated
    FROM monetary_donation
    WHERE YEAR(date_donated) = ? 
      AND MONTH(date_donated) = ?
    GROUP BY full_name
    ORDER BY total_donated DESC
    LIMIT ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $year, $month, $limit);
$stmt->execute();
$result = $stmt->get_result();
$topDonors = $result;
$stmt->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Donation</title>
  <link rel="stylesheet" href="CSS/globals.css">
  <link rel="stylesheet" href="CSS/essentials.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* --- Layout Improvements --- */
    body {
      background-color: #fafafa;
      
    }


    /* --- Hero Section --- */
    .hero-section {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      border-radius: 15px;
      margin: 40px auto;
      width: 90%;
      max-width: 1200px;
      height: 350px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .hero-section img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      filter: brightness(0.6);
    }

    .hero-section-details {
      position: absolute;
      text-align: center;
      color: white;
      padding: 20px;
    }

    .hero-section-header {
      font-size: 2rem;
      font-weight: 700;
    }

    .highlight {
      color: #ffd166;
    }

    .hero-section-subheader {
      margin: 10px 0 20px;
      font-size: 1.1rem;
    }

    .hero-section-btn {
      background-color: #e63946;
      border: none;
      color: white;
      padding: 10px 25px;
      margin: 5px;
      border-radius: 25px;
      cursor: pointer;
      font-weight: 600;
      transition: 0.3s;
    }

    .hero-section-btn:hover {
      background-color: #d62828;
      transform: scale(1.05);
    }

    /* --- Donation Section --- */
    .feature-section {
      width: 90%;
      max-width: 1200px;
      margin: 0 auto 60px;
    }

    .collapsible-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-direction: column;
      background-color: #fff;
      padding: 15px 25px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      margin-bottom: 20px;
    }

    table{
      width: 100%;
    }

    th, td{
      text-align: center;
    }

    thead{
      background-color: #e63946;
      color: #fff;
    }

    td{
      background-color: #fafafa;
    }


    .card-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 25px;
    }

    .donation-card {
      display: flex;
      flex-direction: column;
      background: #fff;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgba(0,0,0,0.08);
      transition: transform 0.3s;
      position: relative;
      height: 400px;
    }

    .donation-card:hover {
      transform: translateY(-5px);
    }

    .card-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }

    .card-details {
      /*flex: 1;*/
      padding: 15px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%;
      width: 100%;
      position: relative;
    }

    .card-description {
      max-height: 90px;
      overflow-y: auto;
      margin-bottom: 10px;
      font-size: 0.9rem;
      line-height: 1.4;
    }

    .donation-progress-group {
      margin-bottom: 15px;
    }

    .progress-bar {
      width: 100%;
      height: 10px;
      background-color: #eee;
      border-radius: 5px;
      overflow: hidden;
      margin-bottom: 8px;
    }

    .progress-level {
      height: 100%;
      background-color: #06d6a0;
      border-radius: 5px;
    }

    .donate-card-btn {
      width: 100%;
      background-color: #e63946;
      color: white;
      padding: 10px;
      font-weight: 600;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      transition: 0.3s;
      position: sticky;
      bottom: 0;
    }

    .donate-card-btn:hover {
      background-color: #d62828;
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
          <li><a class='active' href="donation.php">Donation</a></li>
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

  <div class="content-area">
    <article class="hero-section">
      <img src="Assets/Images/Donation_Banner.jpg" alt="">
      <div class="hero-section-details">
        <h1 class="hero-section-header">TAARA appreciates every <span class="highlight">HELP</span> we can get</h1>
        <h5 class="hero-section-subheader">Help us continue our mission by either helping us financially or in-kind</h5>
        <button class="hero-section-btn"><a href="monetary_donation.php?donationId=4" style="color: white;">Monetary Donation</a></button>
        <button class="hero-section-btn"><a href="inkind_donation.php" style="color: white;">In-kind Donation</a></button>
      </div>
    </article>

    <article class="feature-section">
      <div class="collapsible-header">
        

      </div>

      <div class="card-container" style="flex-direction: column;">
        <h2 class="subsection-header-text"><b>This Month's Top Donors</b></h2>
        <!-- RESULT TABLE -->
        <table class="">
            <thead>
              <tr>
                <th>Rank</th>
                <th>Donor Name</th>
                <th>Total Donation Amount</th>
              </tr>
            </thead>

            <tbody>
            <?php 
            $rank = 1;
            if ($topDonors && $topDonors->num_rows > 0): 
                foreach ($topDonors as $td): ?>
                  <tr>
                      <td><b><?= $rank++ ?></b></td>
                      <td><?= htmlspecialchars($td['full_name']) ?></td>
                      <td>₱<?= htmlspecialchars($td['total_donated']) ?></td>
                  </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" style="color: gray; padding-block: 10px;">No top donors yet. Donate now to become one of our top donors.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
      </div>
      
      <div class='donation-card'>
                
        </div>
        <?php
        $conn = connect();
        $query = "SELECT dpost_id, title, description, post_img, goal_amount, current_amount FROM donation_post";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $id = $row['dpost_id'];
            $title = $row['title'];
            $desc = $row['description'];
            $img = $row['post_img'];
            $goal = $row['goal_amount'];
            $progress = $row['current_amount'];
            $prog_lvl = getPercentage($progress, $goal);

            echo "
              <div class='donation-card'>
                <img src='Assets/UserGenerated/$img' class='card-img'>
                <div class='card-details'>
                  <h4 class='card-title text-lg font-bold mb-1'>$title</h4>
                  <p class='card-description'>$desc</p>
                  <div class='donation-progress-group'>
                    <div class='progress-bar'>
                      <div class='progress-level' style='width: $prog_lvl%'></div>
                    </div>
                    <p class='card-text donation-goal'>Goal: ₱$progress / ₱$goal ($prog_lvl%)</p>
                  </div>
                  <a href='monetary_donation.php?donationId=$id'>
                    <button class='donate-card-btn'>Donate Now</button>
                  </a>
                </div>
              </div>";
          }
        } else {
          echo "<p>No donation posts available yet.</p>";
        }
        $conn->close();
        ?>
      </div>
    </article>
  </div>

  <!-- FOOTER -->
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

    function closeModal() {
      modal.style.display = "none";
    }

    function logout() {
      fetch('includes/logout.php').then(() => window.location.href = 'login.php');
    }

    window.addEventListener('click', e => { if (e.target === modal) closeModal(); });
  </script>
</body>
</html>
