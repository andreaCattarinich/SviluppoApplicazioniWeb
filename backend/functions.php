<?php

//use JetBrains\PhpStorm\NoReturn;

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

// TODO: valutare se togliere questa funzione
function validateUsername($username): false|int{
    $regex = "/^[A-Za-z0-9._]{0,28}$/"; // TODO: Accetate anche una stringa vuota
    return preg_match($regex, htmlspecialchars($username));
}

// TODO: togliere questa funzione
function validRememberMe(mixed $rememberMe): bool{
    return $rememberMe == "true" || $rememberMe == "false";
}

function validateInput($data): string{
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data);
}

#[NoReturn]
function JSONResponse($code, $message, $optional = array()): void{
  http_response_code($code);
  $success = $code >= 200 && $code <= 299;
  $standard = array(
        'success' => $success,
        'code'    => $code,
        'message' => $success ? $message : null,
        'error'   => !$success ? $message : null
  );
  if(!empty($optional))
      $standard = array_merge($standard, $optional);
  echo json_encode($standard);
  exit;
}