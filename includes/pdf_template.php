<?php
// /includes/pdf_template.php
// Corporate PDF generator for TAARA system

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function pdfImage($filename) {
    if (!$filename) return '';

    $filePath = realpath(__DIR__ . '/../Assets/UserGenerated/' . $filename);

    if (!$filePath || !file_exists($filePath)) {
        return '<span style="color:#c00">No image</span>';
    }

    // Determine file extension
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    // If WebP, convert to PNG on-the-fly
    if ($ext === 'webp') {
        if (function_exists('imagecreatefromwebp')) {
            $im = imagecreatefromwebp($filePath);
            if ($im) {
                // Save to temp PNG file
                $tmpFile = tempnam(sys_get_temp_dir(), 'pdfimg_') . '.png';
                imagepng($im, $tmpFile);
                //imagedestroy($im);
                $filePath = $tmpFile; // use this for embedding
            } else {
                return '<span style="color:#c00">Invalid WebP</span>';
            }
        } else {
            return '<span style="color:#c00">WebP not supported</span>';
        }
    }

    // Convert file path to full URL for Dompdf
    $url = 'https://pawsitive-change-taara.com/Assets/UserGenerated/' . rawurlencode($filename);
    return "<img src='{$url}' style='width:80px;height:80px;object-fit:cover;border-radius:8px;'>";


    return "<img src='{$url}' style='width:80px;height:80px;object-fit:cover;border-radius:8px;'>";
}


function buildTaaraPDF($tableName, $columns, $rows) {
    $formattedTableName = strtoupper($tableName);
    $brandTitle = "TAARA {$formattedTableName} REPORT";
    $now = date("m/d/Y, h:i A");

    // Build table HTML
    $tableHTML = "<table class='data-table'><thead><tr>";
    foreach ($columns as $col) {
        $tableHTML .= "<th>" . ucwords(str_replace('_', ' ', $col)) . "</th>";
    }
    $tableHTML .= "</tr></thead><tbody>";

    foreach ($rows as $r) {
        $tableHTML .= "<tr>";
        foreach ($columns as $col) {
            // Check if the column is an image
            if ($col === 'img') {
                $tableHTML .= "<td>" . pdfImage($r[$col] ?? '') . "</td>";
            } else {
                $tableHTML .= "<td>" . htmlspecialchars($r[$col] ?? '') . "</td>";
            }
        }
        $tableHTML .= "</tr>";
    }
    $tableHTML .= "</tbody></table>";

    return "
<!DOCTYPE html>
<html>
<head>
<style>
body { font-family: Arial, sans-serif; margin: 40px; font-size: 12px; }
.header { display: flex; align-items: center; margin-bottom: 20px; }
.header img { width: 80px; height: 80px; object-fit: contain; }
.header-text { margin-left: 15px; }
.header-text .brand { font-size: 20px; font-weight: bold; }
.header-text .subtext { font-size: 12px; color: #555; margin-top: 5px; }
.section-title { margin-top: 25px; font-size: 16px; font-weight: bold; border-bottom: 1px solid #444; padding-bottom: 5px; }
.data-table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 11px; }
.data-table th { background: #e8e8e8; border: 1px solid #aaa; padding: 6px; font-weight: bold; text-align: left; }
.data-table td { border: 1px solid #aaa; padding: 6px; }
.footer { position: fixed; bottom: -10px; left: 0; right: 0; text-align: center; font-size: 10px; color: #555; }
.header {
  display: flex;
  align-items: center;   /* vertically center logo and text */
  margin-bottom: 20px;
}

.header img {
  width: 80px;
  height: 80px;
  object-fit: contain;
  margin-right: 15px;    /* space between logo and text */
}

.header-text {
  display: flex;
  flex-direction: column; /* stack brand and subtext vertically */
}

.header-text .brand {
  font-size: 20px;
  font-weight: bold;
}

.header-text .subtext {
  font-size: 12px;
  color: #555;
  margin-top: 5px;
}

</style>
</head>
<body>
<div class='header'>
    <img src='https://pawsitive-change-taara.com/Assets/UI/taara_logo.png'>
    <div class='header-text'>
        <div class='brand'><?php echo $brandTitle; ?></div>
        <div class='subtext'>
            P-3 Burac St., San Lorenzo, Tabaco, Philippines — Generated on <?php echo $now; ?>
        </div>
    </div>
</div>

<div class='section-title'>{$formattedTableName} TABLE</div>
{$tableHTML}
<div class='footer'>© 2025 Tabaco Animal Advocates and Rescuers Association. All Rights Reserved.</div>
</body>
</html>
    ";
}
?>
