<?php
// admin_animals.php
include "../includes/db_connection.php";
include "../Admin/admin_ui.php";

// Instantiate CRUD for 'animal' table
$m_donation_Crud = new DatabaseCRUD('monetary_donation');

// default dataset - read all
$m_donation_table = $m_donation_Crud->readAll();
$message = "";

// Helper: safe output
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES); }

// Handle POST requests (Add / Update / Delete / Search)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---------- RESET FILTERS ----------
    if (isset($_POST['reset_btn'])) {
        $m_donation_table = $m_donation_Crud->readAll();
        $_POST = [];
        goto END_POST_PROCESSING;
    }

    // ---------- SEARCH ----------  
    if (isset($_POST['search_btn'])) {
        $conn = connect();

        $allowed_columns = ['m_donation_id','full_name','amount','payment_option','date_donated','status'];

        $search_by = $_POST['search_by'] ?? 'none';
        $search_value = trim($_POST['search_bar'] ?? '');
        $order_by = $_POST['order_by'] ?? 'ascending';
        $limit = isset($_POST['num_of_results']) ? intval($_POST['num_of_results']) : 10;

        $order_dir = strtolower($order_by) === 'descending' ? 'DESC' : 'ASC';
        if ($limit <= 0 || $limit > 1000) $limit = 50;

        // CASE 1: No search + No filter → remove WHERE completely
        if ($search_by === 'none' && $search_value === '') {
            $sql = "SELECT * FROM `monetary_donation` ORDER BY `m_donation_id` $order_dir LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $limit);
        }

        // CASE 2: Valid search column + numeric search
        elseif (in_array($search_by, ['m_donation_id','amount'], true) && is_numeric($search_value)) {
            $sql = "SELECT * FROM `monetary_donation` 
                    WHERE `$search_by` = ? 
                    ORDER BY `m_donation_id` $order_dir 
                    LIMIT ?";
            $stmt = $conn->prepare($sql);
            $ival = intval($search_value);
            $stmt->bind_param("ii", $ival, $limit);
        }

        // CASE 3: Valid search column + string search
        elseif (in_array($search_by, $allowed_columns, true)) {
            $sql = "SELECT * FROM `monetary_donation` 
                    WHERE `$search_by` = ? 
                    ORDER BY `m_donation_id` $order_dir 
                    LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $search_value, $limit);
        }

        // CASE 4: Fallback if invalid search option → treat as no search
        else {
            $sql = "SELECT * FROM `monetary_donation` ORDER BY `m_donation_id` $order_dir LIMIT ?";
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


    // ---------- VERIFY DONATION ----------
    if (isset($_POST['action']) && $_POST['action'] === 'verify' && !empty($_POST['m_donation_id'])) {
        $id = intval($_POST['m_donation_id']);
        $success = $m_donation_Crud->update($id, ['status' => 'Verified'], "m_donation_id");

        if ($success) {
            $message = "Donation #$id has been verified.";
        } else {
            $message = "Failed to verify donation #$id.";
        }

        $m_donation_table = $m_donation_Crud->readAll();
        goto END_POST_PROCESSING;
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
    <header class='top-nav flex-r'>
        <a href='donations-topdonors.php'>Top Donors</a>
        <a href='donations-monetary.php' class='top-active'>Monetary Donation</a>
        <a href='donations-inkind.php'>In-kind Donation</a>
    </header>

    <div style='padding-inline: 10px;'>
    <h2>💰Monetary Donations</h2>

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
                    <option value="donor" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'full_name') ? 'selected' : '' ?>>Donor</option>
                    <option value="amount" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'amount') ? 'selected' : '' ?>>Amount</option>
                    <option value="payment_option" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'payment_option') ? 'selected' : '' ?>>Payment Option</option>
                    <option value="date_donated" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'date_donated') ? 'selected' : '' ?>>Date Donated</option>
                    <option value="status" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'status') ? 'selected' : '' ?>>Status</option>
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
                <th>Amount</th>
                <th>Payment Option</th>
                <th>Date Donated</th>
                <th>Status</th>
                <th class="action-row">Action</th>
            </tr>
            </thead>

            <tbody>
            <?php if (!empty($m_donation_table)): ?>
                <?php foreach($m_donation_table as $donation): ?>
                    <tr>
                        <td><?= e($donation['m_donation_id']) ?></td>
                        <td><?= e($donation['full_name']) ?></td>
                        <td>₱<?= e($donation['amount']) ?></td>
                        <td><?= e($donation['payment_option']) ?></td>
                        <td><?= e($donation['date_donated']) ?></td>
                        <td><?= e($donation['status']) ?></td>
                        <td class="action-row">
                            <?php $json = htmlspecialchars(json_encode($donation), ENT_QUOTES); ?>
                            <?php if ($donation['status'] === 'Verified'): ?>
                            <button class="btn" onclick='openSharedModal("view", <?= $json ?>)'>View Details</button>
                            <?php else: ?>
                                <button class="btn btn-success" onclick='openSharedModal("verify", <?= $json ?>)'>Verify Donation</button>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">No records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    </div>

