<?php
// admin_animals.php
include "../includes/db_connection.php";
include "../Admin/admin_ui.php";

// Instantiate CRUD for 'animal' table
$i_donation_Crud = new DatabaseCRUD('inkind_donation'); 

// default dataset - read all
$i_donation_table = $i_donation_Crud->readAll();
$message = "";

// Helper: safe output
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES); }

// Handle POST requests (Add / Update / Delete / Search)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---------- RESET FILTERS ----------
    if (isset($_POST['reset_btn'])) {
        $i_donation_table = $i_donation_Crud->readAll();
        $_POST = [];
        goto END_POST_PROCESSING;
    }

    // ---------- SEARCH ----------  
    if (isset($_POST['search_btn'])) {
        $conn = connect();

        $allowed_columns = ['i_donation_id','full_name', 'item_name', 'donation_type','message','contact_num','location', 'date'];

        $search_by = $_POST['search_by'] ?? 'none';
        $search_value = trim($_POST['search_bar'] ?? '');
        $order_by = $_POST['order_by'] ?? 'ascending';
        $limit = isset($_POST['num_of_results']) ? intval($_POST['num_of_results']) : 10;

        $order_dir = strtolower($order_by) === 'descending' ? 'DESC' : 'ASC';
        if ($limit <= 0 || $limit > 1000) $limit = 50;

        // CASE 1: No search + No filter → remove WHERE completely
        if ($search_by === 'none' && $search_value === '') {
            $sql = "SELECT * FROM `inkind_donation` ORDER BY `i_donation_id` $order_dir LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $limit);
        }

        // CASE 2: Valid search column + numeric search
        elseif (in_array($search_by, ['i_donation_id','amount'], true) && is_numeric($search_value)) {
            $sql = "SELECT * FROM `inkind_donation` 
                    WHERE `$search_by` = ? 
                    ORDER BY `i_donation_id` $order_dir 
                    LIMIT ?";
            $stmt = $conn->prepare($sql);
            $ival = intval($search_value);
            $stmt->bind_param("ii", $ival, $limit);
        }

        // CASE 3: Valid search column + string search
        elseif (in_array($search_by, $allowed_columns, true)) {
            $sql = "SELECT * FROM `inkind_donation` 
                    WHERE `$search_by` = ? 
                    ORDER BY `i_donation_id` $order_dir 
                    LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $search_value, $limit);
        }

        // CASE 4: Fallback if invalid search option → treat as no search
        else {
            $sql = "SELECT * FROM `inkind_donation` ORDER BY `i_donation_id` $order_dir LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $limit);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;

        $m_donation_table = $rows;
        $stmt->close();
        $conn->close();
    }


    // ---------- UPDATE ----------
    if (isset($_POST['action']) && $_POST['action'] === 'update' && !empty($_POST['i_donation_id'])) {
        $id = intval($_POST['i_donation_id']);
        // fetch current row to preserve img if no new upload
        $current = $i_donation_Crud->read($id, 'i_donation_id') ?: [];

        $fields = [
            'item_name' => $_POST['item_name'] ?? $current['item_name'] ?? '',
            'donation_type' => $_POST['donation_type'] ?? $current['donation_type'] ?? '',
            'full_name' => $_POST['full_name'] ?? $current['full_name'] ?? '',
            'contact_num' => $_POST['contact_num'] ?? $current['contact_num'] ?? '',
            'location' => $_POST['location'] ?? $current['location'] ?? '',
            'date' => $_POST['date'] ?? $current['date'] ?? date('Y-m-d'),
            'message' => $_POST['message'] ?? $current['message'] ?? '',
            'status' => 'Received'
        ];

        $success = $i_donation_Crud->update($id, $fields, "i_donation_id");
        $message = $success ? "Updated donation $id" : "Failed to update animal $id";
        $i_donation_table = $i_donation_Crud->readAll();
    }


}

END_POST_PROCESSING:
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Donations Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/admin_style.css">
    <link rel="icon" type="image/png" href="../Assets/UI/Taara_Logo.webp">
    <style>

        
    </style>
</head>
<body>

<?= displayNav('donations'); ?>

