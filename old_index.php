<?php
include "includes/db_connection.php";
session_start();

$email;
$uid = $username = $user_type = $prof_img = "";

if (isset($_SESSION["email"])){
    $email = @$_SESSION["email"];
    $conn = connect();
    $query = "SELECT * FROM user WHERE email=\"$email\" ";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $uid = $row["user_id"];
        $username = $row["username"];
        $user_type = $row["user_type"];
        $prof_img = $row["profile_img"];

        //echo $uid; echo $username; echo $user_type; echo $prof_img;

    }
    $conn->close();
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAARA - Bicolandia’s Voice for the Voiceless</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }
    body {
      background: #f9f9f9;
      color: #222;
      line-height: 1.6;
    }
    a {
      text-decoration: none;
      color: inherit;
    }
    img {
      max-width: 100%;
      display: block;
    }


    header {
      background: #fff;
      padding: 1rem 5%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    header img {
      height: 55px;
    }
    .nav-container {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    nav ul {
      display: flex;
      gap: 20px;
      list-style: none;
    }
    nav a {
      font-weight: bold;
      color: #222;
      transition: color 0.3s;
    }
    nav a:hover {
      color: hwb(240 4% 63%);
    }
    .login-btn {
      width: 35px;
      height: 35px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 55%;
      background: #2b2ba1;
      color: #fff;
      font-size: 1rem;
      transition: .3s;
    }
    .login-btn:hover {
      background:#1c1c7d;
      transform: scale(1.05);
    }


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
      background:hwb(240 4% 63%);
      color: #fff;
      border: none;
      padding: 1rem 2.5rem;
      border-radius: 50px;
      cursor: pointer;
      font-weight: bold;
      font-size: 1rem; 
    }
    .hero-text button:hover {
      background: #1c1c7d;
    }
    .hero img {
      max-width: 350px;
      border-radius: 12px;
    }

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
      color: hwb(240 4% 63%);
    }

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
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .services {
      padding: 3rem 5%;
      text-align: center;
    }
    .services h2 {
      color: hwb(240 4% 63%);
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
    }
    .service-list i {
      font-size: 2rem;
      margin-bottom: 0.8rem;
      color: #e83e8c;
      display: block;
    }
    .service-list h3 {
      margin-bottom: 0.5rem;
      color: #222;
    }

    .stats {
      padding: 2rem 5%;
      display: flex;
      justify-content: space-around;
      background: hwb(240 4% 63%);
      color: #fff;
      text-align: center;
      flex-wrap: wrap;
      gap: 1rem;
    }
    .stats div {
      font-size: 1.2rem;
    }
    .stats span {
      display: block;
      font-size: 2rem;
      font-weight: bold;
    }

    .mission {
      padding: 3rem 5%;
      text-align: center;
      background: #fff;
    }
    .mission h2 {
      color: #040446;
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
    }
    .mission-cards img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }
    .mission-cards .card-content {
      padding: 1rem;
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
    }
    .mission-cards .read-btn:hover {
      background: #2b2ba1;
      color: #fff;
    }

    footer {
      background: #1c1c1c;
      color: #fff;
      padding: 1.5rem 5%;
      text-align: center;
    }
    footer a {
      color: #fff;
      margin: 0 8px;
    }
  </style>
