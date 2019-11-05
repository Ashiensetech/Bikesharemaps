<?php

require_once("routeMiddleWare.php");

if (isset($_GET['action']) && in_array($_GET['action'], $router)) {
    $action = $_GET['action'];
} else {
    $_GET['action'] = "";
}

if(isset($_GET['eventusers_id'])){
    $eventusers_id = $_GET['eventusers_id'];
}

if(isset($_GET['eventid'])){
    $eventid = $_GET['eventid'];
}

switch ($action) {
    case "users-list":
        include '../controller/user/userList.php';
        break;
    case "users-delete":
        include '../controller/user/userDelete.php';
        break;
    case "video-list":
        include '../controller/video/videoList.php';
        break;
    case "video-delete":
        include '../controller/video/videoDelete.php';
        break;
    case "inquiry-list":
        include '../controller/inquiry/inquiryList.php';
        break;
    case "help-list":
        include '../controller/inquiry/helpList.php';
        break;
    case "inquiry-delete":
        include '../controller/inquiry/inquiryDelete.php';
        break;
    case "inquiry-status":
        include '../controller/inquiry/inquiryStatus.php';
        break;
    case "stands-list":
        include '../controller/stand/standList.php';
        break;
    case "stands-delete":
        include '../controller/stand/standDelete.php';
        break;
    case "bikes-list":
        include '../controller/bike/bikeList.php';
        break;
    case "eventusers-list":
        include '../controller/eventuser/eventuserList.php';
        break;
    case "watercrafts-list":
        include '../controller/watercraft/watercraftList.php';
        break;
    case "events-list":
        include '../controller/event/eventList.php';
        break;
    case "bikes-delete":
        include '../controller/bike/bikeDelete.php';
        break;
    case "watercrafts-delete":
        include '../controller/watercraft/watercraftDelete.php';
        break;
    case "eventusers-delete":
        include '../controller/eventuser/eventuserDelete.php';
        break;
    case "events-delete":
        include '../controller/event/eventDelete.php';
        break;
    case "get-stats":
        include '../controller/report/commonStatData.php';
        break;
    case "daily-stats":
        include '../controller/report/dailyStats.php';
        break;
    case "coupon-list":
        include '../controller/coupon/couponList.php';
        break;
    case "maintenance-settings":
        include '../controller/maintenance/@maintenanceSetting.php';
        break;
    case "maintenance-list":
        include '../controller/maintenance/maintenanceList.php';
        break;
    case "watercraft-maintenance-list":
        include '../controller/maintenance/watercraftMaintenanceList.php';
        break;
    case "maintenance-reset":
        include '../controller/maintenance/maintenanceReset.php';
        break;
    case "place-list":
        include '../controller/place/placeList.php';
        break;
    case "place-delete":
        include '../controller/place/placeDelete.php';
        break;
    default:
        echo "Wrong Request 404";
}

?>