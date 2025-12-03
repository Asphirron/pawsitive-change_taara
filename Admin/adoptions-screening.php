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
$tableName = 'adoption_screening';   // Change this to your table
$pk = 'screening_id';       // Primary key column of the table

$crud = new DatabaseCRUD($tableName);
$tableData = $crud->readAll();

if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['application_id'])){
    $tableData = $crud->select('*', ['a_application_id' => $_GET['application_id']]);

    foreach($tableData as $t){
        $message = $t;
    }
}



// Define fields and types for modal & table
$fieldsConfig = [
    'screening_id' => 'id',
    'a_application_id' => 'fk',
    'housing' => ['apartment', 'house', 'other'],
    'reason' => ['companion for child', 'companion for other pets', 'companion for self', 'service animal', 'security'],
    'own_pets' => ['yes', 'no'],
    'time_dedicated' => ['1-2 hours every day', 'Only during my free time', 'I have no time'],
    'children_info' => 'textarea',
    'financial_ready' => ['yes', 'no', 'My family are my pets provider'],
    'breed_interest' => 'textarea',
    'allergy_info' => 'textarea',
    'alone_time_plan' => 'textarea',
    'researched_breed' => ['yes I am well aware', 'no']
];

$searchBy = 'screening_id';

//properties shown in the filters
$filterConfig = [];
$actionType = 'setProperty';

$foreignKeys = [

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
    'screening_id' => 'Screening ID',
    'a_application_id' => 'Application ID',
    'housing' => '1. What type of housing do you live in?',
    'reason' => '2. Reason for adopting a pet?',
    'own_pets' => '3. Have you previously owned pets?',
    'time_dedicated' => '4. How much time are you willing to dedicate to daily exercise and play time?',
    'children_info' => '5. Do you have a house with children? If YES, state the age and your ability to manage. If NONE, put N/A',
    'financial_ready' => '6. Are you prepared for the financial responsibilities of owning a pet?',
    'breed_interest' => '7. What size and breed are you interested in and why?',
    'allergy_info' => '8. Does anyone in your family have a known allergy to animals? If YES, how will you manage it? If NONE, put N/A',
    'alone_time_plan' => '9. How long do you plan to leave your pet alone during the day? Do you have a plan for their care if youre away?',
    'researched_breed' => '10. Have you researched the needs and characteristics of the breed you are interested in adopting?'
];

$setPropertyConfig = [
    'table_name' => 'adoption_application',
    'property' => '',
    'values' => [],
    'modal_title' => 'Application Screenin Details',
    'button_text' => [],
    'button_class' => []
];




$_SESSION['fields_config'] = $fieldsConfig; 

// Columns initially visible in table
$defaultColumns = ['screening_id','a_application_id'];


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
