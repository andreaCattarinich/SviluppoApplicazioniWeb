<?php
function db_connect(): mysqli{ // TODO: togliere le credenziali da qui
    //<editor-fold desc="XAMPP">
    $servername = "localhost";
    $user = "root";
    $db_passw = "";
    $db_name = "saw";
    //</editor-fold>

    //<editor-fold desc="ALTERVISTA">
    //$servername = "localhost";
    //$user = "cattarinich1";
    //$db_passw = "";
    //$db_name = "my_cattarinich1";
    //</editor-fold>
    return new mysqli($servername, $user, $db_passw, $db_name);
}
/*
function numberOfPosts($db){
  $query = "SELECT * FROM posts";
  $result = $db->query($query);
  return $result->num_rows;
}
*/