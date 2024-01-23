<?php
header('Content-Type: application/json');
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    JSONResponse(405, 'Method Not Allowed');
}
if($token = authorization()){
    $fields = array('post');

    foreach ($fields as $name) {
        if (empty($_POST[$name]))
            JSONResponse(400, 'Bad Request');
    }

    // TODO: validare anche $_POST['Post'] e $_POST['Data']
    if(!validateEmail($_POST['email'])){
        JSONResponse(400, 'Invalid Parameters');
    }

    $email = $jwtManager->getEmailFromToken($token);

    try{
        $db = db_connect();
        $currentTime = time();
        $stmt = $db->prepare("INSERT INTO posts (Email,Post,Date) VALUES (?,?,$currentTime)");
        $stmt->bind_param('ss', $email,$_POST['post']);
        $stmt->execute();

        JSONResponse(201, "New Record Successfully");
    }catch(mysqli_sql_exception $e){
        JSONResponse(500, $e->getMessage());
    }
}

