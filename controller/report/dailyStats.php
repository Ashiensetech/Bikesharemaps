<?php
require("../config.php");
require("../pdo.php");
require("../db.class.php");

$db = new Database($dbserver, $dbuser, $dbpassword, $dbname);
$db->connect();
$actionType = "";
if (isset($_GET["type"])) $actionType = trim($_GET["type"]);
switch ($actionType) {
    case "get-bike-rent-by-date":
        getBikeRentByDate();
        break;
    case "get-bike-rent-by-filter":
        getBikeRentByFilter();
        break;
    case "get-bike-rental-by-filter":
        getBikeRentalByFilter();
        break;
    case "get-total-rentals-per-bike":
        getTotalRentalsPerBike();
        break;
    case "get-user-total-rentals-per-bike":
        getTotalRentalsPerBike();
        break;
    case "get-user-all-time-rentals-per-bike":
        getTotalRentalsPerBike();
        break;
    case "get-total-rentals-per-stand":
        getTotalRentalsPerStand();
        break;
    case "get-total-returns-per-stand":
        getTotalReturnsPerStand();
        break;
    case "get-all-users":
        getAllUsers();
        break;
    case "get-all-bikes":
        getAllBikes();
        break;
    case "get-total-bike-rental-time":
        getTotalBikeRentalTime();
        break;
}
function getBikeRentByDate()
{
    global $db;
    $start_time = trim($_POST['start_time']) . ' 00:00:00';
    $end_time = trim($_POST['end_time']) . ' 23:59:59';
    $diff_format = label_decide($start_time, $end_time);
    $time_unit = $diff_format['time_format'];
    $query = "SELECT `activity_time` AS `activity_time`, COUNT(`id`) AS `count` FROM `activity_history` WHERE `action` IN ('rent','force_rent') AND `activity_time` BETWEEN '$start_time' AND '$end_time' ";
    if(isset($_POST['user_id']) && trim($_POST['user_id']) != ''){
        $user_id = trim($_POST['user_id']);
        $query .= "AND `user_id`='$user_id' ";
    }
    $query .= "GROUP BY (" . $diff_format['time_format_sql'] . "( activity_time )) ORDER BY `activity_time` ASC";
    $result = $db->query($query);
    $allDates = [];
    $allRentals = [];
    if ($result->num_rows == 0) {
        array_push($allRentals, 0);
    } else {
        while ($row = $result->fetch_assoc()) {
            $row['activity_time'] = formatedDate($time_unit, $row['activity_time']);
            array_push($allDates, $row['activity_time']);
            array_push($allRentals, $row['count']);
        }
    }
    $json = [
        "label" => ['Bike Rent'],
        "rent_by_date" => $allRentals,
        "time" => $allDates,
        "time_unit" => $time_unit
    ];
    echo json_encode($json);
}

function getBikeRentByFilter()
{
    global $db;
    $filter_age = trim($_POST['filter_age']);
    $filter_race = trim($_POST['filter_race']);
    $filter_gender = trim($_POST['filter_gender']);
    $time_format = 'date';
    $time_unit = 'day';
    $query = "SELECT activity_history.`activity_time` AS activity_time, COUNT(activity_history.`id`) AS `count` FROM `activity_history` LEFT JOIN `users` ON activity_history.`user_id`=users.`userId` WHERE activity_history.`action` IN ('rent','force_rent')";
    if (trim($_POST['start_time']) != '' && trim($_POST['end_time']) != '') {
        $start_time = trim($_POST['start_time']) . ' 00:00:00';
        $end_time = trim($_POST['end_time']) . ' 23:59:59';
        $diff_format = label_decide($start_time, $end_time);
        $time_format = $diff_format['time_format_sql'];
        $time_unit = $diff_format['time_format'];
        $query .= " and activity_history.`activity_time` BETWEEN '$start_time' AND '$end_time'";
    }
    if ($filter_age != '') {
        $query .= " and users.`age`='$filter_age'";
    }
    if ($filter_race != '') {
        $query .= " and users.`race`='$filter_race'";
    }
    if ($filter_gender != '') {
        $query .= " and users.`gender`='$filter_gender'";
    }
    $query .= " GROUP BY (" . $time_format . "( activity_history.`activity_time` )) ORDER BY activity_history.`activity_time` ASC";
    $result = $db->query($query);

    $allDates = [];
    $allRentals = [];
    if ($result->num_rows == 0) {
        array_push($allRentals, 0);
    } else {
        while ($row = $result->fetch_assoc()) {
            $row['activity_time'] = formatedDate($time_unit, $row['activity_time']);
            array_push($allDates, $row['activity_time']);
            array_push($allRentals, $row['count']);
        }
    }
    $json = [
        "label" => ['Bike Rent'],
        "rent_by_filter" => $allRentals,
        "time" => $allDates,
        "time_unit" => $time_unit
    ];
    echo json_encode($json);
}

