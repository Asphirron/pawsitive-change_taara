<?php
include 'ui_elements.php';
setActivePage('volunteer');
displayUI("header");
$displayHeader = true;

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
    <script src="functions.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Home</title>
  </head>

  <body>

  <?php ?>

  <div class="content-area">

    <article style="flex-wrap: wrap-reverse;" class="hero-section">
        <div class="hero-section-details">
            <h1 class="hero-section-header">Hello Volunteers!</h1>
            <h5 class="hero-section-subheader">Be a hero for the voiceless — volunteer with TAARA and make tails wag with hope!"</h5>
            <p class="hero-section-text">TAARA, a community-driven initiative devoted to animal welfare in Tabaco City. We're on the lookout for dedicated volunteers who share our passion for animals and want to make a meaningful difference. Our core activities revolve around Monthly Stray Feeding Program, Pound-to-Adopter Initiative, and Fostering Programs.</p>
            <p class="hero-section-text">If you're passionate about animals and eager to make a difference, this is an incredible opportunity to enhance the well-being and welfare of our beloved four-legged friends. Join us, and together, we can make a positive impact on the lives of our furry companions. Your involvement can truly make a meaningful impact on their lives. Let's work together to make a positive change!</p>
            <button class="hero-section-btn"><a href="volunteer_application.php" style="color: var(--color-text-secondary);">Join Now</a></button>
        </div>
        <img src="Assets/Images/volunteer_banner.jpg" class="hero-section-img">
    </article>

  </div>
  <!-- FOOTER -->
  <?php displayUI('footer'); ?>
    
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
    </script>
  </body>
</html>