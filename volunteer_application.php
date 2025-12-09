<?php
include "includes/db_connection.php";
session_start();

$logged_in = false;
$user_data = $username = $user_img = $uid = $user_type = $email = "";

if (isset($_SESSION['email'])) {
  $logged_in = true;
  $email = $_SESSION['email'];
  $user_table = new DatabaseCRUD('user');
  $user_result = $user_table->select(["*"], ["email" => $email], 1);

  if (!empty($user_result)) {
    $user_data = $user_result[0];
    $uid = $user_data['user_id'];
    $user_img = $user_data['profile_img'];
    $username = $user_data['username'];
    $user_type = $user_data['user_type'];
  }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['name'] ?? null;
    $first_committee = $_POST['first_com'] ?? '';
    $second_committee = $_POST['second_com'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $classification = $_POST['classification'] ?? '';
    $age = $_POST['age'] ?? '';
    $birth_date = $_POST['birth_date'] ?? '';
    $contact_num = $_POST['contact_num'] ?? '';
    $address = $_POST['address'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $date_applied = date('Y-m-d');

    $id_img = '';
    if (isset($_FILES['id_img']) && $_FILES['id_img']['error'] === 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_name = time() . "_" . basename($_FILES['id_img']['name']);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['id_img']['tmp_name'], $target_file)) {
            $id_img = $target_file;
        }
    }

    $conn = connect();
    $query = "INSERT INTO volunteer_application 
        (user_id, first_committee, second_committee, full_name, classification, age, birth_date, contact_num, address, id_img, reason_for_joining, date_appied)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssssisssss", $user_id, $first_committee, $second_committee, $full_name, $classification, $age, $birth_date, $contact_num, $address, $id_img, $reason, $date_applied);
    if ($stmt->execute()) {
        header("Location: includes/transaction_confirmation.php?type=volunteer&title=VOLUNTEER APPLICATION SUCCESSFUL&message=Please wait for our response in your email.");
        exit;
    } else {
        echo "Error submitting application: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Volunteer Application - TAARA</title>

  <link rel="stylesheet" href="CSS/index.css">
  <link rel="stylesheet" href="CSS/essentials.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>

  <style>

    .site-footer {
  width: auto;
  height: auto;
  color: var(--color-text-primary);
  background-color: var(--color-fg);
  box-shadow: 3px 3px 3px 3px rgba(0, 0, 0, 0.275);
  padding-block: 20px;
}
.footer-content {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  gap: 10px;
}
.footer-content img {
  height: 20px;
  width: 20px;
}
  body {
  background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
  font-family: 'Inter', system-ui, sans-serif;
}

/* Main container */
.volunteer-container {
  max-width: 900px;
  margin: 3rem auto;
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.05);
  padding: 3rem 3rem 2.5rem;
  border: 1px solid #f3f3f3;
  animation: fadeIn 0.5s ease-in-out;
}

/* Header */
.volunteer-banner {
  text-align: center;
  margin-bottom: 2.5rem;
}

.volunteer-banner h1 {
  font-size: 2rem;
  font-weight: 800;
  color: #e91e63;
  margin-bottom: 0.5rem;
}

.volunteer-banner p {
  color: #555;
  font-size: 1rem;
}

/* Form grid */
form {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
  gap: 1.5rem 2rem;
}

.labeled-input {
  display: flex;
  flex-direction: column;
}

label {
  font-weight: 600;
  color: #444;
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}



/* Input styling */
input, select, textarea {
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  padding: 0.9rem 1rem;
  font-size: 1rem;
  transition: all 0.2s ease;
  background-color: #fafafa;
  height: 48px;
}

textarea {
  min-height: 110px;
  resize: vertical;
  line-height: 1.5;
}

input:focus, select:focus, textarea:focus {
  border-color: #e91e63;
  background-color: #fff;
  outline: none;
  box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.15);
  transform: translateY(-1px);
}

/* File input button */
input[type="file"] {
  background: #fff;
  cursor: pointer;
  padding: 0.5rem;
}

input[type="file"]::file-selector-button {
  background: #f9f9f9;
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 0.4rem 1rem;
  margin-right: 10px;
  color: #444;
  transition: 0.3s ease;
}

input[type="file"]::file-selector-button:hover {
  background: #e91e63;
  color: white;
  border-color: #e91e63;
}

/* Submit button */
.submit-btn {
  grid-column: 1 / -1;
  width: 100%;
  height: 50px;
  background-color: #e91e63;
  color: #fff;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 1.05rem;
  margin-top: 1rem;
  transition: all 0.3s ease;
}