</head>
<body>

  <header>
    <img src="C:\Users\eugene castillo\Desktop\TAARA_CODES\IMAGES\taaralogo.jpg" alt="TAARA Logo">
    <div class="nav-container">
      <nav>
        <ul>
          <li><a href="index.php">About</a></li>
          <li><a href="rescue.php">Rescue</a></li>
          <li><a href="adoption.php">Adopt</a></li>
          <li><a href="donation.php">Donation</a></li>
          <li><a href="volunteer.php">Volunteer</a></li>
          <li><a href="events.php">Events</a></li>
          <?php if($user_type=='admin'){echo"<li><a href='Admin/index.html'>Go to Admin Page</a></li>"; }?>
        </ul>
      </nav>
      <a href="login.php" class="login-btn"><i class="fa-solid fa-user"></i></a>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero">
    <div class="hero-text">
      <h1>
        Create a <br>
        <span class="highlight">PAWSitive</span> Space <br>
        for Strays
      </h1>
      <p>"Their second chance starts with you—save a life, share your love, support the cause."</p>
      <button>Explore Now</button>
    </div>
    <img src="C:\Users\eugene castillo\Desktop\CAPSTONE\frontpic-removebg-preview.png" alt="Girl with Dog">
  </section>

  <!-- Quick Links -->
  <section class="quick-links">
    <div class="donate"><i class="fa-solid fa-hand-holding-heart"></i><p>Donate</p></div>
    <div class="volunteer"><i class="fa-solid fa-users"></i><p>Be a Volunteer</p></div>
    <div class="adopt"><i class="fa-solid fa-paw"></i><p>Adopt Me!</p></div>
    <div class="report"><i class="fa-solid fa-flag"></i><p>Report</p></div>
  </section>

  <!-- About -->
  <section class="about">
    <h2>TAARA — Bicolandia’s Voice for the Voiceless</h2>
    <p><strong>What is TAARA?</strong><br>
    TAARA (Tabaco Animal Advocates and Rescuers Association) is a dedicated animal welfare organization based in Tabaco City, Albay.</p>
    <p><strong>Mission and Vision of TAARA:</strong><br>
    To protect, rescue, and rehabilitate stray and abused animals while advocating for animal welfare and responsible pet ownership.</p>
    <p><strong>Ano ang mga Karaniwang mga Serbisyo na binibigay ng TAARA?</strong><br>
    Rescue, adoption, medical care, community outreach, and advocacy for animal welfare.</p>
    <p><strong>Address of TAARA:</strong><br>
    P-3 Burac St., San Lorenzo, Tabaco, Philippines</p>
  </section>
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

  <!-- Services -->
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

  <!-- Stats -->
  <section class="stats">
    <div><span>5000+</span>RESCUED</div>
    <div><span>3000+</span>REHOMED</div>
    <div><span>100+</span>VOLUNTEERS</div>
  </section>

  <!-- Mission Section -->
  <section class="mission">
    <div class="mission-header">
      <h2>Fueling Our Mission: The Impact of Your Generosity</h2>
    </div>
    <div class="mission-cards">
      <div class="card">
        <img src="C:\Users\eugene castillo\Desktop\TAARA_CODES\IMAGES\bakuna.jpg" alt="Bakuna Program">
        <div class="card-content">
          <h3>Bakuna Program: Protecting Paws, One Vaccine at a Time</h3>
          <p>TAARA Bakuna Program provides free and affordable vaccinations to stray and rescued animals, helping prevent disease and ensuring a healthier community for all.</p>
          <a href="#" class="read-btn">Read more</a>
        </div>
      </div>
      <div class="card">
        <img src="C:\Users\eugene castillo\Desktop\TAARA_CODES\IMAGES\feeding.jpg" alt="Stray Feeding Program">
        <div class="card-content">
          <h3>Stray Feeding Program</h3>
          <p>TAARA conducts feeding drives to ensure strays get the nourishment they need—bringing comfort, hope, and survival to animals waiting for rescue.</p>
          <a href="#" class="read-btn">Read more</a>
        </div>
      </div>
      <div class="card">
        <img src="C:\Users\eugene castillo\Desktop\CAPSTONE\project2.jpg" alt="Veterinary Care for Rescues">
        <div class="card-content">
          <h3>Veterinary Care for Rescues</h3>
          <p>Every rescued animal receives veterinary diagnosis and treatment to ensure they recover safely and are prepared for adoption into loving homes.</p>
          <a href="#" class="read-btn">Read more</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>TAARA located at P-3 Burac St., San Lorenzo, Tabaco, Philippines</p>
    <p>
      <a href="#"><i class="fa-brands fa-facebook"></i> Facebook</a> | 
      <a href="tel:09055238105"><i class="fa-solid fa-phone"></i> 0905 523 8105</a>
    </p>
  </footer>

</body>
</html>