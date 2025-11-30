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

//GLOBAL VARIABLES
$activePage = '';
$displayHeader = true;
$displayFooter = false;

function setActivePage($active){
    $activePage = $active;
}

function displayUI($ui_element){

    switch($ui_element){
        case "header":
            $displayHeader = true;
            break;

        case "footer":
            echo " 
            <footer>
            <p>TAARA located at P-3 Burac St., San Lorenzo, Tabaco, Philippines</p>
            <p><a href='#'><i class='fa-brands fa-facebook'></i> Facebook</a> | 
            <a href='tel:09055238105'><i class='fa-solid fa-phone'></i> 0905 523 8105</a></p>
            </footer>";
            break;

        default:
            break;
    }
  
                
        

}

    if($displayHeader){
        echo "
            <header>
            <img src='Assets/UI/taaralogo.jpg' alt='TAARA Logo'>
            <div class='nav-container'>
            <nav>
                <ul>
                <li><a href='rescue.php' id= 'rescue'>Rescue</a></li>
                <li><a href='adoption.php' id='adoption'>Adopt</a></li>
                <li><a href='donation.php' id='donation'>Donation</a></li>
                <li><a href='volunteer.php' id='volunteer'>Volunteer</a></li>
                <li><a href='events.php' id='events'>Events</a></li>
                <li><a href='index.php' id='index'>About</a></li>
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


  /*<header>
    <img src="Assets/UI/taaralogo.jpg" alt="TAARA Logo">
    <div class="nav-container">
      <nav>
        <ul>
          <li><a <?php if($activePage === 'rescue'){ echo("class='active' ");} ?>href="rescue.php">Rescue</a></li>
          <li><a <?php if($activePage === 'adopt'){ echo("class='active' ");} ?>href="adoption.php">Adopt</a></li>
          <li><a <?php if($activePage === 'donation'){ echo("class='active' ");} ?>href="donation.php">Donation</a></li>
          <li><a <?php if($activePage === 'volunteer'){ echo("class='active' ");} ?>class='active' href="volunteer.php">Volunteer</a></li>
          <li><a <?php if($activePage === 'events'){ echo("class='active' ");} ?>href="events.php">Events</a></li>
          <li><a <?php if($activePage === 'index'){ echo("class='active' ");} ?> href="index.php">About</a></li>
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
  </header>*/
  ?>