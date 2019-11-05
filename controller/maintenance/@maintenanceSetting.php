<?php
require("../config.php");
require("../common.php");

//require("../config.php");
//
//$sql_details = array(
//    'user' => $dbuser,
//    'pass' => $dbpassword,
//    'db' => $dbname,
//    'host' => $dbserver
//);
//
//

global $db;


$response = null;
$thumbnail = null;
if (!isset($_POST['totalRent'])) {
    httpResponse('Number of rentals can not be empty', 500, "", 1);
} else {
    $totalRent = $_POST['totalRent'];
    try{
        $result = $db->query("INSERT INTO setting SET setting_key='total_rent',setting_value=$totalRent");
        httpResponse('Number of rentals successfully added', 200, "", 1);

    }catch (Exception $exception){
        httpResponse('Something went wrong!', 500, "", 1);
    }
}


function httpResponse($message, $http_code = null, $error = 0, $additional = "", $log = 1)
{
    global $db;
    $json = array("error" => $error, "content" => $message, 'http_code' => $http_code);
    if (is_array($additional)) {
        foreach ($additional as $key => $value) {
            $json[$key] = $value;
        }
    }
    $json = json_encode($json);
    if ($log == 1 AND $message) {
        if (isset($_COOKIE["loguserid"])) {
            $userid = $db->conn->real_escape_string(trim($_COOKIE["loguserid"]));
        } else $userid = 0;
        $number = getphonenumber($userid);
        logresult($number, $message);
    }
    $db->conn->commit();
    echo $json;
    exit;
}