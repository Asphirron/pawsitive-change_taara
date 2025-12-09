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

    // RESET FILTERS + SEARCH
if (isset($_POST['reset_btn'])) {
    $tableData = $crud->readAll();

    // Clear all search and filter inputs
    foreach (array_merge(['search_bar','search_by','order_by','num_of_results'], $filterConfig) as $f) {
        unset($_POST[$f]);
    }

    goto END_POST;
}


    // SEARCH / FILTER
    if (isset($_POST['search_btn']) || !empty(array_intersect(array_keys($_POST), $filterConfig))) {
        $conn = connect();
        $allowedColumns = array_keys($fieldsConfig);

        $searchBy = $_POST['search_by'] ?? $searchBy; // default to $searchBy
        $searchVal = trim($_POST['search_bar'] ?? '');
        $orderDir = strtolower($_POST['order_by'] ?? 'ascending') === 'descending' ? 'DESC' : 'ASC';
        $limit = isset($_POST['num_of_results']) ? intval($_POST['num_of_results']) : 10;
        if ($limit <= 0 || $limit > 1000) $limit = 50;

        // Build WHERE conditions
        $conditions = [];
        $params = [];
        $types = '';

        if ($searchVal !== '' && in_array($searchBy, $allowedColumns)) {
            $conditions[] = "`$searchBy` LIKE ?";
            $params[] = "%$searchVal%";
            $types .= 's';
        }

        foreach ($filterConfig as $f) {
            if (!empty($_POST[$f])) {
                $conditions[] = "`$f` = ?";
                $params[] = $_POST[$f];
                $types .= 's';
            }
        }

        $whereSql = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $sql = "SELECT * FROM `$tableName` $whereSql ORDER BY `$pk` $orderDir LIMIT ?";
        $stmt = $conn->prepare($sql);
        $params[] = $limit;
        $types .= 'i';
        $stmt->bind_param($types, ...$params);

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
            if ($t === 'number') {
                $data[$f] = isset($_POST[$f]) ? intval($_POST[$f]) : 0;
            } elseif ($t === 'image') {
                // Start with whatever was posted (hidden field carries old value)
                $data[$f] = $_POST[$f] ?? '';
            } else {
                $data[$f] = $_POST[$f] ?? '';
            }
        }

        // Handle image upload (only overwrite if new file uploaded)
        foreach ($fieldsConfig as $f => $t) {
            if ($t === 'image') {
                if (isset($_FILES[$f . '_file']) && $_FILES[$f . '_file']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../Assets/UserGenerated/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                    $orig = basename($_FILES[$f . '_file']['name']);
                    $ext = pathinfo($orig, PATHINFO_EXTENSION);
                    $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($orig, PATHINFO_FILENAME));
                    $newName = $safe . '_' . time() . ($ext ? '.' . $ext : '');
                    $target = $uploadDir . $newName;

                    if (move_uploaded_file($_FILES[$f . '_file']['tmp_name'], $target)) {
                        $data[$f] = $newName; // overwrite only if upload succeeded
                    } else {
                        $message .= "Warning: image upload failed; keeping previous image. ";
                    }
                }
                // If no new file uploaded and no hidden value, keep old DB value
                if (empty($data[$f]) && $action === 'update' && !empty($_POST[$pk])) {
                    $existing = $crud->read(intval($_POST[$pk]), $pk);
                    $data[$f] = $existing[$f] ?? 'default.png';
                }
            }
        }

        if ($action === 'add') {
            $newId = $crud->create($data);
            $message .= $newId ? "Successfully added record" : "Failed to add record.";
        } elseif ($action === 'update' && !empty($_POST[$pk])) {
            $success = $crud->update(intval($_POST[$pk]), $data, $pk);
            $message .= $success ? "Successfully updated record " : "Failed to update record.";
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
                        'animal_id'=> $_POST['animal_id'],
                        'date_adopted'=> null,
                        'status'=> 'pending'
                    ]);

                    $tempCrud = new DatabaseCRUD('animal');
                    $tempCrud->update(
                        $_POST['animal_id'],[
                        ['status'=> 'Pending Adoption'], 
                        'animal_id'
                       ]);
                }
                
            }else if ($tableName == 'adoption' && in_array($value, ['adopted','returned'])) {
                if($_POST['status'] !== 'pending'){
                    $message = 'Application has been already adopted/returned!';
                    return;
                }

                $updateData['date_adopted'] = date('Y-m-d H:i:s'); // current date/time
                if($value === 'adopted'){
                    $tempCrud = new DatabaseCRUD('animal');
                    $tempCrud->update(
                        $_POST['animal_id'],[
                        ['status'=> 'Adopted'], 
                        'animal_id'
                       ]);
                }
            }else if ($tableName == 'volunteer_application' && in_array($value, ['accepted','rejected'])) {

                if($_POST['status'] !== 'pending'){
                    $message = 'Application has been already accepted/rejected!';
                    return;
                }

                $updateData['respond_date'] = date('Y-m-d H:i:s'); // current date/time
                if($value === 'accepted'){
                    $tempCrud = new DatabaseCRUD('volunteer');
                    $tempCrud->create([
                        'full_name'=> $_POST['full_name'],
                        'role'=> $_POST['first_committee'],
                        'user_id'=> $_POST['user_id']
                    ]);
                }  

            }else if ($tableName == 'monetary_donation' && in_array($value, ['verified'])) {

                if($_POST['status'] === 'verified'){
                    $message = 'Donation has been already verified.';
                    return;
                }

                if($_POST['status'] === 'cancelled'){
                    $message = 'Donation cannot be verified. It has been already cancelled. .';
                    return;
                }

                //$updateData['respond_date'] = date('Y-m-d H:i:s'); // current date/time
                
            }else if ($tableName == 'inkind_donation' && in_array($value, ['received'])) {

                if($_POST['status'] === 'received'){
                    $message = 'Donation has been already receieved.';
                    return;
                }

                if($_POST['status'] === 'cancelled'){
                    $message = 'Donation cannot be set as received. It has been already cancelled. .';
                    return;
                }

                //$updateData['respond_date'] = date('Y-m-d H:i:s'); // current date/time
                $tempCrud = new DatabaseCRUD('donation_inventory');
                $tempItem = $tempCrud->select(['item_id', 'quantity'], ['item_name'=>$_POST['item_name'], 'item_type'=>$_POST['donation_type']], 1);

                if(empty($tempItem)){
                    $tempCrud->create([
                        'item_name' => $_POST['item_name'],
                        'item_type' => $_POST['donation_type'],
                        'quantity' => $_POST['quantity'],
                        'date_stored'=> date('Y-m-d H:i:s')
                    ]);
                    $message = "Item has been successfully added into the inventory";
                }else{
                    $addQty = intval($tempItem['quantity']) + intval($_POST['quantity']);
                    if($tempCrud->update($tempItem['item_id'], ['quantity' => $addQty], 'item_id')){
                        $message = "Item has been successfully inserted into the inventory $addQty";
                    } 
                }
            }

            $success = $crud->update(intval($_POST[$pk]), $updateData, $pk);
            $message .= $success 
                ? "Successfully updated record."
                : "Failed to update record.";
            $tableData = $crud->readAll();
        }
    }


   

    // DELETE
    if ($action === 'delete' && !empty($_POST[$pk])) {
        $success = $crud->delete(intval($_POST[$pk]), $pk);
        $message .= $success ? "Successfully deleted record" : "Failed to delete record.";
        $tableData = $crud->readAll();
    }
}

END_POST:

