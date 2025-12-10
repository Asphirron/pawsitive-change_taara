<?php
include 'ui_elements.php';

$rescueDB = new DatabaseCRUD('rescue_report');
$rescueTable = $rescueDB->runQuery(
"SELECT * FROM rescue_report WHERE MONTH(date_updated) = ?",[date('n')],"s");

//STATS for Showing
$cases = $rescued = $lost = $found = 0;

foreach($rescueTable as $r){
    $cases++;
    switch($r['type']){
        case 'rescue':
            if($r['status'] == 'resolved') $rescued++;
            break;
        case 'lost_and_found':
            if($r['status'] == 'resolved') $found++;
            else $lost++;
            break;
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


    <style>
    /* Stat Cards Grid */
    .stat-card.container {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1.2rem;
      margin-top: 1.2rem;
    }

    /* Individual Stat Card */
    .stat-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 1.2rem;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }

    .stat-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #333;
      margin-bottom: 0.3rem;
    }

    .stats-number {
      font-size: 2.5rem;
      font-weight: bold;
      color: #e63946; /* a bold accent color */
      margin-bottom: 0.3rem;
    }

    .stat-card small {
      font-size: 0.70rem;
      color: #666;
    }


    </style>
  </head>

  <body>

  <?php displayHeader('rescue') ?>

  <div class="content-area">

    <article style="flex-wrap: wrap-reverse;" class="hero-section" style='width: 80%;'>
        <img src="Assets/Images/volunteer_banner.jpg" class="hero-section-img" style="height: 600px; width: 700px">
    <div class="hero-section-details">
        <h1 class="hero-section-header">Rescue Animals, Restore Hope</h1>
        <h5 class="hero-section-subheader">Every report brings us closer to saving lives and reuniting families.</h5>

        <div class='stat-card container'>
          <div class='stat-card'>
            <b class='stat-title'>Cases</b>
            <p class='stats-number'><?php echo $cases; ?></p>
            <small>Total rescue and lost/found reports this month</small>
          </div>

          <div class='stat-card'>
            <b class='stat-title'>Rescued</b>
            <p class='stats-number'><?php echo $rescued; ?></p>
            <small>Animals successfully rescued this month</small>
          </div>

          <div class='stat-card'>
            <b class='stat-title'>Lost</b>
            <p class='stats-number'><?php echo $lost; ?></p>
            <small>Pets still missing this month</small>
          </div>

          <div class='stat-card'>
            <b class='stat-title'>Found</b>
            <p class='stats-number'><?php echo $found; ?></p>
            <small>Lost pets reunited with families this month</small>
          </div>
        </div>

        <button class="hero-section-btn" onclick="window.location.href='rescue_reporting.php'">Report a Case</button>
    </div>

    </article>

  </div>
  <!-- FOOTER -->
  <?php displayFooter(); ?>
    
    
  </body>
</html>