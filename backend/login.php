<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require 'auth.php';
global $jwtManager;

if($_SERVER["REQUEST_METHOD"] !== 'POST'){
  JSONResponse(405, 'Method Not Allowed');
}

$fields = array(
    'email',
    'pass'
);

foreach ($fields as $name) {
    if (empty($_POST[$name]))
        JSONResponse(400, 'Bad Request');
}

if(!validateEmail($_POST['email']) || !validatePassword($_POST['pass'])){
    JSONResponse(400, 'Invalid Parameters');
}

$email = strtolower(validateInput($_POST['email']));
$password = trim($_POST['pass']);

try{
    $db = db_connect();
    $stmt = $db->prepare("SELECT * FROM users WHERE Email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if($result->num_rows == 0)
        JSONResponse(404, 'Email not found');

    $row = $result->fetch_assoc();
    if(!password_verify($password, $row['Password']))
        JSONResponse(401, 'Unauthorized');

    //<editor-fold desc="COOKIE>
    $expire = (isset($_POST['rememberMe']) && $_POST['rememberMe'] == 'true')
        ? time () + 60*60
        : time() + 300;

    $token = $jwtManager->createToken([
        'ExpireDate' => $expire,
        'Firstname' => $row['Firstname'],
        'Lastname' => $row['Lastname'],
        'Email' => $row['Email']
    ]);

    setcookie('Token', $token, $expire, '/');
    //</editor-fold>

    JSONResponse(200, 'Login Successful', array(
        'token'     => $token,
        'data'      => array(
            'Firstname'   => $row['Firstname'],
            'Lastname'    => $row['Lastname'],
            'Email'       => $row['Email'],
        )
    ));
}catch (mysqli_sql_exception $e){
    JSONResponse(500, $e->getMessage());
}