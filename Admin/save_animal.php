<?php
include '../includes/db_connection.php';
$conn = connect();

$animal_id = $_POST['animal_id'] ?? '';
$name = $_POST['name'];
$description = $_POST['description'];
$type = $_POST['type'];
$breed = $_POST['breed'];
$gender = $_POST['gender'];
$age = $_POST['age'];
$behavior = $_POST['behavior'];
$date_rescued = $_POST['date_rescued'];
$status = $_POST['status'];
$img = null;

// Handle image upload
if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
    $targetDir = "../Assets/Pets/";
    $fileName = basename($_FILES["img"]["name"]);
    $targetFile = $targetDir . $fileName;
    move_uploaded_file($_FILES["img"]["tmp_name"], $targetFile);
    $img = $fileName;
}

// UPDATE or INSERT
if (!empty($animal_id)) {
    // Edit existing record
    if ($img) {
        $query = "UPDATE animal SET name=?, description=?, type=?, breed=?, gender=?, age=?, behavior=?, date_rescued=?, status=?, img=? WHERE animal_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssissssi", $name, $description, $type, $breed, $gender, $age, $behavior, $date_rescued, $status, $img, $animal_id);
    } else {
        $query = "UPDATE animal SET name=?, description=?, type=?, breed=?, gender=?, age=?, behavior=?, date_rescued=?, status=? WHERE animal_id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssisssi", $name, $description, $type, $breed, $gender, $age, $behavior, $date_rescued, $status, $animal_id);
    }
    if ($stmt->execute()) echo "✅ Animal updated successfully!";
    else echo "❌ Failed to update animal.";
    $stmt->close();
} else {
    // Add new record
    $query = "INSERT INTO animal (name, description, type, breed, gender, age, behavior, date_rescued, status, img) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssissss", $name, $description, $type, $breed, $gender, $age, $behavior, $date_rescued, $status, $img);
    if ($stmt->execute()) echo "✅ New animal added successfully!";
    else echo "❌ Failed to add animal.";
    $stmt->close();
}

$conn->close();
?>
