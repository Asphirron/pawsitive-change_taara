<?php
include '../includes/db_connection.php';
$conn = connect();

// Get filters
$search = isset($_GET['search']) ? "%".$_GET['search']."%" : "%%";
$type = $_GET['type'] ?? '';
$breed = $_GET['breed'] ?? '';
$gender = $_GET['gender'] ?? '';
$behavior = $_GET['behavior'] ?? '';
$age = $_GET['age'] ?? '';

$query = "SELECT * FROM animal WHERE status='At a Shelter'";
$params = [];
$types = "";

// Add filters dynamically
if (!empty($type)) { $query .= " AND type = ?"; $params[] = $type; $types .= "s"; }
if (!empty($breed)) { $query .= " AND breed = ?"; $params[] = $breed; $types .= "s"; }
if (!empty($gender)) { $query .= " AND gender = ?"; $params[] = $gender; $types .= "s"; }
if (!empty($behavior)) { $query .= " AND behavior = ?"; $params[] = $behavior; $types .= "s"; }

// Age filtering
if (!empty($age)) {
  if ($age == "0-2") $query .= " AND age BETWEEN 0 AND 2";
  elseif ($age == "3-5") $query .= " AND age BETWEEN 3 AND 5";
  elseif ($age == "6+") $query .= " AND age >= 6";
}

// Search condition
$query .= " AND (name LIKE ? OR breed LIKE ? OR type LIKE ?)";
$params[] = $search;
$params[] = $search;
$params[] = $search;
$types .= "sss";

$stmt = $conn->prepare($query);
if (!empty($params)) {
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $id = $row['animal_id'];
    $name = htmlspecialchars($row['name']);
    $breed = htmlspecialchars($row['breed']);
    $type = htmlspecialchars($row['type']);
    $age = htmlspecialchars($row['age']);
    $gender = htmlspecialchars($row['gender']);
    $behavior = htmlspecialchars($row['behavior']);
    $img = htmlspecialchars($row['img']);

    echo "
    <div class='bg-gray-50 rounded-lg shadow-md p-4'>
      <img src='../Assets/Pets/$img' alt='$name' class='w-full h-40 object-cover rounded-md mb-3'>
      <h2 class='text-xl font-bold'>$name</h2>
      <p class='text-gray-700'>Type: $type</p>
      <p class='text-gray-700'>Breed: $breed</p>
      <p class='text-gray-700'>Gender: $gender</p>
      <p class='text-gray-700'>Age: $age yrs</p>
      <p class='text-gray-700'>Behavior: $behavior</p>
      <div class='flex gap-2 mt-3'>
        <button onclick='editForm($id, \"$name\", \"$breed\", $age, \"$img\")' class='bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600'>Edit</button>
        <button onclick='deleteAnimal($id)' class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600'>Delete</button>
      </div>
    </div>";
  }
} else {
  echo "<p class='text-gray-500'>No animals found.</p>";
}

$stmt->close();
$conn->close();
?>
