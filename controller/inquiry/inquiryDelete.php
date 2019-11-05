<?php
require("../config.php");
require('../actions-web.php');

$sql_details = array(
    'user' => $dbuser,
    'pass' => $dbpassword,
    'db' => $dbname,
    'host' => $dbserver
);

$table = 'notes';
$primary_key = 'id';

if (isset($_GET['inquiry-id'])) {
    $id = $_GET['inquiry-id'];
}


require('../external/orm/DaoFactory.php');

$message = DaoFactory::delete($sql_details, $table, $primary_key, $id);
httpResponseNoQuery($message['content'], $message['http_code']);

?>