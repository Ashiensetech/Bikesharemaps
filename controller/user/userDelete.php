<?php
require("../config.php");
require('../actions-web.php');

$sql_details = array(
    'user' => $dbuser,
    'pass' => $dbpassword,
    'db' => $dbname,
    'host' => $dbserver
);

$table = 'users';
$primary_key = 'userId';

if (isset($_GET['user-id'])) {
    $id = $_GET['user-id'];
}


require('../external/orm/DaoFactory.php');

$message = DaoFactory::delete($sql_details, $table, $primary_key, $id);
httpResponseNoQuery($message['content'], $message['http_code']);

?>