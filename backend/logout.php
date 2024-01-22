<?php
session_start();

if(isset($_SESSION['Token'])){
    $_SESSION = [];
    session_destroy(); // Chiusura sessione

    header('Location: ../frontend/signin.html'); // Reindirizzamento
}else{
    // echo "Sessione non settata";
    header('Location: ../frontend/');
}
exit;