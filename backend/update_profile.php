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
    $old_email  = $jwtManager->getEmailFromToken($token); // TODO: fare con userID

    if(isset($_POST['instagram']) && validateUsername($_POST['instagram']))
        $instagram = validateInput($_POST['instagram']);
    else
        $instagram = null;

    $db = db_connect();
    $stmt = $db->prepare("UPDATE users SET firstname=?, lastname=?, email=?, instagram=? WHERE email=?");
    $stmt->bind_param('sssss', $firstname, $lastname, $new_email, $instagram, $old_email);
    $stmt->execute();

    if($stmt->affected_rows == 0)
        throw new Exception('Nothing changed', 200);

    if($new_email != $old_email){
        // TODO: fare con userID
        $token = $jwtManager->createToken([
            'iss' => 'http://localhost',
            'iat' => time(),
            'exp' => time() + 1200, // 20 minutes
            'data' => [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $new_email,
            ],
        ]);

        setcookie('auth-token', $token, time()+1200, '/');
    }

    if ($stmt->affected_rows == 1) {
        $options = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $new_email,
            'instagram' => $instagram,
        ];
        JSONResponse('Update Successful', 200, $options);
    }

} catch (mysqli_sql_exception $e){
    JSONResponse('Internal Server Error', 500);
} catch (Exception $e) {
    JSONResponse($e->getMessage(), $e->getCode());
}