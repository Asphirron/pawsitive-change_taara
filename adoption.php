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
    background: linear-gradient(135deg, #f8f9fc, #eef0ff);
    font-family: 'Poppins', sans-serif;
    color: #222;
    margin: 0;
    padding: 0;
  }

 

  /* === ADOPTION CONTAINER === */
  .adoption-container {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    max-width: 1200px;
    margin: 2rem auto;
    padding: 1.5rem 2rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  /* Gradient top border */
  .adoption-container::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(135deg, #c9378a, #8e2bb5);
    border-radius: 20px 20px 0 0;
  }

  /* Title & subtitle */
  .adoption-container h2 {
    font-size: 1.75rem;
    color: #2b2ba1;
    font-weight: 700;
    margin-bottom: 0.4rem;
  }

  .adoption-container p {
    color: #555;
    font-size: 0.95rem;
    max-width: 650px;
    margin: 0 auto 1.2rem;
    line-height: 1.5;
  }

  /* === FILTER SECTION === */
  .filter-wrapper {
    display: flex;
    justify-content: center;
    margin: 1.5rem auto 2rem;
  }

  .filter-box {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: flex-end;
    gap: 1rem 1.2rem;
    background: #f9f9fc;
    padding: 1.2rem 1.5rem;
    border-radius: 14px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    max-width: 700px;
    margin: 0 auto;
  }

  /* Each field container */
  .filter-field {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    min-width: 130px;
  }

  .filter-field label {
    font-size: 0.85rem;
    color: #444;
    margin-bottom: 0.35rem;
    font-weight: 500;
  }

  .filter-box select,
  .filter-box input {
    border: 1px solid #d5d5f5;
    border-radius: 8px;
    padding: 0.45rem 0.7rem;
    font-size: 0.9rem;
    background: #fff;
    transition: 0.2s ease;
    width: 100%;
  }

  .filter-box select:focus,
  .filter-box input:focus {
    border-color: #7a3cc1;
    box-shadow: 0 0 0 2px rgba(122, 60, 193, 0.15);
    outline: none;
  }

  /* === BUTTONS SIDE BY SIDE === */
  .filter-actions {
    display: flex;
    align-items: flex-end;
    gap: 0.6rem;
  }

  .filter-btn,
  .reset-btn {
    border: none;
    border-radius: 8px;
    padding: 0.45rem 1.2rem;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.85rem;
    transition: all 0.2s ease;
  }

  /* Search button (primary) */
  .filter-btn {
    background: linear-gradient(135deg, #2b2ba1, #6a2cb5);
    color: white;
  }

  .filter-btn:hover {
    background: linear-gradient(135deg, #3a3aa8, #7b37c6);
    transform: translateY(-1px);
  }

  /* Reset button (secondary) */
  .reset-btn {
    background: #f0f0f7;
    color: #444;
    border: 1px solid #ddd;
  }

  .reset-btn:hover {
    background: #e6e6f3;
    transform: translateY(-1px);
  }

  /* === CARD GRID === */
  .animal-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.6rem;
    margin-top: 2rem;
  }

  .animal-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #e3e3f7;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
  }

  .animal-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
  }

  .animal-card img {
    width: 100%;
    height: 210px;
    object-fit: cover;
  }

  .animal-info {
    padding: 1rem 1.2rem;
    text-align: left;
  }

  .animal-info h3 {
    margin-bottom: 0.4rem;
    font-size: 1.1rem;
    color: #2b2b85;
    font-weight: 600;
  }

  .animal-info p {
    margin: 0.25rem 0;
    font-size: 0.88rem;
    color: #555;
    line-height: 1.4;
  }

  .adopt-btn {
    background: linear-gradient(135deg, #c9378a, #8e2bb5);
    border: none;
    color: white;
    width: 90%;
    padding: 0.6rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.9rem;
    margin: 0.8rem auto 1rem;
    display: block;
    transition: 0.25s ease;
  }

  .adopt-btn:hover {
    background: linear-gradient(135deg, #b02f7a, #7a23a4);
    transform: translateY(-2px);
  }

  /* === RESPONSIVE DESIGN === */
  @media (max-width: 768px) {
    .filter-box {
      flex-direction: column;
      align-items: stretch;
      width: 90%;
      gap: 0.8rem;
    }

    .filter-actions {
      justify-content: center;
      margin-top: 0.5rem;
    }

    .filter-btn,
    .reset-btn {
      width: 45%;
    }

    .animal-grid {
      gap: 1rem;
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

<!-- FILTER DROPDOWN -->


<section class="adoption-container">
  <h2>Available Pets for Adoption</h2>
  <p>Find your new best friend! Use the filters below to narrow down pets based on your preferences.</p>

  <!-- Filter Box with Labels -->
<div class="filter-wrapper">
  <div class="filter-box">

    <div class="filter-field">
      <label for="filter-type">Type</label>
      <select id="filter-type">
        <option value="">All Types</option>
        <option value="Dog">Dog</option>
        <option value="Cat">Cat</option>
      </select>
    </div>

    <div class="filter-field">
      <label for="filter-breed">Breed</label>
      <input list="breed-options" id="filter-breed" placeholder="Enter breed...">
      <datalist id="breed-options">
        <option value="Labrador">
        <option value="Poodle">
        <option value="Golden Retriever">
        <option value="Persian">
        <option value="Siamese">
        <option value="Bengal">
      </datalist>
    </div>

    <div class="filter-field">
      <label for="filter-age">Age</label>
      <select id="filter-age">
        <option value="">Any Age</option>
        <option value="1">1 year</option>
        <option value="2">2 years</option>
        <option value="3">3 years</option>
        <option value="4">4 years</option>
        <option value="5">5+ years</option>
      </select>
    </div>

    <div class="filter-field">
      <label for="filter-gender">Gender</label>
      <select id="filter-gender">
        <option value="">Any Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>
    </div>

    <div class="filter-field">
      <label for="filter-behavior">Behavior</label>
      <select id="filter-behavior">
        <option value="">Any Behavior</option>
        <option value="Friendly">Friendly</option>
        <option value="Playful">Playful</option>
        <option value="Calm">Calm</option>
        <option value="Aggressive">Aggressive</option>
      </select>
    </div>

    <!-- Button row -->
    <div class="filter-actions">
      <button class="filter-btn" onclick="loadAnimals()">Search</button>
      <button class="reset-btn" onclick="resetFilters()">Reset</button>
    </div>

  </div>
</div>



  <div id="animal-grid" class="animal-grid"></div>
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
      <button onclick="window.location.href='adoptions.php'">Your Adoptions</button>
      <button onclick="window.location.href='donations.php'">Your Donations</button>
      <button onclick="window.location.href='events.php'">Events</button>
      <button class="close-btn" onclick="closeModal()">Close</button>
    </div>
  

<script>
function resetFilters() {
  document.getElementById("filter-type").value = "";
  document.getElementById("filter-breed").value = "";
  document.getElementById("filter-age").value = "";
  document.getElementById("filter-gender").value = "";
  document.getElementById("filter-behavior").value = "";
  loadAnimals(); // reload all animals
}
</script>


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