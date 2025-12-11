<?php
// admin_dynamic.php
include "../includes/db_connection.php"; //Establishes database connection
include "../Admin/admin_ui.php"; //Displays Navigation

session_start();
if($_SERVER['REQUEST_METHOD'] === 'GET'){

    if(!isset($_GET['unset'])){
        unset($_SESSION['visibleColumns']);
    }
}else{
    unset($_SESSION['visibleColumns']);
}

// -------------------- CONFIG --------------------
$tableName = 'animal';   // Change this to your table
$pk = 'animal_id';       // Primary key column of the table

$crud = new DatabaseCRUD($tableName);
$tableData = $crud->readAll();

// Define fields and types for modal & table
$fieldsConfig = [
    'animal_id' => 'id',
    'name' => 'text',
    'description' => 'textarea',
    'type' => ['Dog','Cat','Other'],
    'breed' => 'text',
    'gender' => ['Male','Female'],
    'age' => 'number',
    'behavior' => ['Friendly','Playful','Calm','Aggressive','Timid'],
    'date_rescued' => 'date',
    'status' => ['At a Shelter','Adopted','Pending Adoption'],
    'img' => 'image'
];

$fieldLabels = [  //Sets the labels/table headers for modal/table
    //if blank then set to default but capitalized and underscore(_) removed
    'animal_id' => 'ID',
    'name' => 'Name',
    'description' => 'Description',
    'type' => 'Type',
    'breed' => 'Breed',
    'gender' => 'Gender',
    'age' => 'Age',
    'behavior' => 'Behavior',
    'date_rescued' => 'Date Rescued',
    'status' => 'Status',
    'img' => 'Picture'
];

$searchBy = 'name';

//properties shown in the filters
$filterConfig = ['type', 'gender', 'age', 'behavior', 'breed', 'status'];


$_SESSION['fields_config'] = $fieldsConfig; 

// Columns initially visible in table
$defaultColumns = ['img','name','type','breed','age','behavior', 'status'];


if(!isset($_SESSION['visibleColumns'])) {
    $_SESSION['visibleColumns'] = $defaultColumns;
}

$visibleColumns = $_SESSION['visibleColumns'];

$message = "";

// -------------------- HELPER --------------------
function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES); }

include "../includes/post_handler.php" //Handles POST (search, CRUD, etc)

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= ucwords($tableName) ?> Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../CSS/admin_style.css">
<link rel="icon" type="image/png" href="../Assets/UI/Taara_Logo.webp">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> <!-- for icons -->
</head>

<body>
<?= displayNav('animals'); ?>

<main class="content flex-c">

    <div style='padding-inline:10px;'>

        <?php include "../includes/search_and_filters.php"; ?>

        <!-- RESULT TABLE -->
        <?php include "../includes/render_table.php"; ?>

        
    </div>
</main>




<?php if(!empty($message)): ?>
<script> showMessage("<?= e($message) ?>"); </script>
<?php endif; ?>
</body>
</html>
