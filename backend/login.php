<?php
require 'constants.php';
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

    // TODO: ho già il PHP in check_email. Eventualmente ri-utilizzarlo
    $db = db_connect();
    $stmt = $db->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows == 0)
        throw new Exception('Email not found', 401);

    $row = $result->fetch_assoc();
    if(!password_verify($password, $row['password']))
        throw new Exception('Unauthorized', 401);

    $data = [
        'firstname' => $row['firstname'],
        'lastname'  => $row['lastname'],
        'email'     => $row['email'],
    ];

    $delta = isset($_POST['rememberMe']) && $_POST['rememberMe'] == 'true'
        ? JWT_TTL
        : JWT_REMEMBERME;

    $currentTime = time();

    $token = $jwtManager->createToken([
        'iat'       => $currentTime,
        'exp'       => $currentTime + $delta,
        'user_id'   => $row['user_id'],
        'role'      => $row['role'],
        'data'      => $data,
    ]);

    setcookie('auth-token', $token, $currentTime + $delta, '/');

    $options = [
        'token'     => $token,
        'data'      => $data,
    ];

    JSONResponse('Login Successfully', 200);
} catch (mysqli_sql_exception $e){
    JSONResponse('Internal Server Error', 500);
} catch (Exception $e){
    JSONResponse($e->getMessage(), $e->getCode());
}