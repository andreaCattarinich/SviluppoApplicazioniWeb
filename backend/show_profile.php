<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';

if($_SERVER["REQUEST_METHOD"] !== 'GET'){
    JSONResponse(405, 'Method Not Allowed');
}

if($token = authorization()) try {
    $email = getEmailFromToken($token);

    $db = db_connect();
    $result = $db->query("SELECT * FROM users WHERE Email='$email'");
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        echo json_encode(array(
            'success' => true,
            'code' => 200,
            'token' => $token,
            'firstname' => $row['Firstname'],
            'lastname' => $row['Lastname'],
            'email' => $row['Email'],
            'instagram' => $row['Instagram'],
        ));
        http_response_code(200);
        exit;
    }
    JSONResponse(500, 'Internal Server Error');
} catch (mysqli_sql_exception $e) {
    JSONResponse(500, $e->getMessage());
}