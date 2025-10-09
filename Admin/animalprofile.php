<?php
include '../includes/db_connection.php';
$conn = connect();

// CRUD handling (add, edit, delete) — same as before
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $name = $_POST['name'];
        $breed = $_POST['breed'];
        $age = $_POST['age'];
        $img = '';

        if (!empty($_FILES['img']['name'])) {
            $target_dir = "../Assets/Pets/";
            $file_name = time() . "_" . basename($_FILES['img']['name']);
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                $img = $file_name;
            }
        }

        $stmt = $conn->prepare("INSERT INTO animal(name, breed, age, img, status, type) VALUES (?, ?, ?, ?, 'At a Shelter', 'Dog')");
        $stmt->bind_param("ssis", $name, $breed, $age, $img);
        $stmt->execute();
        $stmt->close();
    }

    if ($_POST['action'] == 'edit') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $breed = $_POST['breed'];
        $age = $_POST['age'];
        $img = $_POST['old_img'];

        if (!empty($_FILES['img']['name'])) {
            $target_dir = "../Assets/Pets/";
            $file_name = time() . "_" . basename($_FILES['img']['name']);
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                $img = $file_name;
            }
        }

        $stmt = $conn->prepare("UPDATE animal SET name=?, breed=?, age=?, img=? WHERE animal_id=?");
        $stmt->bind_param("ssisi", $name, $breed, $age, $img, $id);
        $stmt->execute();
        $stmt->close();
    }

    if ($_POST['action'] == 'delete') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM animal WHERE animal_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAARA Admin Dashboard - Animal Profiles</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

  <div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#0b1d3a] text-white min-h-screen p-6">
      <div class="flex flex-col items-center mb-10">
        <img src="logo.png" alt="Logo" class="w-20 h-20 mb-4">
        <h1 class="text-lg font-bold">T.A.A.R.A</h1>
      </div>
      <nav>
        <ul class="space-y-4">
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='index.php'"><span class="material-icons">dashboard</span> Dashboard</li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='adoptionrequest.php'"><span class="material-icons">pets</span> Adoption Requests</li>
          <li class="flex items-center gap-3 p-3 rounded-lg bg-gray-700 cursor-pointer" onclick="window.location.href='animalprofile.php'"><span class="material-icons">favorite</span> Animal Profiles</li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='volunteers.php'"><span class="material-icons">groups</span> Volunteers</li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='donation.php'"><span class="material-icons">volunteer_activism</span> Donations</li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='events.php'"><span class="material-icons">event</span> Events</li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='reports.php'"><span class="material-icons">report</span> Rescue Reports</li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-700 cursor-pointer" onclick="window.location.href='settings.php'"><span class="material-icons">settings</span> Settings</li>
          <li class="flex items-center gap-3 p-3 rounded-lg hover:bg-red-600 cursor-pointer mt-10" onclick="logout()"><span class="material-icons">logout</span> Logout</li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
  <main class="flex-1 p-8">
  <h1 class="text-3xl font-bold mb-6">🐾 Animal Profiles</h1>
  <div class="bg-white p-6 rounded-xl shadow">

    <!-- Search & Filters -->
    <div class="flex flex-wrap gap-4 justify-between items-center mb-6">
      <div class="flex flex-wrap gap-2">
        <input id="searchInput" type="text" placeholder="Search by name..." class="border rounded-lg px-4 py-2 w-60 focus:outline-none focus:ring-2 focus:ring-pink-400">

        <select id="filterType" class="border rounded-lg px-4 py-2">
          <option value="">All Types</option>
          <option value="Dog">Dog</option>
          <option value="Cat">Cat</option>
          <option value="Other">Other</option>
        </select>

        <select id="filterBreed" class="border rounded-lg px-4 py-2">
          <option value="">All Breeds</option>
          <?php
          $breeds = $conn->query("SELECT DISTINCT breed FROM animal WHERE breed IS NOT NULL AND breed != '' ORDER BY breed ASC");
          while ($b = $breeds->fetch_assoc()) {
              echo "<option value='".htmlspecialchars($b['breed'])."'>".htmlspecialchars($b['breed'])."</option>";
          }
          ?>
        </select>

        <select id="filterGender" class="border rounded-lg px-4 py-2">
          <option value="">All Genders</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>

        <select id="filterBehavior" class="border rounded-lg px-4 py-2">
          <option value="">All Behaviors</option>
          <option value="Calm">Calm</option>
          <option value="Playful">Playful</option>
          <option value="Aggressive">Aggressive</option>
          <option value="Friendly">Friendly</option>
          <option value="Timid">Timid</option>
        </select>

        <select id="filterAge" class="border rounded-lg px-4 py-2">
          <option value="">All Ages</option>
          <option value="0-2">0–2 years</option>
          <option value="3-5">3–5 years</option>
          <option value="6+">6+ years</option>
        </select>
      </div>

      <button onclick="addAnimal()" class="bg-pink-500 text-white px-4 py-2 rounded-lg shadow hover:bg-pink-600">
        ➕ Add Animal
      </button>
    </div>

    <!-- Animal Profiles Grid -->
    <div id="animalProfiles" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Cards loaded dynamically -->
    </div>

  </div>


   <<!-- VACCINATION RECORDS SECTION -->
