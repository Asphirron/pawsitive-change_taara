<?php

    include "db_connection.php";   

    session_start();

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
    $fullname = $_POST['fullname'];
    $type = $_POST['type'];
    $img = '';

    /*$img = basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'],'../Assets/UserGenerated/');*/
    if (isset($_FILES['image'])) {
       $target_dir = '../Assets/UserGenerated/';
       $file_name = basename( ($_FILES['image']['name']));
       $target_file = $target_dir . $file_name;
       if(move_uploaded_file($_FILES["image"]['tmp_name'], $target_file)){
        $img = $target_file;
       }
      
    }

    $message = $_POST['message'];
    $contact_num = $_POST['contact'];
    $location = $_POST['contact'];
    $agreed = (isset($_POST['agreed'])) ? $_POST['agreed'] :'false';
    $date = $_POST['date'];
    
    $sql = "INSERT INTO inkind_donation (user_id, full_name, donation_type, img, message, contact_num, location, date, agreed_to_email)
                VALUES (\"$uid\", \"$fullname\", \"$type\", \"$img\", \"$message\", \"$contact_num\", \"$location\", \"$date\",\"$agreed\")";

    
    if ($conn->query($sql) === TRUE) {
      
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn -> close();

    header("Location: transaction_confirmation.php?donation_type=inkind&title=DONATION SUCCESSFUL&message=Please wait for an admin to a confirm your donation");