<?php
use JetBrains\PhpStorm\NoReturn;

function validateName($name): false|int{
    $regex = "/^[\p{L}'\s]*\p{L}[\p{L}'\s]*$/u";
    return preg_match($regex, htmlspecialchars($name));
}

function validateEmail($email){
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password): false|int{
    $regex = '/.*/s'; // TODO: scegliere la regexpr
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
    $data = stripslashes($data);
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