function getTotalBikeRentalTime()
{
    global $db;
    $bikeRentalTime = [];
    $user_id = trim($_POST['user_id']);
    $bike_id = trim($_POST['bike_id']);
    $time_format = 'date';
    $time_unit = 'day';
    $query = "SELECT `activity_time` AS `activity_time`,`bike_id`, SUM(`rental_time`) AS `total_rental_time` FROM `activity_history` WHERE `action` IN('return','force_return')";
    if (trim($_POST['start_time']) != '' && trim($_POST['end_time']) != '') {
        $start_time = trim($_POST['start_time']) . ' 00:00:00';
        $end_time = trim($_POST['end_time']) . ' 23:59:59';
        $diff_format = label_decide($start_time, $end_time);
        $time_format = $diff_format['time_format_sql'];
        $time_unit = $diff_format['time_format'];
        $query .= " and `activity_time` BETWEEN '$start_time' AND '$end_time'";
    }
    if ($user_id != '') {
        $query .= " and `user_id`='$user_id'";
    }
    if ($bike_id != '') {
        $query .= " and `bike_id`='$bike_id'";
    }
    $query .= " GROUP BY (" . $time_format . "( activity_time )),bike_id ORDER BY `activity_time` ASC";
    $result = $db->query($query);

    $allDates = [];
    $allRentals = [];
    $allBikes = [];
    $totalData = [];
    if ($result->num_rows == 0) {
        array_push($bikeRentalTime, 0);
    } else {
        while ($row = $result->fetch_assoc()) {
            $row['activity_time'] = formatedDate($time_unit, $row['activity_time']);
            if (!in_array($row['activity_time'], $allDates)) {
                array_push($allDates, $row['activity_time']);
            }
            if (!in_array($row['bike_id'], $allBikes)) {
                array_push($allBikes, $row['bike_id']);
            }
            array_push($allRentals, $row);
        }
    }

    foreach ($allBikes as $key => $bikes) {
        $results = [];
        $dates = [];
        foreach ($allRentals as $item) {
            if (!in_array($item['activity_time'], $dates)) {
                array_push($dates, $item['activity_time']);
                if ($item['bike_id'] == $bikes) {
                    array_push($results, $item['total_rental_time']);
                } else {
                    array_push($results, 0);
                }
            } else if ($item['bike_id'] == $bikes) {
                $results[count($results) - 1] = $item['total_rental_time'];
            }
        }
        $data = [
            "label" => "Bike: " . $bikes,
            "backgroundColor" => rand_color(),
            "data" => $results,
            "borderWidth" => 1,
            "borderColor" => rand_color(),
            "borderDash" => [5, 2],
            "fill" => false
        ];
        array_push($totalData, $data);
    }
    $json = [
        "items" => $totalData,
        "time" => $allDates,
        "time_unit" => $time_unit
    ];
    echo json_encode($json);
}

