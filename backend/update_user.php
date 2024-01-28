<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';

try{
    if($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception('Method Not Allowed', 405);

    if($token = admin()){
        $fields = array('Role', 'Email');

        foreach ($fields as $name) {
            if (empty($_POST[$name]))
                throw new Exception('Bad Request', 400);
        }

        $roles = ['Admin', 'Moderator', 'Editor', 'Blocked'];
        if(!in_array($_POST['Role'], $roles))
            throw new Exception('Bad Request', 400);

        $db = db_connect();
        $stmt = $db->prepare("UPDATE users SET role=? WHERE email=?");
        $stmt->bind_param('ss', $_POST['Role'], $_POST['Email']);
        $stmt->execute();

        if($stmt->affected_rows == 0)
            throw new Exception('Nothing changed', 304);

        if ($stmt->affected_rows != 1)
            throw new Exception('Internal Server Error', 500);

    }
}catch (Exception | mysqli_sql_exception $e){
    //JSONResponse($e->getMessage(), $e->getCode());
    $e->getCode() === 401
        ? header('Location: ../frontend/signin.html')
        : header("HTTP/1.1 {$e->getCode()} {$e->getMessage()}");
    exit;
} finally {
    JSONResponse('Role Update Successful', 200);
}