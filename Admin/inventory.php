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
$filterConfig = ['item_type', 'item_name', 'status', 'quantity','date_stored'];
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

$overrideCrud = true;

// Shared POST handler (search, filters, CRUD)
include "../includes/post_handler.php";

// -------------------- POST HANDLER --------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD: merge if item_name + item_type already exists
    if ($action === 'add') {
        $item_name = trim($_POST['item_name'] ?? '');
        $item_type = trim($_POST['item_type'] ?? '');
        $quantity  = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
        $date_stored = date('Y-m-d');

        if ($item_name === '' || $item_type === '' || $quantity <= 0) {
            $message = "Please provide item name, type and a positive quantity.";
            goto END_POST;
        }

        $conn = connect();
        $check = $conn->prepare("SELECT item_id, quantity FROM donation_inventory WHERE item_name=? AND item_type=? LIMIT 1");
        $check->bind_param("ss", $item_name, $item_type);
        $check->execute();
        $res = $check->get_result();
        $existing = $res->fetch_assoc();
        $check->close();

        if ($existing) {
            $newQty = intval($existing['quantity']) + $quantity;
            $success = $crud->update(intval($existing['item_id']), [
                'quantity' => $newQty,
                'date_stored' => $date_stored
            ], "item_id");
            $message = $success ? "Successfully inserted amount of items: +$quantity (now $newQty)" : "Failed to update inventory data.";
        } else {
            $newId = $crud->create([
                'item_name' => $item_name,
                'item_type' => $item_type,
                'quantity'  => $quantity,
                'date_stored' => $date_stored,
                'item_img' => $_POST['item_img'] ?? '',
                'donater_name' => $_POST['donater_name'] ?? ''
            ]);
            $message = $newId ? "Successfully added new item into the inventory" : "Failed to add item.";
        }
        $conn->close();
    }

    // INSERT: add quantity to existing item
    if ($action === 'insert' && !empty($_POST['item_id'])) {
        $id = intval($_POST['item_id']);
        $addQty = intval($_POST['quantity'] ?? 0);
        if ($addQty <= 0) { $message = "Quantity must be positive."; goto END_POST; }

        $current = $crud->read($id, "item_id");
        if (!$current) { $message = "Item not found."; goto END_POST; }

        $newQty = intval($current['quantity']) + $addQty;
        $success = $crud->update($id, ['quantity' => $newQty, 'date_stored' => date('Y-m-d')], "item_id");
        $message = $success ? "Successfully added $addQty items into records (now $newQty)." : "Failed to insert.";
    }

    // TAKEOUT: subtract quantity
    if ($action === 'takeout' && !empty($_POST['item_id'])) {
        $id = intval($_POST['item_id']);
        $takeQty = intval($_POST['quantity'] ?? 0);
        if ($takeQty <= 0) { $message = "Quantity must be positive."; goto END_POST; }

        $current = $crud->read($id, "item_id");
        if (!$current) { $message = "Item not found."; goto END_POST; }

        $newQty = intval($current['quantity']) - $takeQty;
        if ($newQty < 0) { $message = "Cannot take out more than available."; goto END_POST; }

        $success = $crud->update($id, ['quantity' => $newQty, 'date_stored' => date('Y-m-d')], "item_id");
        $message = $success ? "Successfully took out $takeQty items from the inventory (now $newQty)." : "Failed to take out.";
    }

    END_POST:

    $tableData = $crud->runQuery("
        SELECT item_id, item_name, item_type, quantity, date_stored, item_img, donater_name,
               CASE
                   WHEN quantity <= 0 THEN 'out of stock'
                   WHEN quantity <= 10 THEN 'low stock'
                   ELSE 'in stock'
               END AS status
        FROM donation_inventory
    ");
}



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

    <div style='padding-inline:10px;'>
        <!-- SEARCH & FILTERS -->
        <?php include "../includes/search_and_filters.php"; ?>

        <!-- RESULT TABLE -->
        <?php include "../includes/render_table.php"; ?>

        <!-- INVENTORY MODAL -->
        <div id="inventoryBackdrop" class="modal-backdrop hidden" onclick="closeInventoryModal()"></div>
        <div id="inventoryModal" class="modal hidden" role="dialog" aria-modal="true">
            <form id="inventoryForm" method="POST">
                <input type="hidden" name="action" id="inventory_action" value="">
                <input type="hidden" name="item_id" id="inventory_item_id" value="">

                <h2 id="inventoryTitle">Inventory Action</h2>

                <div class="row">
                    <div class="col">
                        <label>Quantity</label>
                        <input type="number" name="quantity" id="inventory_quantity" value="1" min="1">
                    </div>
                </div>

                <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeInventoryModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="inventoryConfirmBtn">Confirm</button>
                </div>
            </form>
        </div>

    </div>
</main>

<!-- DYNAMIC MODALS -->
<?php include "dynamic_modal.php"; ?>

<?php if(!empty($message)): ?>
<script> showMessage("<?= e($message) ?>"); </script>
<?php endif; ?>

<script>
function openInventoryModal(mode, data) {
    document.getElementById('inventoryBackdrop').classList.remove('hidden');
    document.getElementById('inventoryModal').classList.remove('hidden');

    document.getElementById('inventory_action').value = mode;
    document.getElementById('inventory_item_id').value = data.item_id;
    document.getElementById('inventory_quantity').value = 1;

    const title = document.getElementById('inventoryTitle');
    const confirmBtn = document.getElementById('inventoryConfirmBtn');

    if (mode === 'insert') {
        title.textContent = "Insert into " + data.item_name;
        confirmBtn.textContent = "Insert";
    } else {
        title.textContent = "Take out from " + data.item_name;
        confirmBtn.textContent = "Take Out";
    }
}

function closeInventoryModal() {
    document.getElementById('inventoryBackdrop').classList.add('hidden');
    document.getElementById('inventoryModal').classList.add('hidden');
}

</script>
</body>
</html>
