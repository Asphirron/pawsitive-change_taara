<?php
include "db_connection.php";

$animalDB = new DatabaseCRUD("animal");

$where = ["status" => "At a Shelter"];

if (!empty($_GET['type'])) $where["type"] = $_GET['type'];
if (!empty($_GET['breed'])) $where["breed"] = $_GET['breed'];
if (!empty($_GET['gender'])) $where["gender"] = $_GET['gender'];
if (!empty($_GET['behavior'])) $where["behavior"] = $_GET['behavior'];

$all = $animalDB->select(["*"], $where);
$filtered = [];

if (!empty($_GET['age'])) {
  foreach ($all as $row) {
    if ((int)$row['age'] >= (int)$_GET['age']) {
      $filtered[] = $row;
    }
  }
} else {
  $filtered = $all;
}

if (empty($filtered)) {
  echo "<p class='text-center col-span-full text-gray-600'>No animals found matching your criteria.</p>";
  exit;
}

foreach ($filtered as $row) {
  echo "
  <div class='animal-card'>
    <img src='Assets/Pets/{$row['img']}' alt='{$row['name']}'>
    <div class='animal-info'>
      <h3>{$row['name']}</h3>
      <p>Type: {$row['type']}</p>
      <p>Breed: {$row['breed']}</p>
      <p>Gender: {$row['gender']}</p>
      <p>Age: {$row['age']} years</p>
      <p>Behavior: {$row['behavior']}</p>
      <a href='adoption-application.php?adoptionId={$row['animal_id']}'>
        <button class='adopt-btn'>Apply for Adoption</button>
      </a>
    </div>
  </div>";
}
?>
