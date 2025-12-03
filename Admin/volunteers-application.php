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
$tableName = 'volunteer_application';   // Change this to your table
$pk = 'v_application_id';       // Primary key column of the table

$crud = new DatabaseCRUD($tableName);
$tableData = $crud->readAll();
// Define fields and types for modal & table
$fieldsConfig = [
    'v_application_id' => 'id',
    'user_id' => 'fk',
    'first_committee' => ['secretariat', 'logistics', 'public relations & research', 'adoption & foster', 'rescue initiatives', 'multimedia/creatives', 'documentation'],
    'second_committee' =>  ['secretariat', 'logistics', 'public relations & research', 'adoption & foster', 'rescue initiatives', 'multimedia/creatives', 'documentation'],
    'full_name' => 'text',
    'classification' => ['employed', 'unemployed', 'self employed', 'student'],
    'birth_date' => 'date',
    'contact_num' => 'number',
    'address' => 'text',
    'id_img' => 'image',
    'reason_for_joining' => 'textarea',
    'date_applied' => 'date',
    'status' => ['pending', 'accepted', 'rejected'],
    'respond_date' => 'date'
];

$searchBy = 'full_name';

//properties shown in the filters
$filterConfig = ['user_id', 'first_committee', 'second_committee', 'classification', 'status', 'date_applied', 'respond_date'];
$actionType = 'setProperty';

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
    'v_application_id' => 'ID',
    'user_id' => 'Username',
    'first_committee' => '1st Committee',
    'second_committee' => '2nd Committee',
    'full_name' => 'Applicant',
    'classification' => 'Classification',
    'birth_date' => 'Birth Date',
    'contact_num' => 'Contact',
    'address' => 'Address',
    'id_img' => 'ID Image',
    'reason_for_joining' => 'Reason for Joining',
    'date_applied' => 'Date Applied',
    'status' => 'Status',
    'respond_date' => 'Date Responded'
];

$setPropertyConfig = [
    'table_name' => 'volunteer_application',
    'property' => 'status',
    'values' => ['accepted', 'rejected'],
    'modal_title' => 'Application Details',
    'button_text' => ['Accept', 'Reject'],
    'button_class' => ['btn btn-primary', 'btn btn-danger']
];




$_SESSION['fields_config'] = $fieldsConfig; 

// Columns initially visible in table
$defaultColumns = ['v_application_id','user_id','date_applied','status', 'id_img','respond_date'];


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
<?= displayNav('volunteers'); ?>

<main class="content flex-c">
    <header class='top-nav flex-r'>
        <a href='volunteers-records.php'>Records</a>
        <a href='volunteers-apllication.php' class='top-active'>Applications</a>
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
