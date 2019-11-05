<?php
require("../config.php");
require('../actions-web.php');

$sql_details = array(
    'user' => $dbuser,
    'pass' => $dbpassword,
    'db' => $dbname,
    'host' => $dbserver
);

$table = 'event_users';
$primary_key = 'id';

if (isset($_GET['eventuser-id'])) {
    $id = $_GET['eventuser-id'];
}


require('../external/orm/DaoFactory.php');

$condition_rsvp = 'rsvp_date >= CURDATE()';
$rsvp_response = DaoFactory::rsvp_check($sql_details, 'events', 'id', $eventid, $condition_rsvp);
if($rsvp_response){
    $message = DaoFactory::delete($sql_details, $table, $primary_key, $id);
    httpResponseNoQuery($message['content'], $message['http_code']);
}else {

    $message = array(
        "content" => "Event rsvp date expired!",
        "http_code" => 500
    );
    httpResponseNoQuery($message['content'], $message['http_code']);
}

?>