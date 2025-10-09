<?php
include '../includes/db_connection.php';
$conn = connect();

$id = $_POST['id'];

$stmt = $conn->prepare("DELETE FROM vaccinations WHERE vaccination_id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Vaccination deleted successfully!";
} else {
    echo "Error deleting vaccination.";
}
$stmt->close();
$conn->close();
?>
