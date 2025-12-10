<?php
session_start();
include 'includes/db_connection.php';

// Replace this with actual session user_id
$user_id = $_SESSION['user_id'] ?? 1; // fallback for testing

$reportDB = new DatabaseCRUD('rescue_report');

// Handle cancel request
if (isset($_POST['cancel_report'])) {
    $id = intval($_POST['report_id']);
    $type = $_POST['type'];

    $reportDB->update($id, ['status' => 'cancelled'], 'report_id');


    header('Location: user_reports.php');
    exit;
}

// Fetch user donations
$rescue_reports = $reportDB->select(["*"], ["user_id" => $user_id, "type" => "rescue"]);
$lost_and_found = $reportDB->select(["*"], ["user_id" => $user_id, "type" => "lost_and_found"]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Rescue and Lost/Found Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        h2 {
            font-weight: 600;
            color: #1f2937;
        }
        table {
            vertical-align: middle;
        }
        .btn-cancel {
            background-color: #ef4444;
            color: white;
        }
        .btn-cancel:hover {
            background-color: #dc2626;
        }
        .btn-back {
            background-color: #3b82f6;
            color: white;
        }
        .btn-back:hover {
            background-color: #2563eb;
        }
        @media (max-width: 768px) {
            table {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Your Rescue and Lost/Found Reports</h1>
        <button class="btn btn-back" onclick="window.history.back();">← Back</button>
    </div>

    <!-- Rescue Reports -->
    <div class="card p-4">
        <h2 class="mb-3">Rescue Reports</h2>
        <?php if (!empty($rescue_reports)): ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th>Date Posted</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rescue_reports as $index => $r): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($r['description']) ?></td>
                                <td><?= htmlspecialchars($r['date_posted']) ?></td>
                                <td>
                                    <span class="badge 
                                        <?= $r['status'] == 'pending' ? 'bg-warning' : 
                                           ($r['status'] == 'resolved' ? 'bg-success' : 'bg-secondary') ?>">
                                        <?= ucfirst($r['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($r['status'] === 'pending'): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="report_id" value="<?= $r['report_id'] ?>">
                                            <input type="hidden" name="type" value="report">
                                            <button type="submit" name="cancel_report" class="btn btn-sm btn-cancel">Cancel</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">You have not made any rescue reports yet.</p>
        <?php endif; ?>
    </div>

    <!-- Rescue Reports -->
    <div class="card p-4">
        <h2 class="mb-3">Lost and Found Reports</h2>
        <?php if (!empty($lost_and_found)): ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th>Date Posted</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lost_and_found as $index => $r): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($r['description']) ?></td>
                                <td><?= htmlspecialchars($r['date_posted']) ?></td>
                                <td>
                                    <span class="badge 
                                        <?= $r['status'] == 'pending' ? 'bg-warning' : 
                                           ($r['status'] == 'resolved' ? 'bg-success' : 'bg-secondary') ?>">
                                        <?= ucfirst($r['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($r['status'] === 'pending'): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="report_id" value="<?= $r['report_id'] ?>">
                                            <input type="hidden" name="type" value="report">
                                            <button type="submit" name="cancel_report" class="btn btn-sm btn-cancel">Cancel</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">You have not made any lost and found reports yet.</p>
        <?php endif; ?>
    </div>

    

</div>

</body>
</html>
