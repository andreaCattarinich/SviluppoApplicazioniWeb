<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

try{
    if($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception('Method Not Allowed', 405);

    $token = isModerator();
    if (empty($_POST['content']))
        throw new Exception('Bad Request', 400);

    $user_id = $jwtManager->getUserIDFromToken($token);

    $db = db_connect();
    $stmt = $db->prepare("
        INSERT INTO blog (user_id,content)
        VALUES (?,?)");
    $stmt->bind_param('is',$user_id,$_POST['content']);
    $stmt->execute();

    JSONResponse('Post Added Successfully', 201);
} catch (mysqli_sql_exception $e){
    JSONResponse('Internal Server Error', 500);
} catch (Exception $e) {
    JSONResponse($e->getMessage(), $e->getCode());
}