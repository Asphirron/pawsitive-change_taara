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
$tableName = 'adoption_application';   // Change this to your table
$pk = 'a_application_id';       // Primary key column of the table

$crud = new DatabaseCRUD($tableName);
$tableData = $crud->readAll();
// Define fields and types for modal & table
$fieldsConfig = [
    'a_application_id' => 'id',
    'user_id' => 'fk',
    'animal_id' => 'fk',
    'full_name' => 'text',
    'address' => 'textarea',
    'classification' => ['employed', 'unemployed', 'self employed', 'student'],
    'comp_name' => 'text',
    'id_img' => 'image',
    'date_applied' => 'date',
    'status' => ['pending', 'accepted', 'rejected', 'cancelled'],
    'date_responded' => 'date'
];

$searchBy = 'full_name';

//properties shown in the filters
$filterConfig = ['user_id', 'animal_id', 'address', 'classification', 'date_applied', 'status', 'date_responded'];
$actionType = 'setProperty';


$foreignKeys = [
    'animal_id' => [
        'table' => 'animal',
        'key'   => 'animal_id',
        'label' => 'name'
    ],
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
    'a_application_id' => 'ID',
    'user_id' => 'Username',
    'animal_id' => 'Animal Name',
    'full_name' => 'Applicant',
    'address' => 'Address',
    'classification' => 'Employment',
    'comp_name' => 'Company Name',
    'id_img' => 'ID Image',
    'date_applied' => 'Date Applied',
    'status' => 'Status',
    'date_responded' => 'Response Date'
];

$setPropertyConfig = [
    'table_name' => 'adoption_application',
    'property' => 'status',
    'values' => ['accepted', 'rejected'],
    'modal_title' => 'Application Details',
    'button_text' => ['Accept', 'Reject'],
    'button_class' => ['btn btn-primary', 'btn btn-danger']
];




$_SESSION['fields_config'] = $fieldsConfig; 

// Columns initially visible in table
$defaultColumns = ['a_application_id','full_name','animal_id','date_applied','status', 'date_responded'];


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
<?= displayNav('adoptions'); ?>

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
