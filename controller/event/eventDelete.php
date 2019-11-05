<?php
require("../config.php");
require('../actions-web.php');

$sql_details = array(
    'user' => $dbuser,
    'pass' => $dbpassword,
    'db' => $dbname,
    'host' => $dbserver
);

$table = 'events';
$primary_key = 'id';
$condition = 'is_deleted=1, is_active=0';
if (isset($_GET['event-id'])) {
    $id = $_GET['event-id'];
}


require('../external/orm/DaoFactory.php');

$message = DaoFactory::delete_is_deleted_column_update($sql_details, $table, $primary_key, $id,$condition);
httpResponseNoQuery($message['content'], $message['http_code']);

?>