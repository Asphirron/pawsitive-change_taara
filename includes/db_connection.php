<?php

function connect() {
    $servername = "pawsitive-change-taara.com";
    $username = "u578970591_taara_db_pass2025";
    $password = "Taara_db_2025";
    $dbname = "u578970591_taara_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function getPercentage($value, $compared){
    return ($value / $compared) * 100;
}

function alert($message){
    echo "<script>alert('$message');</script>";
  }


function getAnimalData($target_id) {
    $conn = connect();

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT animal_id, name, description, type, breed, gender, age, date_rescued, img 
                            FROM animal WHERE animal_id = ?");
    $stmt->bind_param("i", $target_id); // "i" means integer

    $stmt->execute();
    $result = $stmt->get_result();

    $data = null;

    if ($row = $result->fetch_assoc()) {
        $data = [
            "id"     => $row['animal_id'],
            "name"   => $row['name'],
            "desc"   => $row['description'],
            "type"   => $row['type'],
            "breed"  => $row['breed'],
            "gender" => $row['gender'],
            "age"    => $row['age'],
            "date"   => $row['date_rescued'],
            "img"    => $row['img']
        ];
    }

    $stmt->close();
    $conn->close();

    return $data; // Returns null if not found
}

function getAllAnimals() {
    $conn = connect();

    $query = "SELECT animal_id, name, description, type, breed, gender, age, date_rescued, img FROM animal";
    $result = $conn->query($query);

    $animals = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $animals[] = [
                "id"     => $row['animal_id'],
                "name"   => $row['name'],
                "desc"   => $row['description'],
                "type"   => $row['type'],
                "breed"  => $row['breed'],
                "gender" => $row['gender'],
                "age"    => $row['age'],
                "date"   => $row['date_rescued'],
                "img"    => $row['img']
            ];
        }
    }

    $conn->close();
    return $animals; // Returns [] if no animals
}

function updateAnimal($id, $name, $desc, $type, $breed, $gender, $age, $date_rescued, $img) {
    $conn = connect();

    $stmt = $conn->prepare("UPDATE animal 
        SET name = ?, description = ?, type = ?, breed = ?, gender = ?, age = ?, date_rescued = ?, img = ? 
        WHERE animal_id = ?");

    $stmt->bind_param("sssssis si", 
        $name, 
        $desc, 
        $type, 
        $breed, 
        $gender, 
        $age, 
        $date_rescued, 
        $img, 
        $id
    );

    $success = $stmt->execute();

    $stmt->close();
    $conn->close();

    return $success; // true if update succeeded, false if failed
}







class DatabaseCRUD {
    private $conn;
    private $table;
    private $columns;

    public function __construct($table) {
        $this->conn = connect(); 
        $this->table = $table;
        $this->columns = $this->getTableColumns(); 
    }

    // ✅ Fetch table metadata (private)
    private function getTableColumns() {
        $query = "SHOW COLUMNS FROM `$this->table`";
        $result = $this->conn->query($query);

        $cols = [];
        while ($row = $result->fetch_assoc()) {
            $cols[$row['Field']] = $row;
        }
        return $cols;
    }

    // ✅ Public method to view table schema
    public function describe() {
        return $this->columns;
    }

    // ✅ Create / Insert
   public function create($data) {
    $fields = array_intersect_key($data, $this->columns);
    if (empty($fields)) return false;

    $columns = implode(", ", array_keys($fields));
    $placeholders = implode(", ", array_fill(0, count($fields), "?"));

    $sql = "INSERT INTO `$this->table` ($columns) VALUES ($placeholders)";
    $stmt = $this->conn->prepare($sql);

    $types = $this->getParamTypes($fields);
    $stmt->bind_param($types, ...array_values($fields));

    $success = $stmt->execute();
    $lastId = $this->conn->insert_id; // ✅ get last inserted ID

    $stmt->close();

    return $success ? $lastId : false;
    }


    // ✅ Read (single row or all)
    public function read($id = null, $idColumn = "id") {
        if ($id) {
            $stmt = $this->conn->prepare("SELECT * FROM `$this->table` WHERE $idColumn = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            return $data ?: null;
        } else {
            $result = $this->conn->query("SELECT * FROM `$this->table`");
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

     // Read ALL rows from the table
    public function readAll() {
        $sql = "SELECT * FROM {$this->table}";
        $result = $this->conn->query($sql);

        $rows = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }

        return $rows; // Array of associative arrays
    }

    public function select($columns = ["*"], $where = [], $limit = null) {
    // Make sure columns is an array
    $cols = is_array($columns) ? implode(", ", $columns) : $columns;

    // Base query
    $sql = "SELECT $cols FROM {$this->table}";

    // Add WHERE if provided
    if (!empty($where)) {
        $conditions = [];
        foreach ($where as $col => $val) {
            $val = $this->conn->real_escape_string($val);
            $conditions[] = "$col = '$val'";
        }
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    // Add LIMIT if provided
    if ($limit !== null) {
        $sql .= " LIMIT " . intval($limit);
    }

    $result = $this->conn->query($sql);
    $rows = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    return $rows; // Always returns an array (can be empty if no results)
}


    // ✅ Update (dynamic fields)
    public function update($id, $fields, $idColumn = "id") {
        $fields = array_intersect_key($fields, $this->columns);
        if (empty($fields)) return false;

        $setParts = [];
        foreach ($fields as $col => $val) {
            $setParts[] = "$col = ?";
        }

        $sql = "UPDATE `$this->table` SET " . implode(", ", $setParts) . " WHERE $idColumn = ?";
        $stmt = $this->conn->prepare($sql);

        $types = $this->getParamTypes($fields) . "i";
        $values = array_values($fields);
        $values[] = $id;

        $stmt->bind_param($types, ...$values);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    // ✅ Delete
    public function delete($id, $idColumn = "id") {
        $stmt = $this->conn->prepare("DELETE FROM `$this->table` WHERE $idColumn = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // ✅ Detect parameter types
    private function getParamTypes($fields) {
        $types = "";
        foreach ($fields as $value) {
            if (is_int($value)) {
                $types .= "i";
            } elseif (is_float($value)) {
                $types .= "d";
            } else {
                $types .= "s";
            }
        }
        return $types;
    }

    public function __destruct() {
        $this->conn->close();
    }
}



 //Example Usage:
    /* Create instance for "animal" table
        $animalDB = new DatabaseCRUD("animal");

        // Create a new record
        $animalDB->create([
            "name" => "Buddy",
            "description" => "Playful puppy",
            "type" => "Dog",
            "breed" => "Labrador",
            "gender" => "Male",
            "age" => 2,
            "date_rescued" => "2025-09-01",
            "img" => "buddy.jpg"
        ]);

        // Read all animals
        $animals = $animalDB->read();
        print_r($animals);

        // Read one animal
        $dog = $animalDB->read(5, "animal_id");

        // Update animal
        $animalDB->update(5, ["name" => "Max", "age" => 3], "animal_id");

        // Delete animal
        $animalDB->delete(5, "animal_id");
    */