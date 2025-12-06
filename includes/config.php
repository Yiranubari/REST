<?php

$db_user = 'root';
$db_password = '';
$db_name = 'My Database';
$db_host = 'localhost';

$db = $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);


//set some db attributes
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


define('APP_NAME', 'REST API');
