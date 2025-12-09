<?php
session_start();
include 'includes/db_connection.php';

// Replace with session user
$user_id = $_SESSION['user_id'] ?? 1;

$appDB = new DatabaseCRUD('adoption_application');
$screenDB = new DatabaseCRUD('adoption_screening');
$adoptDB = new DatabaseCRUD('adoption');

// Handle cancellation
if (isset($_POST['cancel_application'])) {
    $id = intval($_POST['a_application_id']);
    $appDB->update($id, ['status' => 'cancelled'], 'a_application_id');
    $appDB->update($id, ['date_responded' => date('Y-m-d H:i:s')], 'a_application_id');
    header('Location: user_adoptions.php');
    exit;
}

// Fetch applications by user
$applications = $appDB->select(["*"], ["user_id" => $user_id]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Adoption Applications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container { margin-top: 50px; }
        .card { border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        h1, h2 { font-weight: 600; color: #1f2937; }
        .btn-cancel { background-color: #ef4444; color: white; }
        .btn-cancel:hover { background-color: #dc2626; }
        .btn-back { background-color: #3b82f6; color: white; }
        .btn-back:hover { background-color: #2563eb; }
        .badge { font-size: 0.9em; }
    </style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Your Adoption Applications</h1>
        <button class="btn btn-back" onclick="window.history.back();">← Back</button>
    </div>

    <div class="card p-4">
        <h2 class="mb-3">🐾 Applications</h2>
        <?php if (!empty($applications)): ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Animal ID</th>
                            <th>Full Name</th>
                            <th>Address</th>
                            <th>Classification</th>
                            <th>Company/School</th>
                            <th>Date Applied</th>
                            <th>Status</th>
                            <th>Responded</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $i => $app): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($app['animal_id']) ?></td>
                                <td><?= htmlspecialchars($app['full_name']) ?></td>
                                <td><?= htmlspecialchars($app['address']) ?></td>
                                <td><?= htmlspecialchars($app['classification']) ?></td>
                                <td><?= htmlspecialchars($app['comp_name']) ?></td>
                                <td><?= htmlspecialchars($app['date_applied']) ?></td>
                                <td>
                                    <span class="badge 
                                        <?= $app['status'] == 'pending' ? 'bg-warning' :
                                           ($app['status'] == 'approved' ? 'bg-success' :
                                           ($app['status'] == 'cancelled' ? 'bg-secondary' : 'bg-danger')) ?>">
                                        <?= ucfirst($app['status']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($app['date_responded'] ?: '—') ?></td>
                                <td>
                                    <?php if ($app['status'] === 'pending'): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="a_application_id" value="<?= $app['a_application_id'] ?>">
                                            <button type="submit" name="cancel_application" class="btn btn-sm btn-cancel">Cancel</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- Screening Display -->
                            <?php 
                                $screenings = $screenDB->select(["*"], ["a_application_id" => $app['a_application_id']]);
                                if (!empty($screenings)):
                            ?>
                            <tr>
                                <td colspan="10">
                                    <div class="bg-light p-3 rounded">
                                        <strong>📋 Screening Details:</strong>
                                        <ul class="mb-0">
                                            <?php foreach ($screenings as $s): ?>
                                                <li>Housing: <?= htmlspecialchars($s['housing']) ?> | 
                                                    Reason: <?= htmlspecialchars($s['reason']) ?> |
                                                    Own Pets: <?= htmlspecialchars($s['own_pets']) ?> |
                                                    Financial: <?= htmlspecialchars($s['financial_ready']) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">You haven’t submitted any adoption applications yet.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
