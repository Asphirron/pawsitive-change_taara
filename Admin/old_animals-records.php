<?php
// admin_animals.php
include "../includes/db_connection.php";
include "../Admin/admin_ui.php";

// Instantiate CRUD for 'animal' table
$animalCrud = new DatabaseCRUD('animal');

// default dataset - read all
$animal_table = $animalCrud->readAll();
$message = "";

// Helper: safe output
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES); }

// Handle POST requests (Add / Update / Delete / Search)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---------- RESET FILTERS ----------
    if (isset($_POST['reset_btn'])) {
        $animal_table = $animalCrud->readAll();
        $_POST = [];
        goto END_POST_PROCESSING;
    }

    // ---------- SEARCH ----------  
    if (isset($_POST['search_btn'])) {
        $conn = connect();

        $allowed_columns = ['animal_id','name','description','type','breed','gender','age','behavior','date_rescued','status'];

        $search_by = $_POST['search_by'] ?? 'none';
        $search_value = trim($_POST['search_bar'] ?? '');
        $order_by = $_POST['order_by'] ?? 'ascending';
        $limit = isset($_POST['num_of_results']) ? intval($_POST['num_of_results']) : 10;

        $order_dir = strtolower($order_by) === 'descending' ? 'DESC' : 'ASC';
        if ($limit <= 0 || $limit > 1000) $limit = 50;

        // CASE 1: No search + No filter → remove WHERE completely
        if ($search_by === 'none' && $search_value === '') {
            $sql = "SELECT * FROM `animal` ORDER BY `animal_id` $order_dir LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $limit);
        }

        // CASE 2: Valid search column + numeric search
        elseif (in_array($search_by, ['animal_id','age'], true) && is_numeric($search_value)) {
            $sql = "SELECT * FROM `animal` 
                    WHERE `$search_by` = ? 
                    ORDER BY `animal_id` $order_dir 
                    LIMIT ?";
            $stmt = $conn->prepare($sql);
            $ival = intval($search_value);
            $stmt->bind_param("ii", $ival, $limit);
        }

        // CASE 3: Valid search column + string search
        elseif (in_array($search_by, $allowed_columns, true)) {
            $sql = "SELECT * FROM `animal` 
                    WHERE `$search_by` = ? 
                    ORDER BY `animal_id` $order_dir 
                    LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $search_value, $limit);
        }

        // CASE 4: Fallback if invalid search option → treat as no search
        else {
            $sql = "SELECT * FROM `animal` ORDER BY `animal_id` $order_dir LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $limit);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;

        $animal_table = $rows;
        $stmt->close();
        $conn->close();
    }
    // ---------- ADD ----------
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        // Ensure date_rescued exists (schema requires NOT NULL)
        $date_rescued = $_POST['date_rescued'] ?? date('Y-m-d');

        // process uploaded image (if any)
        $imgToSave = $_POST['img'] ?? 'dog.png'; // fallback filename input or default
        if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../Assets/UserGenerated/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $orig = basename($_FILES['img_file']['name']);
            $ext = pathinfo($orig, PATHINFO_EXTENSION);
            $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($orig, PATHINFO_FILENAME));
            $newName = $safeBase . '_' . time() . ($ext ? '.' . $ext : '');
            $target = $uploadDir . $newName;
            if (move_uploaded_file($_FILES['img_file']['tmp_name'], $target)) {
                $imgToSave = $newName;
            } else {
                $message = "Warning: image upload failed; using default or provided filename.";
            }
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'type' => $_POST['type'] ?? '',
            'breed' => $_POST['breed'] ?? '',
            'gender' => $_POST['gender'] ?? '',
            'age' => isset($_POST['age']) ? intval($_POST['age']) : 0,
            'behavior' => $_POST['behavior'] ?? '',
            'date_rescued' => $date_rescued,
            'status' => $_POST['status'] ?? '',
            'img' => $imgToSave
        ];

        $newId = $animalCrud->create($data);
        $message = $newId ? "Added animal ID: $newId" : "Failed to add animal.";
        $animal_table = $animalCrud->readAll();
    }

    // ---------- UPDATE ----------
    if (isset($_POST['action']) && $_POST['action'] === 'update' && !empty($_POST['animal_id'])) {
        $id = intval($_POST['animal_id']);
        // fetch current row to preserve img if no new upload
        $current = $animalCrud->read($id, 'animal_id') ?: [];

        // image handling
        $imgToSave = $current['img'] ?? 'dog.png';
        // if a filename was typed in the img input, use it as fallback
        if (!empty($_POST['img'])) $imgToSave = $_POST['img'];
        if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../Assets/UserGenerated/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $orig = basename($_FILES['img_file']['name']);
            $ext = pathinfo($orig, PATHINFO_EXTENSION);
            $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($orig, PATHINFO_FILENAME));
            $newName = $safeBase . '_' . time() . ($ext ? '.' . $ext : '');
            $target = $uploadDir . $newName;
            if (move_uploaded_file($_FILES['img_file']['tmp_name'], $target)) {
                $imgToSave = $newName;
            } else {
                $message = "Warning: image upload failed; keeping previous image.";
            }
        }

        $fields = [
            'name' => $_POST['name'] ?? $current['name'] ?? '',
            'description' => $_POST['description'] ?? $current['description'] ?? '',
            'type' => $_POST['type'] ?? $current['type'] ?? '',
            'breed' => $_POST['breed'] ?? $current['breed'] ?? '',
            'gender' => $_POST['gender'] ?? $current['gender'] ?? '',
            'age' => isset($_POST['age']) ? intval($_POST['age']) : intval($current['age'] ?? 0),
            'behavior' => $_POST['behavior'] ?? $current['behavior'] ?? '',
            'date_rescued' => $_POST['date_rescued'] ?? $current['date_rescued'] ?? date('Y-m-d'),
            'status' => $_POST['status'] ?? $current['status'] ?? '',
            'img' => $imgToSave
        ];

        $success = $animalCrud->update($id, $fields, "animal_id");
        $message = $success ? "Updated animal $id" : "Failed to update animal $id";
        $animal_table = $animalCrud->readAll();
    }

    // ---------- DELETE ----------
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && !empty($_POST['animal_id'])) {
        $id = intval($_POST['animal_id']);
        $success = $animalCrud->delete($id, "animal_id");
        $message = $success ? "Deleted animal $id" : "Failed to delete animal $id";
        $animal_table = $animalCrud->readAll();
    }
}

END_POST_PROCESSING:
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Animal Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/admin_style.css">
    <link rel="icon" type="image/png" href="../Assets/UI/Taara_Logo.webp">
    <style>

        
    </style>
</head>
<body>

<?= displayNav('animals'); ?>

<main class="content flex-c">
    <header class='top-nav flex-r'>
        <a href='index.php' class='top-active'>Records</a>
        <a href='index.php'>Activities</a>
        <a href='index.php'>Vaccinations</a>
    </header>

    <div style='padding-inline: 10px;'>
    <h2>🐾Animals Table</h2>

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
                    <option value="animal_id" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'animal_id') ? 'selected' : '' ?>>Id</option>
                    <option value="name" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'name') ? 'selected' : '' ?>>Name</option>
                    <option value="type" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'type') ? 'selected' : '' ?>>Type</option>
                    <option value="breed" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'breed') ? 'selected' : '' ?>>Breed</option>
                    <option value="age" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'age') ? 'selected' : '' ?>>Age</option>
                    <option value="status" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'status') ? 'selected' : '' ?>>Status</option>
                    <option value="date_rescued" <?= (isset($_POST['search_by']) && $_POST['search_by'] === 'date_rescued') ? 'selected' : '' ?>>Date Rescued</option>
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

    <!-- ACTION BUTTONS -->
    <div class="action-buttons flex-c wrap" style="margin-bottom:8px;">
        <div class="action_btn_group flex-r">
            <button class="btn btn-primary" onclick="openSharedModal('add')"><img class="icon-1" src="../Assets/Ui/more.png"> Add Animal</button>
        </div>
    </div>
   
    <!-- RESULT TABLE -->
    <div class="result-table">
        <table class="rounded-border">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Breed</th>
                <th>Age</th>
                <th>Status</th>
                <th class="action-row">Action</th>
            </tr>
            </thead>

            <tbody>
            <?php if (!empty($animal_table)): ?>
                <?php foreach($animal_table as $animal): ?>
                    <tr>
                        <td><?= e($animal['animal_id']) ?></td>
                        <td><?= e($animal['name']) ?></td>
                        <td><?= e($animal['type']) ?></td>
                        <td><?= e($animal['breed']) ?></td>
                        <td><?= e($animal['age']) ?></td>
                        <td><?= e($animal['status']) ?></td>
                        <td class="action-row">
                            <?php $json = htmlspecialchars(json_encode($animal), ENT_QUOTES); ?>
                            <button class="btn" onclick='openSharedModal("view", <?= $json ?>)'><img class="icon-1" src="../Assets/Ui/view.png" title="View more details"></button>
                            <button class="btn" onclick='openSharedModal("edit", <?= $json ?>)'><img class="icon-1" src="../Assets/Ui/pencil.png" title="Edit"></button>
                            <button class="btn btn-danger" onclick='openDeleteModal(<?= e($animal["animal_id"]) ?>, <?= json_encode(e($animal["name"])) ?>)'><img class="icon-1" src="../Assets/Ui/trash.png" title="Delete"></button>
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

