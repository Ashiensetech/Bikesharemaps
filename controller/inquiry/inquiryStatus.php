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

if (isset($_GET['status'])) {
    $status = $_GET['status'];
}


$repoStatus = array("Y", "N");


if (in_array($status, $repoStatus)) {

    $condition = "solved='" . $_GET['status'] . "'";
    require('../external/orm/DaoFactory.php');
    $currentTime = date("Y-m-d H:i:s");
    $condition = 'solved=' . "'" . $status . "', deleted='" . $currentTime . "'" ;
    $message = DaoFactory::status_change($sql_details, $table, $primary_key, $id, $condition);
    httpResponseNoQuery($message['content'], $message['http_code']);

} else {
    httpResponseNoQuery("Status out of context!", 500);
}


?>