<?php
require 'constants.php';
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

try{
    if($_SERVER['REQUEST_METHOD'] !== 'GET')
        throw new Exception('Method Not Allowed', 405);

    $token = authorization();
    $db = db_connect();

    $search = '%' . ($_GET['search'] ?? '') . '%';

    // Prelevo dal DB il numero dei post (totali oppure cercati in base al campo search)
    $stmt = $db->prepare("SELECT COUNT(*) FROM blog WHERE content LIKE ?");
    $stmt->bind_param('s', $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_number = (int)$result->fetch_assoc()['COUNT(*)'];

    if($total_number == 0) // TODO: non mettere 200
        JSONResponse('No posts found', 200, ['posts' => '']);

    $firstResult = ((int)$_GET['page'] - 1) * POSTS_PER_PAGE;

    $sql = "
    SELECT blog.post_id AS id, CONCAT(users.firstname, ' ', users.lastname) AS fullname, role, content, created_at
    FROM blog
    LEFT JOIN users ON users.user_id = blog.user_id
    WHERE
    content LIKE ?
    ORDER BY created_at DESC
    LIMIT ?," . POSTS_PER_PAGE;

    $stmt = $db->prepare($sql);
    $stmt->bind_param('si',$search, $firstResult);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = $result->fetch_all(MYSQLI_ASSOC);

    $options = [
        'posts' => $data,
        'num_posts' => $total_number,
        'num_pagination' => ceil($total_number / POSTS_PER_PAGE),
        'curr_page' => (int)$_GET['page'] // Ridondante
    ];
    JSONResponse('Show Posts Successful', 200, $options);
} catch (mysqli_sql_exception $e){
    JSONResponse('Internal Server Error', 500);
} catch (Exception $e) {
    JSONResponse($e->getMessage(), $e->getCode());
}