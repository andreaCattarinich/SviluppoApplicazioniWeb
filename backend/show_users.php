<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

try{
    if($_SERVER['REQUEST_METHOD'] !== 'GET')
        throw new Exception('Method Not Allowed', 405);

    $token = admin();
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
    if ($result->num_rows == 0)
        JSONResponse('No recent posts', 200);

    $data = [];
    while($row = $result->fetch_assoc())
        $data[] = $row;

    $optional = [
        'num_users' => $result->num_rows,
        'users'     => $data,
    ];

    JSONResponse('Show all User OK', 200, $optional);
} catch (mysqli_sql_exception $e) {
    JSONResponse('Internal Server Error', 500);
} catch (Exception $e){
    JSONResponse($e->getMessage(), $e->getCode());
}