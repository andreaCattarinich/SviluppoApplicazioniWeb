<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

try{
    if($_SERVER['REQUEST_METHOD'] !== 'GET')
        throw new Exception('Method Not Allowed', 405);

    if($token = authorization()) {
        $email = $jwtManager->getEmailFromToken($token);

        $db = db_connect();
        $result = $db->query("SELECT * FROM users WHERE email='$email'"); // TODO: serve il prepared Statement?
        if ($result->num_rows != 1)
            throw new Exception('Internal Server Error', 500);

        $row = $result->fetch_assoc();
        $optional = [
            'token'     => $token,
            'firstname' => $row['firstname'],
            'lastname'  => $row['lastname'],
            'email'     => $row['email'],
            'instagram' => $row['Instagram'],
        ];
    }
} catch (Exception | mysqli_sql_exception $e) {
    $e->getCode() >= 500
        ? header('HTTP/1.1 500 Internal Server Error')
        : header("HTTP/1.1 {$e->getCode()} {$e->getMessage()}");
    exit;
} finally {
    JSONResponse('Show Profile Successful', 200, $optional);
}