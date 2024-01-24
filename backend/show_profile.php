<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

if($_SERVER["REQUEST_METHOD"] !== 'GET'){
    JSONResponse(405, 'Method Not Allowed');
}

if($token = authorization()) try {
    $email = $jwtManager->getEmailFromToken($token);

    $db = db_connect();
    $result = $db->query("SELECT * FROM users WHERE Email='$email'");
    if ($result->num_rows != 1)
        JSONResponse(500, 'Internal Server Error');

    $row = $result->fetch_assoc();
    JSONResponse(200, 'Show profile OK', array(
        'token' => $token,
        'firstname' => $row['Firstname'],
        'lastname' => $row['Lastname'],
        'email' => $row['Email'],
        'instagram' => $row['Instagram'],
    ));
} catch (mysqli_sql_exception $e) {
    JSONResponse(500, $e->getMessage());
}