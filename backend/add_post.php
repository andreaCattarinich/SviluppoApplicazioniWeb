<?php
header('Content-Type: application/json');
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

try{
    if($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception('Method Not Allowed', 405);

    if($token = authorization()) {
        if (empty($_POST['post']))
            throw new Exception('Bad Request', 400);

        $email = $jwtManager->getEmailFromToken($token);

        //<editor-fold desc="ADD POST> Lato backend
        $fullname = $jwtManager->getFullnameFromToken($token);
        //</editor-fold>

        $db = db_connect();
        $currentTime = time();
        $stmt = $db->prepare("INSERT INTO posts (Fullname,Email,Role,Post,Date) VALUES (?,?,'Admin',?,$currentTime)");
        $stmt->bind_param('sss', $fullname, $email,$_POST['post']);
        $stmt->execute();
    }
}catch (Exception | mysqli_sql_exception $e){
    JSONResponse($e->getMessage(), $e->getCode());
} finally {
    JSONResponse('New Record Added Successfully', 201);
}