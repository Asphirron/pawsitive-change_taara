<?php
include '../includes/db_connection.php';
$conn = connect();

$id = $_POST['id'];
$vaccine_type = $_POST['vaccine_type'];
$date_vaccinated = $_POST['date_vaccinated'];

$stmt = $conn->prepare("UPDATE vaccinations SET vaccine_type=?, date_vaccinated=? WHERE vaccination_id=?");
$stmt->bind_param("ssi", $vaccine_type, $date_vaccinated, $id);

if ($stmt->execute()) {
    echo "Vaccination updated successfully!";
} else {
    echo "Error updating record.";
}
$stmt->close();
$conn->close();
?>
