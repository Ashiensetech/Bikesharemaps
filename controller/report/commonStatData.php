<?php
require("../config.php");
require("../pdo.php");
require("../db.class.php");

$db = new Database($dbserver, $dbuser, $dbpassword, $dbname);
$db->connect();
$actionType = "";
$sql_details = array(
    'user' => $dbuser,
    'pass' => $dbpassword,
    'db' => $dbname,
    'host' => $dbserver
);
if (isset($_GET["type"])) $actionType = trim($_GET["type"]);
switch ($actionType) {
    case "get-users":
        getUsersByName();
        break;
    case "get-bikes":
        getBikesByNumber();
        break;
    case "get-new-inquiry":
        getNewInquirys();
        break;
}

function getNewInquirys(){
    global $db,$sql_details;
    $result = $db->query("SELECT count(*) as count FROM notes where notified='N'");
    $row_count = 0;
    while ($row = $result->fetch_assoc()) {
        $row_count = $row['count'];
    }
    $message = "";
    $http_code = "";
    if($row_count > 0) {
        try {
            $table = 'notes';
            require('../external/orm/DaoFactory.php');
            $condition = 'notified="Y"';
            $where = 'notified="N"';
            $messages = DaoFactory::status_update($sql_details, $table, $where, $condition);
        }catch (Exception $e) {

        }
        $message = 'You got a new report';
        $http_code = 403;
    }else{
        $message = 'No Inquiry found';
        $http_code = 200;
    }
    $json = array("content" => $message, 'http_code' => $http_code);
    echo json_encode($json);
}

function getUsersByName()
{
    global $db;
    $search = trim($_GET['search']);
    $json = [];
    if($search != ''){
        $query = "SELECT `userId`,`username` FROM `users` WHERE `userName` LIKE '$search%'";
        $result = $db->query($query);
        while ($row = $result->fetch_assoc()) {
            $array_data = [
                "id" => $row['userId'],
                "text" => $row['username']
            ];
            array_push($json, $array_data);
        }
    }
    echo json_encode($json);
}

function getBikesByNumber()
{
    global $db;
    $search = trim($_GET['search']);
    $json = [];
    if($search != ''){
        $query = "SELECT `bikeNum` FROM `bikes` WHERE `bikeNum` LIKE '$search%'";
        $result = $db->query($query);
        while ($row = $result->fetch_assoc()) {
            $array_data = [
                "id" => $row['bikeNum'],
                "text" => "Bike: ".$row['bikeNum']
            ];
            array_push($json, $array_data);
        }
    }
    echo json_encode($json);
}

?>
