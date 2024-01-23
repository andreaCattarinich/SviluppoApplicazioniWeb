<?php
header('Content-Type: application/json; charset=utf-8');
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

if($_SERVER["REQUEST_METHOD"] !== 'GET'){
    JSONResponse(405, 'Method Not Allowed');
}

if($token = authorization()) try {
    $db = db_connect();

    //<editor-fold desc="POSTS">
    $postsXpage = 3;
    // TODO SALTARE QUESTA QUERY SE SONO GIA' ENTRATO...
    // Magari farlo con l'utilizzo di JWT
    $posts = $db->query("SELECT * FROM posts");
    if($posts->num_rows == 0)
        JSONResponse(200, 'No recent posts');

    $numPagination = ceil($posts->num_rows / $postsXpage);

    if((int)$_GET['page'] <= 0)
        $currentPage = 1;
    else if((int)$_GET['page'] >= $numPagination)
        $currentPage = $numPagination;
    else
        $currentPage = (int)$_GET['page'];

    $firstResult = ($currentPage - 1) * $postsXpage;

    $showPosts = $db->query("SELECT * FROM posts ORDER BY date DESC LIMIT $firstResult , $postsXpage");
    $data = [];
    while($row = $showPosts->fetch_assoc()){
        $data[] = $row;
    }

    $fullname = $jwtManager->getFullnameFromToken($token);
    echo json_encode(array(
        'success' => true,
        'code' => 200,
        'token' => $token,
        'fullname' => $fullname,
        'posts' => $data,
        'num_posts' => $posts->num_rows,
        'num_pagination' => $numPagination,
        'curr_page' => $currentPage,
    ));
    setcookie('Fullname', $fullname, $jwtManager->getExpireFromToken($token), '/');
    http_response_code(200);
    exit;
    //</editor-fold>
} catch (mysqli_sql_exception $e) {
    JSONResponse(500, $e->getMessage());
}