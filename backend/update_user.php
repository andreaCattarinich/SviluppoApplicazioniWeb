<?php
require 'constants.php';
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

try{
    if($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception('Method Not Allowed', 405);

    $token = isAdmin();
    $fields = array('Role', 'Email');

    foreach ($fields as $name) {
        if (empty($_POST[$name]))
            throw new Exception('Bad Request', 400);
    }

    if(!in_array($_POST['Role'], DEFAULT_ROLES))
        throw new Exception('Bad Request', 400);

    if($jwtManager->getEmailFromToken($token) == $_POST['Email'])
        throw new Exception('You cannot change your role', 400);

    $db = db_connect();
    $stmt = $db->prepare("UPDATE users SET role=? WHERE email=?");
    $stmt->bind_param('ss', $_POST['Role'], $_POST['Email']);
    $stmt->execute();

    if($stmt->affected_rows == 0) // TODO: sarebbe meglio 304
        JSONResponse('Nothing changed', 200);

    if ($stmt->affected_rows != 1)
        throw new Exception('Internal Server Error', 500);

    JSONResponse('Role Update Successful', 200);
} catch (mysqli_sql_exception $e){
    JSONResponse('Internal Server Error', 500);
} catch (Exception $e) {
    JSONResponse($e->getMessage(), $e->getCode());
}