<!-- SHARED ADD/VIEW/EDIT MODAL (single modal used for all three modes) -->
<div id="sharedModalBackdrop" class="modal-backdrop hidden"></div>
<div id="sharedModal" class="modal hidden" role="dialog" aria-modal="true">
    <form id="sharedForm" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" id="shared_action" value="">
        <input type="hidden" name="animal_id" id="shared_animal_id" value="">

        <!-- IMAGE PREVIEW (Shown only on view/edit) -->
        <div style="margin: 10px;" class="flex-c center">
            <h2 id="sharedTitle">Modal</h2>
            <img id="img_preview" src="" 
                style="max-height:150px; max-width:150px; border-radius:8px; display:none;">
        </div>

        <div class="row">
            <div class="col">
                <label>Name</label>
                <input type="text" name="name" id="name_field" required>
            </div>
            <div class="col">
                <label>Type</label>
                <select name="type" id="type_field">
                    <option value="Dog">Dog</option>
                    <option value="Cat">Cat</option>
                    <option value="Other">Other</option>
                </select>
            </div>
        </div>

        <div class="row" style="margin-top:8px;">
            <div class="col">
                <label>Breed</label>
                <input list="breed-options" id="breed_field" name='breed' placeholder="Select Breed"required>
                <datalist id="breed-options">
                    <option value="Labrador">
                    <option value="Poodle">
                    <option value="Golden Retriever">
                    <option value="Persian">
                    <option value="Siamese">
                    <option value="Bengal">
                </datalist>
            </div>
            <div class="col">
                <label>Gender</label>
                <select name="gender" id="gender_field">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
        </div>

        <div class="row" style="margin-top:8px;">
            <div class="col">
                <label>Age</label>
                <input type="number" name="age" id="age_field" min="0">
            </div>
            <div class="col">
                <label>Date Rescued</label>
                <input type="date" name="date_rescued" id="date_rescued_field">
            </div>
        </div>

        <div class="row" style="margin-top:8px;">
            <div class="col">
                <label>Status</label>
                 <select name="status" id="status_field">
                    <option value="At a Shelter">At a Shelter</option>
                    <option value="Adopted">Adopted</option>
                    <option value="Pending Adoption">Pending Adoption</option>
                </select>
            </div>

            <div class="col">
                <label>Behavior</label>
                <select name="behavior" id="behavior_field">
                    <option value="Friendly">Friendly</option>
                    <option value="Playful">Playful</option>
                    <option value="Calm">Calm</option>
                    <option value="Aggressive">Aggressive</option>
                    <option value="Timid">Timid</option>
                </select>
            </div>

        </div>
       
        <div class="row" style="margin-top:8px;">     
            <div class="col">
                <label>Description</label>
                <textarea name="description" id="description_field" rows="3"></textarea>
            </div>

            <div class="col">
                <label>Image filename (or upload new)</label>
                <input type="text" name="img" id="img_field" placeholder="filename.png">
                <input type="file" name="img_file" id="img_file_field" accept="image/*">
            </div>
        </div>

        <div style="margin-top:12px; display:flex; gap:8px; justify-content:flex-end;">
            <button type="button" class="btn btn-secondary" onclick="closeSharedModal()">Cancel</button>
            <button type="submit" class="btn btn-primary" id="sharedConfirmBtn">Confirm</button>
        </div>
    </form>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div id="deleteBackdrop" class="modal-backdrop hidden"></div>
