<?php
require("../config.php");
require('../actions-web.php');

$sql_details = array(
    'user' => $dbuser,
    'pass' => $dbpassword,
    'db' => $dbname,
    'host' => $dbserver
);

$table = 'videos';
$primary_key = 'videoId';

if (isset($_GET['video-id'])) {
    $id = $_GET['video-id'];
}

require('../external/orm/DaoFactory.php');

$message = DaoFactory::delete($sql_details, $table, $primary_key, $id);
httpResponseNoQuery($message['content'], $message['http_code']);

?>