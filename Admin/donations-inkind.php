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
$tableName = 'inkind_donation';   // Change this to your table
$pk = 'i_donation_id';       // Primary key column of the table

$crud = new DatabaseCRUD($tableName);
$tableData = $crud->readAll();
// Define fields and types for modal & table
$fieldsConfig = [
    'i_donation_id' => 'id',
    'user_id' => 'fk',
    'full_name' => 'text',
    'item_name' => 'text',
    'donation_type' => ['food', 'medicine', 'toys', 'supplies','equipment', 'other'],
    'img' => 'image',
    'message' => 'textarea',
    'contact_num' => 'number',
    'location' => 'text',
    'date' => 'date',
    'status' => ['received', 'pending', 'cancelled']
];

$searchBy = 'item_name';

//properties shown in the filters
$filterConfig = ['item_name', 'donation_type', 'location', 'date', 'status'];
$actionType = 'setProperty';

$foreignKeys = [
    'user_id' => [
        'table' => 'user',
        'key'   => 'user_id',
        'label' => 'username'
    ],
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
    'i_donation_id' => 'ID',
    'user_id' => 'Username',
    'full_name' => 'Donor',
    'item_name' => 'Item Name',
    'donation_type' => 'Item Type',
    'quantity' => 'number',
    'img' => 'Item Photo',
    'message' => 'Message',
    'contact_num' => 'Contact',
    'location' => 'Drop-ff Location',
    'date' => 'Drop-off Date',
    'status' => 'Status'
];

$setPropertyConfig = [
    'table_name' => 'inkind_donation',
    'property' => 'status',
    'values' => ['received'],
    'modal_title' => 'Manage Donation',
    'button_text' => ['Set as Received'],
    'button_class' => ['btn btn-primary']
];




$_SESSION['fields_config'] = $fieldsConfig; 

// Columns initially visible in table
$defaultColumns = ['i_donation_id','full_name','item_name','donation_type', 'quantity', 'img', 'status'];


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
<?= displayNav('donations'); ?>

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