<div id="deleteModal" class="modal hidden" role="dialog" aria-modal="true" style="width:400px;">
    <form id="deleteForm" method="POST">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="animal_id" id="delete_animal_id" value="">
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
    // Utility to open the shared modal in different modes:
    // mode = 'add' | 'view' | 'edit'
    // data = JS object for view/edit (animal row)
    function openSharedModal(mode, data = null) {
        document.getElementById('sharedModalBackdrop').classList.remove('hidden');
        const modal = document.getElementById('sharedModal');
        modal.classList.remove('hidden');

        const title = document.getElementById('sharedTitle');
        const actionInput = document.getElementById('shared_action');
        const idInput = document.getElementById('shared_animal_id');

        const fields = {
            name: document.getElementById('name_field'),
            type: document.getElementById('type_field'),
            breed: document.getElementById('breed_field'),
            gender: document.getElementById('gender_field'),
            age: document.getElementById('age_field'),
            date_rescued: document.getElementById('date_rescued_field'),
            status: document.getElementById('status_field'),
            behavior: document.getElementById('behavior_field'),
            description: document.getElementById('description_field'),
            img: document.getElementById('img_field'),
            img_file: document.getElementById('img_file_field')
        };

        const preview = document.getElementById('img_preview');

        // Clear previous file input value
        fields.img_file.value = "";

        if (mode === 'add') {
            // ADD MODE
            preview.style.display = "none";  // hide preview
            preview.src = "";
            title.textContent = 'Add Animal';
            actionInput.value = 'add';
            idInput.value = '';

            // empty fields and make editable
            for (const k in fields) {
                if (k === 'img_file') continue;
                fields[k].value = '';
                fields[k].removeAttribute('readonly');
                fields[k].removeAttribute('disabled');
                fields[k].classList.remove('disabled');
            }
            // default date to today
            fields.date_rescued.value = new Date().toISOString().slice(0,10);
            document.getElementById('sharedConfirmBtn').textContent = 'Add';
        } else if (mode === 'view') {
            // SHOW IMAGE PREVIEW
            if (data.img) {
                preview.src = "../Assets/UserGenerated/" + data.img;
                preview.style.display = "block";
            } else {
                preview.style.display = "none";
            }

            fields.img.style.display = "block";
            fields.img.style.file = "block";

            title.textContent = 'View Animal';
            actionInput.value = ''; // no action on view
            idInput.value = data.animal_id ?? '';

            // populate fields, make readonly/disabled
            for (const k in fields) {
                if (k === 'img_file') continue;
                fields[k].value = data[k] ?? '';
                fields[k].setAttribute('readonly','readonly');
                fields[k].setAttribute('disabled','disabled');
                fields[k].classList.add('disabled');
            }
            document.getElementById('sharedConfirmBtn').textContent = 'Close';
            // override submit to simply close
            document.getElementById('sharedForm').onsubmit = function(e){
                e.preventDefault();
                closeSharedModal();
            };
        } else if (mode === 'edit') {
            // SHOW IMAGE PREVIEW
            if (data.img) {
                preview.src = "../Assets/UserGenerated/" + data.img;
                preview.style.display = "block";
            } else {
                preview.style.display = "none";
            }

            title.textContent = 'Edit Animal';
            actionInput.value = 'update';
            idInput.value = data.animal_id ?? '';

            // populate fields and make editable
            for (const k in fields) {
                if (k === 'img_file') continue;
                fields[k].value = data[k] ?? '';
                fields[k].removeAttribute('readonly');
                fields[k].removeAttribute('disabled');
                fields[k].classList.remove('disabled');
            }
            document.getElementById('sharedConfirmBtn').textContent = 'Save';
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