.submit-btn:hover {
  background-color: #d81b60;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(233, 30, 99, 0.25);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
/* ----- Stepper ----- */
.stepper {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: .75rem;
  margin: 0 0 1.5rem 0;
}
.stepper .dot {
  position: relative;
  display: flex;
  align-items: center;
  gap: .75rem;
}
.stepper .dot::before {
  content: attr(data-step);
  display: grid;
  place-items: center;
  width: 34px; height: 34px;
  border-radius: 999px;
  background: #f1f1f3;
  color: #777;
  font-weight: 700;
  box-shadow: inset 0 0 0 2px #e5e7eb;
}
.stepper .label { color:#666; font-weight:600; font-size:.95rem; }
.stepper .dot.active::before { background:#e91e63; color:#fff; box-shadow:none; }
.stepper .dot.active .label { color:#e91e63; }

/* Make each step its own grid (uses your existing input styles) */
form { display: block; }               /* override your old form grid */
.form-step {
  display: none;
  grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
  gap: 1.5rem 2rem;
}
.form-step.active { display: grid; }

/* Nav buttons */
.form-nav {
  display: flex;
  justify-content: space-between;
  gap: .75rem;
  margin-top: 1rem;
  border-top: 1px solid #eee;
  padding-top: 1rem;
}
.btn {
  padding: .75rem 1.25rem;
  border-radius: 10px;
  font-weight: 600;
  border: 1px solid #e5e7eb;
  background:#fff;
  color:#374151;
  transition:.2s;
}
.btn:hover { background:#f8f9fb; }
.btn-primary {
  background:#e91e63; border-color:#e91e63; color:#fff;
}
.btn-primary:hover { background:#d81b60; }

/* small invalid shake/highlight */
.invalid {
  border-color:#ef4444 !important;
  box-shadow: 0 0 0 3px rgba(239,68,68,.15) !important;
}

  </style>
</head>
<body>
  <!-- HEADER -->
  <header>
    <img src="Assets/UI/taaralogo.jpg" alt="TAARA Logo">
    <div class="nav-container">
      <nav>
        <ul>
          <li><a href="rescue.php">Rescue</a></li>
          <li><a href="adoption.php">Adopt</a></li>
          <li><a href="donation.php">Donation</a></li>
          <li><a href="volunteer.php">Volunteer</a></li>
          <li><a href="events.php">Events</a></li>
          <li><a class='active' href="index.php">About</a></li>
        </ul>
      </nav>

      <?php
        if ($logged_in) {
          echo "<img src='Assets/Profile_Images/$user_img' class='profile-img' id='user_profile'>";
        } else {
          echo "<a href='register.html' class='bg-pink-600 text-white px-4 py-2 rounded-full font-bold hover:bg-pink-700 flex items-center gap-2'>
                  <i class='fa-solid fa-user-plus'></i> Register
                </a>";
        }
      ?>
    </div>
  </header>

  <!-- CONTENT -->
 <div class="volunteer-container">
  <div class="volunteer-banner">
    <h1>Volunteer Application</h1>
    <p>Join us and help make a difference for our animal friends.</p>
  </div>

  <form action="volunteer_application.php" method="post" enctype="multipart/form-data" novalidate>

  <!-- Stepper -->
  <div class="stepper">
    <div class="dot active" data-step="1"><span class="label">Committees & Name</span></div>
    <div class="dot" data-step="2"><span class="label">Details & Contact</span></div>
    <div class="dot" data-step="3"><span class="label">Address & ID</span></div>
  </div>

  <!-- STEP 1 -->
  <section class="form-step active" data-step="1">
    <div class="labeled-input">
      <label for="first_com">First Committee</label>
      <select name="first_com" required>
        <option value="">-- Select Committee --</option>
        <option value="Secretariat">Secretariat</option>
        <option value="Logistics">Logistics</option>
        <option value="PR and Research">Public Relations & Research</option>
        <option value="Adoption and Foster">Adoption and Foster</option>
        <option value="Rescue Initiatives">Rescue Initiatives</option>
        <option value="Multimedia/Creatives">Multimedia/Creatives</option>
        <option value="Documentation">Documentation</option>
      </select>
    </div>

    <div class="labeled-input">
      <label for="second_com">Second Committee</label>
      <select name="second_com">
        <option value="">-- Select Committee --</option>
        <option value="Secretariat">Secretariat</option>
        <option value="Logistics">Logistics</option>
        <option value="PR and Research">Public Relations & Research</option>
        <option value="Adoption and Foster">Adoption and Foster</option>
        <option value="Rescue Initiatives">Rescue Initiatives</option>
        <option value="Multimedia/Creatives">Multimedia/Creatives</option>
        <option value="Documentation">Documentation</option>
      </select>
    </div>

    <div class="labeled-input">
      <label for="full_name">Full Name</label>
      <input type="text" name="full_name" required>
    </div>
  </section>

  <!-- STEP 2 -->
  <section class="form-step" data-step="2">
    <div class="labeled-input">
      <label for="classification">Classification</label>
      <input type="text" name="classification" required>
    </div>

    <div class="labeled-input">
      <label for="age">Age</label>
      <input type="number" name="age" required>
    </div>

    <div class="labeled-input">
      <label for="birth_date">Birth Date</label>
      <input type="date" name="birth_date" required>
    </div>

    <div class="labeled-input">
      <label for="contact_num">Contact Number</label>
      <input type="text" name="contact_num" required>
    </div>
  </section>

  <!-- STEP 3 -->
  <section class="form-step" data-step="3">
    <div class="labeled-input full-width">
      <label for="address">Address</label>
      <input type="text" name="address" required>
    </div>

    <div class="labeled-input">
      <label for="id_img">Upload ID</label>
      <input type="file" name="id_img" accept="image/*" required>
    </div>

    <div class="labeled-input full-width">
      <label for="reason">Reason for Joining</label>
      <textarea name="reason" required></textarea>
    </div>
  </section>

  <!-- Wizard Nav -->
  <div class="form-nav">
    <button type="button" class="btn" id="prevBtn">Back</button>
    <div style="display:flex; gap:.5rem;">
      <button type="button" class="btn" id="saveDraftBtn" style="display:none;">Save draft</button>
      <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
      <button type="submit" class="submit-btn" id="submitBtn" style="display:none;">Submit Application</button>
    </div>
  </div>
</form>

</div>


    <!-- FOOTER -->
  <footer>
    <p>TAARA located at P-3 Burac St., San Lorenzo, Tabaco, Philippines</p>
    <p>
      <a href="#"><i class="fa-brands fa-facebook"></i> Facebook</a> | 
      <a href="tel:09055238105"><i class="fa-solid fa-phone"></i> 0905 523 8105</a>
    </p>
  </footer>
</body>

<script>
(() => {
  const steps = Array.from(document.querySelectorAll('.form-step'));
  const dots  = Array.from(document.querySelectorAll('.stepper .dot'));
  const next  = document.getElementById('nextBtn');
  const prev  = document.getElementById('prevBtn');
  const submit= document.getElementById('submitBtn');
  const save  = document.getElementById('saveDraftBtn'); // (optional)
  let i = 0;

  function showStep(idx) {
    steps.forEach((s,k)=> s.classList.toggle('active', k===idx));
    dots.forEach((d,k)=> d.classList.toggle('active', k<=idx));
    prev.style.visibility = idx === 0 ? 'hidden' : 'visible';
    next.style.display    = idx === steps.length-1 ? 'none'   : 'inline-block';
    submit.style.display  = idx === steps.length-1 ? 'inline-block' : 'none';
    // save.style.display = idx > 0 ? 'inline-block' : 'none'; // if you want it
  }

  function validateStep(idx) {
    let valid = true;
    const step = steps[idx];
    const fields = step.querySelectorAll('input, select, textarea');
    fields.forEach(f => {
      f.classList.remove('invalid');
      if (f.hasAttribute('required')) {
        if ((f.type === 'file' && !f.files.length) || (f.value || '').trim() === '') {
          f.classList.add('invalid');
          valid = false;
        }
      }
    });
    return valid;
  }

  next.addEventListener('click', () => {
    if (!validateStep(i)) return;
    if (i < steps.length-1) { i++; showStep(i); }
  });

  prev.addEventListener('click', () => {
    if (i > 0) { i--; showStep(i); }
  });

  // Press Enter moves to next step (but not on textarea/file)
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      const target = e.target;
      if (['TEXTAREA','FILE'].includes(target?.type?.toUpperCase())) return;
      const inVisibleStep = steps[i].contains(target);
      if (inVisibleStep && next.style.display !== 'none') {
        e.preventDefault();
        next.click();
      }
    }
  });

  showStep(i);
})();
</script>

</html>
