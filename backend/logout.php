<?php
    if(isset($_COOKIE['auth-token'])){
        setcookie('auth-token', '', time()-3600, '/');

        // Reindirizzamento
        header('Location: ../frontend/signin.html');
    }else{
        header('Location: ../frontend/');
    }
exit;