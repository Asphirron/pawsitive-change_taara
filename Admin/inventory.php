<?php
// admin_items.php
include "../includes/db_connection.php";
include "../Admin/admin_ui.php";

// Instantiate CRUD for 'donation_inventory' table
$inventoryCrud = new DatabaseCRUD('donation_inventory');

// default dataset - read all
$inventory_table = $inventoryCrud->readAll();

$message = "";


session_start();
if($_SERVER['REQUEST_METHOD'] === 'GET'){

    if(!isset($_GET['unset'])){
        unset($_SESSION['visibleColumns']);
    }
}else{
    unset($_SESSION['visibleColumns']);
}

$_SESSION['visibleColumns'] = [
    'item_id', 'item_name', 'item_type', 'quantity', 'date_stored'
];

// Helper: safe output
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES); }

// Handle POST requests (Add / Insert / TakeOut / Search)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---------- RESET FILTERS ----------
    if (isset($_POST['reset_btn'])) {
        $inventory_table = $inventoryCrud->readAll();
        $_POST = [];
        goto END_POST_PROCESSING;
    }

    // ---------- SEARCH ----------
    if (isset($_POST['search_btn'])) {
        $conn = connect();

        $allowed_columns = ['item_id','item_name','item_type','quantity','date_stored'];

        $search_by = $_POST['search_by'] ?? 'none';
        $search_value = trim($_POST['search_bar'] ?? '');
        $order_by = $_POST['order_by'] ?? 'ascending';
        $limit = isset($_POST['num_of_results']) ? intval($_POST['num_of_results']) : 10;

        $order_dir = strtolower($order_by) === 'descending' ? 'DESC' : 'ASC';
        if ($limit <= 0 || $limit > 1000) $limit = 50;

        // CASE 1: No search + No filter → remove WHERE completely
        if ($search_by === 'none' && $search_value === '') {
            $sql = "SELECT * FROM `donation_inventory` ORDER BY `item_id` $order_dir LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $limit);
        }

        // numeric search
        elseif (in_array($search_by, ['item_id','quantity'], true) && is_numeric($search_value)) {
            $sql = "SELECT * FROM `donation_inventory` 
                    WHERE `$search_by` = ? 
                    ORDER BY `item_id` $order_dir 
                    LIMIT ?";
            $stmt = $conn->prepare($sql);
            $ival = intval($search_value);
            $stmt->bind_param("ii", $ival, $limit);
        }

        // string search
        elseif (in_array($search_by, $allowed_columns, true)) {
            $sql = "SELECT * FROM `donation_inventory` 
                    WHERE `$search_by` = ? 
                    ORDER BY `item_id` $order_dir 
                    LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $search_value, $limit);
        }

        // fallback
        else {
            $sql = "SELECT * FROM `donation_inventory` ORDER BY `item_id` $order_dir LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $limit);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;

        $inventory_table = $rows;
        $stmt->close();
        $conn->close();
    }

    // ---------- ADD (merge if name+type exists) ----------
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $item_name = trim($_POST['item_name'] ?? '');
        $item_type = trim($_POST['item_type'] ?? '');
        $quantity  = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
        $date_stored = date('Y-m-d');

        if ($item_name === '' || $item_type === '' || $quantity <= 0) {
            $message = "Please provide item name, type and a positive quantity.";
            $inventory_table = $inventoryCrud->readAll();
            goto END_POST_PROCESSING;
        }

        $conn = connect();
        // check if an item with same name+type exists
        $check = $conn->prepare("SELECT item_id, quantity FROM `donation_inventory` WHERE item_name = ? AND item_type = ? LIMIT 1");
        $check->bind_param("ss", $item_name, $item_type);
        $check->execute();
        $res = $check->get_result();
        $existing = $res->fetch_assoc();
        $check->close();

        if ($existing) {
            // update existing: add quantity and update date_stored
            $newQty = intval($existing['quantity']) + $quantity;
            $success = $inventoryCrud->update(intval($existing['item_id']), ['quantity' => $newQty, 'date_stored' => $date_stored], "item_id");
            $message = $success ? "Existing item updated: +$quantity (now $newQty)" : "Failed to update existing item.";
        } else {
            // insert new record
            $data = [
                'item_name' => $item_name,
                'item_type' => $item_type,
                'quantity'  => $quantity,
                'date_stored'=> $date_stored,
                'item_img'  => $_POST['item_img'] ?? '',
                'donater_name' => $_POST['donater_name'] ?? ''
            ];
            $newId = $inventoryCrud->create($data);
            $message = $newId ? "Added new item ID: $newId" : "Failed to add item.";
        }

        $conn->close();
        $inventory_table = $inventoryCrud->readAll();
    }

    // ---------- INSERT (add quantity to existing by item_id) ----------
    if (isset($_POST['action']) && $_POST['action'] === 'insert' && !empty($_POST['item_id'])) {
        $id = intval($_POST['item_id']);
        $addQty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
        if ($addQty <= 0) {
            $message = "Quantity must be positive.";
            $inventory_table = $inventoryCrud->readAll();
            goto END_POST_PROCESSING;
        }
        $current = $inventoryCrud->read($id, 'item_id') ?: null;
        if (!$current) {
            $message = "Item not found.";
            $inventory_table = $inventoryCrud->readAll();
            goto END_POST_PROCESSING;
        }
        $newQty = intval($current['quantity']) + $addQty;
        $success = $inventoryCrud->update($id, ['quantity' => $newQty, 'date_stored' => date('Y-m-d')], "item_id");
        $message = $success ? "Inserted $addQty into item ID $id (now $newQty)." : "Failed to insert quantity.";
        $inventory_table = $inventoryCrud->readAll();
    }

    // ---------- TAKEOUT (subtract quantity from existing by item_id) ----------
    if (isset($_POST['action']) && $_POST['action'] === 'takeout' && !empty($_POST['item_id'])) {
        $id = intval($_POST['item_id']);
        $takeQty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
        if ($takeQty <= 0) {
            $message = "Quantity must be positive.";
            $inventory_table = $inventoryCrud->readAll();
            goto END_POST_PROCESSING;
        }
        $current = $inventoryCrud->read($id, 'item_id') ?: null;
        if (!$current) {
            $message = "Item not found.";
            $inventory_table = $inventoryCrud->readAll();
            goto END_POST_PROCESSING;
        }
        $newQty = intval($current['quantity']) - $takeQty;
        if ($newQty < 0) {
            $message = "Cannot take out $takeQty from item ID $id — insufficient quantity (current: {$current['quantity']}).";
            $inventory_table = $inventoryCrud->readAll();
            goto END_POST_PROCESSING;
        }
        $success = $inventoryCrud->update($id, ['quantity' => $newQty, 'date_stored' => date('Y-m-d')], "item_id");
        $message = $success ? "Took out $takeQty from item ID $id (now $newQty)." : "Failed to take out quantity.";
        $inventory_table = $inventoryCrud->readAll();
    }

    // ---------- DELETE ----------
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && !empty($_POST['item_id'])) {
        $id = intval($_POST['item_id']);
        $success = $inventoryCrud->delete($id, "item_id");
        $message = $success ? "Deleted item $id" : "Failed to delete item $id";
        $inventory_table = $inventoryCrud->readAll();
    }
}

