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
$tableName = 'donation_post';   // Change this to your table
$pk = 'dpost_id';       // Primary key column of the table

$crud = new DatabaseCRUD($tableName);
$tableData = $crud->select();
// Define fields and types for modal & table
$fieldsConfig = [
    'dpost_id' => 'id',
    'title' => 'text',
    'description' => 'textarea',
    'post_img' => 'image',
    'goal_amount' => 'number',
    'current_amount' => 'number',
    'date_posted' =>  'date',
    'deadline' => 'date'
];

$searchBy = 'title';

//properties shown in the filters
$filterConfig = ['title', 'goal_amount', 'current_amount'];

$foreignKeys = [
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
    'dpost_id' => 'ID',
    'title' => 'Title',
    'description' => 'Description',
    'post_img' => 'Poster Image',
    'goal_amount' => 'Goal Amount',
    'current_amount' => 'Current Amount',
    'date_posted' => 'Date Starting',
    'deadline' => 'Date Closing'
];

$setPropertyConfig = [];


$_SESSION['fields_config'] = $fieldsConfig; 

// Columns initially visible in table
$defaultColumns = ['post_img', 'title','current_amount', 'goal_amount'];


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
<?= displayNav('events'); ?>

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
