<?php
include 'sessions.php';
//echo $_SESSION['name'];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../donation.php");
    exit;
}

$conn = connect();

// Collect form inputs
$uid = $_SESSION["name"];
$fullname = $_POST['fullname'] ?? '';
$amount   = $_POST['amount'] ?? 0;
$message  = $_POST['message'] ?? '';
$contact  = $_POST['contact'] ?? '';
$agreed   = isset($_POST['agreed']) ? 'yes' : 'no';
$dpost    = $_POST['dpost'] ?? 0;
$method   = $_POST['payment_method'] ?? '';

if (!$fullname || !$amount || !$dpost || !$method) {
    die("⚠ Missing required fields.");
}

// Save donation record in DB (pending until payment confirmed)
$stmt = $conn->prepare("INSERT INTO monetary_donation 
    (user_id, dpost_id, full_name, amount, payment_option, message, contact_num, agreed_to_email, status, date_donated) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

$status = "Pending"; // default

$stmt->bind_param(
    "iisdsssss",
    $uid,
    $dpost,
    $fullname,
    $amount,
    $method,
    $message,
    $contact,
    $agreed,
    $status
);

if ($stmt->execute()) {
    $donation_id = $stmt->insert_id;
} else {
    die("DB Error: " . $conn->error);
}

$stmt->close();
$conn->close();

// -------------------- PAYMENT HANDLING --------------------

// PAYPAL REST API CONFIG
define('PAYPAL_CLIENT_ID', 'AaTFYnkVjsImpWn9NA9cTfQ8HHk6qwC3K_kXdYqBTVnCZAyq8tKUM9zRVkzMrCxA-QKQ0NY2PIBxo_14');
define('PAYPAL_SECRET', 'EERCuYNpruROe5hxZSwjrtmBnap2q_bCIkQdOv1D8ETieBn1TKsaQR-hNJ3F2FvqgeGbTByZpliYDzFJ');
define('PAYPAL_BASE', 'https://api-m.sandbox.paypal.com');

function getPayPalAccessToken() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_BASE."/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json","Accept-Language: en_US"]);
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID.":".PAYPAL_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $res['access_token'] ?? null;
}

function createPayPalOrder($amount, $donation_id) {
    $token = getPayPalAccessToken();
    if (!$token) die("Failed to get PayPal access token.");

    $data = [
        "intent" => "CAPTURE",
        "purchase_units" => [[
            "amount" => [
                "currency_code" => "PHP",
                "value" => $amount
            ],
            "description" => "Donation #$donation_id to TAARA"
        ]],
        "application_context" => [
            "return_url" => "https:pawsitive-change-taara/includes/donation_success.php?donation_id=$donation_id",
            "cancel_url" => "https:pawsitive-change-taara/includes/donation_cancel.php?donation_id=$donation_id"
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_BASE."/v2/checkout/orders");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $token"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = json_decode(curl_exec($ch), true);
    curl_close($ch);

    return $res['links'][1]['href'] ?? null; // approval_url
}

// PayPal payment
if ($method === "paypal") {
    $approvalUrl = createPayPalOrder($amount, $donation_id);
    if (!$approvalUrl) die("Error creating PayPal order.");
    header("Location: $approvalUrl");
    exit;
}

// If GCash chosen
elseif ($method === "gcash") {
    // Create a small "fake" transaction reference to show in the QR
    $ref = 'GC' . strtoupper(bin2hex(random_bytes(3))) . '-' . rand(1000,9999);
    $qrData = "TAARA|GCash|ref:$ref|donation_id:$donation_id|amount:PHP$amount";
    // Use QRServer (no API key required). size 250x250
    $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qrData);

    // Render a simple confirmation page with the QR image
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8'>
        <title>Pay with GCash</title>
        <meta name='viewport' content='width=device-width,initial-scale=1'>
        <style>
            body { font-family: Arial, Helvetica, sans-serif; text-align:center; padding:30px; background:#f7fafc; color:#1f2937; }
            .card { display:inline-block; background:#fff; padding:24px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08); max-width:420px; width:100%; }
            img { width:250px; height:250px; object-fit:contain; margin:16px 0; }
            .btn { display:inline-block; margin:10px 6px; padding:10px 18px; background:#0077ff; color:white; border-radius:6px; text-decoration:none; }
            .btn.secondary { background:#6b7280; }
            .small { color:#6b7280; font-size:0.9rem; }
            .ref { font-weight:600; margin-top:8px; }
        </style>
    </head>
    <body>
      <div class='card'>
        <h2>Scan to Pay with GCash</h2>
        <p class='small'>Amount: <strong>₱" . htmlspecialchars(number_format($amount,2)) . "</strong></p>
        <img src='" . htmlspecialchars($qrCodeUrl) . "' alt='GCash QR Code' />
        <p class='ref'>Reference: " . htmlspecialchars($ref) . "</p>
        <p class='small'>Scan the QR with your GCash app and include the reference in the payment notes.</p>

        <form action='donation_success.php' method='get' style='margin-top:14px;'>
            <input type='hidden' name='donation_id' value='" . htmlspecialchars($donation_id) . "'>
            <input type='hidden' name='ref' value='" . htmlspecialchars($ref) . "'>
            <button class='btn' type='submit'>I have paid with GCash</button>
        </form>

        <a href='../index.php' class='btn secondary' style='display:inline-block; margin-top:8px;'>Return to Dashboard</a>
        <p class='small' style='margin-top:12px'>This QR is a simulated/test QR for local development only.</p>
      </div>
    </body>
    </html>";
    exit;
}


?>
