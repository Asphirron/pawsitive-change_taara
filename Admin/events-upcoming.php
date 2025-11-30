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
$tableName = 'event';   // Change this to your table
$pk = 'event_id';       // Primary key column of the table

$crud = new DatabaseCRUD($tableName);
$tableData = $crud->select();
// Define fields and types for modal & table
$fieldsConfig = [
    'event_id' => 'id',
    'title' => 'text',
    'description' => 'textarea',
    'img' => 'image',
    'location' => 'date',
    'event_date' => 'date',
    'date_posted' => 'date'
];

$foreignKeys = [
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
    'event_id' => 'ID',
    'title' => 'Title',
    'description' => 'Description',
    'img' => 'Event Poster',
    'location' => 'Location',
    'event_date' => 'Event Date',
    'date_posted' => 'Date Posted'
];

$setPropertyConfig = [
    'table_name' => '',
    'property' => '',
    'values' => [],
    'modal_title' => 'Event Information',
    'button_text' => [],
    'button_class' => []
];




$_SESSION['fields_config'] = $fieldsConfig; 

// Columns initially visible in table
$defaultColumns = ['event_id','title','img', 'location', 'event_date'];


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
<?= displayNav('events'); ?>

<main class="content flex-c">
    <header class='top-nav flex-r'>
        <a href='volunteers-records.php' class='top-active'>Records</a>
        <a href='volunteers-application.php'>Applications</a>
    </header>

    <div style='padding-inline:10px;'>
        <h2>📋 <?= ucwords(str_replace('_',' ',$tableName)) ?> Table</h2>

        <!-- SEARCH FORM -->
        <form method="POST" enctype="multipart/form-data" style="margin-bottom:12px;">
            <div class="search-group">
                <input type="text" name="search_bar" class="search-bar" placeholder="Search..." value="<?= e($_POST['search_bar'] ?? '') ?>">
                <select name="search_by">
                    <option value="none">None</option>
                    <?php foreach(array_keys($fieldsConfig) as $f): ?>
                        <option value="<?= $f ?>" <?= (isset($_POST['search_by']) && $_POST['search_by']===$f)?'selected':'' ?>><?= ucwords(str_replace('_',' ',$f)) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="order_by" placeholder='Order by'>
                    <option value="ascending" <?= (isset($_POST['order_by']) && $_POST['order_by']==='ascending')?'selected':'' ?>>Ascending</option>
                    <option value="descending" <?= (isset($_POST['order_by']) && $_POST['order_by']==='descending')?'selected':'' ?>>Descending</option>
                </select>
                <input type="number" name="num_of_results" min="1" max="1000" value="<?= intval($_POST['num_of_results']??10) ?>" placeholder='Results shown'>
                <button type="button" class="btn btn-secondary" onclick="toggleColumnSelector()">Columns</button>

                <div id="column-selector" style="display:none; position:absolute; background:#fff; border:1px solid #ccc; padding:10px; top: 10%; left: 50%;" placeholder='Table columns shown'>
                    <?php foreach($fieldsConfig as $f=>$t): ?>
                        <label>
                            <input type="checkbox" class="column-toggle" value="<?= $f ?>" 
                                <?= in_array($f,$visibleColumns)?'checked':'' ?>> <?= ucwords(str_replace('_',' ',$f)) ?>
                        </label><br>
                    <?php endforeach; ?>
                    <button type="button" onclick="applyColumnSelection()">Apply</button>
                </div>

                <button type="submit" name="reset_btn" class="btn btn-secondary">Reset</button >
                <button type="submit" name="search_btn" class="btn btn-primary">Search</button>

            </div>
        </form>

        <!-- ACTION BUTTON -->
        <button class="btn btn-primary" onclick="openSharedModal('add')">+ Add <?= ucwords($tableName) ?></button>
        <a href="../export/export_pdf.php?table=<?=$tableName?>" target="_blank"><button type='button' class="btn btn-success">Export as PDF</button></a>

        <!-- RESULT TABLE -->
        <div class="result-table">
            <table class="rounded-border">
                <thead>
                <tr>
                    <?php foreach($visibleColumns as $f): ?>
                        <?php 
                            // Check if the column is a foreign key
                            if(isset($foreignKeys[$f])) {
                                $label = $foreignKeys[$f]['label']; // Use the FK label instead of column name
                                echo "<th>" . e($fieldLabels[$f] ?? ucwords(str_replace('_',' ',$f))) . "</th>";
                            } else {
                                echo "<th>" . e($fieldLabels[$f] ?? ucwords(str_replace('_',' ',$f))) . "</th>";
                            }
                        ?>
                    <?php endforeach; ?>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                <?php if(empty($tableData)): ?>
                    <tr><td colspan="<?= sizeOF($visibleColumns) ?>">No records found.</td></tr>
                <?php endif; ?>

                <?php foreach($tableData as $row): ?>
                <tr>

                    <?php foreach($visibleColumns as $f): ?>
                        <?php if ($fieldsConfig[$f] === 'image'): ?>

                            <?php
                                $imgFile = $row[$f] ?? '';
                                $imgPath = "../Assets/UserGenerated/" . $imgFile;
                                $exists = !empty($imgFile) && file_exists(__DIR__ . "/../Assets/UserGenerated/" . $imgFile);
                            ?>
                            <td>
                                <?php if ($exists): ?>
                                    <img 
                                        src="<?= $imgPath ?>" 
                                        class="thumb-img"
                                        onclick="openImagePreview('<?= $imgPath ?>')">
                                <?php else: ?>
                                    <span>No image</span>
                                <?php endif; ?>
                            </td>

                        <?php elseif(isset($foreignKeys[$f])): ?>
                            <td><?= e($fkCache[$f][$row[$f]] ?? "Unknown") ?></td>
                        <?php else: ?>
                            <td><?= e($row[$f] ?? '') ?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <td style="width: auto;">
                        <?php $json = htmlspecialchars(json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8'); ?>
                        <button onclick="openSharedModal('edit', <?= $json ?>)" style='width:60px;' class='btn btn-info action-btn'>Edit</button>
                        <button onclick='openDeleteModal(<?= e($row[$pk]) ?>,"<?= e($row[$pk]) ?>")' style='width:60px;' class='btn btn-danger action-btn'>Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
        </div>
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
