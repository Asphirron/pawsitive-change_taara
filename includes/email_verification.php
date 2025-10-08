<?php

include "includes/db_connection.php";

if(!$_SERVER['REQUEST_METHOD'] == 'POST'){
    header("Location: ../register.php");
    exit();
}

if(isset( $_POST["email"])){

}