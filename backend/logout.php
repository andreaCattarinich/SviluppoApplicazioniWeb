<?php
if(isset($_COOKIE['Token'])){
    // TODO: destroy JWT
    // Devo anche togliere il cookie dal client?
//    $unencodedData = (array) $_COOKIE['Token'];
//    echo $unencodedData['expireDate'];
//    exit;

    setcookie('Token', '', time()-3600, '/');

    header('Location: ../frontend/signin.html'); // Reindirizzamento
}else{
    header('Location: ../frontend/');
}
exit;