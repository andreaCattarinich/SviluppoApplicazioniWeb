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
        if (empty($_POST['content']))
            throw new Exception('Bad Request', 400);

        $email = $jwtManager->getEmailFromToken($token);

        $db = db_connect();

        //<editor-fold desc="GET ROLE"> Lato backend
        $stmt = $db->prepare("SELECT role FROM users WHERE email=?");
        $stmt->bind_param('s', $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $role = $result->fetch_assoc()['role'];
        //</editor-fold>

        $stmt = $db->prepare("
            INSERT INTO blog (user_email,content)
            VALUES (?,?)");
        $stmt->bind_param('ss',$email,$_POST['content']);
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