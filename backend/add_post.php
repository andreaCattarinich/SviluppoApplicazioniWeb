<?php
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


        $fullname = $jwtManager->getFullnameFromToken($token);

        $db = db_connect();
        //<editor-fold desc="GET ROLE"> Lato backend
        $stmt = $db->prepare("SELECT Role FROM users WHERE Email=?");
        $stmt->bind_param('s', $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $role = $result->fetch_assoc();
        $role = $role['Role'];
        //</editor-fold>

        $currentTime = time();
        $stmt = $db->prepare("INSERT INTO posts (Fullname,Email,Role,Post,Date) VALUES (?,?,'{$role}',?,$currentTime)");
        $stmt->bind_param('sss', $fullname, $email,$_POST['post']);
        $stmt->execute();
    }
}catch (Exception | mysqli_sql_exception $e){
    //JSONResponse($e->getMessage(), $e->getCode());
    header("HTTP/1.1 {$e->getCode()} {$e->getMessage()}");
    exit;
} finally {
    //JSONResponse('New Record Added Successfully', 201);
    header('HTTP/1.1 201 New Record Added Successfully');
    exit;
}