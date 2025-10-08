<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Confirmation - TAARA</title>

    <link rel="stylesheet" href="../CSS/globals.css">
    <link rel="stylesheet" href="../CSS/index.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
      body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background: linear-gradient(180deg, #fdf2f8 0%, #fff 100%);
        font-family: 'Poppins', sans-serif;
        text-align: center;
      }

      .confirmation-container {
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-radius: 16px;
        padding: 2.5rem 3rem;
        width: 90%;
        max-width: 500px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
        animation: fadeIn 0.8s ease;
      }

      .confirmation-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 1rem;
      }

      .confirmation-message {
        font-size: 1.75rem;
        font-weight: 700;
        color: #d63384;
      }

      .confirmation-text {
        font-size: 1rem;
        color: #555;
        line-height: 1.6;
        margin-bottom: 1rem;
      }

      .confirmation-button {
        background-color: #d63384;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.8rem 1.8rem;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
      }

      .confirmation-button:hover {
        background-color: #b82b6e;
        transform: translateY(-2px);
      }

      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
      }
    </style>
  </head>
  <body>
    <div class="confirmation-container">
      <img src="../Assets/UI/Taara_Logo.webp" class="confirmation-img" alt="TAARA Logo">
      <h1 class="confirmation-message"><?php echo $_GET['title'] ?></h1>
      <h4 class="confirmation-text"><?php echo $_GET['message']; ?></h4>
      <a href="../index.php">
        <button class="confirmation-button">Return to Home</button>
      </a>
    </div>
  </body>
</html>