</main>

<!-- VERIFY MODAL -->
<div id="sharedModalBackdrop" class="modal-backdrop hidden"></div>
<div id="sharedModal" class="modal hidden" role="dialog" aria-modal="true">
    <form id="sharedForm" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" id="shared_action" value="">
        <input type="hidden" name="m_donation_id" id="shared_animal_id">

        <h2 id="sharedTitle" class="flex-c center">Modal</h2>
        <div style="margin:10px;" class="flex-c center">
            <img id="img_preview" src="" 
                style="max-height:150px; max-width:150px; border-radius:8px; display:none;">
        </div>

        <div class="flex-c">
                <div class='row'>
                    <b><p>ID: </p></b>
                    <p id='id_field'>None</p>
                </div>
                <div class='row'>
                    <b><p>Donor: </p></b>
                    <p id='donor_field'>None</p>
                </div>
                <div class='row'>
                    <b><p>Amount:</p></b>
                    <p id='amount_field'>None</p>
                </div>
                <div class='row'>
                    <b><p>Payment Method: </p></b>
                    <p id='payment_method_field'>None</p>
                </div>
                <div class='row'>
                    <b><p>Date Donated: </p></b>
                    <p id='date_donated_field'>None</p>
                </div>
                <div class='row'>
                    <b><p>Status: </p></b>
                    <p id='status_field'>None</p>
                </div>
                <div class='row'>
                    <b><p>Message: </p></b>
                    <p id='message_field'>None</p>
                </div>
                <div class='row'>
                    <b><p>Contact: </p></b>
                    <p id='contact_field'>None</p>
                </div>
            
                <input class="hidden" type="text" name="img" id="img_field" placeholder="filename.png">
                <input class="hidden" type="file" name="img_file" id="img_file_field" accept="image/*">
            
                <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeSharedModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="sharedConfirmBtn">Confirm</button>
                </div>
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
    const confirmBtn = document.getElementById('sharedConfirmBtn');
    const cancelBtn = document.querySelector("#sharedForm .btn-secondary");
    const actionInput = document.getElementById('shared_action');

    // Hidden input for PHP (note name is m_donation_id)
    const idInput = document.getElementById('shared_animal_id');

    // Fields (these are <p> elements in your modal)
    const fields = {
        m_donation_id: document.getElementById('id_field'),
        full_name: document.getElementById('donor_field'),
        amount: document.getElementById('amount_field'),
        payment_option: document.getElementById('payment_method_field'),
        date_donated: document.getElementById('date_donated_field'),
        status: document.getElementById('status_field'),
        message: document.getElementById('message_field'),
        contact_num: document.getElementById('contact_field'),
    };

    // image preview element
    const preview = document.getElementById('img_preview');

    // Ensure we have a data object
    data = data || {};

    // Fill fields (safe fallback to empty string)
    for (const k in fields) {
        fields[k].innerHTML = data[k] ?? '';
    }

    // set hidden id for form submission (m_donation_id expected by PHP)
    idInput.value = data.m_donation_id ?? '';

    // handle proof image (column name used in your DB earlier was 'proof')
    // try a few possible property names just in case: proof, img, image, receipt
    const proofName = data.proof ?? data.img ?? data.image ?? data.receipt ?? '';

    // --- Hide proof image if payment method is PayPal ---
    const paymentMethod = (data.payment_option || "").toLowerCase();

    if (paymentMethod === "paypal") {
        preview.style.display = "none";
        preview.src = "";
    } 
    else if (proofName) {
        const url = "../Assets/UserGenerated/" + encodeURIComponent(proofName);
        preview.src = url;
        preview.style.display = "block";

        preview.onerror = function () {
            this.style.display = "none";
            this.src = "";
        };
    } 
    else {
        preview.style.display = "none";
        preview.src = "";
    }


    // -------------- MODE: VERIFY ---------------
    if (mode === "verify") {
        title.textContent = "Verify Donation";

        // form action for PHP
        actionInput.value = "verify";

        confirmBtn.textContent = "Verify";
        confirmBtn.style.display = "inline-block";
        confirmBtn.classList.remove('hidden');

        cancelBtn.textContent = "Cancel";

        // make sure confirm submits normally (verify handler expects m_donation_id)
        document.getElementById('sharedForm').onsubmit = null;

        return;
    }

    // -------------- MODE: VIEW (Verified donation) ---------------
    if (mode === "view") {
        title.textContent = "Donation Details";

        // hide the confirm (verify) button, make cancel read "Close"
        confirmBtn.style.display = "none";
        cancelBtn.textContent = "Close";

        // make sure the form does NOT submit if someone hits Enter
        document.getElementById('sharedForm').onsubmit = function(e){ e.preventDefault(); };

        return;
    }

    // fallback: if you ever open with 'edit' mode or others, handle them here
    // (not used in this donation modal)
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


