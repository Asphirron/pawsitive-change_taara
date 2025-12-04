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
$tableName = 'volunteer';   // Change this to your table
$pk = 'volunteer_id';       // Primary key column of the table

$crud = new DatabaseCRUD($tableName);
$tableData = $crud->readAll();
// Define fields and types for modal & table
$fieldsConfig = [
    'volunteer_id' => 'id',
    'full_name' => 'text',
    'role' => ['secretariat', 'logistics', 'public relations & research', 'adoption & foster', 'rescue initiatives', 'multimedia/creatives', 'documentation'],
    'user_id' => 'fk'];

$searchBy = 'full_name';

//properties shown in the filters
$filterConfig = ['full_name', 'role', 'user_id'];

$foreignKeys = [
      'user_id' => [
        'table' => 'user',
        'key'   => 'user_id',
        'label' => 'username'
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
    'volunteer_id' => 'ID',
    'full_name' => 'Volunteer',
    'role' => 'role',
    'user_id' => 'Username'
];

$setPropertyConfig = [
    'table_name' => 'volunteer',
    'property' => 'role',
    'values' => ['secretariat', 'logistics', 'public relations & research', 'adoption & foster', 'rescue initiatives', 'multimedia/creatives', 'documentation'],
    'modal_title' => 'Volunteer Details',
    'button_text' => [],
    'button_class' => []
];




$_SESSION['fields_config'] = $fieldsConfig; 

// Columns initially visible in table
$defaultColumns = ['volunteer_id','full_name','role',];


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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> <!-- for icons -->
</head>

<body>
<?= displayNav('volunteers'); ?>

<main class="content flex-c">

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