END_POST_PROCESSING:
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inventory Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/admin_style.css">
    <link rel="icon" type="image/png" href="../Assets/UI/Taara_Logo.webp">
    <style>

    </style>
</head>
<body>

<?= displayNav('inventory'); ?>

<main class="content flex-c">
    <header class='top-nav flex-r'>
        <a href='index.php' class='top-active'>Inventory</a>
        <!--a href='index.php'>History</a-->
    </header>

    <div style='padding-inline: 10px;'>
    <h2>🐾 Inventory</h2>

    <!-- SEARCH / FILTER FORM -->
    <form method="POST" enctype="multipart/form-data" style="margin-bottom:12px;">
        <div class="search-group">
            <div>
                <input type="text" name="search_bar" class="search-bar" placeholder="Search..." value="<?= isset($_POST['search_bar']) ? e($_POST['search_bar']) : '' ?>">
            </div>

            <div class='flex-r c-gap center'>
                <label for="search-by">Search by</label><br>
                <select name="search_by" id="search-by">
                    <option value="none">None</option>
                    <option value="item_id" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'item_id') ? 'selected' : '' ?>>Id</option>
                    <option value="item_name" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'item_name') ? 'selected' : '' ?>>Item</option>
                    <option value="item_type" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'item_type') ? 'selected' : '' ?>>Type</option>
                    <option value="quantity" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'quantity') ? 'selected' : '' ?>>Quantity</option>
                    <option value="date_stored" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'date_stored') ? 'selected' : '' ?>>Date Updated</option>
                </select>
            </div>

            <div class='flex-r c-gap center'>
                <label for="order_by">Order</label><br>
                <select name="order_by" id="order_by">
                    <option value="ascending" <?= (isset($_POST['order_by']) && $_POST['order_by'] === 'ascending') ? 'selected' : '' ?>>Ascending</option>
                    <option value="descending" <?= (isset($_POST['order_by']) && $_POST['order_by'] === 'descending') ? 'selected' : '' ?>>Descending</option>
                </select>
            </div>

            <div class='flex-r c-gap center'>
                <label for="results">No. of results</label><br>
                <input type="number" id="results" name="num_of_results" value="<?= isset($_POST['num_of_results']) ? intval($_POST['num_of_results']) : 10 ?>" min="1" max="1000">
            </div>

            <div>
                <button type="submit" name="search_btn" class="btn btn-primary">Search</button>
            </div>

            <div>
                <button type="submit" name="reset_btn" class="btn btn-secondary">Reset Filters</button>
            </div>
        </div>
    </form>

    <!-- ACTION BUTTONS -->
    <div class="action-buttons flex-c wrap" style="margin-bottom:8px;">
        <div class="action_btn_group flex-r">
            <button class="btn btn-primary" onclick="openSharedModal('add')"><img class="icon-1" src="../Assets/Ui/more.png"> Add item</button>
            <a href="../export/export_pdf.php?table=donation_inventory" target="_blank"><button type='button' class="btn btn-success">Export as PDF</button></a>
        </div>
    </div>

    <!-- RESULT TABLE -->
    <div class="result-table">
        <table class="rounded-border">
            <thead>
            <tr>
                <th>ID</th>
                <th>Item</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Date Updated</th>
                <th class="action-row">Action</th>
            </tr>
            </thead>

            <tbody>
            <?php if (!empty($inventory_table)): ?>
                <?php foreach($inventory_table as $item): ?>
                    <tr>
                        <td><?= e($item['item_id']) ?></td>
                        <td><?= e($item['item_name']) ?></td>
                        <td><?= e($item['item_type']) ?></td>
                        <td><?= e($item['quantity']) ?></td>
                        <td><?= e($item['date_stored']) ?></td>
                        <td class="action-row">
                            <?php $json = htmlspecialchars(json_encode($item), ENT_QUOTES); ?>
                            <!-- Insert and Take Out buttons -->
                            <button class="btn btn-success" onclick='openSharedModal("insert", <?= $json ?>)'>Insert</button>
                            <button class="btn btn-warning" onclick='openSharedModal("takeout", <?= $json ?>)'>Take Out</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    </div>

