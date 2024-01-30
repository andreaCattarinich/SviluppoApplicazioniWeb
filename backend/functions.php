<?php
use JetBrains\PhpStorm\NoReturn;

function validateName($name): false|int{
    // TODO: aggiungere commento
    $regex = "/^[\p{L}'\s]*\p{L}[\p{L}'\s]*$/u";
    return preg_match($regex, htmlspecialchars($name));
}

function validateEmail($email){
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password): false|int{
    /* Password must contain at least 1 number, 1 capital and 1 lower letter
    if( strlen($password) <= 0 ||
        !preg_match('#[0-9]+#',$password) ||
        !preg_match("#[A-Z]+#",$password) ||
        !preg_match("#[a-z]+#",$password) ){
        return false;
    }
    */
    $regex = '/.*/s'; // Regex che accetta tutti i caratteri
    return preg_match($regex, htmlspecialchars($password));
}
function validateUsername($username): false|int{
    /* Aggiuntiva:
     *  Accetta stringhe che contengono caratteri alfabetici,
     *  numeri, punti e underscore
    */
    $regex = "/^[A-Za-z0-9._]{0,28}$/";
    return preg_match($regex, htmlspecialchars($username));
}

function validateInput($data): string{
    $data = trim($data);
    return htmlspecialchars($data);
}

#[NoReturn]
function JSONResponse($message, $status, $optional = array()): void{
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode(array_merge(
        ['message' => $message],
        $optional
    ));
    exit;
}