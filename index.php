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
    //  Extract first row from array
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
  <title>TAARA - Bicolandia’s Voice for the Voiceless</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="CSS/essentials.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    

    /* HERO */
    .hero {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 4rem 5%;
      background: #fff;
      flex-wrap: wrap;
    }
    .hero-text h1 {
      font-size: 3.5rem;
      color: #222;
    }
    .hero-text h1 .highlight {
      color: #e83e8c;
    }
    .hero-text p {
      margin: 1rem 0;
      font-size: 1.1rem;
      color: #555;
    }
    .hero-text button {
      background: #2b2ba1;
      color: #fff;
      border: none;
      padding: 1rem 2.5rem;
      border-radius: 50px;
      cursor: pointer;
      font-weight: bold;
      font-size: 1rem; 
      transition: 0.3s;
    }
    .hero-text button:hover {
      background: #1c1c7d;
      transform: scale(1.05);
    }
    .hero img {
      max-width: 350px;
      border-radius: 12px;
    }

    /* QUICK LINKS */
    .quick-links {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1.5rem;
      text-align: center;
      padding: 2rem 5%;
    }
    .quick-links div {
      background: #fff;
      padding: 1.5rem;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      transition: 0.3s;
      cursor: pointer;
    }
    .quick-links div:hover {
      transform: translateY(-5px);
      background: #f0f0ff;
    }
    .quick-links i {
      font-size: 2rem;
      margin-bottom: 0.8rem;
    }
    .quick-links .donate i,
    .quick-links .volunteer i {
      color: #e83e8c;
    }
    .quick-links .adopt i,
    .quick-links .report i {
      color: #2b2ba1;
    }

    /* ABOUT */
    .about {
      padding: 3rem 5%;
      background: #fdfdfd;
    }
    .about h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #2b2ba1;
    }
    .about p {
      margin-bottom: 1rem;
      color: #444;
    }

    /* LOCATION */
    .location {
      padding: 3rem 5%;
      background: #fff;
      text-align: center;
    }
    .location h2 {
      color: #2b2ba1;
      margin-bottom: 1rem;
    }
    .map-container {
      max-width: 900px;
      margin: 0 auto;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    /* SERVICES */
    .services {
      padding: 3rem 5%;
      text-align: center;
    }
    .services h2 {
      color: #2b2ba1;
      margin-bottom: 2rem;
    }
    .service-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
    }
    .service-list div {
      background: #e7ecec;
      padding: 1.5rem;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      transition: 0.3s;
    }
    .service-list div:hover {
      transform: scale(1.05);
      background: #f0f0ff;
    }
    .service-list i {
      font-size: 2rem;
      margin-bottom: 0.8rem;
      color: #e83e8c;
      display: block;
    }

    /* STATS */
    .stats {
      padding: 2rem 5%;
      display: flex;
      justify-content: space-around;
      background: #2b2ba1;
      color: #fff;
      text-align: center;
      flex-wrap: wrap;
      gap: 1rem;
    }
    .stats div {
      font-size: 1.2rem;
      transition: 0.3s;
    }
    .stats div:hover {
      transform: scale(1.1);
    }
    .stats span {
      display: block;
      font-size: 2rem;
      font-weight: bold;
    }

    /* MISSION */
    .mission {
      padding: 3rem 5%;
      text-align: center;
      background: #fff;
    }
    .mission h2 {
      color: #2b2ba1;
      margin-bottom: 1rem;
      font-size: 1.8rem
    }
    .mission-header {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 4rem;
      margin-bottom: 2rem;
      flex-wrap: wrap;
    }
    .mission-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
    }
    .mission-cards .card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      overflow: hidden;
      text-align: left;
      transition: 0.3s;
    }
    .mission-cards .card:hover {
      transform: translateY(-5px);
    }
    .mission-cards img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }
    .mission-cards .card-content {
      padding: 1rem;
    }
    .mission-cards .card-content:last-child{
      margin-top: auto;
    }
    .mission-cards h3 {
      color: #222;
      font-size: 1.1rem;
      margin-bottom: 0.5rem;
    }
    .mission-cards p {
      font-size: 0.95rem;
      color: #555;
      margin-bottom: 0.8rem;
    }
    .mission-cards .read-btn {
      display: inline-block;
      font-size: 0.9rem;
      font-weight: bold;
      color: #2b2ba1;
      border: 1px solid #2b2ba1;
      padding: 0.3rem 0.8rem;
      border-radius: 6px;
      transition: 0.3s;
    }
    .mission-cards .read-btn:hover {
      background: #2b2ba1;
      color: #fff;
      transform: scale(1.05);
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
          <li><a class='active' href="index.php">About</a></li> <!-- About moved to last -->
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

  <!-- HERO -->
  <section class="hero">
    <div class="hero-text">
      <h1>
        Create a <br>
        <span class="highlight">PAWSitive</span> Space <br>
        for Strays
      </h1>
      <p>"Their second chance starts with you—save a life, share your love, support the cause."</p>
      <a href="#intro"><button>Explore Now</button></a>
    </div>
    <img src="Assets/UI/frontpic-removebg-preview.png" alt="Girl with Dog">
  </section>

  <!-- QUICK LINKS -->
  <section class="quick-links">
    <div class="donate" onclick="redirect('donation.php')"><i class="fa-solid fa-hand-holding-heart"></i><p>Donate</p></div>
    <div class="volunteer" onclick="redirect('volunteer.php')"><i class="fa-solid fa-users"></i><p>Be a Volunteer</p></div>
    <div class="adopt" onclick="redirect('adoption.php')"><i class="fa-solid fa-paw"></i><p>Adopt Me!</p></div>
    <div class="report" onclick="redirect('rescue.php')"><i class="fa-solid fa-flag"></i><p>Report</p></div>
  </section>

  <!-- ABOUT -->
  <section class="about">
    <h2 id="intro">TAARA — Bicolandia’s Voice for the Voiceless</h2>
    <p><strong>What is TAARA?</strong><br>
    TAARA (Tabaco Animal Advocates and Rescuers Association) is a dedicated animal welfare organization based in Tabaco City, Albay.</p>
    <p><strong>Mission and Vision of TAARA:</strong><br>
    To protect, rescue, and rehabilitate stray and abused animals while advocating for animal welfare and responsible pet ownership.</p>
    <p><strong>Ano ang mga Karaniwang mga Serbisyo na binibigay ng TAARA?</strong><br>
    Rescue, adoption, medical care, community outreach, and advocacy for animal welfare.</p>
    <p><strong>Address of TAARA:</strong><br>
    P-3 Burac St., San Lorenzo, Tabaco, Philippines</p>
  </section>

  <!-- LOCATION -->
  <section class="location">
    <h2>📍Find Us Here</h2>
    <div class="map-container">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3882.1746199749728!2d123.7277168741907!3d13.339411487010771!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a1ac48307d60d7%3A0x98e696207e232d9d!2sp%2C%203%20Burac%20St%2C%20Tabaco%20City%2C%20Albay!5e0!3m2!1sen!2sph!4v1759390061227!5m2!1sen!2sph" 
        width="100%" 
        height="400" 
        style="border:0;" 
        allowfullscreen="" 
        loading="lazy" 
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </section>

  <!-- SERVICES -->
  <section class="services">
    <h2>What Does TAARA Do?</h2>
    <div class="service-list">
      <div>
        <i class="fa-solid fa-headset"></i>
        <h3>Animal Reporting and Rescue Services</h3>
        <p>Provides essential services such as rescue for lost, found, or injured animals ensuring immediate response, proper care, and safe recovery.</p>
      </div>
      <div>
        <i class="fa-solid fa-gift"></i>
        <h3>Acceptance of Donations</h3>
        <p>Accepts financial support and items like food, medicine, and supplies for rescue, care, and rehabilitation.</p>
      </div>
      <div>
        <i class="fa-solid fa-handshake"></i>
        <h3>Partnerships</h3>
        <p>Collaborates with groups and organizations to strengthen rescue, adoption, and advocacy programs.</p>
      </div>
      <div>
        <i class="fa-solid fa-house-chimney"></i>
        <h3>Animal Adoption</h3>
        <p>Facilitates secure and compassionate adoption of rescued animals into loving homes.</p>
      </div>
      <div>
        <i class="fa-solid fa-chalkboard-teacher"></i>
        <h3>Educational Campaigns</h3>
        <p>Organizes seminars on animal welfare, responsible ownership, and rescue procedures.</p>
      </div>
      <div>
        <i class="fa-solid fa-bullhorn"></i>
        <h3>Events and Fundraising</h3>
        <p>Conducts adoption drives, fundraising, and outreach to raise awareness and support for animal welfare.</p>
      </div>
    </div>
  </section>

  <!-- STATS -->
  <section class="stats">
    <div><span>5000+</span>RESCUED</div>
    <div><span>3000+</span>REHOMED</div>
    <div><span>100+</span>VOLUNTEERS</div>
  </section>

  <!-- MISSION -->
  <section class="mission">
    <div class="mission-header">
      <h2>Fueling Our Mission: The Impact of Your Generosity</h2>
    </div>
    <div class="mission-cards">
      <div class="card">
        <img src="Assets/Images/bakuna.jpg" alt="Bakuna Program">
        <div class="card-content">
          <h3>Bakuna Program: Protecting Paws, One Vaccine at a Time</h3>
          <p>TAARA Bakuna Program provides free and affordable vaccinations to stray and rescued animals, helping prevent disease and ensuring a healthier community for all.</p>
          <a href="#" class="read-btn">Read more</a>
        </div>
      </div>
      <div class="card">
        <img src="Assets/Images/feeding.jpg" alt="Stray Feeding Program">
        <div class="card-content">
          <h3>Stray Feeding Program</h3>
          <p>TAARA conducts feeding drives to ensure strays get the nourishment they need—bringing comfort, hope, and survival to animals waiting for rescue.</p>
          <a href="#" class="read-btn">Read more</a>
        </div>
      </div>
      <div class="card">
        <img src="Assets/Images/project2.jpg" alt="Veterinary Care for Rescues">
        <div class="card-content">
          <h3>Veterinary Care for Rescues</h3>
          <p>Every rescued animal receives veterinary diagnosis and treatment to ensure they recover safely and are prepared for adoption into loving homes.</p>
          <a href="#" class="read-btn">Read more</a>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <p>TAARA located at P-3 Burac St., San Lorenzo, Tabaco, Philippines</p>
    <p>
      <a href="#"><i class="fa-brands fa-facebook"></i> Facebook</a> | 
      <a href="tel:09055238105"><i class="fa-solid fa-phone"></i> 0905 523 8105</a>
    </p>
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
      <button onclick="window.location.href='user_adoptions.php'">Your Adoptions</button>
      <button onclick="window.location.href='user_donations.php'">Your Donations</button>
      <button onclick="window.location.href='user_reports.php'">Your Reports</button>
      <button class="close-btn" onclick="closeModal()">Close</button>
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
      fetch('includes/logout.php')
        .then(() => window.location.href = 'login.php');
    }

    window.addEventListener('click', function(e) {
      if (e.target === modal) {
        closeModal();
      }
    });


    function redirect(url){
    window.location.href = url;
    }

  </script>
 

  

</body>
</html>