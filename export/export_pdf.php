<?php
// /export/export_pdf.php

require "../vendor/autoload.php";
include "../includes/db_connection.php";
include "../includes/pdf_template.php";

use Dompdf\Dompdf;

session_start();

// Table name (you may pass via GET or SESSION)
$tableName = $_SESSION['export_table'] ?? $_GET['table'];

// Visible columns (same ones used in UI)
$visibleColumns = $_SESSION['visibleColumns'] ?? [];

// Load table data
$crud = new DatabaseCRUD($tableName);
$rows = $crud->readAll();

// Remove soft-deleted records (if using soft delete)
if (isset($_SESSION['soft_deleted'][$tableName])) {
    $deletedIDs = $_SESSION['soft_deleted'][$tableName];
    $rows = array_filter($rows, fn($r) => !in_array($r['animal_id'], $deletedIDs));
}

// Generate HTML from template
$html = buildTaaraPDF($tableName, $visibleColumns, $rows);

// Initialize Dompdf
$dompdf = new Dompdf([
    'isHtml5ParserEnabled' => true,
    'isRemoteEnabled' => true,  // allows HTTP and file:// images
]);


$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Output
$filename = strtoupper($tableName) . "_REPORT_" . date("Ymd_His") . ".pdf";
$dompdf->stream($filename, ["Attachment" => false]);
exit;
?>
