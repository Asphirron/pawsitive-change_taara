<?php
// admin_items.php
include "../includes/db_connection.php";
include "../Admin/admin_ui.php";

session_start();
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(!isset($_GET['unset'])){
        unset($_SESSION['visibleColumns']);
    }
}else{
    unset($_SESSION['visibleColumns']);
}

// -------------------- CONFIG --------------------
$tableName = 'donation_inventory';
$pk = 'item_id';

$crud = new DatabaseCRUD($tableName);
$tableData = $crud->runQuery("
    SELECT item_id, item_name, item_type, quantity, date_stored, item_img, donater_name,
           CASE
               WHEN quantity <= 0 THEN 'out of stock'
               WHEN quantity <= 10 THEN 'low stock'
               ELSE 'in stock'
           END AS status
    FROM donation_inventory
");


$fieldsConfig = [
    'item_id'      => 'id',
    'item_name'    => 'text',
    'item_type'    => 'text',
    'quantity'     => 'number',
    'date_stored'  => 'date',
    'item_img'     => 'image',
    'donater_name' => 'text',
    'status' => ['in stock', 'low stock', 'out of stock']
];

$fieldLabels = [
    'item_id'      => 'ID',
    'item_name'    => 'Item',
    'item_type'    => 'Type',
    'quantity'     => 'Quantity',
    'date_stored'  => 'Date Stored',
    'item_img'     => 'Image',
    'donater_name' => 'Donater',
    'status' => 'Status'
];

$searchBy = 'item_name';
$filterConfig = ['item_type', 'status', 'quantity','date_stored','donater_name'];
$actionType = 'in_and_out';

// Columns initially visible
$defaultColumns = ['item_id','item_name','item_type','quantity', 'status', 'date_stored'];

if(!isset($_SESSION['visibleColumns'])) {
    $_SESSION['visibleColumns'] = $defaultColumns;
}
$visibleColumns = $_SESSION['visibleColumns'];

$message = "";

// -------------------- HELPER --------------------
function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES); }

// Shared POST handler (search, filters, CRUD)
include "../includes/post_handler.php";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Inventory Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../CSS/admin_style.css">
<link rel="icon" type="image/png" href="../Assets/UI/Taara_Logo.webp">
</head>
<body>

<?= displayNav('inventory'); ?>

<main class="content flex-c">
    <header class='top-nav flex-r'>
        <a href='index.php' class='top-active'>Inventory</a>
    </header>

    <div style='padding-inline:10px;'>
        <!-- SEARCH & FILTERS -->
        <?php include "../includes/search_and_filters.php"; ?>

        <!-- RESULT TABLE -->
        <?php include "../includes/render_table.php"; ?>
    </div>
</main>

<!-- DYNAMIC MODALS -->
<?php include "dynamic_modal.php"; ?>

<?php if(!empty($message)): ?>
<script> showMessage("<?= e($message) ?>"); </script>
<?php endif; ?>
</body>
</html>