<h1 class="text-3xl font-bold mt-10 mb-6">💉 Vaccination Records</h1>

<div class="bg-white p-6 rounded-xl shadow">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-semibold">Vaccinations</h2>
    <button onclick="openVaccModal()" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-green-600">
      ➕ Add Vaccination
    </button>
  </div>

  <table class="w-full border-collapse border border-gray-300">
    <thead class="bg-gray-200">
      <tr>
        <th class="border px-4 py-2 text-left">Photo</th>
        <th class="border px-4 py-2 text-left">Animal Name</th>
        <th class="border px-4 py-2 text-left">Vaccine Type</th>
        <th class="border px-4 py-2 text-left">Date Vaccinated</th>
        <th class="border px-4 py-2 text-center">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $vacc_table = new DatabaseCRUD('vaccinations');
        $vacc_records = $conn->query("
          SELECT v.vaccination_id, v.animal_id, v.vaccine_type, v.date_vaccinated, 
                 a.name AS animal_name, a.img AS animal_img 
          FROM vaccinations v 
          JOIN animal a ON v.animal_id = a.animal_id
          ORDER BY v.date_vaccinated DESC
        ");

        if ($vacc_records->num_rows > 0) {
          while ($row = $vacc_records->fetch_assoc()) {
            echo "
              <tr id='row_{$row['vaccination_id']}' class='hover:bg-gray-50'>
                <td class='border px-4 py-2'>
                  <img src=\"../Assets/Pets/{$row['animal_img']}\" class='w-16 h-16 object-cover rounded-lg border'>
                </td>
                <td class='border px-4 py-2'>{$row['animal_name']}</td>
                <td class='border px-4 py-2'>
                  <span class='vaccineType'>{$row['vaccine_type']}</span>
                  <input type='text' class='hidden editVaccineType border rounded px-2 py-1 w-full' value='{$row['vaccine_type']}'>
                </td>
                <td class='border px-4 py-2'>
                  <span class='vaccineDate'>{$row['date_vaccinated']}</span>
                  <input type='date' class='hidden editVaccineDate border rounded px-2 py-1 w-full' value='{$row['date_vaccinated']}'>
                </td>
                <td class='border px-4 py-2 text-center'>
                  <button onclick=\"editVaccination({$row['vaccination_id']})\" class='bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600'>Edit</button>
                  <button onclick=\"deleteVaccination({$row['vaccination_id']})\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 ml-2'>Delete</button>
                  <button onclick=\"saveVaccination({$row['vaccination_id']})\" id='saveBtn_{$row['vaccination_id']}' class='hidden bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 ml-2'>Save</button>
                  <button onclick=\"cancelEdit({$row['vaccination_id']})\" id='cancelBtn_{$row['vaccination_id']}' class='hidden bg-gray-400 text-white px-3 py-1 rounded hover:bg-gray-500 ml-2'>Cancel</button>
                </td>
              </tr>
            ";
          }
        } else {
          echo "<tr><td colspan='5' class='text-center py-4 text-gray-500'>No vaccination records found.</td></tr>";
        }
      ?>
    </tbody>
  </table>
</div>

<!-- ADD VACCINATION MODAL -->
<div id="vaccModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-md relative shadow-lg">
    <h2 class="text-2xl font-bold mb-4">Add Vaccination Record</h2>
    <form id="vaccForm" enctype="multipart/form-data">
      <div class="mb-4">
        <label class="block text-gray-700 mb-1">Select Animal</label>
        <select name="animal_id" id="animal_id" class="border rounded-lg px-3 py-2 w-full" required>
          <option value="">Select an animal</option>
          <?php
            $animals = $conn->query("SELECT animal_id, name FROM animal ORDER BY name ASC");
            while ($a = $animals->fetch_assoc()) {
              echo "<option value='{$a['animal_id']}'>{$a['name']}</option>";
            }
          ?>
        </select>
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 mb-1">Vaccine Type</label>
        <input type="text" name="vaccine_type" id="vaccine_type" class="border rounded-lg px-3 py-2 w-full" required>
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 mb-1">Date Vaccinated</label>
        <input type="date" name="date_vaccinated" id="date_vaccinated" class="border rounded-lg px-3 py-2 w-full" required>
      </div>

      <div class="flex justify-end gap-3">
        <button type="button" onclick="closeVaccModal()" class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">Cancel</button>
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">Save</button>
      </div>
    </form>
  </div>
</div>


  

</main>

  </div>

  <!-- Add/Edit Modal -->
  <!-- ADD / EDIT ANIMAL MODAL -->
<div id="animalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-2xl relative shadow-lg">
    <h2 id="modalTitle" class="text-2xl font-bold mb-4">Add Animal</h2>
    <form id="animalForm" enctype="multipart/form-data">
      <input type="hidden" id="animal_id" name="animal_id">

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-gray-700">Name</label>
          <input type="text" id="name" name="name" class="border rounded-lg px-3 py-2 w-full" required>
        </div>

        <div>
          <label class="block text-gray-700">Type</label>
          <select id="type" name="type" class="border rounded-lg px-3 py-2 w-full" required>
            <option value="Dog">Dog</option>
            <option value="Cat">Cat</option>
            <option value="Other">Other</option>
          </select>
        </div>

        <div>
          <label class="block text-gray-700">Breed</label>
          <!--input type="text" id="breed" name="breed" class="border rounded-lg px-3 py-2 w-full" required-->

          <input list="breed-options" id="filter-breed" name='breed' placeholder="Select Breed" class="border rounded-lg px-3 py-2 w-full" required>
          <datalist id="breed-options">
            <option value="Labrador">
            <option value="Poodle">
            <option value="Golden Retriever">
            <option value="Persian">
            <option value="Siamese">
            <option value="Bengal">
          </datalist>
        </div>

        

        <div>
          <label class="block text-gray-700">Gender</label>
          <select id="gender" name="gender" class="border rounded-lg px-3 py-2 w-full" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>

        <div>
          <label class="block text-gray-700">Age (years)</label>
          <input type="number" id="age" name="age" class="border rounded-lg px-3 py-2 w-full" required min="0">
        </div>

        <div>
          <label class="block text-gray-700">Behavior</label>
          <select id="behavior" name="behavior" class="border rounded-lg px-3 py-2 w-full" required>
            <option value="Calm">Calm</option>
            <option value="Playful">Playful</option>
            <option value="Aggressive">Aggressive</option>
            <option value="Friendly">Friendly</option>
            <option value="Timid">Timid</option>
          </select>
        </div>

        <div>
          <label class="block text-gray-700">Date Rescued</label>
          <input type="date" id="date_rescued" name="date_rescued" class="border rounded-lg px-3 py-2 w-full" required>
        </div>

        <div>
          <label class="block text-gray-700">Status</label>
          <select id="status" name="status" class="border rounded-lg px-3 py-2 w-full" required>
            <option value="At a Shelter">At a Shelter</option>
            <option value="Adopted">Adopted</option>
            <option value="Pending Adoption">Pending Adoption</option>
          </select>
        </div>
      </div>

      <div class="mt-4">
        <label class="block text-gray-700">Description</label>
        <textarea id="description" name="description" class="border rounded-lg px-3 py-2 w-full" rows="3" required></textarea>
      </div>

      <div class="mt-4 flex items-center gap-4">
        <div>
          <label class="block text-gray-700">Animal Image</label>
          <input type="file" id="img" name="img" accept="image/*" class="border rounded-lg px-3 py-2 w-full">
        </div>
        <img id="previewImg" src="" alt="Preview" class="w-24 h-24 object-cover rounded-lg border hidden">
      </div>

      <div class="flex justify-end gap-3 mt-6">
        <button type="button" onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">Cancel</button>
        <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600">Save</button>
      </div>
    </form>
  </div>
</div>


  <script>
  // Load animals dynamically
  function loadAnimals() {
  const search = $('#searchInput').val();
  const type = $('#filterType').val();
  const breed = $('#filterBreed').val();
  const gender = $('#filterGender').val();
  const behavior = $('#filterBehavior').val();
  const age = $('#filterAge').val();

  $.ajax({
    url: 'fetch_animals.php',
    method: 'GET',
    data: {
      search: search,
      type: type,
      breed: breed,
      gender: gender,
      behavior: behavior,
      age: age
    },
    success: function(response) {
      $('#animalProfiles').html(response);
    }
  });
}


  $(document).ready(function() {
    loadAnimals();

    $('#searchInput').on('keyup', function() {
      loadAnimals($(this).val(), $('#filterType').val());
    });

    $('#filterType').on('change', function() {
      loadAnimals($('#searchInput').val(), $(this).val());
    });
  });

 function addAnimal() {
  $('#modalTitle').text('Add Animal');
  $('#animalForm')[0].reset();
  $('#animal_id').val('');
  $('#previewImg').addClass('hidden');
  $('#animalModal').removeClass('hidden');
}

function editForm(id, name, breed, age, img, gender, type, behavior, date_rescued, status, description) {
  $('#modalTitle').text('Edit Animal');
  $('#animal_id').val(id);
  $('#name').val(name);
  $('#breed').val(breed);
  $('#age').val(age);
  $('#gender').val(gender);
  $('#type').val(type);
  $('#behavior').val(behavior);
  $('#date_rescued').val(date_rescued);
  $('#status').val(status);
  $('#description').val(description);

  if (img) {
    $('#previewImg').attr('src', '../Assets/Pets/' + img).removeClass('hidden');
  } else {
    $('#previewImg').addClass('hidden');
  }

  $('#animalModal').removeClass('hidden');
}

function closeModal() {
  $('#animalModal').addClass('hidden');
}

// Preview uploaded image
$('#img').on('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      $('#previewImg').attr('src', e.target.result).removeClass('hidden');
    };
    reader.readAsDataURL(file);
  }
});

