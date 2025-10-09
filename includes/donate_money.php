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
    // Generate reference and QR data
    $ref = 'GC' . strtoupper(bin2hex(random_bytes(3))) . '-' . rand(1000,9999);
    $qrData = "TAARA|GCash|ref:$ref|donation_id:$donation_id|amount:PHP$amount";
    $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qrData);

    // If user uploaded proof
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['proof_img'])) {
        $targetDir = "../Assets/UserGenerated/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        // Generate clean, unique filename
        $extension = pathinfo($_FILES["proof_img"]["name"], PATHINFO_EXTENSION);
        $safeExt = strtolower(preg_replace('/[^a-z0-9]+/i', '', $extension));
        $fileName = "proof_uid{$uid}_donation{$donation_id}_" . time() . "." . $safeExt;
        $targetFile = $targetDir . $fileName;

        // Move uploaded file to Assets/UserGenerated/
        if (move_uploaded_file($_FILES["proof_img"]["tmp_name"], $targetFile)) {
            $conn = connect();
            $stmt = $conn->prepare("UPDATE monetary_donation SET proof_img=?, status='Pending Verification' WHERE m_donation_id=?");
            $stmt->bind_param("si", $fileName, $donation_id);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            //header("Location: donation_success.php");

            echo "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <title>Proof Uploaded</title>
                <meta name='viewport' content='width=device-width,initial-scale=1'>
                <style>
                    body { font-family: Arial, Helvetica, sans-serif; text-align:center; padding:30px; background:#f7fafc; color:#1f2937; }
                    .card { display:inline-block; background:#fff; padding:24px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08); max-width:420px; width:100%; }
                    .btn { display:inline-block; margin-top:20px; padding:10px 18px; background:#0077ff; color:white; border-radius:6px; text-decoration:none; }
                    .btn:hover { background:#005fcc; }
                </style>
            </head>
            <body>
                <div class='card'>
                    <h2>Proof of Payment Uploaded ✅</h2>
                    <p>Thank you for your donation! Your payment proof has been submitted for verification.</p>
                    <a href='../index.php' class='btn'>Go to Dashboard</a>
                </div>
            </body>
            </html>";
            exit;
        } else {
            echo "<p style='color:red;text-align:center;'>⚠️ Failed to upload image. Please try again.</p>";
        }
    }

    // Display QR and upload form
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
            input[type=file] { display:block; margin:10px auto; padding:10px; }
            button { background:#0077ff; color:white; border:none; border-radius:6px; padding:10px 18px; cursor:pointer; }
            button:hover { background:#005fcc; }
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
            <p class='small'>Scan the QR using your GCash app, complete payment, and upload your proof below.</p>

            <form action='' method='POST' enctype='multipart/form-data' style='margin-top:16px;'>
                <input type='file' name='proof_img' accept='image/*' required>
                <button type='submit'>Send Proof</button>
            </form>
        </div>
    </body>
    </html>";
    exit;
}



?>
