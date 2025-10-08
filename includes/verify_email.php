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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    <p class="text-gray-600 mb-6">We’ve sent a 6-digit code to your email <?php echo $verification_code ; ?> <b><?= htmlspecialchars($user_data['email']) ?></b></p>

    <?php if (isset($error)): ?>
      <p class="text-red-500 mb-4"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="code" maxlength="6" required placeholder="Enter verification code"
        class="border border-gray-300 rounded-lg w-full px-4 py-2 text-center text-lg tracking-widest mb-4 focus:outline-none focus:ring-2 focus:ring-pink-500">
      <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white px-5 py-2 rounded-lg font-semibold">Verify</button>
    </form>
  </div>
</body>
</html>
