<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';

/* TODO: controllare se serve o no!
* Se lo tolgo e accedo a /backend/show_profile.php
*/

if($_SERVER["REQUEST_METHOD"] !== 'GET'){
    JSONResponse(405, 'Method Not Allowed');
}

if($token = authorization()) {
    try {
        // TODO: togliere connessione al DB e fare tutto con JWT
        $db = db_connect();
        $currentTime = time();

        //$result = $db->query("SELECT * FROM users WHERE Email='{$_POST['Email']}' OR Token=$token AND ExpirationDate > $currentTime");
        $result = $db->query("SELECT * FROM users WHERE Token='$token' AND ExpirationDate > $currentTime");
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            echo json_encode(array(
                'success' => true,
                'code' => 200,
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
}