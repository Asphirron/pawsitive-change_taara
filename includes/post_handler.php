<?php
// -------------------- POST HANDLER --------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Column selector save
    if (($_POST['action'] ?? '') === 'set_columns') {
        $cols = json_decode($_POST['visible_columns'], true);
        if (is_array($cols)) {
            $_SESSION['visibleColumns'] = array_intersect(array_keys($fieldsConfig), $cols);
        }
        header("Location: " . $_SERVER['PHP_SELF'] . '?unset=false'); // reload page
        exit;
    }

    // RESET FILTERS
    if (isset($_POST['reset_btn'])) {
        $tableData = $crud->readAll();
        $_POST = [];
        goto END_POST;
    }

    // SEARCH / FILTER
    if (isset($_POST['search_btn'])) {
        $conn = connect();
        $allowedColumns = array_keys($fieldsConfig);

        $searchBy = $_POST['search_by'] ?? 'none';
        $searchVal = trim($_POST['search_bar'] ?? '');
        $orderDir = strtolower($_POST['order_by'] ?? 'ascending') === 'descending' ? 'DESC' : 'ASC';
        $limit = isset($_POST['num_of_results']) ? intval($_POST['num_of_results']) : 10;
        if ($limit <= 0 || $limit > 1000) $limit = 50;

        if ($searchBy === 'none' || $searchVal === '') {
            $sql = "SELECT * FROM `$tableName` ORDER BY `$pk` $orderDir LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $limit);
        } elseif (in_array($searchBy, ['age']) && is_numeric($searchVal)) {
            $sql = "SELECT * FROM `$tableName` WHERE `$searchBy` = ? ORDER BY `$pk` $orderDir LIMIT ?";
            $stmt = $conn->prepare($sql);
            $ival = intval($searchVal);
            $stmt->bind_param("ii", $ival, $limit);
        } elseif (in_array($searchBy, $allowedColumns)) {
            $sql = "SELECT * FROM `$tableName` WHERE `$searchBy` = ? ORDER BY `$pk` $orderDir LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $searchVal, $limit);
        } else {
            $sql = "SELECT * FROM `$tableName` ORDER BY `$pk` $orderDir LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $limit);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        $tableData = $rows;
        $stmt->close();
        $conn->close();
    }

    if (isset($overrideCrud)) {
        if ($overrideCrud) { goto END_POST; }
    }

    // ADD / UPDATE
    $action = $_POST['action'] ?? '';
    if ($action === 'add' || $action === 'update') {
        $data = [];
        foreach ($fieldsConfig as $f => $t) {
            if ($t === 'number') $data[$f] = isset($_POST[$f]) ? intval($_POST[$f]) : 0;
            elseif ($t === 'image') $data[$f] = $_POST[$f] ?? 'default.png';
            else $data[$f] = $_POST[$f] ?? '';
        }

        // Handle image upload
        foreach ($fieldsConfig as $f => $t) {
            if ($t === 'image' && isset($_FILES[$f . '_file']) && $_FILES[$f . '_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../Assets/UserGenerated/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $orig = basename($_FILES[$f . '_file']['name']);
                $ext = pathinfo($orig, PATHINFO_EXTENSION);
                $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($orig, PATHINFO_FILENAME));
                $newName = $safe . '_' . time() . ($ext ? '.' . $ext : '');
                $target = $uploadDir . $newName;
                if (move_uploaded_file($_FILES[$f . '_file']['tmp_name'], $target)) {
                    $data[$f] = $newName;
                } else {
                    $message .= "Warning: image upload failed; using previous/default. ";
                }
            }
        }

        if ($action === 'add') {
            $newId = $crud->create($data);
            $message .= $newId ? "Added record ID: $newId" : "Failed to add record.";
        } elseif ($action === 'update' && !empty($_POST[$pk])) {
            $success = $crud->update(intval($_POST[$pk]), $data, $pk);
            $message .= $success ? "Updated record ID: " . $_POST[$pk] : "Failed to update record.";
        }

        $tableData = $crud->readAll();
    }

    // SET PROPERTY (status update)
    if ($action === 'set_property' && !empty($_POST[$pk])) {
        $property = $_POST['property'] ?? '';
        $value    = $_POST['value'] ?? '';

        if ($property && $value) {
            // Build update array
            $updateData = [$property => $value];

            // If we're setting status to adopted or returned, also set date_adopted
            if (in_array($value, ['adopted','returned'])) {
                $updateData['date_adopted'] = date('Y-m-d H:i:s'); // current date/time

            }else if ($tableName == 'adoption_application' && in_array($value, ['accepted','rejected'])) {
                if($_POST['status'] !== 'pending'){
                    $message = 'Application has been already accepted/rejected!';
                    return;
                }
                
                $updateData['date_responded'] = date('Y-m-d H:i:s'); // current date/time
                if($value === 'accepted'){
                    $tempCrud = new DatabaseCRUD('adoption');
                    $tempCrud->create([
                        'user_id'=> $_POST['user_id'],
                        'animal_id'=> $_POST['animal_d'],
                        'date_adopted'=> null,
                        'status'=> 'pending'
                    ]);
                }
                

            }else if ($tableName == 'volunteer_application' && in_array($value, ['accepted','rejected'])) {
                $updateData['respond_date'] = date('Y-m-d H:i:s'); // current date/time

                $tempCrud = new DatabaseCRUD('volunteer');
                $tempCrud->create([
                    'full_name'=> $_POST['full_name'],
                    'role'=> $_POST['first_committee'],
                    'user_id'=> $_POST['user_id']
                ]);
            }

            $success = $crud->update(intval($_POST[$pk]), $updateData, $pk);
            $message .= $success 
                ? "Updated record ID: ".$_POST[$pk]." set $property = $value"
                : "Failed to update record.";
            $tableData = $crud->readAll();
        }
    }


   

    // DELETE
    if ($action === 'delete' && !empty($_POST[$pk])) {
        $success = $crud->delete(intval($_POST[$pk]), $pk);
        $message .= $success ? "Deleted record ID: " . $_POST[$pk] : "Failed to delete record.";
        $tableData = $crud->readAll();
    }
}

END_POST:
