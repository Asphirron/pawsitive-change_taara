<?php
include 'ui_elements.php';

$rescueDB = new DatabaseCRUD('rescue_report');
$rescueTable = $rescueDB->runQuery(
"SELECT * FROM rescue_report WHERE MONTH(date_updated) = ?",[date('n')],"s");

//STATS for Showing
$cases = $rescued = $lost = $found = '';

if(empty($rescueTable)){
  $cases = 'nthing';
}

foreach($rescueTable as $r){
  $cases++;
  if($r['type' === 'rescue'] && $r['status'] === 'resolved'){
    $rescued++;
  }elseif('type' == 'lost_and_found'){
    if($r['status'] == 'resolved'){
      $found++;
    }
    else{
      $lost++;
    }
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
    <script src="functions.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Home</title>
  </head>

  <body>

  <?php displayHeader('rescue') ?>

  <div class="content-area">

    <article style="flex-wrap: wrap-reverse;" class="hero-section">
        <img src="Assets/Images/volunteer_banner.jpg" class="hero-section-img">
        <div class="hero-section-details">
            <h1 class="hero-section-header">Hello Volunteers!</h1>
            <h5 class="hero-section-subheader">Be a hero for the voiceless — volunteer with TAARA and make tails wag with hope!"</h5>
            <div class='stat-card container'>
              <div class='stat-card'>
                <b class='stat-title'>Cases</b>
                <p class='stats-number'><?php $cases ?></p>
                <small>Total number of rescue reports and lost/found this month</small>
              </div>

              <div class='stat-card'>
                <b class='stat-title'>Rescued</b>
                <p class='stats-number'><?php $rescued ?></p>
                <small>Total number of pets rescued this month</small>
              </div>

              <div class='stat-card'>
                <b class='stat-title'>Lost</b>
                <p class='stats-number'><?php echo $cases; ?></p>
                <small>Total number of lost animals this month</small>
              </div>

              <div class='stat-card'>
                <b class='stat-title'>Found</b>
                <p class='stats-number'><?php $cases ?></p>
                <small>Total number of found animals this month</small>
              </div>
              
            </div>
            <p class="hero-section-text">If you're passionate about animals and eager to make a difference, this is an incredible opportunity to enhance the well-being and welfare of our beloved four-legged friends. Join us, and together, we can make a positive impact on the lives of our furry companions. Your involvement can truly make a meaningful impact on their lives. Let's work together to make a positive change!</p>
            <button class="hero-section-btn" onclick="window.location.href='rescue_reporting.php'">Report Now</button>
        </div>
    </article>

  </div>
  <!-- FOOTER -->
  <?php displayFooter(); ?>
    
    
  </body>
</html>