function getBikeRentalByFilter()
{
    global $db;
    $bikeRentalTime = [];
    $filter_age = trim($_POST['filter_age']);
    $filter_race = trim($_POST['filter_race']);
    $filter_gender = trim($_POST['filter_gender']);
    $bike_id = trim($_POST['filter_bike_id']);
    $time_format = 'date';
    $time_unit = 'day';
    $query = "SELECT activity_history.`activity_time` AS `activity_time`,activity_history.`bike_id` AS `bike_id`, SUM(activity_history.`rental_time`) AS `total_rental_time` FROM `activity_history` LEFT JOIN `users` ON activity_history.`user_id`=users.`userId` WHERE activity_history.`action` IN('return','force_return')";
    if (trim($_POST['start_time']) != '' && trim($_POST['end_time']) != '') {
        $start_time = trim($_POST['start_time']) . ' 00:00:00';
        $end_time = trim($_POST['end_time']) . ' 23:59:59';
        $diff_format = label_decide($start_time, $end_time);
        $time_format = $diff_format['time_format_sql'];
        $time_unit = $diff_format['time_format'];
        $query .= " and activity_history.`activity_time` BETWEEN '$start_time' AND '$end_time'";
    }
    if ($filter_age != '') {
        $query .= " and users.`age`='$filter_age'";
    }
    if ($filter_race != '') {
        $query .= " and users.`race`='$filter_race'";
    }
    if ($filter_gender != '') {
        $query .= " and users.`gender`='$filter_gender'";
    }
    if ($bike_id != '') {
        $query .= " and activity_history.`bike_id`='$bike_id'";
    }
    $query .= " GROUP BY (" . $time_format . "( activity_history.`activity_time` )),activity_history.`bike_id` ORDER BY activity_history.`activity_time` ASC";
    $result = $db->query($query);

    $allDates = [];
    $allRentals = [];
    $allBikes = [];
    $totalData = [];
    if ($result->num_rows == 0) {
        array_push($bikeRentalTime, 0);
    } else {
        while ($row = $result->fetch_assoc()) {
            $row['activity_time'] = formatedDate($time_unit, $row['activity_time']);
            if (!in_array($row['activity_time'], $allDates)) {
                array_push($allDates, $row['activity_time']);
            }
            if (!in_array($row['bike_id'], $allBikes)) {
                array_push($allBikes, $row['bike_id']);
            }
            array_push($allRentals, $row);
        }
    }

    foreach ($allBikes as $key => $bikes) {
        $results = [];
        $dates = [];
        foreach ($allRentals as $item) {
            if (!in_array($item['activity_time'], $dates)) {
                array_push($dates, $item['activity_time']);
                if ($item['bike_id'] == $bikes) {
                    array_push($results, $item['total_rental_time']);
                } else {
                    array_push($results, 0);
                }
            } else if ($item['bike_id'] == $bikes) {
                $results[count($results) - 1] = $item['total_rental_time'];
            }
        }
        $data = [
            "label" => "Bike: " . $bikes,
            "backgroundColor" => rand_color(),
            "data" => $results,
            "borderWidth" => 1,
            "borderColor" => rand_color(),
            "borderDash" => [5, 2],
            "fill" => false
        ];
        array_push($totalData, $data);
    }
    $json = [
        "items" => $totalData,
        "time" => $allDates,
        "time_unit" => $time_unit
    ];
    echo json_encode($json);
}

function getTotalRentalsPerBike()
{
    $query = "SELECT id,`bike_id`,COUNT(`id`) AS `count` FROM `activity_history` where `action` IN('rent','force_rent')";
    if(isset($_REQUEST['start_time']) && isset($_REQUEST['end_time'])){
        $start_time = trim($_REQUEST['start_time']) . ' 00:00:00';
        $end_time = trim($_REQUEST['end_time']) . ' 23:59:59';
        if($start_time != '' && $end_time != ''){
            $query .= " and `activity_time` BETWEEN '$start_time' AND '$end_time'";
        }
    }
    if(isset($_REQUEST['user_id'])){
        $user_id = trim($_REQUEST['user_id']);
        if($user_id != ''){
            $query .= " and `user_id`='$user_id'";
        }
    }
    $query .= " GROUP BY `bike_id` ORDER BY `bike_id` ASC";
    $columns = array(
        array('db' => 'bike_id', 'dt' => 0),
        array('db' => 'count', 'dt' => 1)
    );
    $primaryKey = 'id';
    getDataWithDatatable($query,$columns,$primaryKey);
}

