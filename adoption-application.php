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
    <title>Home</title>

    <style>
  body {
    min-height: 100%;
    background-color: #f8f9fa;
  }

  main {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 50px 0;
  }

  /* Container for banner + form */
  .application-container {
    width: 70%;
    background: white;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .banner {
    width: 100%;
    height: 200px;
    background-image: url('Assets/Images/rescue_banner.jpg');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 36px;
    font-weight: 800;
    letter-spacing: 1px;
    text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
  }

  form {
    width: 90%;
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 40px 0;
  }

  .form-row {
    display: flex;
    gap: 20px;
    width: 100%;
  }

  fieldset {
    border: none;
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: 100%;
  }

  label {
    font-weight: 600;
    color: #333;
  }

  input, select, textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    box-sizing: border-box;
  }

  textarea {
    resize: vertical;
    height: 80px;
  }

  .animal-card {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: flex-start;
    gap: 20px;
    border: 2px solid #ddd;
    border-radius: 12px;
    padding: 15px;
    background: #fdfdfd;
    width: 50%;
  }

  .pet-img-preview {
    width: 150px;
    height: 150px;
    border-radius: 10px;
    object-fit: cover;
  }

  .pet-info {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 14px;
    color: #444;
  }

  .pet-info .pet-detail {
    font-weight: 500;
  }

  span.button-row {
    display: flex;
    justify-content: center;
    gap: 20px;
    width: 100%;
  }

  button {
    flex: 1;
    height: 45px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s ease;
    font-size: 15px;
  }

  button:hover {
    opacity: 0.9;
  }

  .submit {
    background-color: #c9378a;
    color: white;
  }

  button:not(.submit) {
    background-color: #161670;
    color: white;
  }

  @media screen and (max-width: 900px) {
    form {
      width: 90%;
    }

    .form-row {
      flex-direction: column;
    }

    .animal-card {
      flex-direction: column;
      align-items: center;
      width: auto;
    }

    .pet-img-preview {
      width: 120px;
      height: 120px;
    }
  }
</style>


  </head>
  <body>
    <header class="site-header">
        <div class="nav-left">
            <img src="Assets/UI/Taara_Logo.webp" class="brand-logo">
            <p class="brand-name">TAARA</p>
        </div>

        <div class="nav-right">
            <a class="nav-link" href="index.php">About</a>
            <a class="nav-link" href="adoption.php">Adoption</a>
            <a class="nav-link" href="donation.php">Donation</a>
            <a class="nav-link" href="rescue.php">Rescue</a>
            <a class="nav-link" href="volunteer.php">Volunteer</a>
            <a class="nav-link" href="events.php">Events</a>
            <button class="user-action"><img src="Assets/UI/bell.png" class="user-action-img"></button>
            <button class="user-action"><img src="Assets/UI/settings.png" class="user-action-img"></button>
            <button class="user-action"><img src="Assets/UI/user_icon.png" class="user-action-img"></button>
        </div>
    </header>

    <main>
  <div class="application-container">
    <div class="banner">
      <h1>ADOPTION APPLICATION</h1>
    </div>

    <form action="adoption_screening.php" method="post" enctype="multipart/form-data">
      <?php
        if(isset($_GET['message'])){
          $message = @$_GET['message'];
          alert($message);
        }
        $data = getAnimalData(@$_GET['adoptionId']);
      ?>

      <!-- Pet info card -->
      <fieldset class="animal-card">
        <img class="pet-img-preview" src="Assets/Pets/<?php echo $data['img']; ?>">
        <div class="pet-info">
          <strong><p class="pet-detail">Pet to be Adopted</p></strong>
          <p class="pet-detail">Name: <?php echo $data['name'];?></p>
          <p class="pet-detail">Type: <?php echo $data['type'];?></p>
          <p class="pet-detail">Breed: <?php echo $data['breed'];?></p>
          <p class="pet-detail">Age: <?php echo $data['age'];?></p>
          <input type="hidden" value="<?php echo @$_GET['adoptionId'];?>" name="animal_id">
          <button type="button" onclick="window.location.href='adoption.php'">Change</button>
        </div>
      </fieldset>

      <fieldset>
        <p><strong>PLEASE SUBMIT ONLY VALID DATA</strong></p>
        <p>Providing false information leads to consequences as outlined in the Revised Penal Code (Act No. 3825).</p>
      </fieldset>

      <div class="form-row">
        <fieldset>
          <label>Full Name</label>
          <input type="text" name="full_name" placeholder="Enter your complete name" required>
        </fieldset>

        <fieldset>
          <label>Address</label>
          <input type="text" name="address" placeholder="Enter your address" required>
        </fieldset>
      </div>

      <div class="form-row">
        <fieldset>
          <label>Classification</label>
          <select name="classification" required>
            <option value="">Select Classification</option>
            <option value="employed">Employed</option>
            <option value="unemployed">Unemployed</option>
            <option value="self employed">Self Employed</option>
            <option value="student">Student</option>
          </select>
        </fieldset>

        <fieldset>
          <label>Company/School Name</label>
          <input type="text" name="comp_name" placeholder="Enter company/school name" required>
        </fieldset>
      </div>

      <fieldset>
        <label>Photo of your Valid ID</label>
        <input type="file" name="id_img" accept="image/*" required>
      </fieldset>

      <fieldset>
        <span class="button-row">
          <button type="reset" name="reset">Cancel</button>
          <button type="submit" class="submit" name="submit">Next</button>
        </span>
      </fieldset>
    </form>
  </div>
</main>



    <footer class="site-footer">
        <div class="footer-content">
            <img src="Assets/UI/facebook.png" class="footer-content-img">
            <h3 class="footer-content-text">Facebook</h3>
        </div>
        <div class="footer-content">
            <img src="Assets/UI/phone.png" class="footer-content-img">
            <h3 class="footer-content-text">09055238105</h3>
        </div><div class="footer-content">
            <h3 class="footer-content-text">Tabaco Animal Advocates and Rescuers Association - All rights reserved</h3>
        </div>
    </footer>
  </body>
</html>