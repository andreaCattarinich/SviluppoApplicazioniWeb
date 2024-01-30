<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

try{
    if($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception('Method Not Allowed', 405);

    $token = authorization();
    $fields = array('firstname', 'lastname', 'email');

    foreach ($fields as $name) {
        if (empty($_POST[$name]))
            throw new Exception('Bad Request', 400);
    }

    if(!validateName($_POST['firstname'])  || !validateName($_POST['lastname']) || !validateEmail($_POST['email']))
        throw new Exception('Invalid Parameters', 400);

    $firstname  = validateInput($_POST['firstname']);
    $lastname   = validateInput($_POST['lastname']);
    $new_email  = strtolower(validateInput($_POST['email']));
    $user_id  = $jwtManager->getUserIDFromToken($token);

    // Variabile aggiuntiva al progetto
    if(isset($_POST['instagram']) && validateUsername($_POST['instagram']))
        $instagram = validateInput($_POST['instagram']);
    else
        $instagram = null;

    $db = db_connect();
    $stmt = $db->prepare("UPDATE users SET firstname=?, lastname=?, email=?, instagram=? WHERE user_id=?");
    $stmt->bind_param('ssssi', $firstname, $lastname, $new_email, $instagram, $user_id);
    $stmt->execute();

    $options = [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $new_email,
        'instagram' => $instagram,
    ];

    if($stmt->affected_rows == 0)   // TODO: sarebbe meglio utilizzare 304 Not Modified, ma non funziona lato JS
        JSONResponse('Nothing changed', 200, $options);

    if ($stmt->affected_rows == 1)
        JSONResponse('Update Successful', 200, $options);

} catch (mysqli_sql_exception $e){
    $e->getCode() == 1062
        ? JSONResponse('Email Already Registered', 409)
        : JSONResponse('Internal Server Error', 500);
} catch (Exception $e) {
    JSONResponse($e->getMessage(), $e->getCode());
}