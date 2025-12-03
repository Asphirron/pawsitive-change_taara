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
$tableName = 'rescue_report';   // Change this to your table
$pk = 'report_id';       // Primary key column of the table

$crud = new DatabaseCRUD($tableName);
$tableData = $crud->readAll();
// Define fields and types for modal & table
$fieldsConfig = [
    'report_id' => 'id',
    'user_id' => 'fk',
    'type' => ['rescue', 'lost_and_found'],
    'description' => 'textarea',
    'full_name' => 'text',
    'contact_num' => 'number',
    'location' => 'text',
    'img' => 'image',
    'date_posted' => 'date',
    'status' => ['pending', 'cancelled', 'resolved']
];

$searchBy = 'full_name';

//properties shown in the filters
$filterConfig = ['type', 'location', 'date_posted', 'status'];
$actionType = 'setProperty';

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

$fieldLabels = [
    'report_id' => 'Id',
    'user_id' => 'User',
    'type' => 'Report type',
    'description' => 'Description',
    'full_name' => 'Reporter',
    'contact_num' => 'Contact',
    'location' => 'Location(geodata)',
    'img' => 'Documentation',
    'date_posted' => 'Date Posted',
    'status' => 'Status'
];

$setPropertyConfig = [
    'table_name' => 'rescue_report',
    'property' => 'status',
    'values' => ['resolved', 'cancelled'],
    'modal_title' => 'Handle Report',
    'button_text' => ['Set as Resolved', 'Cancel'],
    'button_class' => ['btn btn-danger', 'btn btn-success']
];




$_SESSION['fields_config'] = $fieldsConfig; 

// Columns initially visible in table
$defaultColumns = ['report_id','img','type','description','full_name','date_posted', 'status'];


if(!isset($_SESSION['visibleColumns'])) {
    $_SESSION['visibleColumns'] = $defaultColumns;
}

$visibleColumns = $_SESSION['visibleColumns'];

$message = "";

// -------------------- HELPER --------------------
function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES); }

include "../includes/post_handler.php"; //Handles POST (search, CRUD, etc)

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
<?= displayNav('reports'); ?>

<main class="content flex-c">
    <header class='top-nav flex-r'>
        <a href='reports-rescue.php' class='top-active'>Rescue Reports</a>
        <a href='reports-poi.php'>Points of Interest</a>
        <a href='reports-map.php'>Map</a>
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
