<?php
require("../config.php");
require('../actions-web.php');

$sql_details = array(
    'user' => $dbuser,
    'pass' => $dbpassword,
    'db' => $dbname,
    'host' => $dbserver
);

$table = 'maintenance';
$where='';
$condition = '';

if (!isset($_GET['maintenanceId']) || !is_numeric($_GET['maintenanceId'])) {
    httpResponse('Something went wrong', 500, "", 1);
} else {
    $id = $_GET['maintenanceId'];
    $userId = '174';
    try{
        if( isset($id) && is_numeric($id) && isset($userId) && is_numeric($userId)){
            $where = 'id='.$id;
            $condition='total_rental=0,status="Green", updated_by='.$userId;
            require('../external/orm/DaoFactory.php');

            $message = DaoFactory::status_update($sql_details, $table, $where,$condition);
            httpResponseNoQuery($message['content'], $message['http_code']);
//            httpResponse('Number of rentals reset successfully', 201, "", 1);
        }
        else {
            httpResponse('Something went wrong', 500, "", 1);
        }
    }catch (Exception $exception){
        httpResponse('Something went wrong!', 500, "", 1);
    }
}

?>
