<?php
// test_dompdf.php

// Always use __DIR__ to avoid relative path issues
require __DIR__ . "/../vendor/autoload.php";

use Dompdf\Dompdf;

echo "<pre>PHP Version: " . phpversion() . "</pre>";
echo "<pre>Extensions: " . implode(", ", get_loaded_extensions()) . "</pre>";

$html = "<h1>Hello from Dompdf</h1><p>If you see this as a PDF, Dompdf works!</p>";

$dompdf = new Dompdf([
    'isHtml5ParserEnabled' => true,
    'isRemoteEnabled' => true,
]);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Stream inline PDF
$dompdf->stream("test.pdf", ["Attachment" => false]);
exit;
