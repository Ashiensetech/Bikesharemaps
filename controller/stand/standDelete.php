<?php

require("../config.php");
require('../actions-web.php');

$sql_details = array(
    'user' => $dbuser,
    'pass' => $dbpassword,
    'db' => $dbname,
    'host' => $dbserver
);

$table = 'stands';
$primary_key = 'standId';

if (isset($_GET['stand-id'])) {
    $id = $_GET['stand-id'];
}

require('../external/orm/DaoFactory.php');

$message = DaoFactory::delete($sql_details, $table, $primary_key, $id);
httpResponseNoQuery($message['content'], $message['http_code']);

?>