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
    <title>Home</title>
    <style>
    body {
      background-color: #fafafa;
    }

    .filter-bar {
      background: #fff;
      padding: 1rem;
      margin: 1rem auto;
      border-radius: 12px;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-around;
      gap: 1rem;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      max-width: 1200px;
    }

    .filter-bar select, .filter-bar input, .filter-bar datalist {
      border: 1px solid #ccc;
      padding: 0.5rem 0.8rem;
      border-radius: 6px;
      min-width: 150px;
    }

    .filter-bar button {
      background-color: #2b2ba1;
      color: white;
      padding: 0.6rem 1.5rem;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s ease-in;
    }

    .filter-bar button:hover {
      background-color: #1e1e80;
    }

    .animal-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1.5rem;
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
    }

    .animal-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      overflow: hidden;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .animal-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }

    .animal-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
    }

    .animal-info {
      padding: 1rem;
    }

    .animal-info h3 {
      margin: 0;
      font-size: 1.2rem;
      font-weight: 700;
    }

    .animal-info p {
      margin: 0.2rem 0;
      font-size: 0.9rem;
      color: #555;
    }

    .adopt-btn {
      background: #e83e8c;
      border: none;
      color: white;
      width: 100%;
      padding: 0.6rem;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      margin-top: 0.6rem;
      transition: background 0.2s ease;
    }

    .adopt-btn:hover {
      background: #d03075;
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
          <li><a class='active' href="adoption.php">Adopt</a></li>
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
        echo "<a href='register.html' class='bg-pink-600 text-white px-4 py-2 rounded-full font-bold hover:bg-pink-700 flex items-center gap-2'>
                <i class='fa-solid fa-user-plus'></i> Register
              </a>";
      }
      ?>
    </div>
  </header>

      <!-- FILTER BAR -->
  
  <section class="filter-bar">
    <h1>Tell us what you want?</h1>
    <select id="filter-type">
      <option value="">All Types</option>
      <option value="Dog">Dog</option>
      <option value="Cat">Cat</option>
    </select>

    <input list="breed-options" id="filter-breed" placeholder="Select Breed">
    <datalist id="breed-options">
      <option value="Labrador">
      <option value="Poodle">
      <option value="Golden Retriever">
      <option value="Persian">
      <option value="Siamese">
      <option value="Bengal">
    </datalist>

    <select id="filter-age">
      <option value="">Any Age</option>
      <option value="1">1 year</option>
      <option value="2">2 years</option>
      <option value="3">3 years</option>
      <option value="4">4 years</option>
      <option value="5">5 years or older</option>
    </select>

    <select id="filter-gender">
      <option value="">Any Gender</option>
      <option value="Male">Male</option>
      <option value="Female">Female</option>
    </select>

    <select id="filter-behavior">
      <option value="">Any Behavior</option>
      <option value="Friendly">Friendly</option>
      <option value="Playful">Playful</option>
      <option value="Calm">Calm</option>
      <option value="Aggressive">Aggressive</option>
    </select>

    <button onclick="loadAnimals()">Search</button>
  </section>

  <!-- ANIMAL GRID -->
  <div id="animal-grid" class="animal-grid"></div>
   

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
      <button onclick="window.location.href='adoptions.php'">Your Adoptions</button>
      <button onclick="window.location.href='donations.php'">Your Donations</button>
      <button onclick="window.location.href='events.php'">Events</button>
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


    // Dynamically update breed options
  document.getElementById("filter-type").addEventListener("change", function() {
    const type = this.value;
    const datalist = document.getElementById("breed-options");
    datalist.innerHTML = "";

    if (type === "Dog") {
      datalist.innerHTML = `
        <option value="Labrador">
        <option value="Poodle">
        <option value="Golden Retriever">
        <option value="German Shepherd">
        <option value="Bulldog">`;
    } else if (type === "Cat") {
      datalist.innerHTML = `
        <option value="Persian">
        <option value="Siamese">
        <option value="Bengal">
        <option value="Ragdoll">
        <option value="Maine Coon">`;
    } else {
      datalist.innerHTML = `
        <option value="Labrador">
        <option value="Poodle">
        <option value="Golden Retriever">
        <option value="Persian">
        <option value="Siamese">`;
    }
  });

  async function loadAnimals() {
    const params = new URLSearchParams({
      type: document.getElementById("filter-type").value,
      breed: document.getElementById("filter-breed").value,
      age: document.getElementById("filter-age").value,
      gender: document.getElementById("filter-gender").value,
      behavior: document.getElementById("filter-behavior").value
    });

    const response = await fetch("includes/fetch_animals.php?" + params.toString());
    const data = await response.text();
    document.getElementById("animal-grid").innerHTML = data;
  }

  // Load all on start
  loadAnimals();
  </script>
  </body>
</html>