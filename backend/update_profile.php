<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';

try{
    if($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception('Method Not Allowed', 405);

    if($token = authorization()){
        $fields = array('firstname', 'lastname', 'email');

        foreach ($fields as $name) {
            if (empty($_POST[$name]))
                throw new Exception('Bad Request', 400);
        }

        if(!validateName($_POST['firstname'])  || !validateName($_POST['lastname']) || !validateEmail($_POST['email']))
            throw new Exception('Invalid Parameters', 400);

        $firstname  = validateInput($_POST['firstname']);
        $lastname   = validateInput($_POST['lastname']);
        $email      = strtolower(validateInput($_POST['email']));

        // TODO Gestisco variabile aggiuntiva al progetto: va bene?
        if(isset($_POST['instagram']) && validateUsername($_POST['instagram']))
            $instagram = validateInput($_POST['instagram']);
        else{
            $instagram = null;
            //throw new Exception('Invalid Parameters', 400);
        }

        $db = db_connect();
        $stmt = $db->prepare("UPDATE users SET Firstname=?, Lastname=?, Instagram=? WHERE Email=?");
        $stmt->bind_param('ssss', $firstname, $lastname, $instagram, $email);
        $stmt->execute();

        if($stmt->affected_rows == 0) // TODO codice errore 304 non funge
            throw new Exception('Nothing changed', 304); // [adesso funziona!!!]

        if ($stmt->affected_rows == 1) {
            $options = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'instagram' => $instagram,
            ];
        }
    }
}catch (Exception | mysqli_sql_exception $e){
    //JSONResponse($e->getMessage(), $e->getCode());
    header("HTTP/1.1 {$e->getCode()} {$e->getMessage()}");
    exit;
} finally {
    JSONResponse('Update Successful', 200, $options);
}