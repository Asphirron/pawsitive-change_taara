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


function displayHeader($activePage){

  echo "
  <header>
  <img src='Assets/UI/taaralogo.jpg' alt='TAARA Logo'>
  <div class='nav-container'>
  <nav>
      <ul>
      <li><a href='rescue.php' id= 'rescue' class='($activePage == 'rescue' ? 'active' : '')'>Rescue</a></li>
      <li><a href='adoption.php' id='adoption' class='($activePage == 'adoption' ? 'active' : '')>Adopt</a></li>
      <li><a href='donation.php' id='donation' class='($activePage == 'donation' ? 'active' : '')>Donation</a></li>
      <li><a href='volunteer.php' id='volunteer' class='($activePage == 'volunteer' ? 'active' : '')>Volunteer</a></li>
      <li><a href='events.php' id='events' class='($activePage == 'events' ? 'active' : '')>Events</a></li>
      <li><a href='index.php' id='index' class='($activePage == 'index' ? 'active' : '')>About</a></li>
      </ul>
  </nav>";

  echo "
      <script>
      document.getElementById('$activePage').addClass('active');
      </script>
  ";


  if ($logged_in) {
      echo "<img src='Assets/Profile_Images/$user_img' class='profile-img' id='user_profile'>";
  } else {
      echo "<a href='register.php' class='bg-pink-600 text-white px-4 py-2 rounded-full font-bold hover:bg-pink-700 flex items-center gap-2'>
              <i class='fa-solid fa-user-plus'></i> Register
          </a>";
  }

  echo "</header>";

}



function displayFooter(){

  echo " 
  <footer>
  <p>TAARA located at P-3 Burac St., San Lorenzo, Tabaco, Philippines</p>
  <p><a href='#'><i class='fa-brands fa-facebook'></i> Facebook</a> | 
  <a href='tel:09055238105'><i class='fa-solid fa-phone'></i> 0905 523 8105</a></p>
  </footer>";
            
}

  ?>