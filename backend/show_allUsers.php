<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

try{
    if($_SERVER['REQUEST_METHOD'] !== 'POST')
        throw new Exception('Method Not Allowed', 405);

    if($token = admin()) {
        if (empty($_POST['search']))
            $inputSearch = '';
            //throw new Exception('Bad Request', 400);

        $inputSearch = validateInput($_POST['search']);
        $db = db_connect();
        $sql = "
        SELECT Lastname,Firstname,Email,Role,Instagram
        FROM users
        WHERE Lastname LIKE ?
        OR Firstname LIKE ?
        OR Email LIKE ?
        OR Role LIKE ?
        OR Instagram LIKE ?;
        ";
        $search = $inputSearch . '%';

        $stmt = $db->prepare($sql);
        $stmt->bind_param('sssss', $search, $search, $search, $search, $search);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows == 0){
            header('HTTP/1.1 204 No Content');
            exit;
        }

        $data = [];
        while ($row = $result->fetch_assoc())
            $data[] = $row;

        $optional = [
            'token'     => $token,
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