<?php
function db_connect(): mysqli{
    // TODO: togliere le credenziali da qui
    //<editor-fold desc="XAMPP">
    $servername = "localhost";
    $user = "root";
    $db_passw = "";
    $db_name = "saw";
    //</editor-fold>

    //<editor-fold desc="SAW">
//    $servername = "localhost";
//    $user = "S5137057";
//    $db_passw = "NonTiDicoLaPassword";
//    $db_name = "S5137057";
    //</editor-fold>
    try{
        return new mysqli($servername, $user, $db_passw, $db_name);
    }catch (mysqli_sql_exception $e){
        JSONResponse('Internal Server Error', 500);
    }catch (Exception $e){
        JSONResponse($e->getMessage(), $e->getCode());
    }
}