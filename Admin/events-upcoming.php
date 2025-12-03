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

$fieldsConfig = [
    'event_id' => 'id',
    'title' => 'text',
    'description' => 'textarea',
    'img' => 'image',
    'location' => 'text',
    'event_date' => 'date',
    'date_posted' => 'date'
];

$fieldLabels = [
    'event_id' => 'ID',
    'title' => 'Title',
    'description' => 'Description',
    'img' => 'Event Poster',
    'location' => 'Location',
    'event_date' => 'Event Date',
    'date_posted' => 'Date Posted'
];

$_SESSION['fields_config'] = $fieldsConfig; 

$defaultColumns = ['event_id','title','img','location','event_date'];
if(!isset($_SESSION['visibleColumns'])) {
    $_SESSION['visibleColumns'] = $defaultColumns;
}
$visibleColumns = $_SESSION['visibleColumns'];

$message = "";

// -------------------- HELPER --------------------
function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES); }

// Split events by date
$today = date('Y-m-d');
$ongoingEvents = [];
$upcomingEvents = [];
$pastEvents = [];

foreach ($tableData as $row) {
    $eventDate = $row['event_date'] ?? null;
    if (!$eventDate) continue;

    if ($eventDate === $today) {
        $ongoingEvents[] = $row;
    } elseif ($eventDate > $today) {
        $upcomingEvents[] = $row;
    } else {
        $pastEvents[] = $row;
    }
}

include "../includes/post_handler.php"; //Handles POST (search, CRUD, etc)

// -------------------- TABLE RENDERER --------------------
function renderEventTable($rows, $visibleColumns, $fieldsConfig, $pk, $fieldLabels, $headerText, $colorClass) {
    ?>
    <h3 class="section-header"><?= $headerText ?></h3>
    <div class="result-table" style="margin-bottom:20px;">
        <table class="rounded-border">
            <thead>
            <tr>
                <?php foreach($visibleColumns as $f): ?>
                    <th><?= e($fieldLabels[$f] ?? ucwords(str_replace('_',' ',$f))) ?></th>
                <?php endforeach; ?>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if(empty($rows)): ?>
                <tr><td colspan="<?= sizeOF($visibleColumns)+1 ?>">No records found.</td></tr>
            <?php else: ?>
                <?php foreach($rows as $row): ?>
                <tr>
                    <?php foreach($visibleColumns as $f): ?>
                        <?php if ($fieldsConfig[$f] === 'image'): ?>
                            <?php
                                $imgFile = $row[$f] ?? '';
                                $imgPath = "../Assets/Images/" . $imgFile;
                                $exists = !empty($imgFile) && file_exists(__DIR__ . "/../Assets/Images/" . $imgFile);
                            ?>
                            <td>
                                <?php if ($exists): ?>
                                    <img src="<?= $imgPath ?>" class="thumb-img" onclick="openImagePreview('<?= $imgPath ?>')">
                                <?php else: ?>
                                    <span>No image</span>
                                <?php endif; ?>
                            </td>
                        <?php else: ?>
                            <td><?= e($row[$f] ?? '') ?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <td>
                        <?php $json = htmlspecialchars(json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8'); ?>
                        <button onclick="openSharedModal('edit', <?= $json ?>)" class="btn btn-info action-btn">Edit</button>
                        <button onclick="openDeleteModal(<?= e($row[$pk]) ?>,'<?= e($row[$pk]) ?>')" class="btn btn-danger action-btn">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= ucwords($tableName) ?> Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../CSS/admin_style.css">
<link rel="icon" type="image/png" href="../Assets/UI/Taara_Logo.webp">
<style>
    .section-header {
        padding: 8px 12px;
        border-radius: 4px;
        color: #fff;
        margin-top: 20px;
        width: 30%;
        background-color: #007bff;
    } 
</style>
</head>

<body>
<?= displayNav('events'); ?>

<main class="content flex-c">
    <header class='top-nav flex-r'>
        <a href='events-upcoming.php' class='top-active'>Events</a>
        <a href='events-calendar.php'>Calendar</a>
    </header>

    <div style='padding-inline:10px;'>
        <h2>📋 <?= ucwords(str_replace('_',' ',$tableName)) ?> Tables</h2>

        <!-- ACTION BUTTON -->
        <button class="btn btn-primary" onclick="openSharedModal('add')">+ Add <?= ucwords($tableName) ?></button>
        <a href="../export/export_pdf.php?table=<?=$tableName?>" target="_blank"><button type='button' class="btn btn-success">Export as PDF</button></a>

        <!-- THREE SEPARATE TABLES -->
        <?php
            renderEventTable($ongoingEvents, $visibleColumns, $fieldsConfig, $pk, $fieldLabels, "📌 Ongoing Events", "ongoing");
            renderEventTable($upcomingEvents, $visibleColumns, $fieldsConfig, $pk, $fieldLabels, "📅 Upcoming Events", "upcoming");
            renderEventTable($pastEvents, $visibleColumns, $fieldsConfig, $pk, $fieldLabels, "🕑 Past Events", "past");
        ?>
    </div>
</main>

<!-- DYNAMIC MODAL -->
<?php include 'dynamic_modal.php'; ?>

<?php if(!empty($message)): ?>
<script> showMessage("<?= e($message) ?>"); </script>
<?php endif; ?>
</body>
</html>
