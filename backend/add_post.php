<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

try{
    if($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception('Method Not Allowed', 405);

    $token = moderator();
    if (empty($_POST['content']))
        throw new Exception('Bad Request', 400);

    $email = $jwtManager->getEmailFromToken($token);

    $db = db_connect();

    // TODO: fare funzione getRoleByEmail()
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

    JSONResponse('Post Added Successfully', 201);
} catch (mysqli_sql_exception $e){
    JSONResponse('Internal Server Error', 500);
} catch (Exception $e) {
    JSONResponse($e->getMessage(), $e->getCode());
}