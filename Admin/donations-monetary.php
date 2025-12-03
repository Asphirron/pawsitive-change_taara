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
$tableName = 'monetary_donation';   // Change this to your table
$pk = 'm_donation_id';       // Primary key column of the table

$crud = new DatabaseCRUD($tableName);
$tableData = $crud->readAll();
// Define fields and types for modal & table
$fieldsConfig = [
    'm_donation_id' => 'id',
    'user_id' => 'fk',
    'dpost_id' => 'fk',
    'full_name' => 'text',
    'amount' => 'number',
    'payment_option' => ['gcash, paypal'],
    'message' => 'textarea',
    'contact_num' => 'number',
    'date_donated' => 'date',
    'status' => ['verified', 'pending', 'cancelled'],
    'proof' => 'image'
];

$searchBy = 'full_name';

//properties shown in the filters
$filterConfig = ['amount', 'payment_option', 'date_donated', 'status'];
$actionType = 'setProperty';

$foreignKeys = [
    'user_id' => [
        'table' => 'user',
        'key'   => 'user_id',
        'label' => 'username'
    ],
    'dpost_id' => [
        'table' => 'donation_post',
        'key'   => 'dpost_id',
        'label' => 'title'
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
    'm_donation_id' => 'ID',
    'user_id' => 'Username',
    'dpost_id' => 'Allocated to',
    'full_name' => 'Donor',
    'amount' => 'Amount',
    'payment_option' => 'Payment Method',
    'message' => 'Message',
    'contact_num' => 'Contact',
    'date_donated' => 'Date Donated',
    'status' => 'Status',
    'proof' => 'Proof of Donation'
];

$setPropertyConfig = [
    'table_name' => 'monetary_donation',
    'property' => 'status',
    'values' => ['verified','cancelled'],
    'modal_title' => 'Verify Donation',
    'button_text' => ['Verify', 'Cancel'],
    'button_class' => ['btn btn-primary', 'btn btn-secondary']
];




$_SESSION['fields_config'] = $fieldsConfig; 

// Columns initially visible in table
$defaultColumns = ['m_donation_id','full_name','amount','payment_option','dpost_id','date_donated', 'status'];


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
