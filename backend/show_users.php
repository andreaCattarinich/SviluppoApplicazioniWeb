<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

try{
    if($_SERVER['REQUEST_METHOD'] !== 'GET')
        throw new Exception('Method Not Allowed', 405);

    if($token = admin()) {
        $search = '%' . ($_GET['search'] ?? '') . '%';

        $db = db_connect();
        $sql = "
        SELECT lastname,firstname,email,role,instagram 
        FROM users
        WHERE 
        (lastname   LIKE ? OR
         firstname  LIKE ? OR
         email      LIKE ? OR
         role       LIKE ? OR 
         instagram  LIKE ?);";

        $stmt = $db->prepare($sql);
        $stmt->bind_param('sssss', $search, $search, $search, $search, $search);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows == 0){
            header('HTTP/1.1 204 No Content');
            exit;
        }

        //$data = $result->fetch_all();
        $data = [];
        while($row = $result->fetch_assoc())
            $data[] = $row;

        $optional = [
            'num_users' => $result->num_rows,
            'users'     => $data,
        ];
    }
} catch (Exception | mysqli_sql_exception $e) {
    if($e->getCode() === 401)
        header('Location: ../frontend/signin.html');
    else if($e->getCode() >= 500)
        header('HTTP/1.1 500 Internal Server Error');
    else
        header("HTTP/1.1 {$e->getCode()} {$e->getMessage()}");
    exit;
} finally {
    JSONResponse('Show all User OK', 200, $optional);
}