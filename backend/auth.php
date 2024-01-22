<?php
const SECRET_KEY = 'QWERTYZZZ';
function base64url_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data)
{
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function getSign($header, $payload)
{
    $string = $header . '.' . $payload;
    $binSign = hash_hmac('SHA256', $string, SECRET_KEY, true);
    $base64sign = base64url_encode($binSign);

    return $base64sign;
}

function generateToken($payload)
{
    $header = json_encode([
        'alg' => 'HS256',
        'typ' => 'JWT'
    ]);
    $payload = json_encode($payload);

    $base64header = base64url_encode($header);
    $base64payload = base64url_encode($payload);

    $base64sign = getSign($base64header, $base64payload);

    return "$base64header.$base64payload.$base64sign";
}

function validateToken($token)
{
    $parts = explode('.', $token);
    if (sizeof($parts) != 3) {
        return false;
    }
    list($header, $payload, $sign) = $parts;
    if (getSign($header, $payload) == $sign) {
        return true;
    }
    return false;
}

function getTokenData($token)
{
    $parts = explode('.', $token);
    if (sizeof($parts) != 3) {
        return false;
    }
    $payload = $parts[1];
    return json_decode(base64url_decode($payload), true);
}

/******** CONTROLLO AUTORIZZAZIONE CON TOKEN JWT *******/
function authorization(){
    if(isset($_SESSION['Token'])){
        return $_SESSION['Token'];
    }else {
        if (!isset($_SERVER['HTTP_AUTHENTICATION'])) {
            ErrorResponse(401, 'Unauthorized');
        }

        $tokenParts = explode(' ', $_SERVER['HTTP_AUTHENTICATION']);
        if (sizeof($tokenParts) != 2) {
            ErrorResponse(401, 'Unauthorized');
        }
        $token = $tokenParts[1];

        if (!validateToken($token)) {
            ErrorResponse(401, 'Unauthorized');
        }
        return $token;
    }
}