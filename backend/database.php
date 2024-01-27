<?php
function db_connect(): mysqli{ // TODO: togliere le credenziali da qui
    //<editor-fold desc="XAMPP">
    $servername = "localhost";
    $user = "root";
    $db_passw = "";
    $db_name = "catta";
    //</editor-fold>

    //<editor-fold desc="ALTERVISTA">
//    $servername = "localhost";
//    $user = "cattarinich1";
//    $db_passw = "";
//    $db_name = "my_cattarinich1";
    //</editor-fold>

    //<editor-fold desc="SAW">
//    $servername = "localhosta";
//    $user = "S5137057";
//    $db_passw = "NonTiDicoLaPassword";
//    $db_name = "S5137057";
    //</editor-fold>

    return new mysqli($servername, $user, $db_passw, $db_name);
}