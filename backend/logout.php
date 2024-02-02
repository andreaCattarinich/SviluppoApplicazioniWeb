<?php
require 'functions.php';
// TODO: invalidare JWT al logout

if(isset($_COOKIE['auth-token'])){
    setcookie('auth-token', '', time()-3600, '/');
    JSONResponse('Logout successfully', 200);
}
JSONResponse('No session', 404);