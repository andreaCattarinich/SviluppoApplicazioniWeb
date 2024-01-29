<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception('Method Not Allowed', 405);

    if (empty($_POST['email']))
        throw new Exception('Insert valid email.', 400);

    if(!validateEmail($_POST['email']))
        throw new Exception('Email not valid.', 400);

    $db = db_connect();
    $stmt = $db->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param('s',$_POST['email']);
    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows == 1)
        throw new Exception('Email already used.', 409);
    else
        JSONResponse('Email available.', 200);

} catch (mysqli_sql_exception $e){
    JSONResponse('Internal Server Error', 500);
} catch (Exception $e){
    JSONResponse($e->getMessage(), $e->getCode());
}