<main class="content flex-c">


    <div style='padding-inline: 10px;'>
    <h2>📦In-kind Donations</h2>

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
                    <option value="m_donation_id" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'm_donation_id') ? 'selected' : '' ?>>Id</option>
                    <option value="full_name" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'full_name') ? 'selected' : '' ?>>Donor</option>
                    <option value="donation_type" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'donation_type') ? 'selected' : '' ?>>Type</option>
                    <option value="message" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'message') ? 'selected' : '' ?>>Message</option>
                    <option value="contact_num" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'contact_num') ? 'selected' : '' ?>>Contact</option>
                    <option value="location" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'location') ? 'selected' : '' ?>>Drop-off Location</option>
                    <option value="date" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'date') ? 'selected' : '' ?>>Drop-off Date</option>
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

            <!-- NEW RESET BUTTON -->
            <div>
                <button type="submit" name="reset_btn" class="btn btn-secondary">Reset Filters</button>
            </div>
        </div>
    </form>
   
    <!-- RESULT TABLE -->
    <div class="result-table">
        <table class="rounded-border">
            <thead>
            <tr>
                <th>ID</th>
                <th>Donor</th>
                <th>Item Name</th>
                <th>Item Type</th>
                <th>Drop-off Location</th>
                <th>Drop-off Date</th>
                <th>Status</th>
                <th class="action-row">Action</th>
            </tr>
            </thead>

            <tbody>
            <?php if (!empty($i_donation_table)): ?>
                <?php foreach($i_donation_table as $d): ?>
                    <tr>
                        <td><?= e($d['i_donation_id']) ?></td>
                        <td><?= e($d['full_name']) ?></td>
                        <td><?= e($d['item_name']) ?></td>
                        <td><?= e($d['donation_type']) ?></td>
                        <td><?= e($d['location']) ?></td>
                        <td><?= e($d['date']) ?></td>
                        <td><?= e($d['status']) ?></td>
                        <td class="action-row">
                            <?php $json = htmlspecialchars(json_encode($d), ENT_QUOTES); ?>
                             <button class="btn" onclick='openSharedModal("edit", <?= $json ?>)'>View Details</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    </div>

</main>

