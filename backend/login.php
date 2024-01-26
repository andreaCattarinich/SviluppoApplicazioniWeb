<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require 'auth.php';
global $jwtManager;

try{
    if($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception('Method Not Allowed', 405);

    $fields = array('email', 'pass');

    foreach ($fields as $name) {
        if (empty($_POST[$name]))
            throw new Exception('Bad Request', 400);
    }

    if(!validateEmail($_POST['email']) || !validatePassword($_POST['pass']))
        throw new Exception('Invalid Parameters', 400);

    $email = strtolower(validateInput($_POST['email']));
    $password = trim($_POST['pass']);

    $db = db_connect();
    $stmt = $db->prepare("SELECT * FROM users WHERE Email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows == 0)
        throw new Exception('Email not found', 404);

    $row = $result->fetch_assoc();
    if(!password_verify($password, $row['Password']))
        throw new Exception('Unauthorized', 401);

    $data = [
        'Firstname' => $row['Firstname'],
        'Lastname' => $row['Lastname'],
        'Email' => $row['Email'],
    ];

    //<editor-fold desc="COOKIE>
    $delta = isset($_POST['rememberMe']) && $_POST['rememberMe'] == 'true'
        ? 60*60 // 1 hour
        : 300;  // 5 minutes

    $token = $jwtManager->createToken([
        'iss' => 'http://localhost',
        'iat' => time(),
        'exp' => time() + $delta,
        'data' => $data,
    ]);

    // TODO l'orario è corretto? Non ho ritardi?
    setcookie('auth-token', $token, time()+$delta, '/');
    //</editor-fold>

    $options = [
        'token'     => $token,
        'data'      => $data,
    ];
} catch (Exception | mysqli_sql_exception $e){
    //JSONResponse('Unauthorized', 401);
    //JSONResponse($e->getMessage(), $e->getCode());
    //JSONResponse($e->getMessage(), 500);
    header('HTTP/1.1 401 Unauthorized');
    exit;
} finally {
    //JSONResponse('Login Successful', 200, $options);
    header('Location: ../frontend/profile.html');
    exit;
}