</main>

<!-- SHARED ADD/INSERT/TAKEOUT MODAL -->
<div id="sharedModalBackdrop" class="modal-backdrop hidden"></div>
<div id="sharedModal" class="modal hidden" role="dialog" aria-modal="true">
    <form id="sharedForm" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" id="shared_action" value="">
        <input type="hidden" name="item_id" id="shared_item_id" value="">

        <h2 id="sharedTitle">Modal</h2>

        <div class="row">

            <div class="col" id="col_name">
                <label>Item Name</label>
                <input type="text" name="item_name" id="name_field" placeholder="e.g. Dog Food, Vitamins">
            </div>

            <div class="col" id="col_type">
                <label>Type</label>
                <input list="type-options" id="type_field" name="item_type" placeholder="Enter item type">
                <datalist id="type-options">
                    <option value="Food">
                    <option value="Supplies">
                    <option value="Medicine">
                    <option value="Toys">
                    <option value="Materials">
                    <option value="Equipment">
                </datalist>
            </div>
            
            <div class="col">
                <label>Quantity</label>
                <input type="number" name="quantity" id="quantity_field" value="1" min="1">
            </div>
        </div>

        <input type="text" name="item_img" id="img_field" placeholder="filename.png" class='hidden'>
        <input type="file" name="img_file" id="img_file_field" accept="image/*" class='hidden'>

        <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
            <button type="button" class="btn btn-secondary" onclick="closeSharedModal()">Cancel</button>
            <button type="submit" class="btn btn-primary" id="sharedConfirmBtn">Add Item</button>
        </div>
    </form>
</div>

<!-- DELETE CONFIRMATION MODAL (kept for completeness) -->
<div id="deleteBackdrop" class="modal-backdrop hidden"></div>
<div id="deleteModal" class="modal hidden" role="dialog" aria-modal="true" style="width:400px;">
    <form id="deleteForm" method="POST">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="item_id" id="delete_item_id" value="">
        <h4 id="deleteTitle">Delete</h4>
        <div id="deleteMessage">Are you sure?</div>

        <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            <button type="submit" class="btn btn-danger">Delete</button>
        </div>
    </form>
</div>

<!-- FEEDBACK MODAL -->
<div id="feedbackBackdrop" class="modal-backdrop hidden"></div>
<div id="feedbackModal" class="modal hidden" role="dialog" aria-modal="true" style="width:350px;">
    <h4>Message</h4>
    <div id="feedbackMessage" style="margin-top:10px; font-size:14px;"></div>

    <div style="margin-top:12px; display:flex; justify-content:flex-end;">
        <button class="btn btn-primary" onclick="closeFeedbackModal()">OK</button>
    </div>
</div>

