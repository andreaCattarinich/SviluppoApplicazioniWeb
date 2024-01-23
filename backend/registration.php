<?php
header('Content-Type: application/json');
require 'functions.php';
require 'database.php';
include 'error_reporting.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    JSONResponse(405, 'Method Not Allowed');
}

$fields = array(
    'firstname',
    'lastname',
    'email',
    'pass',
    'confirm'
);

foreach ($fields as $name) {
    if (empty($_POST[$name]))
        JSONResponse(400, 'Bad Request');
}

if( !validateName($_POST['firstname'])  || !validateName($_POST['lastname']) ||
    !validateEmail($_POST['email'])     || !validatePassword($_POST['pass'])){
    JSONResponse(400, 'Invalid Parameters');
}

$firstname = validateInput($_POST['firstname']);
$lastname = validateInput($_POST['lastname']);
$email = strtolower(validateInput($_POST['email']));

if($_POST['pass'] !== $_POST['confirm'])
  JSONResponse(400, 'Password do not match');

$hash = password_hash(trim($_POST['pass']), PASSWORD_DEFAULT);

try{
  $db = db_connect();
  $stmt = $db->prepare("INSERT INTO users (Firstname,Lastname,Email,Password) VALUES (?,?,?,?)");
  $stmt->bind_param('ssss', $firstname, $lastname, $email, $hash);
  $stmt->execute();

  JSONResponse(201, "Registration Successful");
}catch(mysqli_sql_exception $e){
  ($e->getCode() == 1062) ? JSONResponse(409, "Email already exits") :
  JSONResponse(500, 'Internal error');
}