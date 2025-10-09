<?php
include '../includes/db_connection.php';
$conn = connect();

$animal_id = $_POST['animal_id'];
$vaccine_type = $_POST['vaccine_type'];
$date_vaccinated = $_POST['date_vaccinated'];

$stmt = $conn->prepare("INSERT INTO vaccinations (animal_id, vaccine_type, date_vaccinated) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $animal_id, $vaccine_type, $date_vaccinated);

if ($stmt->execute()) {
    echo "Vaccination added successfully!";
} else {
    echo "Error adding vaccination.";
}
$stmt->close();
$conn->close();
?>