// Submit Add/Edit
$('#animalForm').on('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);

  $.ajax({
    url: 'save_animal.php',
    type: 'POST',
    data: formData,
    contentType: false,
    processData: false,
    success: function(response) {
      alert(response);
      closeModal();
      loadAnimals();
    },
    error: function() {
      alert('Error saving animal.');
    }
  });
});


  function deleteAnimal(id) {
    if (confirm("Are you sure you want to delete this animal?")) {
      let form = document.createElement("form");
      form.method = "POST";
      form.innerHTML = `<input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="${id}">`;
      document.body.appendChild(form);
      form.submit();
    }
  }

  function closeForm() {
    document.getElementById("animalFormModal").classList.add("hidden");
  }

  function logout() {
  alert("Logging out...");
  window.location.href = "../login.php";
}function openVaccModal() {
  $('#vaccForm')[0].reset();
  $('#vaccModal').removeClass('hidden');
}

function closeVaccModal() {
  $('#vaccModal').addClass('hidden');
}

$('#vaccForm').on('submit', function(e) {
  e.preventDefault();
  $.ajax({
    url: 'save_vaccination.php',
    type: 'POST',
    data: $(this).serialize(),
    success: function(response) {
      alert(response);
      closeVaccModal();
      location.reload();
    },
    error: function() {
      alert('Error adding vaccination record.');
    }
  });
});