<!-- SHARED ADD/VIEW/EDIT MODAL (single modal used for all three modes) -->
<div id="sharedModalBackdrop" class="modal-backdrop hidden"></div>
<div id="sharedModal" class="modal hidden" role="dialog" aria-modal="true">
    <form id="sharedForm" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" id="shared_action" value="">
        <input type="hidden" name="i_donation_id" id="shared_animal_id" value="">

        
        <!-- IMAGE PREVIEW (Shown only on view/edit) -->
        <div style="margin-top:10px;" class="flex-c center">
            <h2 id="sharedTitle">Modal</h2>
            <img id="img_preview" src="" 
                style="max-height:150px; max-width:150px; border-radius:8px; display:none;">
        </div>

        <div class="row">
            <div class="col">
                <label>Item Name</label>
                <input type="text" name="item_name" id="item_name_field" required>
            </div>
            <div class="col">
                <label>Type</label>
                <input list="type-options" id="type_field" name='donation_type' placeholder="Enter item type"required>
                <datalist id="type-options">
                    <option value="Food">
                    <option value="Supplies">
                    <option value="Medicine">
                    <option value="Toys">
                    <option value="Materials">
                    <option value="Equipment">
                </datalist> 
            </div>
        </div>

        <div class="row" style="margin-top:8px;">
            <div class="col">
                <label>Donor</label>
                <input type="text" name="full_name" id="donor_field" required>
            </div>
            <div class="col">
                <label>Contact</label>
                <input type="number" name="contact_num" id="contact_field" min="0">
            </div>
        </div>

        <div class="row" style="margin-top:8px;">
            <div class="col">
                <label>Drop-off Location</label>
                <input type="text" name="location" id="location_field" required>
            </div>
            <div class="col">
                <label>Drop-off Date</label>
                <input type="date" name="date" id="date_field">
            </div>
        </div>

        <div class="row" style="margin-top:8px;">
            <div class="col">
                <label>Status</label>
                 <select name="status" id="status_field">
                    <option value="Not Received">Not Received</option>
                    <option value="Received">Received</option>
                    <option value="In Inventory">In Inventory</option>
                </select>
            </div>

            <div class="col">
               <label>Message</label>
                <textarea name="message" id="message_field" rows="1"></textarea>
            </div>

        </div>
       
        <div class="row" style="margin-top:8px;">     
       

            <div class="col hidden">
                <input type="text" name="img" id="img_field" placeholder="filename.png" class="hidden">
                <input type="file" name="img_file" id="img_file_field" accept="image/*" class="hidden">
            </div>
        </div>

        <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
            <button type="button" class="btn btn-secondary" onclick="closeSharedModal()">Cancel</button>
            <button type="submit" class="btn btn-primary" id="sharedConfirmBtn">Confirm</button>
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

    function openSharedModal(mode, data = null) {
        document.getElementById('sharedModalBackdrop').classList.remove('hidden');
        const modal = document.getElementById('sharedModal');
        modal.classList.remove('hidden');

        const title = document.getElementById('sharedTitle');
        const actionInput = document.getElementById('shared_action');
        const idInput = document.getElementById('shared_animal_id');

        const fields = {
            item_name: document.getElementById('item_name_field'),
            donation_type: document.getElementById('type_field'),
            full_name: document.getElementById('donor_field'),
            contact_num: document.getElementById('contact_field'),
            location: document.getElementById('location_field'),
            date: document.getElementById('date_field'),
            status: document.getElementById('status_field'),
            message: document.getElementById('message_field'),
            img: document.getElementById('img_field'),
            img_file: document.getElementById('img_file_field')
        };

        const preview = document.getElementById('img_preview');

        // Clear previous file input value
        fields.img_file.value = "";

        
        if (mode === 'edit') {
            // SHOW IMAGE PREVIEW
            if (data.img) {
                preview.src = "../Assets/UserGenerated/" + data.img;
                preview.style.display = "block";
            } else {
                preview.style.display = "none";
            }

            title.textContent = 'Donation Details';
            actionInput.value = 'update';
            idInput.value = data.i_donation_id ?? '';

            // populate fields and make editable
            for (const k in fields) {
                if (k === 'img_file') continue;
                fields[k].value = data[k] ?? '';
                fields[k].removeAttribute('readonly');
                fields[k].removeAttribute('disabled');
                fields[k].classList.remove('disabled');
            }
            document.getElementById('sharedConfirmBtn').textContent = 'Set as Received';
            // ensure normal submit will proceed (update)
            document.getElementById('sharedForm').onsubmit = null;
        }
    }




    function closeSharedModal() {
        document.getElementById('sharedModalBackdrop').classList.add('hidden');
        document.getElementById('sharedModal').classList.add('hidden');
        // reset form submit handler to default
        document.getElementById('sharedForm').onsubmit = null;
    }

    // delete modal
    function openDeleteModal(id, name) {
        document.getElementById('deleteBackdrop').classList.remove('hidden');
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        document.getElementById('delete_animal_id').value = id;
        document.getElementById('deleteMessage').textContent = 'Are you sure you want to delete "' + name.replace(/(^"|"$)/g,'') + '" (ID ' + id + ')? This action cannot be undone.';
    }
    function closeDeleteModal() {
        document.getElementById('deleteBackdrop').classList.add('hidden');
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // allow closing modals by clicking on backdrop
    document.getElementById('sharedModalBackdrop').addEventListener('click', closeSharedModal);
    document.getElementById('deleteBackdrop').addEventListener('click', closeDeleteModal);

    // When shared modal is in "view" mode, the Confirm button was hooked to close.
    // For 'add' and 'edit' it will submit the form normally to the server.

   document.getElementById('img_file_field').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const url = URL.createObjectURL(file);
        document.getElementById('img_preview').src = url;
        document.getElementById('img_preview').style.display = "block";
    }
    });

    function openFeedbackModal(msg) {
        document.getElementById('feedbackMessage').textContent = msg;
        document.getElementById('feedbackBackdrop').classList.remove('hidden');
        document.getElementById('feedbackModal').classList.remove('hidden');
    }

    function closeFeedbackModal() {
        document.getElementById('feedbackBackdrop').classList.add('hidden');
        document.getElementById('feedbackModal').classList.add('hidden');
    }

    // close on backdrop click
    document.getElementById('feedbackBackdrop').addEventListener('click', closeFeedbackModal);



</script>

<?php if (!empty($message)): ?>
<script>
    openFeedbackModal("<?= e($message) ?>");
</script>
<?php endif; ?>


</body>
</html>


