<?php
require 'DB_config.php';

function db_connect(): mysqli{
    try{
        $db = getDBConfig();
        $hostname = $db['hostname'];
        $username = $db['username'];
        $password = $db['password'];
        $database = $db['database'];

        return new mysqli($hostname, $username, $password, $database);
    }catch (mysqli_sql_exception $e){
        JSONResponse('Internal Server Error', 500);
    }catch (Exception $e){
        JSONResponse($e->getMessage(), $e->getCode());
    }
}