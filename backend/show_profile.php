<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

try{
    if($_SERVER['REQUEST_METHOD'] !== 'GET')
        throw new Exception('Method Not Allowed', 405);

    $token = authorization();
    $email = $jwtManager->getEmailFromToken($token);

    $db = db_connect();
    $result = $db->query("SELECT * FROM users WHERE email='$email'");

    // TODO: non serve perché email è UNIQUE
    if ($result->num_rows != 1) throw new Exception('Internal Server Error', 500);

    $row = $result->fetch_assoc();
    $optional = [
        'token'     => $token,
        'firstname' => $row['firstname'],
        'lastname'  => $row['lastname'],
        'email'     => $row['email'],
        'instagram' => $row['instagram'],
        'role' => $row['role'],
    ];

    JSONResponse('Show Profile Successful', 200, $optional);
} catch (mysqli_sql_exception $e) {
    JSONResponse('Internal Server Error', 500);
} catch (Exception $e){
    JSONResponse($e->getMessage(), $e->getCode());
}