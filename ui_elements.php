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
                echo "<a href='login.php' class='bg-pink-600 text-white px-4 py-2 rounded-full font-bold hover:bg-pink-700 flex items-center gap-2'>
                        <i class='fa-solid fa-user-plus'></i> Login or Signup
                    </a>";
            }

            echo "</header>";
    }

  echo `
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
  `;




  ?>


    

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
    </script>


   