<?php
include "db_connection.php";

if(isset($_POST['gcash_donate'])){
  $conn = connect();
  $img = 'no proof provided';
  $m_donation_id = $_POST['donation_id'];

  if (!empty($_FILES['img']['name'])) {
    $target_dir = "../Assets/UserGenerated/";
    $file_name = time() . "_" . basename($_FILES['img']['name']);
    $target_file = $target_dir . $file_name;
    if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
        $img = $file_name;

    }
  }
  
  $query = "UPDATE monetary_donation SET proof=? WHERE m_donation_id=?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("si", $img, $m_donation_id);
  $stmt->execute();
  $stmt->close();
  $conn->close();
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Donation Successful</title>
  <style>
    body { font-family: Arial; text-align:center; padding:50px; }
    h1 { color: green; }
    .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background: #0077ff;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
    }
    .btn:hover { background: #005fcc; }
  </style>
</head>
<body>
  <h1>Thank You for Your Donation!</h1>
  <p>Your donation has been received successfully.</p>
  <a href="../donation.php" class="btn">Return</a>
</body>
</html>