function getTotalRentalsPerStand()
{
    $query = "SELECT id,`stand_id`,COUNT(`id`) AS `count` FROM `activity_history` where `action` IN('rent','force_rent') GROUP BY `stand_id` ORDER BY `stand_id` ASC";
    $columns = array(
        array('db' => 'stand_id', 'dt' => 0),
        array('db' => 'count', 'dt' => 1)
    );
    $primaryKey = 'id';
    getDataWithDatatable($query,$columns,$primaryKey);
}

function getTotalReturnsPerStand()
{
    $query = "SELECT id,`stand_id`,COUNT(`id`) AS `count` FROM `activity_history` where `action` IN('return','force_return') GROUP BY `stand_id` ORDER BY `stand_id` ASC";
    $columns = array(
        array('db' => 'stand_id', 'dt' => 0),
        array('db' => 'count', 'dt' => 1)
    );
    $primaryKey = 'id';
    getDataWithDatatable($query,$columns,$primaryKey);
}

function getAllUsers()
{
    global $db;
    $result = $db->query("SELECT `userId` AS `id`,`username` As `fullname` FROM `users` ORDER BY `userId` ASC");
    $allUsers = [];
    while ($row = $result->fetch_assoc()) {
        array_push($allUsers, $row);
    }
    $json = [
        "all_users" => $allUsers
    ];
    echo json_encode($json);
}

function getAllBikes()
{
    global $db;
    $result = $db->query("SELECT `bikeNum` AS `id` FROM `bikes` ORDER BY `bikeNum` ASC");
    $allBikes = [];
    while ($row = $result->fetch_assoc()) {
        array_push($allBikes, $row);
    }
    $json = [
        "all_bikes" => $allBikes
    ];
    echo json_encode($json);
}

function getDateDifference($date1, $date2)
{
    $diff = abs(strtotime($date2) - strtotime($date1));
    $years = floor($diff / (365 * 60 * 60 * 24));
    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
    $totalDays = floor(($diff / (60 * 60 * 24)));
    $diff_array = [];
    $diff_array['years'] = $years;
    $diff_array['months'] = $months;
    $diff_array['days'] = $days;
    $diff_array['total_days'] = $totalDays;
    return $diff_array;
}

function label_decide($start_time, $end_time)
{
    $time_format = "";
    $time_format_sql = "";
    $delta_time = getDateDifference($end_time, $start_time);

    if (1 > $delta_time['total_days']) {
        $time_format = "hour";
        $time_format_sql = "hour";
    } else if (1 <= $delta_time['total_days'] && $delta_time['total_days'] <= 7) {
        $time_format = "day";
        $time_format_sql = "date";
    } else if (7 < $delta_time['total_days'] && $delta_time['total_days'] < 31) {
        $time_format = "week";
        $time_format_sql = "date";
    } else if (31 < $delta_time['total_days'] && $delta_time['total_days'] <= 365) {
        $time_format = "month";
        $time_format_sql = "month";
    } else if (365 < $delta_time['total_days']) {
        $time_format = "year";
        $time_format_sql = "year";
    }
    $format_array = [];
    $format_array['time_format'] = $time_format;
    $format_array['time_format_sql'] = $time_format_sql;
    return $format_array;
}

function formatedDate($time_unit, $date)
{
    if ($time_unit == 'day') {
        $date = date('D', strtotime($date));
    } else if ($time_unit == 'month') {
        $date = date('F, Y', strtotime($date));
    } else if ($time_unit == 'year') {
        $date = date('Y', strtotime($date));
    } else if ($time_unit == 'hour') {
        $date = date('h A', strtotime($date));
    } else {
        $date = date('Y-m-d', strtotime($date));
    }
    return $date;
}

function getDataWithDatatable($query,$columns,$primaryKey){
    global $dbuser,$dbpassword,$dbname,$dbserver;
    $sql_details = array(
        'user' => $dbuser,
        'pass' => $dbpassword,
        'db' => $dbname,
        'host' => $dbserver
    );
    $table = <<<EOT
     ($query
     ) temp
EOT;
    require('../external/dataTable/ssp.class.php');

    echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns)
    );
}

?>
