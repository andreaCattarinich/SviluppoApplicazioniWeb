<?php
const SECRET_KEY = 'QWERTYZZZ';
$jwtManager = new JwtManager(SECRET_KEY);

/******** CONTROLLO AUTORIZZAZIONE CON TOKEN JWT ******
 * @throws Exception
 */
function authorization(){
    global $jwtManager;
    $token = $jwtManager->getTokenFromServer();
    if($jwtManager->expiredToken($token))
        return $token;
    else
        throw new Exception('Login Time-out', 440);
}

/******** CONTROLLO AUTORIZZAZIONE E RUOLO **********
 * @throws Exception
 */
function isAdmin(){
    global $jwtManager;
    try {
        $token = authorization();
        $db = db_connect();
        $user_id = $jwtManager->getUserIDFromToken($token);
        $stmt = $db->prepare("SELECT role FROM users WHERE user_id=?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows != 1)
            throw new Exception('Unauthorized', 401);
        $role = $result->fetch_assoc();
        if ($role['role'] != 'Admin')
            throw new Exception('Forbidden', 403);

        return $token;
    } catch (mysqli_sql_exception $e){
        JSONResponse('Internal Server Error', 500);
    } catch (Exception $e) {
        JSONResponse($e->getMessage(), $e->getCode());
    }
}

function isModerator(){
    global $jwtManager;
    try {
        $token = authorization();
        $db = db_connect();
        $user_id = $jwtManager->getUserIDFromToken($token);
        $stmt = $db->prepare("SELECT role FROM users WHERE user_id=?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows != 1)
            throw new Exception('Unauthorized', 401);
        $role = $result->fetch_assoc();
        if ($role['role'] == 'Moderator' || $role['role'] == 'Admin')
            return $token;
        else
            throw new Exception('Only Moderators Can Add Posts', 403);
    } catch (mysqli_sql_exception $e){
        JSONResponse('Internal Server Error', 500);
    } catch (Exception $e) {
        JSONResponse($e->getMessage(), $e->getCode());
    }
}

/************** JWT OPERATIONS *******************/
class JwtManager
{
//    private $secretKey;
    public function __construct($secretKey)
    {
        //$this->secretKey = $secretKey;
    }
    public function createToken($payload): string
    {
        $header = json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]);
        $payload = json_encode($payload);

        $base64header = $this->base64UrlEncode($header);
        $base64payload = $this->base64UrlEncode($payload);

        $base64sign = $this->getSign($base64header, $base64payload);

        return "$base64header.$base64payload.$base64sign";
    }
    public function validateToken($token): bool
    {
        $parts = explode('.', $token);
        if (sizeof($parts) != 3) {
            return false;
        }
        list($header, $payload, $sign) = $parts;
        if ($this->getSign($header, $payload) == $sign) {
            return true;
        }
        return false;
    }
    public function base64UrlEncode($data): string
    {
        $base64 = base64_encode($data);
        $base64Url = strtr($base64, '+/', '-_');
        return rtrim($base64Url, '=');
    }
    public function base64UrlDecode($data): false|string
    {
        $base64 = strtr($data, '-_', '+/');
        $base64Padded = str_pad($base64, strlen($base64) % 4, '=');
        return base64_decode($base64Padded);
    }
    public function getSign(string $header, string $payload): string
    {
        $string = $header . '.' . $payload;
        $binSign = hash_hmac('SHA256', $string, SECRET_KEY, true);
        return $this->base64UrlEncode($binSign);
    }
    public function getTokenPayload($token)
    {
        $parts = explode('.', $token);
        if (sizeof($parts) != 3) {
            return false;
        }
        $payload = $parts[1];
        return json_decode($this->base64UrlDecode($payload), true);
    }
    public function getTokenData($token)
    {
        $payload = $this->getTokenPayload($token);
        return $payload['data'];
    }
    public function expiredToken($token): bool
    {
        $payload = $this->getTokenPayload($token);
        return $payload['exp'] > time();
    }
    public function getUserIDFromToken($token): int
    {
        $payload = $this->getTokenPayload($token);
        return $payload['user_id'];
    }
    public function getEmailFromToken($token){
        $data = $this->getTokenData($token);
        return $data['email'];
    }
    public function getFullnameFromToken($token): string
    {
        $data = $this->getTokenData($token);
        return $data['firstname'] . ' ' . $data['lastname'];
    }
    public function getExpireFromToken($token)
    {
        $payload = $this->getTokenPayload($token);
        return $payload['exp'];
    }

    /**
     * @throws Exception
     */
    public function getTokenFromServer(){
        // PRELEVO IL TOKEN DAL COOKIE (se presente)
        if(isset($_COOKIE['auth-token'])){
            if($this->validateToken($_COOKIE['auth-token']))
                return $_COOKIE['auth-token'];
            else
                throw new Exception('Unauthorized', 401);
        }

        // PRELEVO IL TOKEN DALL'HEADER (se presente)
        if (!isset($_SERVER['HTTP_AUTHENTICATION']))
            throw new Exception('Unauthorized', 401);

        $tokenParts = explode(' ', $_SERVER['HTTP_AUTHENTICATION']);
        if (sizeof($tokenParts) != 2)
            throw new Exception('Unauthorized', 401);

        $token = $tokenParts[1];
        if (!($this->validateToken($token)))
            throw new Exception('Unauthorized', 401);

        return $token;
    }
}