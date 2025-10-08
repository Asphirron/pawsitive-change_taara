<?php

    include "db_connection.php";   

    session_start();

    if(isset($_SESSION["name"]) == false){
        header("Location: ../index.php?message=Must login first");
    }

$application_table = new DatabaseCRUD('adoption_screening');

//print_r($application_table->describe());
$a_application_id = $_POST['a_application_id']; 
$housing = $_POST['housing'];
$reason = $_POST['reason'];
$own_pets = $_POST['own_pets'];
$time_dedicated = $_POST['time_dedicated'];
$children_info = $_POST['children_info'];
$financial_ready = $_POST['financial_ready'];
$breed_interest = $_POST['breed_interest'];
$allergy_info = $_POST['allergy_info'];
$alone_time_plan = $_POST['alone_time_plan'];
$researched_breed = $_POST['researched_breed'];


($application_table->create([
    "a_application_id" => $a_application_id,
    "housing" => $housing,
    "reason" => "$reason",
    "own_pets" => "$own_pets",
    "time_dedicated" => "$time_dedicated",
    "children_info" => "$children_info",
    "financial_ready" => "$financial_ready",
    "breed_interest" => "$breed_interest",
    "allergy_info" => "$allergy_info",
    "alone_time_plan" => "$alone_time_plan",
    "researched_breed" => "$researched_breed"
]));

header("Location: application_confirmation.php");


    