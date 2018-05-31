<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
define('DB_USERNAME', 'root');
define('DB_PASS', 'dfab7c358bb163');
define('DB_NAME','dsh_db');
define('DB_HOST','localhost');
define('DB_DSN','mysql:host=' . DB_HOST . ';dbname=' . DB_NAME);


?>