// Edit inline
function editVaccination(id) {
  $(`#row_${id} .vaccineType, #row_${id} .vaccineDate`).hide();
  $(`#row_${id} .editVaccineType, #row_${id} .editVaccineDate`).removeClass('hidden');
  $(`#saveBtn_${id}, #cancelBtn_${id}`).removeClass('hidden');
}

function cancelEdit(id) {
  $(`#row_${id} .vaccineType, #row_${id} .vaccineDate`).show();
  $(`#row_${id} .editVaccineType, #row_${id} .editVaccineDate`).addClass('hidden');
  $(`#saveBtn_${id}, #cancelBtn_${id}`).addClass('hidden');
}

function saveVaccination(id) {
  const vaccine_type = $(`#row_${id} .editVaccineType`).val();
  const date_vaccinated = $(`#row_${id} .editVaccineDate`).val();

  $.ajax({
    url: 'update_vaccination.php',
    type: 'POST',
    data: { id, vaccine_type, date_vaccinated },
    success: function(response) {
      alert(response);
      location.reload();
    },
    error: function() {
      alert('Error saving vaccination.');
    }
  });
}

function deleteVaccination(id) {
  if (confirm('Are you sure you want to delete this vaccination record?')) {
    $.ajax({
      url: 'delete_vaccination.php',
      type: 'POST',
      data: { id },
      success: function(response) {
        alert(response);
        location.reload();
      },
      error: function() {
        alert('Error deleting vaccination.');
      }
    });
  }
}

  </script>
</body>
</html>
