<?php
session_start();
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';

if($_SERVER["REQUEST_METHOD"] !== 'POST'){
    JSONResponse(405, 'Method Not Allowed');
}

if($token = authorization()) {
    $fields = array(
        'firstname',
        'lastname',
        'email'
        //'instagram'
    );

    foreach ($fields as $name) {
        if (empty($_POST[$name]))
            JSONResponse(400, 'Bad Request');
    }

    if(!validateName($_POST['firstname'])  || !validateName($_POST['lastname']) || !validateEmail($_POST['email'])){
        JSONResponse(400, 'Invalid Parameters');
    }

    $firstname = validateInput($_POST['firstname']);
    $lastname = validateInput($_POST['lastname']);
    $email = strtolower(validateInput($_POST['email']));
    $instagram = isset($_POST['instagram']) ? validateInput($_POST['instagram']) : ''; // TODO: vedere come gestire questo parametro e i test automatici

    try {
        $db = db_connect();
        $stmt = $db->prepare("UPDATE users SET Firstname=?, Lastname=?, Instagram=? WHERE Email=?");
        $stmt->bind_param('ssss', $firstname, $lastname, $instagram, $email);
        $stmt->execute();

        if ($stmt->affected_rows == 1) {
            echo json_encode(array(
                'success' => true,
                'code' => 200,
                'message' => 'Data changed',
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'instagram' => $instagram,
            ));
            http_response_code(200);
        } else {
            JSONResponse(400, 'Nothing changed');
        }
        exit;
    } catch (mysqli_sql_exception $e) {
        //JSONResponse(500, $e->getMessage() . $e->getCode());
        JSONResponse(500, 'Internal error');
    }
}