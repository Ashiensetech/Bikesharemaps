<?php
require("../config.php");
require('../actions-web.php');

$sql_details = array(
    'user' => $dbuser,
    'pass' => $dbpassword,
    'db' => $dbname,
    'host' => $dbserver
);

$table = 'bikes';
$primary_key = 'bikeNum';

if (isset($_GET['watercraft-id'])) {
    $id = $_GET['watercraft-id'];
}


require('../external/orm/DaoFactory.php');

$message = DaoFactory::delete($sql_details, $table, $primary_key, $id);
httpResponseNoQuery($message['content'], $message['http_code']);

?>