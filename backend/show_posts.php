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

        $q = '%' . ($_GET['q'] ?? '') . '%';

        $from = ((int)$_GET['page'] - 1) * $postsXpage;
        $db = db_connect();
        $stmt = $db->prepare("SELECT p.id as id, CONCAT(u.Firstname, ' ', u.Lastname) as fullname, role, content, created_at FROM catta.posts2 as p left join catta.users as u on u.Email = p.user_email WHERE content LIKE ? ORDER BY created_at DESC limit " . $postsXpage . " offset " . $from . ";");
        $stmt->bind_param('s', $q);
        $stmt->execute();

        $result = $stmt->get_result();

        $posts = [];

         while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }

        $stmt = $db->prepare("SELECT COUNT(*) as total_number FROM catta.posts2");
        $stmt->execute();
        $total_number = $stmt->get_result()->fetch_assoc()['total_number'];

        $options = [
            'posts' => $posts,
            'num_posts' => (int) $total_number,
            'num_pagination' => ceil((int) $total_number / $postsXpage),
            'curr_page' => (int) $_GET['page'],
        ];
    }
} catch (Exception $e) {
    // TODO va bene?
    //JSONResponse($e->getMessage(), $e->getCode());
    //$e->getCode() === 401
      //  ? header('Location: ../frontend/signin.html')
        //: header("HTTP/1.1 {$e->getCode()} {$e->getMessage()}");

    echo $e->getMessage();

    exit;
} finally {
    JSONResponse('Show Posts Successful', 200, $options);
}