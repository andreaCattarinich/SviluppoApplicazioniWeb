<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';

try{
    if($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception('Method Not Allowed', 405);

    $fields = array(
        'firstname',
        'lastname',
        'email',
        'pass',
        'confirm'
    );

    foreach ($fields as $name) {
        if (empty($_POST[$name]))
            throw new Exception('Bad Request', 400);
    }

    if( !validateName($_POST['firstname'])  || !validateName($_POST['lastname']) ||
        !validateEmail($_POST['email'])     || !validatePassword($_POST['pass'])){
        throw new Exception('Invalid Parameters', 400);
    }

    $firstname = validateInput($_POST['firstname']);
    $lastname = validateInput($_POST['lastname']);
    $email = strtolower(validateInput($_POST['email']));

    if($_POST['pass'] !== $_POST['confirm'])
        throw new Exception('Password do not match', 400);

    $hash = password_hash(trim($_POST['pass']), PASSWORD_DEFAULT);
    $db = db_connect();
    $stmt = $db->prepare("INSERT INTO users (Firstname,Lastname,Email,Password) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $firstname, $lastname, $email, $hash);
    $stmt->execute();

} catch(Exception | mysqli_sql_exception $e){
    if($e->getCode() == 1062)
        //JSONResponse('Email already exits', 409);
        header('HTTP/1.1 409 Email already exits');
    else
        //JSONResponse('Something goes wrong', 500);
        //header("HTTP/1.1 {$e->getCode()} {$e->getMessage()}");
        header('HTTP/1.1 500 Something goes wrong');
    exit;
} finally {
    //JSONResponse('Registration Successful', 201);
    header('HTTP/1.1 201 Registration Successful');
    exit;
}