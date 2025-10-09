<?php
session_start();
include 'includes/db_connection.php';

// Replace this with actual session user_id
$user_id = $_SESSION['user_id'] ?? 1; // fallback for testing

$monetaryDB = new DatabaseCRUD('monetary_donation');
$inkindDB   = new DatabaseCRUD('inkind_donation');

// Handle cancel request
if (isset($_POST['cancel_donation'])) {
    $id = intval($_POST['donation_id']);
    $type = $_POST['donation_type'];

    if ($type === 'monetary') {
        $monetaryDB->update($id, ['status' => 'cancelled'], 'm_donation_id');
    } elseif ($type === 'inkind') {
        $inkindDB->delete($id, 'i_donation_id');
    }

    header('Location: your_donations.php');
    exit;
}

// Fetch user donations
$monetary_donations = $monetaryDB->select(["*"], ["user_id" => $user_id]);
$inkind_donations   = $inkindDB->select(["*"], ["user_id" => $user_id]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Donations</title>
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
        <h1 class="fw-bold">Your Donations</h1>
        <button class="btn btn-back" onclick="window.history.back();">← Back</button>
    </div>

    <!-- Monetary Donations -->
    <div class="card p-4">
        <h2 class="mb-3">💰 Monetary Donations</h2>
        <?php if (!empty($monetary_donations)): ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Amount</th>
                            <th>Payment Option</th>
                            <th>Message</th>
                            <th>Contact</th>
                            <th>Date Donated</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monetary_donations as $index => $donation): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($donation['full_name']) ?></td>
                                <td>₱<?= number_format($donation['amount'], 2) ?></td>
                                <td><?= htmlspecialchars($donation['payment_option']) ?></td>
                                <td><?= htmlspecialchars($donation['message']) ?></td>
                                <td><?= htmlspecialchars($donation['contact_num']) ?></td>
                                <td><?= htmlspecialchars($donation['date_donated']) ?></td>
                                <td>
                                    <span class="badge 
                                        <?= $donation['status'] == 'pending' ? 'bg-warning' : 
                                           ($donation['status'] == 'completed' ? 'bg-success' : 'bg-secondary') ?>">
                                        <?= ucfirst($donation['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($donation['status'] === 'pending'): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="donation_id" value="<?= $donation['m_donation_id'] ?>">
                                            <input type="hidden" name="donation_type" value="monetary">
                                            <button type="submit" name="cancel_donation" class="btn btn-sm btn-cancel">Cancel</button>
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
            <p class="text-muted">You have not made any monetary donations yet.</p>
        <?php endif; ?>
    </div>

    <!-- In-Kind Donations -->
    <div class="card p-4">
        <h2 class="mb-3">🎁 In-Kind Donations</h2>
        <?php if (!empty($inkind_donations)): ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Donation Type</th>
                            <th>Message</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inkind_donations as $index => $donation): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($donation['full_name']) ?></td>
                                <td><?= htmlspecialchars($donation['donation_type']) ?></td>
                                <td><?= htmlspecialchars($donation['message']) ?></td>
                                <td><?= htmlspecialchars($donation['contact_num']) ?></td>
                                <td><?= htmlspecialchars($donation['location']) ?></td>
                                <td><?= htmlspecialchars($donation['date']) ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="donation_id" value="<?= $donation['i_donation_id'] ?>">
                                        <input type="hidden" name="donation_type" value="inkind">
                                        <button type="submit" name="cancel_donation" class="btn btn-sm btn-cancel">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">You have not made any in-kind donations yet.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
