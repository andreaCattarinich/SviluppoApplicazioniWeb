<?php
require 'functions.php';
require 'database.php';
include 'error_reporting.php';
require  'auth.php';
global $jwtManager;

try{
    if($_SERVER['REQUEST_METHOD'] !== 'GET')
        throw new Exception('Method Not Allowed', 405);

    if($token = authorization()) {
        $db = db_connect();

        $postsXpage = 3;
        $search = '%' . ($_GET['search'] ?? '') . '%';

        // Prelevo dal DB il numero totale dei post
        $stmt = $db->query("SELECT COUNT(*) FROM blog");
        $total_number = (int)$stmt->fetch_assoc()['COUNT(*)'];

        if($total_number == 0)
            JSONResponse('No recent posts', 204);
        $firstResult = ((int)$_GET['page'] - 1) * $postsXpage;

        $sql = "
        SELECT blog.id AS id, CONCAT(users.firstname, ' ', users.lastname) AS fullname, role, content, created_at
        FROM blog
        LEFT JOIN users ON users.email = blog.user_email
        WHERE
        content LIKE ?
        ORDER BY created_at DESC
        LIMIT ?, $postsXpage;
        ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('si',$search, $firstResult);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = $result->fetch_all(MYSQLI_ASSOC);

        $options = [
            'posts' => $data,
            'num_posts' => $total_number,
            'num_pagination' => ceil($total_number / $postsXpage),
            'curr_page' => (int)$_GET['page'] // TODO eliminarla, tanto è ridondante
        ];
    }
} catch (Exception | mysqli_sql_exception $e) {
    // TODO va bene?
    //JSONResponse($e->getMessage(), $e->getCode());
    $e->getCode() === 401
        ? header('Location: ../frontend/signin.html')
        : header("HTTP/1.1 {$e->getCode()} {$e->getMessage()}");
    exit;
} finally {
    JSONResponse('Show Posts Successful', 200, $options);
}