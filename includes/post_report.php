<?php

    include "db_connection.php";   

    session_start();

    if(isset($_SESSION["name"]) == false){
        header("Location: ../rescue.php?message=Must login first");
    }
    /*if(!$_SERVER["REQUEST_METHOD"] == "POST"){
        header("Location: ../monetary_donation.php");
        exit();
    }

    $required_fields = ['fullname', 'amount', 'payment_option', 'contact'];
    foreach($required_fields as $field){
        if(!isset($_POST[$field])){
            header("Location: ../donation.php");
            exit();
        }
    }*/

    $conn = connect();

    $uid = $_SESSION['name'];
    $type = $_POST['report_type'];
    $desc = $_POST['description'];
    $fullname = $_POST['full_name'];
    $contact_num = $_POST['contact_num'];
    $location = $_POST['location'];
    
    $img = '';

    if (isset($_FILES['image'])) {
       $target_dir = '../Assets/UserGenerated/';
       $file_name = basename( ($_FILES['image']['name']));
       $target_file = $target_dir . $file_name;
       if(move_uploaded_file($_FILES["image"]['tmp_name'], $target_file)){
        $img = $target_file;
       }
      
    }

    //$agreed = (isset($_POST['agreed'])) ? $_POST['agreed'] :'false';

    date_default_timezone_set('Asia/Manila');
    $date = date('Y-m-d');

    $status = 'pending';
   


    $sql = "INSERT INTO rescue_report (user_id, type, description, full_name, contact_num, location, img, date_posted, status)
                VALUES (\"$uid\", \"$type\", \"$desc\", \"$fullname\", \"$contact_num\", \"$location\", \"$img\",\"$date\",\"$status\")";

    
    if ($conn->query($sql) === TRUE) {
      
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn -> close();

    header("Location: transaction_confirmation.php?title=REPORT SUCCESSFUL&message=Please wait for our response");