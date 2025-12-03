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
$tableName = 'vaccinations';   // Change this to your table
$pk = 'vaccination_id';       // Primary key column of the table

$crud = new DatabaseCRUD($tableName);
$tableData = $crud->readAll();

$fk_table = new DatabaseCRUD($tableName);
$fk_animal = $fk_table->readAll();

// Define fields and types for modal & table
$fieldsConfig = [
    'vaccination_id' => 'id',
    'animal_id' => 'number',
    'vaccine_type' => 'text',
    'date_vaccinated' => 'date'
];

$fieldLabels = [  //Sets the labels/table headers for modal/table
    //if blank then set to default but capitalized and underscore(_) removed
    'vaccination_id' => 'ID',
    'animal_id' => 'Animal',
    'vaccine_type' => 'Vaccine Type',
    'date_vaccinated' => 'Date Vaccinated'
];

$searchBy = 'name';

//properties shown in the filters
$filterConfig = ['animal_id', 'vaccine_type', 'date_vaccinated'];

$foreignKeys = [
    'animal_id' => [
        'table' => 'animal',
        'key'   => 'animal_id',
        'label' => 'name'
    ]
    // add more here...
];

$fkCache = [];

foreach ($foreignKeys as $fkColumn => $fkConf) {
    $fkCrud = new DatabaseCRUD($fkConf["table"]);
    $records = $fkCrud->readAll();

    foreach ($records as $r) {
        $fkCache[$fkColumn][$r[$fkConf["key"]]] = $r[$fkConf["label"]];
    }

    // Also store the raw list for dropdowns
    $foreignKeys[$fkColumn]['records'] = $records;
}


$_SESSION['fields_config'] = $fieldsConfig; 

// Columns initially visible in table
$defaultColumns = ['vaccination_id','animal_id','vaccine_type','date_vaccinated'];


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
</head>

<body>
<?= displayNav('animals'); ?>

<main class="content flex-c">
    <header class='top-nav flex-r'>
        <a href='animals-records.php'>Records</a>
        <a href='animals-activities.php'>Activity Logs</a>
        <a href='animals-vaccinations.php' class='top-active'>Vaccination</a>
    </header>

    <div style='padding-inline:10px;'>
        <?php include "../includes/search_and_filters.php"; ?>

        <!-- RESULT TABLE -->
        <?php include "../includes/render_table.php"; ?>
    </div>
</main>

<!-- DYNAMIC MODAL -->
<?php 
include 'dynamic_modal.php'; 
//Includes Add/Edit/View, Delete, Message, and ImagePreview Modals
?> 

<?php if(!empty($message)): ?>
<script> showMessage("<?= e($message) ?>"); </script>
<?php endif; ?>
</body>
</html>
