<?php
include "../includes/db_connection.php";
include "../Admin/admin_ui.php";

$m_donation_Crud = new DatabaseCRUD('monetary_donation');

// DEFAULT filter values
$year  = date("Y");
$month = date("n");
$limit = 5;

// If filters submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search_btn'])) {
    $year  = intval($_POST['filter_year'] ?? $year);
    $month = intval($_POST['filter_month'] ?? $month);
    $limit = intval($_POST['filter_limit'] ?? $limit);
}

$conn = connect();

// Query Top Donors
$sql = "
    SELECT full_name, SUM(amount) AS total_donated
    FROM monetary_donation
    WHERE YEAR(date_donated) = ? 
      AND MONTH(date_donated) = ?
    GROUP BY full_name
    ORDER BY total_donated DESC
    LIMIT ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $year, $month, $limit);
$stmt->execute();
$result = $stmt->get_result();
$topDonors = $result;
$stmt->close();
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Donations Admin/TopDonors</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/admin_style.css">
    <link rel="icon" type="image/png" href="../Assets/UI/Taara_Logo.webp">
</head>
<body>

<?= displayNav('donations'); ?>

<main class="content flex-c">

    <div style='padding-inline: 10px;'>
    <h2>🥇TOP DONORS</h2>

    <!-- FILTER FORM -->
    <form method="POST" style="margin-bottom:12px;">
        <div class="search-group">

            <!-- YEAR FILTER -->
            <div class='flex-r c-gap center'>
                <label>Year</label><br>
                <input type="number" name="filter_year" min="2021" max="2030"
                       value="<?= htmlspecialchars($year) ?>">
            </div>

            <!-- MONTH FILTER -->
            <div class='flex-r c-gap center'>
                <label>Month</label><br>
                <input type="number" name="filter_month" min="1" max="12"
                       value="<?= htmlspecialchars($month) ?>">
            </div>

            <!-- LIMIT FILTER -->
            <div class='flex-r c-gap center'>
                <label>Top</label><br>
                <input type="number" name="filter_limit" min="1" max="50"
                       value="<?= htmlspecialchars($limit) ?>">
            </div>

            <div>
                <button type="submit" name="search_btn" class="btn btn-primary">Apply</button>
            </div>
        </div>
    </form>

    <!-- RESULT TABLE -->
    <div class="result-table">
        <table class="rounded-border">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Donor</th>
                    <th>Total Donation Amount</th>
                </tr>
            </thead>

            <tbody>
            <?php 
            $rank = 1;
            if ($topDonors && $topDonors->num_rows > 0): 
                foreach ($topDonors as $td): ?>
                    <tr>
                        <td><?= $rank++ ?></td>
                        <td><?= htmlspecialchars($td['full_name']) ?></td>
                        <td>₱<?= htmlspecialchars($td['total_donated']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">No records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    </div>

</main>
</body>
</html>



