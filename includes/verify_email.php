<?php
session_start();
include "../includes/db_connection.php";
include "../includes/send_email.php";

if (!isset($_SESSION['pending_user'])) {
    header('Location: ../register.php?msg=Session expired');
    exit();
}

$user_data = $_SESSION['pending_user'];
$verification_code = $user_data['verification_code'];
$email = $user_data['email'];
$username = $user_data['username'];

// ✅ Function to send the verification email
function sendVerificationCode($email, $username, $code) {
    $subject = "Your Verification Code for Account Creation";
    $message = "
        <html>
        <body style='font-family: Poppins, sans-serif;'>
            <h2 style='color:#E91E63;'>Hello, {$username}!</h2>
            <p>Thank you for registering. Please use the following 6-digit verification code to complete your account setup:</p>
            <h1 style='color:#333; letter-spacing: 4px;'>{$code}</h1>
            <p>This code will expire soon. If you didn’t request this, you can safely ignore this email.</p>
            <br>
            <p style='font-size: 12px; color: #888;'>– The Admin Team</p>
        </body>
        </html>
    ";
    sendEmail($email, $subject, $message);
}

// ✅ Send email once per session (avoid resending on refresh)
if (!isset($_SESSION['verification_email_sent'])) {
    try {
        sendVerificationCode($email, $username, $verification_code);
        $_SESSION['verification_email_sent'] = true;
    } catch (Exception $e) {
        echo "<script>alert('Failed to send verification email: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// ✅ Handle verification submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify'])) {
    $input_code = trim($_POST['code']);

    if ($input_code == $verification_code) {
        // Save user to database
        $user_table = new DatabaseCRUD('user');
        $data = [
            'username' => $user_data['username'],
            'email' => $user_data['email'],
            'password' => $user_data['password']
        ];

        if ($user_table->create($data)) {
            unset($_SESSION['pending_user']);
            unset($_SESSION['verification_email_sent']);
            echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
              <meta charset="UTF-8">
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <title>Account Verified</title>
              <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
              <script src="https://cdn.tailwindcss.com"></script>
            </head>
            <body class="flex justify-center items-center h-screen bg-gray-100 font-[Poppins]">
              <div class="bg-white rounded-2xl shadow-lg p-10 text-center">
                <div class="text-green-600 text-5xl mb-4"><i class="fa-solid fa-check-circle"></i></div>
                <h2 class="text-2xl font-bold mb-2">Email Verified Successfully!</h2>
                <p class="text-gray-600 mb-6">Your account has been created. You can now log in.</p>
                <a href="../login.php" class="bg-pink-600 hover:bg-pink-700 text-white px-5 py-2 rounded-lg font-semibold">Go to Login</a>
              </div>
              <script>
                setTimeout(()=> window.location.href="../login.php", 7000);
              </script>
            </body>
            </html>';
            exit;
        } else {
            echo "<script>alert('Database error: Unable to save account.'); window.history.back();</script>";
        }
    } else {
        $error = "Incorrect verification code. Please try again.";
    }
}

// ✅ Handle resend button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend'])) {
    // Generate new code and update session
    $new_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['pending_user']['verification_code'] = $new_code;
    $_SESSION['verification_email_sent'] = false;

    try {
        sendVerificationCode($email, $username, $new_code);
        $_SESSION['verification_email_sent'] = true;
        $success = "A new verification code has been sent to your email.";
    } catch (Exception $e) {
        $error = "Failed to resend email: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Verification</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex justify-center items-center h-screen bg-gray-100 font-[Poppins]">
  <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md text-center">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Email Verification</h2>
    <p class="text-gray-600 mb-6">
      A 6-digit verification code has been sent to
      <b><?= htmlspecialchars($user_data['email']) ?></b>.
    </p>

    <?php if (isset($error)): ?>
      <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
    <?php elseif (isset($success)): ?>
      <p class="text-green-500 mb-4"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST" class="mb-3">
      <input type="text" name="code" maxlength="6" required placeholder="Enter verification code"
        class="border border-gray-300 rounded-lg w-full px-4 py-2 text-center text-lg tracking-widest mb-4 focus:outline-none focus:ring-2 focus:ring-pink-500">
      <button type="submit" name="verify" class="bg-pink-600 hover:bg-pink-700 text-white px-5 py-2 rounded-lg font-semibold w-full">Verify</button>
    </form>

    <form method="POST">
      <button type="submit" name="resend" class="text-pink-600 hover:text-pink-800 font-medium">Resend verification code</button>
    </form>
  </div>
</body>
</html>