<script>
    // openSharedModal supports 'add' | 'insert' | 'takeout' | 'view' | 'edit'
    function openSharedModal(mode, data = null) {
        document.getElementById('sharedModalBackdrop').classList.remove('hidden');
        const modal = document.getElementById('sharedModal');
        modal.classList.remove('hidden');

        const title = document.getElementById('sharedTitle');
        const actionInput = document.getElementById('shared_action');
        const idInput = document.getElementById('shared_item_id');

        const colName = document.getElementById('col_name');
        const colType = document.getElementById('col_type');
        const nameField = document.getElementById('name_field');
        const typeField = document.getElementById('type_field');
        const qtyField = document.getElementById('quantity_field');
        const confirmBtn = document.getElementById('sharedConfirmBtn');

        // clear previous submit handler
        document.getElementById('sharedForm').onsubmit = null;

        data = data || {};

        if (mode === 'add') {
            title.textContent = 'Add Item';
            actionInput.value = 'add';
            idInput.value = '';
            colName.style.display = 'flex';
            colType.style.display = 'flex';
             colName.addAttribute('required');
            colType.addAttribute('required');
            nameField.value = '';
            typeField.value = '';
            qtyField.value = 1;
            confirmBtn.textContent = 'Add';
            return;
        }

        if (mode === 'insert' || mode === 'takeout') {
            // hide name/type (we're working with existing record)
            colName.style.display = 'none';
            colType.style.display = 'none';

            colName.removeAttribute('required');
            colType.removeAttribute('required');
            

            idInput.value = data.item_id ?? '';
            // show item in title for context
            title.textContent = (mode === 'insert' ? 'Insert into ' : 'Take out from ') + (data.item_name ?? 'Item');

            actionInput.value = (mode === 'insert' ? 'insert' : 'takeout');

            // default quantity
            qtyField.value = 1;

            confirmBtn.textContent = (mode === 'insert' ? 'Insert' : 'Take Out');

            

            // show a small context line inside modal (optional)
            // you can show current quantity somewhere if desired, for now we keep fields simple

            return;
        }

        if (mode === 'view' || mode === 'edit') {
            // show all fields and populate
            colName.style.display = 'block';
            colType.style.display = 'block';

            idInput.value = data.item_id ?? '';
            nameField.value = data.item_name ?? '';
            typeField.value = data.item_type ?? '';
            qtyField.value = data.quantity ?? 1;

            if (mode === 'view') {
                // make read-only (simple approach)
                nameField.setAttribute('readonly','readonly');
                typeField.setAttribute('readonly','readonly');
                qtyField.setAttribute('readonly','readonly');
                confirmBtn.style.display = 'none';
                title.textContent = 'View item';
            } else {
                actionInput.value = 'update';
                confirmBtn.textContent = 'Save';
                title.textContent = 'Edit item';
            }
            return;
        }
    }

    function closeSharedModal() {
        document.getElementById('sharedModalBackdrop').classList.add('hidden');
        document.getElementById('sharedModal').classList.add('hidden');
        // reset form submit handler to default
        document.getElementById('sharedForm').onsubmit = null;
        // ensure inputs are enabled for next open
        document.getElementById('name_field').removeAttribute('readonly');
        document.getElementById('type_field').removeAttribute('readonly');
        document.getElementById('quantity_field').removeAttribute('readonly');
    }

    function openDeleteModal(id, name) {
        document.getElementById('deleteBackdrop').classList.remove('hidden');
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        document.getElementById('delete_item_id').value = id;
        document.getElementById('deleteMessage').textContent = 'Are you sure you want to delete "' + name.replace(/(^"|"$)/g,'') + '" (ID ' + id + ')? This action cannot be undone.';
    }
    function closeDeleteModal() {
        document.getElementById('deleteBackdrop').classList.add('hidden');
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // feedback modal
    function openFeedbackModal(msg) {
        document.getElementById('feedbackMessage').textContent = msg;
        document.getElementById('feedbackBackdrop').classList.remove('hidden');
        document.getElementById('feedbackModal').classList.remove('hidden');
    }
    function closeFeedbackModal() {
        document.getElementById('feedbackBackdrop').classList.add('hidden');
        document.getElementById('feedbackModal').classList.add('hidden');
    }
    document.getElementById('feedbackBackdrop').addEventListener('click', closeFeedbackModal);
    document.getElementById('sharedModalBackdrop').addEventListener('click', closeSharedModal);
    document.getElementById('deleteBackdrop').addEventListener('click', closeDeleteModal);

    // Preview uploaded image in modal (not used by default)
    document.getElementById('img_file_field').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const url = URL.createObjectURL(file);
            document.getElementById('img_preview').src = url;
            document.getElementById('img_preview').style.display = "block";
        }
    });
</script>

<?php if (!empty($message)): ?>
<script>
    openFeedbackModal("<?= e($message) ?>");
</script>
<?php endif; ?>

</body>
</html>
