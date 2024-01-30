<?php
// TODO cancellare questo file
error_reporting(E_ALL);
ini_set("display_error", 1);

function customErrorHandler($errno, $errstr, $errfile, $errline): void
{
$message = "Error: [$errno] $errstr - $errfile:$errline";
error_log($message . PHP_EOL, 3, "error_log.txt");
}

set_error_handler("customErrorHandler");