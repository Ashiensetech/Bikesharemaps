<?php
require("common.php");
//require("db.class.php");
require_once('vendor/autoload.php');
require_once('third_party_config/stripe/config_stripe.php');

function  response($message, $error = 0, $additional = "", $log = 1, $http_code = null)
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

function httpResponseNoQuery($message, $http_code = null, $error = 0, $additional = "", $log = 1)
{
    $json = array("error" => $error, "content" => $message, 'http_code' => $http_code);
    $json = json_encode($json);
    echo $json;
    exit;
}

function getsystemurl()
{
    global $systemURL;
    response("System URL key retrieved", 0, array("url" => $systemURL), 0);
}

function braintree()
{
    global $braintree;
    response("Authorization key retrieved", 0, array("authorization" => $braintree["authorization"]), 0);
}

function message($message)
{
    if (empty($message))
        response("Broadcast message could not be sent.", 1, "", 1);
    broadcast($message, 0);
    response("Broadcast message successfully sent.");
}
function eventmessage($message,$eventid)
{
    if (empty($message))
        response("Broadcast message could not be sent.", 1, "", 1);
    if(isset($eventid)){
        eventbroadcast($message,$eventid, 0);
    }
    response("Broadcast message successfully sent.");
}

function rent($userId, $bike, $force = FALSE)
{

    global $db, $forcestack, $watches, $credit;
    $stacktopbike = FALSE;
    $bikeNum = $bike; //Bike = bike_num
    $requiredcredit = $credit["min"] + $credit["rent"] + $credit["longrental"];

    //Check subscription status and rented bike total
    $result = $db->query("SELECT privileges FROM users WHERE userId=$userId");
    $isAdmin = 0;
    $rent_allowed = 0;
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $isAdmin = ($row["privileges"] == 7) ? 1:0;
    }else {
        response(_('User privilege not found'), 1);
        return;
    }

    if($isAdmin){
        $rent_allowed = 1;
    }else {
        $rent_allowed = 0;
        $subscriberBikeLimit = 0;
        $subscriberFamilyBikeLimit = 0;
        if($userId != 0){
            $result = $db->query("SELECT * FROM payment_subscription WHERE user_id=" . $userId . " AND is_active=1 AND expiration_date >=CURDATE()");
            $subscriptionList = [];
            while ($row = $result->fetch_assoc()) {
                $data = array("user_id" => $row["user_id"], "subscription_type" => $row["subscription_type"], "is_active" => $row["is_active"], "expiration_date" => $row["expiration_date"]);
                array_push($subscriptionList,$data);
            }
        }else{
            response(_('No data found'), 1);
            return;
        }
        if(count($subscriptionList) > 0 ){
            foreach ($subscriptionList as $subscriptionList){
                    if( ($subscriptionList['subscription_type']== 'annually') || ($subscriptionList['subscription_type']== 'monthly') ){
                        $subscriberBikeLimit = 1;
                    }else if( $subscriptionList['subscription_type']== 'family_weekend'){
                        $subscriberFamilyBikeLimit = 4;
                    }
            }

            //Rented bike Check
            $rentedBikeTotal=0;
            $result = $db->query("SELECT count(*) as rentedBikeTotal FROM bikes where currentUser=$userId");
            $row = $result->fetch_assoc();
            $rentedBikeTotal = $row["rentedBikeTotal"];

            if ($subscriberFamilyBikeLimit > 0 && ($subscriberFamilyBikeLimit > 0 && $rentedBikeTotal < $subscriberFamilyBikeLimit)){
                $rent_allowed = 1;
            }else if($subscriberBikeLimit > 0 && ($subscriberBikeLimit > 0 && $rentedBikeTotal < $subscriberBikeLimit)){
                $rent_allowed = 1;
            }

        }else {
            response(_('No subscription found'), 1);
            return;
        }
    }

    if($rent_allowed){
        //Status check. If Red -> no rent allowed.
        $isStatusRed=0;
        if($bikeNum > 0){
            $resultBikePrimaryKey = $db->query("SELECT * FROM bikes WHERE bike_num=" . $bikeNum ." LIMIT 1");
            $rowBikePrimaryKey = $resultBikePrimaryKey->fetch_assoc();
            $bikePrimaryKey = $rowBikePrimaryKey['bikeNum'];
            $bikePrimaryKeyType = $rowBikePrimaryKey['type'];
            ($bikePrimaryKeyType=="bike") ? $bikePrimaryKeyTypeCap = 'Bike' : $bikePrimaryKeyTypeCap = 'Watercraft';
            $result = $db->query("SELECT status FROM maintenance WHERE bike_id=" . $bikePrimaryKey ." LIMIT 1");
            $row = $result->fetch_assoc();
            ($row != NULL) ? $bikeStatus = $row["status"] : $bikeStatus = 'Not found';

            ($bikeStatus == 'Red') ? $isStatusRed=1 : $isStatusRed=0;
        }

        if($isStatusRed){
            response(_('Reported '.$bikePrimaryKeyType.' is not rentable.'), 1);
            return;
        }
        // Rent codes start
        if ($force == FALSE) {
//            $creditcheck = checkrequiredcredit($userId);
//            if ($creditcheck === FALSE) {
//                response(_('You are below required credit') . " " . $requiredcredit . $credit["currency"] . ". " . _('Please, recharge your credit.'), ERROR);
//            }
//            checktoomany(0, $userId);

//            $result = $db->query("SELECT count(*) as countRented FROM bikes where currentUser=$userId");
//            $row = $result->fetch_assoc();
//            $countRented = $row["countRented"];
//
//            $result = $db->query("SELECT userLimit FROM limits where userId=$userId");
//            $row = $result->fetch_assoc();
//            $limit = $row["userLimit"];
//
//            if ($countRented >= $limit) {
//                if ($limit == 0) {
//                    response(_('You can not rent any bikes. Contact the admins to lift the ban.'), ERROR);
//                } elseif ($limit == 1) {
//                    response(_('You can only rent') . " " . sprintf(ngettext('%d bike', '%d bikes', $limit), $limit) . " " . _('at once') . ".", ERROR);
//                } else {
//                    response(_('You can only rent') . " " . sprintf(ngettext('%d bike', '%d bikes', $limit), $limit) . " " . _('at once') . " " . _('and you have already rented') . " " . $limit . ".", ERROR);
//                }
//            }

            if ($forcestack OR $watches["stack"]) {
                $result = $db->query("SELECT currentStand FROM bikes WHERE bikeNum='$bikePrimaryKey'");
                $row = $result->fetch_assoc();
                $standid = $row["currentStand"];
                $stacktopbike = checktopofstack($standid);
                if ($watches["stack"] AND $stacktopbike <> $bike) {
                    $result = $db->query("SELECT standName FROM stands WHERE standId='$standid'");
                    $row = $result->fetch_assoc();
                    $stand = $row["standName"];
                    $user = getusername($userId);
                    notifyAdmins(_($bikePrimaryKeyTypeCap) . " " . $bike . " " . _('rented out of stack by') . " " . $user . ". " . $stacktopbike . " " . _('was on the top of the stack at') . " " . $stand . ".", 1);
                }
                if ($forcestack AND $stacktopbike <> $bike) {
                    response(_($bikePrimaryKeyTypeCap) . " " . $bike . " " . _('is not rentable now, you have to rent '.$bikePrimaryKeyType) . " " . $stacktopbike . " " . _('from this stand') . ".", ERROR);
                }
            }
        }

        $result = $db->query("SELECT currentUser,currentCode,currentStand FROM bikes WHERE bikeNum=$bikePrimaryKey");
        $row = $result->fetch_assoc();
        $currentCode = sprintf("%04d", $row["currentCode"]);
        $currentUser = $row["currentUser"];
        $bikeStand = $row["currentStand"];
        $result = $db->query("SELECT note FROM notes WHERE bikeNum='$bikePrimaryKey' AND deleted IS NULL ORDER BY time DESC");
        $note = "";
        while ($row = $result->fetch_assoc()) {
            $note .= $row["note"] . "; ";
        }
        $note = substr($note, 0, strlen($note) - 2); // remove last two chars - comma and space

        $newCode = generateRandomCode(4); //do not create a code with more than one leading zero or more than two leading 9s (kind of unusual/unsafe).

        if ($force == FALSE) {
            if ($currentUser == $userId) {
                response(_('You already rented '.$bikePrimaryKeyType) . " " . $bikeNum . ". " . _('Code is') . " " . $currentCode . ".", ERROR);
                return;
            }
            if ($currentUser != 0) {
                response(_($bikePrimaryKeyTypeCap) . " " . $bikeNum . " " . _('is already rented') . ".", ERROR);
                return;
            }
        }

        $message = '<h3 style="line-height: 2">' . _($bikePrimaryKeyTypeCap) . ' ' . $bikeNum . ': <span class="label label-primary">' . _('Open with code') . ' ' . $currentCode . '.</span></h3>' . _('you are responsible for changing the code to') . ' <span class="label label-default">' . $newCode . '</span><br />' . _('(with lock open, rotate silver end-cap, set new code, rotate silver end-cap back)') . '.';
        if ($note) {
            $message .= "<br />" . _('Reported issue') . ": <em>" . $note . "</em>";
        }

        $result = $db->query("UPDATE bikes SET currentUser=$userId,currentCode=$newCode,currentStand=NULL WHERE bikeNum=$bikePrimaryKey");
        if ($force == FALSE) {
            $result = $db->query("INSERT INTO history SET userId=$userId,bikeNum=$bikePrimaryKey,action='RENT',parameter=$newCode");
            $result = $db->query("INSERT INTO activity_history SET user_id=$userId,bike_id=$bikePrimaryKey,action='rent',stand_id=$bikeStand");
        } else {
            $result = $db->query("INSERT INTO history SET userId=$userId,bikeNum=$bikePrimaryKey,action='FORCERENT',parameter=$newCode");
            $result = $db->query("INSERT INTO activity_history SET user_id=$userId,bike_id=$bikePrimaryKey,action='force_rent',stand_id=$bikeStand");
        }
        //check bike id : insert or update
        $bikeMaintenance = $db->query("SELECT id, bike_id, total_rental  FROM maintenance WHERE bike_id=$bikePrimaryKey ORDER BY id DESC LIMIT 1");
        $bikeMaintenanceSetting = $db->query("SELECT *  FROM setting WHERE setting_key='total_rent' ORDER BY id DESC LIMIT 1");

        /*
        #Update if bide id missing : update total rental, updated_at, status
        #Insert if bike id exists  : created_at
        */

        if($bikeMaintenance->num_rows>0){
            $bikeMaintenance= $bikeMaintenance->fetch_assoc();
            $total_rental = $bikeMaintenance['total_rental']+1;
            $rental_id = $bikeMaintenance['id'];

            $bikeMaintenanceSettingVal = '4';
            $bikeMaintenanceSetting= $bikeMaintenanceSetting->fetch_assoc();
            if(isset($bikeMaintenanceSetting['setting_value'])){
                $bikeMaintenanceSettingVal = $bikeMaintenanceSetting['setting_value'];
            }
            $maintenance_status = get_status($total_rental, $bikeMaintenanceSettingVal);
            $updated_at = DATE('y-m-d h:i:s');
            $db->query("UPDATE maintenance SET total_rental=$total_rental,updated_at='$updated_at',status='$maintenance_status' WHERE id=$rental_id");

        }else {
            $created_at = DATE('y-m-d h:i:s');
            $db->query("INSERT INTO maintenance SET bike_id=$bikePrimaryKey, total_rental=1,created_at='$created_at',status='Green'");
        }
        response($message);
        //Rent codes end
    }else {
        response(_('You are not allowed to rent a bike or watercraft.'), 1);
        return;
    }
}


function rentbytype($userId, $bike, $biketype, $force = FALSE)
{
    global $db, $forcestack, $watches, $credit;
    $stacktopbike = FALSE;
    $bikeNum = $bike; //Bike = bike_num
    $requiredcredit = $credit["min"] + $credit["rent"] + $credit["longrental"];

    //Check subscription status and rented bike total
    $result = $db->query("SELECT privileges FROM users WHERE userId=$userId");
    $isAdmin = 0;
    $rent_allowed = 0;
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $isAdmin = ($row["privileges"] == 7) ? 1:0;
    }else {
        response(_('User privilege not found'), 1);
        return;
    }

    if($isAdmin){
        $rent_allowed = 1;
    }else {
        $rent_allowed = 0;
        $subscriberBikeLimit = 0;
        $subscriberFamilyBikeLimit = 0;
        if($userId != 0){
            $result = $db->query("SELECT * FROM payment_subscription WHERE user_id=" . $userId . " AND is_active=1 AND expiration_date >=CURDATE()");
            $subscriptionList = [];
            while ($row = $result->fetch_assoc()) {
                $data = array("user_id" => $row["user_id"], "subscription_type" => $row["subscription_type"], "is_active" => $row["is_active"], "expiration_date" => $row["expiration_date"]);
                array_push($subscriptionList,$data);
            }
        }else{
            response(_('No data found'), 1);
            return;
        }
        if(count($subscriptionList) > 0 ){
            foreach ($subscriptionList as $subscriptionList){
                if( ($subscriptionList['subscription_type']== 'annually') || ($subscriptionList['subscription_type']== 'monthly') ){
                    $subscriberBikeLimit = 1;
                }else if( $subscriptionList['subscription_type']== 'family_weekend'){
                    $subscriberFamilyBikeLimit = 4;
                }
            }

            //Rented bike Check
            $rentedBikeTotal=0;
            $result = $db->query("SELECT count(*) as rentedBikeTotal FROM bikes where currentUser=$userId");
            $row = $result->fetch_assoc();
            $rentedBikeTotal = $row["rentedBikeTotal"];

            if ($subscriberFamilyBikeLimit > 0 && ($subscriberFamilyBikeLimit > 0 && $rentedBikeTotal < $subscriberFamilyBikeLimit)){
                $rent_allowed = 1;
            }else if($subscriberBikeLimit > 0 && ($subscriberBikeLimit > 0 && $rentedBikeTotal < $subscriberBikeLimit)){
                $rent_allowed = 1;
            }

        }else {
            response(_('No subscription found'), 1);
            return;
        }
    }

    if($rent_allowed){
        //Status check. If Red -> no rent allowed.
        $isStatusRed=0;
        if($bikeNum > 0){
            $resultBikePrimaryKey = $db->query("SELECT * FROM bikes WHERE type='$biketype' AND bike_num=" . $bikeNum ." LIMIT 1");
            $rowBikePrimaryKey = $resultBikePrimaryKey->fetch_assoc();
            $bikePrimaryKey = $rowBikePrimaryKey['bikeNum'];
            $bikePrimaryKeyType = $rowBikePrimaryKey['type'];
            ($bikePrimaryKeyType=="bike") ? $bikePrimaryKeyTypeCap = 'Bike' : $bikePrimaryKeyTypeCap = 'Watercraft';
            $result = $db->query("SELECT status FROM maintenance WHERE bike_id=" . $bikePrimaryKey ." LIMIT 1");
            $row = $result->fetch_assoc();
            ($row != NULL) ? $bikeStatus = $row["status"] : $bikeStatus = 'Not found';

            ($bikeStatus == 'Red') ? $isStatusRed=1 : $isStatusRed=0;
        }

        if($isStatusRed){
            response(_('Reported '.$bikePrimaryKeyType.' is not rentable.'), 1);
            return;
        }
        // Rent codes start
        if ($force == FALSE) {

            if ($forcestack OR $watches["stack"]) {
                $result = $db->query("SELECT currentStand FROM bikes WHERE bikeNum='$bikePrimaryKey'");
                $row = $result->fetch_assoc();
                $standid = $row["currentStand"];
                $stacktopbike = checktopofstack($standid);
                if ($watches["stack"] AND $stacktopbike <> $bike) {
                    $result = $db->query("SELECT standName FROM stands WHERE standId='$standid'");
                    $row = $result->fetch_assoc();
                    $stand = $row["standName"];
                    $user = getusername($userId);
                    notifyAdmins(_($bikePrimaryKeyTypeCap) . " " . $bike . " " . _('rented out of stack by') . " " . $user . ". " . $stacktopbike . " " . _('was on the top of the stack at') . " " . $stand . ".", 1);
                }
                if ($forcestack AND $stacktopbike <> $bike) {
                    response(_($bikePrimaryKeyTypeCap) . " " . $bike . " " . _('is not rentable now, you have to rent '.$bikePrimaryKeyType) . " " . $stacktopbike . " " . _('from this stand') . ".", ERROR);
                }
            }
        }

        $result = $db->query("SELECT currentUser,currentCode,currentStand FROM bikes WHERE bikeNum=$bikePrimaryKey");
        $row = $result->fetch_assoc();
        $currentCode = sprintf("%04d", $row["currentCode"]);
        $currentUser = $row["currentUser"];
        $bikeStand = $row["currentStand"];
        $result = $db->query("SELECT note FROM notes WHERE bikeNum='$bikePrimaryKey' AND deleted IS NULL ORDER BY time DESC");
        $note = "";
        while ($row = $result->fetch_assoc()) {
            $note .= $row["note"] . "; ";
        }
        $note = substr($note, 0, strlen($note) - 2); // remove last two chars - comma and space

        $newCode = generateRandomCode(4); //do not create a code with more than one leading zero or more than two leading 9s (kind of unusual/unsafe).

        if ($force == FALSE) {
            if ($currentUser == $userId) {
                response(_('You already rented '.$bikePrimaryKeyType) . " " . $bikeNum . ". " . _('Code is') . " " . $currentCode . ".", ERROR);
                return;
            }
            if ($currentUser != 0) {
                response(_($bikePrimaryKeyTypeCap) . " " . $bikeNum . " " . _('is already rented') . ".", ERROR);
                return;
            }
        }

        $message = '<h3 style="line-height: 2">' . _($bikePrimaryKeyTypeCap) . ' ' . $bikeNum . ': <span class="label label-primary">' . _('Open with code') . ' ' . $currentCode . '.</span></h3>' . _('you are responsible for changing the code to') . ' <span class="label label-default">' . $newCode . '</span><br />' . _('(with lock open, rotate silver end-cap, set new code, rotate silver end-cap back)') . '.';
        if ($note) {
            $message .= "<br />" . _('Reported issue') . ": <em>" . $note . "</em>";
        }

        $result = $db->query("UPDATE bikes SET currentUser=$userId,currentCode=$newCode,currentStand=NULL WHERE bikeNum=$bikePrimaryKey");
        if ($force == FALSE) {
            $result = $db->query("INSERT INTO history SET userId=$userId,bikeNum=$bikePrimaryKey,action='RENT',parameter=$newCode");
            $result = $db->query("INSERT INTO activity_history SET user_id=$userId,bike_id=$bikePrimaryKey,action='rent',stand_id=$bikeStand");
        } else {
            $result = $db->query("INSERT INTO history SET userId=$userId,bikeNum=$bikePrimaryKey,action='FORCERENT',parameter=$newCode");
            $result = $db->query("INSERT INTO activity_history SET user_id=$userId,bike_id=$bikePrimaryKey,action='force_rent',stand_id=$bikeStand");
        }
        //check bike id : insert or update
        $bikeMaintenance = $db->query("SELECT id, bike_id, total_rental  FROM maintenance WHERE bike_id=$bikePrimaryKey ORDER BY id DESC LIMIT 1");
        $bikeMaintenanceSetting = $db->query("SELECT *  FROM setting WHERE setting_key='total_rent' ORDER BY id DESC LIMIT 1");

        /*
        #Update if bide id missing : update total rental, updated_at, status
        #Insert if bike id exists  : created_at
        */

        if($bikeMaintenance->num_rows>0){
            $bikeMaintenance= $bikeMaintenance->fetch_assoc();
            $total_rental = $bikeMaintenance['total_rental']+1;
            $rental_id = $bikeMaintenance['id'];

            $bikeMaintenanceSettingVal = '4';
            $bikeMaintenanceSetting= $bikeMaintenanceSetting->fetch_assoc();
            if(isset($bikeMaintenanceSetting['setting_value'])){
                $bikeMaintenanceSettingVal = $bikeMaintenanceSetting['setting_value'];
            }
            $maintenance_status = get_status($total_rental, $bikeMaintenanceSettingVal);
            $updated_at = DATE('y-m-d h:i:s');
            $db->query("UPDATE maintenance SET total_rental=$total_rental,updated_at='$updated_at',status='$maintenance_status' WHERE id=$rental_id");

        }else {
            $created_at = DATE('y-m-d h:i:s');
            $db->query("INSERT INTO maintenance SET bike_id=$bikePrimaryKey, total_rental=1,created_at='$created_at',status='Green'");
        }
        response($message);
        //Rent codes end
    }else {
        response(_('You are not allowed to rent a bike or watercraft.'), 1);
        return;
    }
}


function rentevent($userId, $eventId)
{
    global $db;
    try{
        // $eventId = event_num
        $eventQuery = $db->query("SELECT * FROM events WHERE event_num=$eventId AND is_deleted=0 AND rsvp_date >= CURDATE() ORDER BY id DESC LIMIT 1");
        $eventRow = $eventQuery->fetch_assoc();
        $eventRsvp = $eventRow['rsvp_date'];
        $eventTotalRides = $eventRow['total_rides'];
        $eventPrimaryKey = $eventRow['id'];

        $userQuery = $db->query("SELECT * FROM event_users WHERE user_id=$userId AND event_id=$eventPrimaryKey ORDER BY id DESC LIMIT 1");
        $userRow = $userQuery->num_rows;

        $userCountQuery = $db->query("SELECT COUNT(id) AS total_users FROM event_users WHERE event_id=$eventPrimaryKey");
        $userCountRow = $userCountQuery->fetch_assoc();
        $userCount = $userCountRow['total_users'];

        if ($userRow == 0 && isset($eventRsvp) && ($userCount <= $eventTotalRides)) {
            $db->query("INSERT INTO event_users SET event_id=$eventPrimaryKey, user_id=$userId");
            $message = 'Successfully registered to RSVP ' . $eventId;
        } else {

            if(!isset($eventRsvp)){
                response(_('RSVP date expired'), 1);

            }else if($userRow == 1) {
                response(_('Already registered to RSVP ' .$eventId), 1);
            }else if($userCount >= $eventTotalRides){
                response(_('All rides taken'), 1);
            }

            return;

        }



        response($message);
    }catch(Exception $e) {
        echo 'Message: ' .$e->getMessage();
    }

}

function get_status($total_rental, $bikeMaintenanceSettingVal){
    $val = $bikeMaintenanceSettingVal;
    $green = $val;
    $yellow = $val*2;
    $orange = $val*3;
    $red = $val*4;
    if($total_rental < $green){
        $status = 'Green';
    }
    else if($total_rental >=$green && $total_rental < $yellow){
        $status = 'Yellow';
    }
    else if($total_rental >= $yellow && $total_rental < $orange){
        $status = 'Orange';
    }
    else if($total_rental >= $orange && $total_rental < $red){
        $status = 'Red';
    }
    else {
        $status = 'Red';
    }
    return $status;
}


function returnBike($userId, $bike, $stand, $note = "", $force = FALSE)
{
    global $db;
    $bikeNum = intval($bike); //bike_num

    //Get bikeNum
    $resultPrimaryKey = $db->query("SELECT * FROM bikes WHERE bike_num=$bikeNum LIMIT 1");
    $rowPrimaryKey = $resultPrimaryKey->fetch_assoc();
    $bikePrimaryKey = $rowPrimaryKey['bikeNum'];
    $bikePrimaryKeyType = $rowPrimaryKey['type'];
    ($bikePrimaryKeyType=='bike') ? $bikePrimaryKeyTypeCap = 'Bike' : $bikePrimaryKeyTypeCap = 'Watercraft';

    $stand = strtoupper($stand);

    if ($force == FALSE) {
        $result = $db->query("SELECT bikeNum FROM bikes WHERE currentUser=$userId ORDER BY bikeNum");
        $bikenumber = $result->num_rows;

        if ($bikenumber == 0) {
            response(_('You currently have no rented '.$bikePrimaryKeyType.'s.'), ERROR);
        }
    }

    if ($force == FALSE) {
        $result = $db->query("SELECT currentCode,type FROM bikes WHERE currentUser=$userId and bikeNum=$bikePrimaryKey");
    } else {
        $result = $db->query("SELECT currentCode, type FROM bikes WHERE bikeNum=$bikePrimaryKey");
    }

    $row = $result->fetch_assoc();
    $currentCode = sprintf("%04d", $row["currentCode"]);
    $biketype = $row["type"];
    $standtype = $biketype."_stand";

    $result = $db->query("SELECT standId FROM stands WHERE standName='$stand' AND type='$standtype'");
    $row = $result->fetch_assoc();
    $standinstandtype = $result->num_rows;

    if ($standinstandtype == 0) {
        response(_('Selected stand is not for '.$biketype .'.'), 1);
    }
    $standId = $row["standId"];

    if ($note){
        $result3 = $db->query("UPDATE maintenance SET status='Red' WHERE bike_id=$bikePrimaryKey");
    }
    $result = $db->query("UPDATE bikes SET currentUser=NULL,currentStand=$standId WHERE bikeNum=$bikePrimaryKey and currentUser=$userId");
    if ($note) addNote($userId, $bikePrimaryKey, $note);

    $biketext = 'Bike';
    if($biketype == 'watercraft'){
        $biketext = 'Watercraft';
    }
    $message1 = '<h4>' . $biketext . ' ' . $bikeNum . ' ' . _('successfully returned, follow instructions below.') . '</h4>';
    $message1 .= '<br/> <h3 style="line-height: 2">' . $biketext . ' ' . $bikeNum . ': <span class="label label-primary">' . _('Lock with code') . ' ' . $currentCode . '.</span></h3>';
    $message1 .= '<br />' . _('Please') . ', <strong>' . _('rotate the lockpad to') . ' <span class="label label-default">0000</span></strong> ' . _('when leaving') . '.';
    if ($note) $message1 .= '<br />' . _('You have also reported this problem:') . ' ' . $note . '.';
    $last_rented = $db->query("SELECT activity_time FROM `activity_history` WHERE user_id=$userId and bike_id=$bikePrimaryKey and action IN('rent','force_rent') ORDER BY id DESC LIMIT 1");
    $rented_bike = $last_rented->fetch_assoc();
    $current_date = date('Y-m-d H:i:s');
    $diff = strtotime($current_date) - strtotime($rented_bike['activity_time']);
    $rental_time = abs(floor($diff / 60));
    if ($force == FALSE) {
        $creditchange = changecreditendrental($bikePrimaryKey, $userId);
        if (iscreditenabled() AND $creditchange) $message1 .= '<br />' . _('Credit change') . ': -' . $creditchange . getcreditcurrency() . '.';
        $result = $db->query("INSERT INTO history SET userId=$userId,bikeNum=$bikePrimaryKey,action='RETURN',parameter=$standId");
        $result = $db->query("INSERT INTO activity_history SET user_id=$userId,bike_id=$bikePrimaryKey,action='return',stand_id=$standId,rental_time=$rental_time");
    } else {
        $result = $db->query("INSERT INTO history SET userId=$userId,bikeNum=$bikePrimaryKey,action='FORCERETURN',parameter=$standId");
        $result = $db->query("INSERT INTO activity_history SET user_id=$userId,bike_id=$bikePrimaryKey,action='force_return',stand_id=$standId,rental_time=$rental_time");
    }
    $user = getusername($userId);
    notifyAdmins($biketext . " " . $bikeNum . " " . _('was returned by') . " " . $user . ". " . _('It was returned to stand') . " " . $stand . ".", 1);

    response($message1,0);
}

function returnBikeByType($userId, $bike, $stand, $note = "", $biketype , $force = FALSE)
{
    global $db;
    $bikeNum = intval($bike); //bike_num

    //Get bikeNum
    $resultPrimaryKey = $db->query("SELECT * FROM bikes WHERE bike_num=$bikeNum AND type='$biketype' LIMIT 1");
    $rowPrimaryKey = $resultPrimaryKey->fetch_assoc();
    $bikePrimaryKey = $rowPrimaryKey['bikeNum'];
    $bikePrimaryKeyType = $rowPrimaryKey['type'];
    ($bikePrimaryKeyType=='bike') ? $bikePrimaryKeyTypeCap = 'Bike' : $bikePrimaryKeyTypeCap = 'Watercraft';

    $stand = strtoupper($stand);

    if ($force == FALSE) {
        $result = $db->query("SELECT bikeNum FROM bikes WHERE currentUser=$userId ORDER BY bikeNum");
        $bikenumber = $result->num_rows;

        if ($bikenumber == 0) {
            response(_('You currently have no rented '.$bikePrimaryKeyType.'s.'), ERROR);
        }
    }

    if ($force == FALSE) {
        $result = $db->query("SELECT currentCode,type FROM bikes WHERE currentUser=$userId and bikeNum=$bikePrimaryKey");
    } else {
        $result = $db->query("SELECT currentCode, type FROM bikes WHERE bikeNum=$bikePrimaryKey");
    }

    $row = $result->fetch_assoc();
    $currentCode = sprintf("%04d", $row["currentCode"]);
    $biketype = $row["type"];
    $standtype = $biketype."_stand";

    $result = $db->query("SELECT standId FROM stands WHERE standName='$stand' AND type='$standtype'");
    $row = $result->fetch_assoc();
    $standinstandtype = $result->num_rows;

    if ($standinstandtype == 0) {
        response(_('Selected stand is not for '.$biketype .'.'), 1);
    }
    $standId = $row["standId"];

    if ($note){
        $result3 = $db->query("UPDATE maintenance SET status='Red' WHERE bike_id=$bikePrimaryKey");
    }
    $result = $db->query("UPDATE bikes SET currentUser=NULL,currentStand=$standId WHERE bikeNum=$bikePrimaryKey and currentUser=$userId");
    if ($note) addNote($userId, $bikePrimaryKey, $note);

    $biketext = 'Bike';
    if($biketype == 'watercraft'){
        $biketext = 'Watercraft';
    }
    $message1 = '<h4>' . $biketext . ' ' . $bikeNum . ' ' . _('successfully returned, follow instructions below.') . '</h4>';
    $message1 .= '<br/> <h3 style="line-height: 2">' . $biketext . ' ' . $bikeNum . ': <span class="label label-primary">' . _('Lock with code') . ' ' . $currentCode . '.</span></h3>';
    $message1 .= '<br />' . _('Please') . ', <strong>' . _('rotate the lockpad to') . ' <span class="label label-default">0000</span></strong> ' . _('when leaving') . '.';
    if ($note) $message1 .= '<br />' . _('You have also reported this problem:') . ' ' . $note . '.';
    $last_rented = $db->query("SELECT activity_time FROM `activity_history` WHERE user_id=$userId and bike_id=$bikePrimaryKey and action IN('rent','force_rent') ORDER BY id DESC LIMIT 1");
    $rented_bike = $last_rented->fetch_assoc();
    $current_date = date('Y-m-d H:i:s');
    $diff = strtotime($current_date) - strtotime($rented_bike['activity_time']);
    $rental_time = abs(floor($diff / 60));
    if ($force == FALSE) {
        $creditchange = changecreditendrental($bikePrimaryKey, $userId);
        if (iscreditenabled() AND $creditchange) $message1 .= '<br />' . _('Credit change') . ': -' . $creditchange . getcreditcurrency() . '.';
        $result = $db->query("INSERT INTO history SET userId=$userId,bikeNum=$bikePrimaryKey,action='RETURN',parameter=$standId");
        $result = $db->query("INSERT INTO activity_history SET user_id=$userId,bike_id=$bikePrimaryKey,action='return',stand_id=$standId,rental_time=$rental_time");
    } else {
        $result = $db->query("INSERT INTO history SET userId=$userId,bikeNum=$bikePrimaryKey,action='FORCERETURN',parameter=$standId");
        $result = $db->query("INSERT INTO activity_history SET user_id=$userId,bike_id=$bikePrimaryKey,action='force_return',stand_id=$standId,rental_time=$rental_time");
    }
    $user = getusername($userId);
    notifyAdmins($biketext . " " . $bikeNum . " " . _('was returned by') . " " . $user . ". " . _('It was returned to stand') . " " . $stand . ".", 1);

    response($message1,0);
}

function returnEvent($userId, $bike, $stand, $note = "", $force = FALSE)
{
    global $db;
    $bikeNum = intval($bike);
    $stand = strtoupper($stand);

    $result = $db->query("SELECT * FROM events WHERE event_num=$bikeNum AND rsvp_date >= CURDATE() LIMIT 1");
    if($result->num_rows == 1) {

        $row = $result->fetch_assoc();
        $eventPrimaryKey = $row['id'];

        $result1 = $db->query("DELETE FROM event_users WHERE event_id=$eventPrimaryKey AND user_id=$userId");

        response(_('RSVP ') . " " . $bikeNum . " " . _('cancelled successfully.') . ".");
    }else {
        response(_('RSVP ') . " " . $bikeNum . " " . _('expired.'), 1);

    }
}

function where($userId, $bike)
{

    global $db;
    $bikeNum = $bike;

    $result = $db->query("SELECT number,userID,userName,stands.standName,bikes.type FROM bikes LEFT JOIN users on bikes.currentUser=users.userID LEFT JOIN stands on bikes.currentStand=stands.standId where bikeNum=$bikeNum");
    $row = $result->fetch_assoc();
    $userId = $row["userID"];
    $type = $row["type"];
    $phone = $row["number"];
    $userName = $row["userName"];
    $standName = $row["standName"];
    $result = $db->query("SELECT note FROM notes WHERE bikeNum='$bikeNum' AND deleted IS NULL ORDER BY time DESC");
    $note = "";
    while ($row = $result->fetch_assoc()) {
        $note .= $row["note"] . "; ";
    }
    $note = substr($note, 0, strlen($note) - 2); // remove last two chars - comma and space
    if ($note) {
        $note = _('Bike note:') . " " . $note;
    }

    if ($standName) {
        response('<h3 class="text-xs-center">' . ucfirst($type) . ' ' . $bikeNum . ' ' . _('at') . ' <span class="label label-primary">' . $standName . '</span>.</h3>' . $note);
    } else {
        response('<h3 class="text-center">' . ucfirst($type) . ' ' . $bikeNum . ' ' . _('rented by') . ' <span class="label label-primary">' . $userName . '</span></h3><p class="text-center">' . _('Phone') . ': <a href="tel:+' . $phone . '">+' . $phone . '</a>. ' . $note . '</p>');
    }

}

function addnote($userId, $bikeNum, $message)
{

    global $db;
    $userNote = $db->conn->real_escape_string(trim($message));

    $result = $db->query("SELECT userName,number from users where userId='$userId'");
    $row = $result->fetch_assoc();
    $userName = $row["userName"];
    $phone = $row["number"];
    $result = $db->query("SELECT stands.standName FROM bikes LEFT JOIN users on bikes.currentUser=users.userID LEFT JOIN stands on bikes.currentStand=stands.standId WHERE bikeNum=$bikeNum");
    $row = $result->fetch_assoc();
    $standName = $row["standName"];
    if ($standName != NULL) {
        $bikeStatus = _('at') . " " . $standName;
    } else {
        $bikeStatus = _('used by') . " " . $userName . " +" . $phone;
    }
    $db->query("INSERT INTO notes SET bikeNum='$bikeNum',userId='$userId',note='$userNote'");
    $noteid = $db->conn->insert_id;
    notifyAdmins(_('Note #') . $noteid . ": b." . $bikeNum . " (" . $bikeStatus . ") " . _('by') . " " . $userName . "/" . $phone . ":" . $userNote);

}

function listbikes($stand)
{
    global $db, $forcestack;

    $stacktopbike = FALSE;
    $stand = $db->conn->real_escape_string($stand);
    if ($forcestack) {
        $result = $db->query("SELECT standId FROM stands WHERE standName='$stand'");
        $row = $result->fetch_assoc();
        $stacktopbike = checktopofstack($row["standId"]);
    }
    $result = $db->query("SELECT bikeNum FROM bikes LEFT JOIN stands ON bikes.currentStand=stands.standId WHERE standName='$stand'");
    while ($row = $result->fetch_assoc()) {
        $bikenum = $row["bikeNum"];
        $result2 = $db->query("SELECT note FROM notes WHERE bikeNum='$bikenum' AND deleted IS NULL ORDER BY time DESC");
        $note = "";
        while ($row = $result2->fetch_assoc()) {
            $note .= $row["note"] . "; ";
        }
        $note = substr($note, 0, strlen($note) - 2); // remove last two chars - comma and space
        if ($note) {
            $bicycles[] = "*" . $bikenum; // bike with note / issue
            $notes[] = $note;
        } else {
            $bicycles[] = $bikenum;
            $notes[] = "";
        }
    }
    if (!$result->num_rows) {
        $bicycles = "";
        $notes = "";
    }
    response($bicycles, 0, array("notes" => $notes, "stacktopbike" => $stacktopbike), 0);

}

function listbikesbytype($stand,$standtype='bike_stand')
{
    global $db, $forcestack;
    $tableName='bikes';
    $columnName='currentStand';
    $primarykey='bikeNum';
    $bikeeventnum='bike_num';
    $eventActive = '';
    $usereventslist = '';
    $whereisactive=" AND bikes.active='Y'";
    if($standtype=='event_stand'){
        $tableName='events';
        $columnName='current_stand';
        $primarykey='id';
        $whereisactive=' AND events.is_active=1';
        $bikeeventnum='event_num';
        $eventActive = ' AND events.is_deleted = 0 AND events.rsvp_date>=CURDATE() ';
        $usereventslist = ' '. excludeusereventssql('events.event_num');
    }


    $stacktopbike = FALSE;
    $stand = $db->conn->real_escape_string($stand);
    if ($forcestack) {
        $result = $db->query("SELECT standId FROM stands WHERE standName='$stand'");
        $row = $result->fetch_assoc();
        $stacktopbike = checktopofstack($row["standId"]);
    }
    $result = $db->query("SELECT $primarykey ,$bikeeventnum FROM $tableName LEFT JOIN stands ON $tableName.$columnName=stands.standId  WHERE standName='$stand' $eventActive  $whereisactive $usereventslist");
    while ($row = $result->fetch_assoc()) {
//        $bikenum = $row["$primarykey"];
        $bikeprimarykey = $row["$primarykey"];
        $bikenum = $row["$bikeeventnum"];
         if($standtype != 'event_stand'){

             $result3 = $db->query("SELECT status FROM maintenance WHERE bike_id=" . $bikeprimarykey);
             $row3 = $result3->fetch_assoc();
//             $bikestatus = '';
//             if($row3["status"] == 'Red'){
//                 //not allowed
//             }


             $result2 = $db->query("SELECT note FROM notes WHERE bikeNum='$bikeprimarykey' AND deleted IS NULL ORDER BY time DESC");
             $note = "";
             while ($row = $result2->fetch_assoc()) {
                 $note .= $row["note"] . "; ";
             }
             $note = substr($note, 0, strlen($note) - 2); // remove last two chars - comma and space
             if ($note) {
                 if(($row3["status"] == 'Red')){
                     $bicycles[] = "*" . $bikenum; // bike with note / issue
                     $notes[] = $note;
                 }else {
                     $bicycles[] = $bikenum;
                     $notes[] = "";
                 }
             } else {
                 $bicycles[] = $bikenum;
                 $notes[] = "";
             }
         }else {
             $bicycles[] = $bikenum;
             $notes[] = "";
         }
    }
    if (!$result->num_rows) {
        $bicycles = "";
        $notes = "";
    }
    response($bicycles, 0, array("notes" => $notes, "stacktopbike" => $stacktopbike), 0);

}

function liststands()
{
    global $db;

    $standBikeCombo = array();//return holding stands and bikes
    $result = $db->query("SELECT standId,standName,standAddress,standPhoto,serviceTag,standDescription,longitude,latitude,active FROM stands ORDER BY standName");
    while ($row = $result->fetch_assoc()) {
        $stand = $row;
        $standId = $row["standId"];
        //get bikes for a stand
        $result2 = $db->query("SELECT bikeNum,currentUser,currentStand,currentCode,note FROM bikes WHERE currentStand='$standId'");
        $bikes = array();
        while ($row = $result2->fetch_assoc()) {
            $bikes[] = $row;
        }
        $standBikeCombo[] = array("stand" => $stand, "bikes" => $bikes);
    }

    echo json_encode($standBikeCombo);
    exit;

}

function liststandsmin()
{
    global $db;

    $stands = array();//return holding stands and bikes
    $result = $db->query("SELECT standId,standName FROM stands WHERE type='bike_stand' AND active='Y' ORDER BY standName");
    while ($row = $result->fetch_assoc()) {
        $stands[] = $row;
    }

    echo json_encode($stands);
    exit;
}

function liststandsminbytype($stand_type)
{
    global $db;

    $stands = array();//return holding stands and bikes
    $result = $db->query("SELECT standId,standName FROM stands WHERE type='$stand_type' AND active='Y' ORDER BY standName");
    while ($row = $result->fetch_assoc()) {
        $stands[] = $row;
    }

    echo json_encode($stands);
    exit;
}

function standsminbytypewithselected($stand_type, $bikeOrEventId)
{
    global $db;
    try{
        ($stand_type == 'event_stand') ? $table = 'events' : $table = 'bikes' ;
        ($stand_type == 'event_stand') ? $col = 'current_stand' : $col = 'currentStand' ;
        ($stand_type == 'event_stand') ? $id = 'id' : $id = 'bikeNum' ;

        $resultSelected = $db->query("SELECT $col FROM $table WHERE $id=$bikeOrEventId");
        $rowSelected = $resultSelected->fetch_assoc();
        $standSelected = $rowSelected[$col];

    }catch(Exception $e) {
        echo 'Message: ' .$e->getMessage();
    }



    $stands = array();//return holding stands and bikes
    $result = $db->query("SELECT standId,standName FROM stands WHERE type='$stand_type' AND active='Y' ORDER BY standName");
    while ($row = $result->fetch_assoc()) {
        $stands[] = $row;
    }
    $stands = array('standSelected'=>$standSelected, 'allStands'=> $stands);
    echo json_encode($stands);
    exit;
}

function removenote($userId, $bikeNum)
{
    global $db;

    $result = $db->query("DELETE FROM notes WHERE bikeNum=$bikeNum LIMIT XXXX");
    response(_('Note for bike') . " " . $bikeNum . " " . _('deleted') . ".");
}

function last($userId, $bike = 0)
{

    global $db, $timezone;
    $bikeNum = intval($bike);
    if ($bikeNum) {
        $bikresult = $db->query("SELECT type FROM `bikes` WHERE bikenum=$bikeNum");
        $biketype = "bike";
        while ($bikerow = $bikresult->fetch_assoc()) {
            $biketype = $bikerow['type'];
        }
        $result = $db->query("SELECT userName,parameter,standName,action,time FROM `history` JOIN users ON history.userId=users.userId LEFT JOIN stands ON stands.standId=history.parameter WHERE bikenum=$bikeNum AND (action NOT LIKE '%CREDIT%') ORDER BY time DESC LIMIT 40");
        $historyInfo = "<h3>" . ucfirst($biketype) . " " . $bikeNum . " " . _('history') . ":</h3><ul>";
        while ($row = $result->fetch_assoc()) {
            $time = strtotime($row["time"]);
            $historyInfo .= "<li>" . date("d/m H:i", $time) . " - ";
            if ($row["standName"] != NULL) {
                $historyInfo .= $row["standName"];
                if (strpos($row["parameter"], "|")) {
                    $revertcode = explode("|", $row["parameter"]);
                    $revertcode = $revertcode[1];
                }
                if ($row["action"] == "REVERT") $historyInfo .= ' <span class="label label-warning">' . _('Revert') . ' (' . str_pad($revertcode, 4, "0", STR_PAD_LEFT) . ')</span>';
            } else {
                $historyInfo .= $row["userName"] . ' (<span class="label label-default">' . str_pad($row["parameter"], 4, "0", STR_PAD_LEFT) . '</span>)';
            }
            $historyInfo .= "</li>";
        }
        $historyInfo .= "</ul>";
    } else {
        $historyInfo  = '';
        $historyInfo  = currentnetworkusage('bike');
        $historyInfo .= currentnetworkusage('watercraft');

    }
    response($historyInfo, 0, "", 0);
}
function currentnetworkusage($type){
    global $db, $timezone;
    $bicyclestr = 'bicycle';
    $bicyclesstr = 'bicycles';
    if($type== 'watercraft'){
        $bicyclestr = 'watercraft';
        $bicyclesstr = 'watercrafts';
    }
    $result = $db->query("SELECT bikeNum FROM bikes WHERE currentUser<>'' AND type='$type'");
    $inuse = $result->num_rows;
    $result = $db->query("SELECT bikeNum,userName,standName,users.userId FROM bikes LEFT JOIN users ON bikes.currentUser=users.userId LEFT JOIN stands ON bikes.currentStand=stands.standId WHERE bikes.type='$type' ORDER BY bikeNum");
    $total = $result->num_rows;
    $historyInfo = "<h3>" . _('Current network usage: ') . $bicyclestr."</h3>";
    $historyInfo .= "<h4>" . sprintf(ngettext('%d '.$bicyclestr, '%d '.$bicyclesstr, $total), $total) . ", " . $inuse . " " . _('in use') . "</h4><ul>";
    while ($row = $result->fetch_assoc()) {
        $historyInfo .= "<li>" . $row["bikeNum"] . " - ";
        if ($row["standName"] != NULL) {
            $historyInfo .= $row["standName"];
        } else {
            $historyInfo .= '<span class="bg-warning">' . $row["userName"];
            if($row["userId"]){
                $result2 = $db->query("SELECT time FROM history WHERE bikeNum=" . $row["bikeNum"] . " AND userId=" . $row["userId"] . " AND action='RENT' ORDER BY time DESC");
            }else{
                $result2 = $db->query("SELECT time FROM history WHERE bikeNum=" . $row["bikeNum"] . " AND action='RENT' ORDER BY time DESC");
            }
            $row2 = $result2->fetch_assoc();

            $historyInfo .= ": " . date("d/m H:i", strtotime($row2["time"])) . '</span>';
        }
        $result2 = $db->query("SELECT note FROM notes WHERE bikeNum='" . $row["bikeNum"] . "' AND deleted IS NULL ORDER BY time DESC");
        $note = "";
        while ($row = $result2->fetch_assoc()) {
            $note .= $row["note"] . "; ";
        }
        $note = substr($note, 0, strlen($note) - 2); // remove last two chars - comma and space
        if ($note) $historyInfo .= " (" . $note . ")";
        $historyInfo .= "</li>";
    }
    $historyInfo .= "</ul>";
    return $historyInfo;
}

function userbikes($userId)
{
    global $db;
    if (!isloggedin()) response("");
    $result = $db->query("SELECT bikeNum,bike_num, currentCode,type FROM bikes WHERE currentUser=$userId ORDER BY bikeNum");
    while ($row = $result->fetch_assoc()) {
//        $bikenum = $row["bikeNum"];
        $bikenum = $row["bike_num"];
        $bicycles[] = $bikenum;
        $standtypes[] = $row["type"]."_stand";
        $codes[] = str_pad($row["currentCode"], 4, "0", STR_PAD_LEFT);
        $result2 = $db->query("SELECT parameter FROM history WHERE bikeNum=$bikenum AND action='RENT' ORDER BY time DESC LIMIT 1,1");
        $row = $result2->fetch_assoc();
        $oldcodes[] = str_pad($row["parameter"], 4, "0", STR_PAD_LEFT);
    }
    if (!$result->num_rows) $bicycles = "";
    if (!isset($codes)) $codes = "";
    else $codes = array("codes" => $codes, "oldcodes" => $oldcodes,"standtypes"=>$standtypes);
    response($bicycles, 0, $codes, 0);
}

function userevents($userId)
{
    global $db;
    if (!isloggedin()) response("");
    $result = $db->query("SELECT id,event_id FROM event_users WHERE user_id=$userId ORDER BY id");
    while ($row = $result->fetch_assoc()) {
        $bikenum = $row["event_id"];

        $result1 = $db->query("SELECT id,event_num FROM events WHERE id=$bikenum ORDER BY id");
        $row1 = $result1->fetch_assoc();
        $bikenum = $row1["event_num"];

        $bicycles[] = $bikenum;
    }
    if (!$result->num_rows) $bicycles = "";

    response($bicycles, 0, $codes='', 0);
}


function getuserevents($userId)
{
    global $db;
    if (!isloggedin()) return "";
    $result = $db->query("SELECT id,event_id FROM event_users WHERE user_id=$userId ORDER BY id");
    while ($row = $result->fetch_assoc()) {
        $bikenum = $row["event_id"];

        $result1 = $db->query("SELECT id,event_num FROM events WHERE id=$bikenum ORDER BY id");
        $row1 = $result1->fetch_assoc();
        $bikenum = $row1["event_num"];

        $bicycles[] = $bikenum;
    }
    if (!$result->num_rows) $bicycles = "";

    return $bicycles;
//    echo json_encode($bicycles);
}

function excludeusereventssql($id){
    if (isset($_COOKIE["loguserid"])) $userid = $_COOKIE["loguserid"];
    else $userid = 0;
    $usereventlist='';
    $usereventlist = getuserevents($userid);
    $usereventssql = '';
    $usereventsforsql = '';
    if($usereventlist !=''){
        foreach ($usereventlist as $userevent) {
            $usereventsforsql .= $userevent . ',';
        }
        $usereventsforsql = rtrim($usereventsforsql,',');
        $usereventssql = "AND $id NOT IN ($usereventsforsql)";
    }
    return $usereventssql;
}

function revert($userId, $bikeNum)
{

    global $db;

    $standId = 0;
    $result = $db->query("SELECT currentUser FROM bikes WHERE bikeNum=$bikeNum AND currentUser IS NOT NULL");
    if (!$result->num_rows) {
        response(_('Bicycle') . " " . $bikeNum . " " . _('is not rented right now. Revert not successful!'), ERROR);
        return;
    } else {
        $row = $result->fetch_assoc();
        $revertusernumber = getphonenumber($row["currentUser"]);
    }
    $result = $db->query("SELECT parameter,standName FROM stands LEFT JOIN history ON stands.standId=parameter WHERE bikeNum=$bikeNum AND action IN ('RETURN','FORCERETURN') ORDER BY time DESC LIMIT 1");
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $standId = $row["parameter"];
        $stand = $row["standName"];
    }
    $result = $db->query("SELECT parameter FROM history WHERE bikeNum=$bikeNum AND action IN ('RENT','FORCERENT') ORDER BY time DESC LIMIT 1,1");
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $code = str_pad($row["parameter"], 4, "0", STR_PAD_LEFT);
    }
    if ($standId and $code) {
        $result = $db->query("UPDATE bikes SET currentUser=NULL,currentStand=$standId,currentCode=$code WHERE bikeNum=$bikeNum");
        $result = $db->query("INSERT INTO history SET userId=$userId,bikeNum=$bikeNum,action='REVERT',parameter='$standId|$code'");
        $result = $db->query("INSERT INTO history SET userId=0,bikeNum=$bikeNum,action='RENT',parameter=$code");
        $result = $db->query("INSERT INTO history SET userId=0,bikeNum=$bikeNum,action='RETURN',parameter=$standId");
        response('<h3>' . _('Bicycle') . ' ' . $bikeNum . ' ' . _('reverted to') . ' <span class="label label-primary">' . $stand . '</span> ' . _('with code') . ' <span class="label label-primary">' . $code . '</span>.</h3>');
        sendSMS($revertusernumber, _('Bike') . " " . $bikeNum . " " . _('has been returned. You can now rent a new bicycle.'));
    } else {
        response(_('No last stand or code for bicycle') . " " . $bikeNum . " " . _('found. Revert not successful!'), ERROR);
    }

}

function register($number, $code, $checkcode, $fullname, $email, $age, $gender, $race, $password, $password2, $existing, $mailingaddress, $physicaladdress, $city, $state, $status, $zipcode)
{
    global $db, $dbpassword, $countrycode, $systemURL;

    $number = $db->conn->real_escape_string(trim($number));
    $code = $db->conn->real_escape_string(trim($code));
    $checkcode = $db->conn->real_escape_string(trim($checkcode));
    $fullname = $db->conn->real_escape_string(trim($fullname));
    $email = $db->conn->real_escape_string(trim($email));

    $age = $db->conn->real_escape_string(trim($age));
    if($age == '65'){
        $age = '65+';
    }
    $gender = $db->conn->real_escape_string(trim($gender));
    $race = $db->conn->real_escape_string(trim($race));
    $password = $db->conn->real_escape_string(trim($password));
    $password2 = $db->conn->real_escape_string(trim($password2));
    $existing = $db->conn->real_escape_string(trim($existing));
    $mailingaddress = $db->conn->real_escape_string(trim($mailingaddress));
    $physicaladdress = $db->conn->real_escape_string(trim($physicaladdress));
    $city = $db->conn->real_escape_string(trim($city));
    $state = $db->conn->real_escape_string(trim($state));
    $status = $db->conn->real_escape_string(trim($status));
    $zipcode = $db->conn->real_escape_string(trim($zipcode));
    $parametercheck = $number . ";" . str_replace(" ", "", $code) . ";" . $checkcode;
    if ($password <> $password2) {
        response(_('Password do not match. Please correct and try again.'), ERROR);
    }

    $existingUser = $db->query("SELECT * FROM users WHERE number='$number' or mail='$email'");

    if ($existingUser->num_rows == 0) {

        if (issmssystemenabled() == TRUE) {

            $result = $db->query("SELECT parameter FROM history WHERE userId=0 AND bikeNum=0 AND action='REGISTER' AND parameter='$parametercheck' ORDER BY time DESC LIMIT 1");
            if ($result->num_rows == 1) {
                if (!$existing) // new user registration
                {
                    $result = $db->query("INSERT INTO users SET userName='$fullname',password=SHA2('$password',512),mail='$email',number='$number',age='$age',gender='$gender',race='$race',mailingAddress='$mailingaddress',physicalAddress='$physicaladdress',city='$city',state='$state',status='$status',zipcode='$zipcode',privileges=0");
                    //insertid method from db.class.php
                    $userId = $db->insertid();
                    sendConfirmationEmail($email);
                    response(_('You have been successfully registered. Please, check your email and read the instructions to finish your registration.'),0,array('user_id'=>$userId));
                } else // existing user, password change
                {
                    $result = $db->query("SELECT userId FROM users WHERE number='$number'");
                    $row = $result->fetch_assoc();
                    $userId = $row["userId"];
                    $result = $db->query("UPDATE users SET password=SHA2('$password',512) WHERE userId='$userId'");
                    response(_('Password successfully changed. Your username is your phone number. Continue to') . ' <a href="' . $systemURL . '">' . _('login') . '</a>.');
                }
            } else {
                response(_('Problem with the SMS code entered. Please check and try again.'), '1');
            }
        } else // SMS system disabled
        {
            $result = $db->query("INSERT INTO users SET userName='$fullname',password=SHA2('$password',512),mail='$email',number='',age='$age',gender='$gender',race='$race',mailingAddress='$mailingaddress',physicalAddress='$physicaladdress',city='$city',state='$state',status='$status',zipcode='$zipcode',privileges=0");
            $userId = $db->insertid();
            $result = $db->query("UPDATE users SET number='$userId' WHERE userId='$userId'");
            sendConfirmationEmail($email);
            response(_('You have been successfully registered. Please, check your email and read the instructions to finish your registration. Your number for login is:') ,0,array('user_id'=>$userId));
        }
    } else {
        response(_('You have been already registered with this email or phone number'), '1');
    }


}

//function register_paypal_return($paypal_amt, $paypal_cc, $paypal_cm, $paypal_item_name, $paypal_item_number, $paypal_st, $paypal_tx, $paypal_info)
//{
//    global $db, $dbpassword, $countrycode, $systemURL;
//
//    $paypal_amt = $db->conn->real_escape_string(trim($paypal_amt));
//    $paypal_cc = $db->conn->real_escape_string(trim($paypal_cc));
//    $paypal_cm = $db->conn->real_escape_string(trim($paypal_cm));
//    $paypal_item_name = $db->conn->real_escape_string(trim($paypal_item_name));
//    $paypal_item_number = $db->conn->real_escape_string(trim($paypal_item_number));
//    $paypal_st = $db->conn->real_escape_string(trim($paypal_st));
//    $paypal_tx = $db->conn->real_escape_string(trim($paypal_tx));
//    $paypal_info = $db->conn->real_escape_string(trim($paypal_info));
//    if($paypal_item_number == 'family-weekend'){
//        $expired_date = date("Y-m-d H:i:s", strtotime("+48 hours"));
//    }else if($paypal_item_number == 'annual-subscription'){
//        $expired_date = date("Y-m-d H:i:s", strtotime("+366 days"));
//    }else {
//        $expired_date = date("Y-m-d H:i:s", strtotime("+31 days"));
//    }
//    $created_date = date("Y-m-d H:i:s");
//
//    $existingUser = $db->query("SELECT * FROM payment_subscription WHERE user_id='$paypal_cm' AND subscription_type='$paypal_item_number'");
//    if ($existingUser->num_rows == 0) {
//
//        //payment_subscription table
//        $db->query("INSERT INTO payment_subscription SET user_id='$paypal_cm',subscription_type='$paypal_item_number',payment_info='$paypal_info',is_active='1',created_date='$created_date',expiration_date='$expired_date'");
//
//        //users table
//        $db->query("UPDATE users SET status='active' WHERE userId='$paypal_cm'");
//
//        response(_('You have been successfully subscribed. Please, check your email and read the instructions to finish your registration.'));
//
//    } else {
//        if($expired_date <= date()){
//            $db->query("UPDATE payment_subscription SET payment_info='$paypal_info',is_active='1',expiration_date='$expired_date' WHERE user_id='$paypal_cm' AND subscription_type='$paypal_item_number' ");
//            $db->query("UPDATE users SET status='active' WHERE userId='$paypal_cm'");
//
//            response(_('You have been successfully updated subscription.'));
//        }else {
//            response(_('You are already subscribed'), '1');
//
//        }
//    }
//}

function login($number, $password)
{
    global $db, $systemURL, $countrycode;

    $number = $db->conn->real_escape_string(trim($number));
    $password = $db->conn->real_escape_string(trim($password));
    $number = str_replace(" ", "", $number);
    $number = str_replace("-", "", $number);
    $number = str_replace("/", "", $number);
    if ($number[0] == "0") $number = $countrycode . substr($number, 1, strlen($number));
    $altnumber = $countrycode . $number;

    $result = $db->query("SELECT userId FROM users WHERE (number='$number' OR number='$altnumber') AND password=SHA2('$password',512)");
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $userId = $row["userId"];
        $sessionId = hash('sha256', $userId . $number . time());
        $timeStamp = time() + 86400 * 7; // 7 days to keep user logged in
        $result = $db->query("DELETE FROM sessions WHERE userId='$userId'");
        $result = $db->query("INSERT INTO sessions SET userId='$userId',sessionId='$sessionId',timeStamp='$timeStamp'");
//        $db->conn->commit();
        setcookie("loguserid", $userId, time() + 86400 * 7);
        setcookie("logsession", $sessionId, time() + 86400 * 7);
        header("HTTP/1.1 301 Moved permanently");
        header("Location: " . $systemURL);
        header("Connection: close");
        exit;
    } else {
        header("HTTP/1.1 301 Moved permanently");
        header("Location: " . $systemURL . "?error=1");
        header("Connection: close");
        exit;
    }
}

function logout()
{
    global $db, $systemURL;
    if (isset($_COOKIE["loguserid"]) AND isset($_COOKIE["logsession"])) {
        $userid = $db->conn->real_escape_string(trim($_COOKIE["loguserid"]));
        $session = $db->conn->real_escape_string(trim($_COOKIE["logsession"]));
        unset($_COOKIE['loguserid']);
        unset($_COOKIE['logsession']);
        $result = $db->query("DELETE FROM sessions WHERE userId='$userid'");
        $db->conn->commit();
        response("logout successfully");
    } else {
        response("logout successfully", 1);
    }
//    header("HTTP/1.1 301 Moved permanently");
//    header("Location: " . $systemURL);
//    header("Connection: close");
//    exit;
}

function checkprivileges($userid)
{
    global $db;
    $privileges = getprivileges($userid);
    if ($privileges < 1) {
        response(_('Sorry, this command is only available for the privileged users.'), ERROR);
        exit;
    }
}

function isAdmin($userid)
{
    global $db;
    $privileges = getprivileges($userid);
    if ($privileges != 7) {
        response(_('Sorry, this command is only available for the privileged users.'), ERROR);
        exit;
    }
}

function smscode($number)
{

    global $db, $gatewayId, $gatewayKey, $gatewaySenderNumber, $connectors;
    srand();
    $number = normalizephonenumber($number);
    $number = $db->conn->real_escape_string($number);
    $userexists = 0;
    $result = $db->query("SELECT userId FROM users WHERE number='$number'");
    if ($result->num_rows) $userexists = 1;

    $smscode = generateRandomCode(6);
    $smscodenormalized = str_replace(" ", "", $smscode);
    $checkcode = md5("WB" . $number . $smscodenormalized);
    if (!$userexists) $text = _('Enter this code to register:') . " " . $smscode;
    else $text = _('Enter this code to change password:') . " " . $smscode;
    $text = $db->conn->real_escape_string($text);

    if (!issmssystemenabled()) $result = $db->query("INSERT INTO sent SET number='$number',text='$text'");
    $result = $db->query("INSERT INTO history SET userId=0,bikeNum=0,action='REGISTER',parameter='$number;$smscodenormalized;$checkcode'");

    if (DEBUG === TRUE) {
        response($number, 0, array("checkcode" => $checkcode, "smscode" => $smscode, "existing" => $userexists));
    } else {
        sendSMS($number, $text);
        if (issmssystemenabled() == TRUE) response($number, 0, array("checkcode" => $checkcode, "existing" => $userexists));
        else response($number, 0, array("checkcode" => $checkcode, "existing" => $userexists));
    }
}

function trips($userId, $bike = 0)
{

    global $db;
    $bikeNum = intval($bike);
    if ($bikeNum) {
        $result = $db->query("SELECT longitude,latitude FROM `history` LEFT JOIN stands ON stands.standid=history.parameter WHERE bikenum=$bikeNum AND action='RETURN' ORDER BY time DESC");
        while ($row = $result->fetch_assoc()) {
            $jsoncontent[] = array("longitude" => $row["longitude"], "latitude" => $row["latitude"]);
        }
    } else {
        $result = $db->query("SELECT bikeNum,longitude,latitude FROM `history` LEFT JOIN stands ON stands.standid=history.parameter WHERE action='RETURN' ORDER BY bikeNum,time DESC");
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $bikenum = $row["bikeNum"];
            $jsoncontent[$bikenum][] = array("longitude" => $row["longitude"], "latitude" => $row["latitude"]);
        }
    }
    echo json_encode($jsoncontent);
}

function getinquirylist()
{
    global $db;
    $result = $db->query("SELECT inquiryid,phone,email,inquiry,solved FROM inquiries ");
    while ($row = $result->fetch_assoc()) {
        $jsoncontent[] = array("inquiryid" => $row["inquiryid"], "phone" => $row["phone"], "email" => $row["email"], "inquiry" => $row["inquiry"], "solved" => $row["solved"]);
    }
    echo json_encode($jsoncontent);
}

function getvideolist()
{
    global $db;
    $result = $db->query("SELECT videoId,fileName,thumbnailPath,videoPath FROM videos ");
    while ($row = $result->fetch_assoc()) {
        $jsoncontent[] = array("videoId" => $row["videoId"], "fileName" => $row["fileName"], "thumbnailPath" => $row["thumbnailPath"], "videoPath" => $row["videoPath"]);
    }
    echo json_encode($jsoncontent);// TODO change to response function
}

function getuserlist()
{
    global $db;
    $result = $db->query("SELECT users.userId,username,mail,number,privileges,credit,userLimit FROM users LEFT JOIN credit ON users.userId=credit.userId LEFT JOIN limits ON users.userId=limits.userId ORDER BY username");
    while ($row = $result->fetch_assoc()) {
        $jsoncontent[] = array("userid" => $row["userId"], "username" => $row["username"], "mail" => $row["mail"], "number" => $row["number"], "privileges" => $row["privileges"], "credit" => $row["credit"], "limit" => $row["userLimit"]);
    }
    echo json_encode($jsoncontent);// TODO change to response function
}

function getuserstats()
{
    global $db;
    $result = $db->query("SELECT users.userId,username,count(action) AS count FROM users LEFT JOIN history ON users.userId=history.userId WHERE history.userId IS NOT NULL GROUP BY username ORDER BY count DESC");
    while ($row = $result->fetch_assoc()) {
        $result2 = $db->query("SELECT count(action) AS rentals FROM history WHERE action='RENT' AND userId=" . $row["userId"]);
        $row2 = $result2->fetch_assoc();
        $result2 = $db->query("SELECT count(action) AS returns FROM history WHERE action='RETURN' AND userId=" . $row["userId"]);
        $row3 = $result2->fetch_assoc();
        $jsoncontent[] = array("userid" => $row["userId"], "username" => $row["username"], "count" => $row["count"], "rentals" => $row2["rentals"], "returns" => $row3["returns"]);
    }
    echo json_encode($jsoncontent);// TODO change to response function
}

function getusagestats()
{
    global $db;
    $result = $db->query("SELECT count(action) AS count,DATE(time) AS day,action FROM history WHERE userId IS NOT NULL AND action IN ('RENT','RETURN') GROUP BY day,action ORDER BY day DESC LIMIT 60");
    while ($row = $result->fetch_assoc()) {
        $jsoncontent[] = array("day" => $row["day"], "count" => $row["count"], "action" => $row["action"]);
    }
    echo json_encode($jsoncontent);// TODO change to response function
}

function ggetDailyStats()
{
    global $db;
}

function editstand($standid)
{
    global $db;
    $result = $db->query("SELECT * FROM stands WHERE stands.standId=" . $standid);
    $row = $result->fetch_assoc();
    $jsoncontent = array("standId" => $row["standId"], "standName" => $row["standName"], "standAddress" => $row["standAddress"], "standPhoto" => $row["standPhoto"], "serviceTag" => $row["serviceTag"], "standDescription" => $row["standDescription"], "type" => $row["type"], "active" => $row["active"], "longitude" => $row["longitude"], "latitude" => $row["latitude"]);
    echo json_encode($jsoncontent);// TODO change to response function
}

function editvideo($videoid)
{
    global $db;
    $result = $db->query("SELECT * FROM videos WHERE videoid=" . $videoid);
    $row = $result->fetch_assoc();
    $jsoncontent = array("videoId" => $row["videoId"], "fileName" => $row["fileName"], "videoPath" => $row["videoPath"], "thumbnailPath" => $row["thumbnailPath"]);
    echo json_encode($jsoncontent);// TODO change to response function
}

function editinquiry($inquiryid)
{
    global $db;
    $result = $db->query("SELECT * FROM inquiries WHERE inquiries.inquiryid=" . $inquiryid);
    $row = $result->fetch_assoc();
    $jsoncontent = array("inquiryid" => $row["inquiryid"], "userid" => $row["userid"], "phone" => $row["phone"], "email" => $row["email"], "inquiry" => $row["inquiry"], "solved" => $row["solved"]);
    echo json_encode($jsoncontent);
}

function edithelp($inquiryid)
{
    global $db;
    $result = $db->query("SELECT * FROM inquiries WHERE inquiries.inquiryid=" . $inquiryid);
    $row = $result->fetch_assoc();
    $jsoncontent = array("id" => $row["inquiryid"], "question" => $row["inquiry"], "answer" => $row["answer"]);
    echo json_encode($jsoncontent);
}

function editbicycle($bicycleid)
{
    global $db;
    $result = $db->query("SELECT * FROM bikes WHERE bikes.bikeNum=" . $bicycleid);
    $row = $result->fetch_assoc();
    $jsoncontent = array("bikeNum" => $row["bikeNum"],"bike_num" => $row["bike_num"], "currentUser" => $row["currentUser"], "currentStand" => $row["currentStand"], "currentCode" => $row["currentCode"], "note" => $row["note"], "bike_status" => $row["active"], "image_path" => $row["image_path"], "bike_type" => $row["type"]);
    echo json_encode($jsoncontent);// TODO change to response function
}

function editevent($eventid)
{
    global $db;
    $result = $db->query("SELECT * FROM events WHERE events.id=" . $eventid);
    $row = $result->fetch_assoc();

    $start_date = $row["start_date"];
    $row["start_date"]= date('Y-m-d', strtotime($start_date));

    $end_date = $row["end_date"];
    $row["end_date"]= date('Y-m-d', strtotime($end_date));

    $rsvp_date = $row["rsvp_date"];
    $row["rsvp_date"]= date('Y-m-d', strtotime($rsvp_date));

    $jsoncontent = array("id" => $row["id"],"event_num" => $row["event_num"], "event_description" => $row["event_description"], "current_stand" => $row["current_stand"],"total_rides" => $row["total_rides"], "image_path" => $row["image_path"], "is_active" => $row["is_active"], "start_date" => $row["start_date"], "end_date" => $row["end_date"], "rsvp_date" => $row["rsvp_date"], "is_deleted" => $row["is_deleted"]);
    echo json_encode($jsoncontent);// TODO change to response function
}

function editplace($placeid)
{
    global $db;
    $result = $db->query("SELECT * FROM places WHERE id=" . $placeid);
    $row = $result->fetch_assoc();

    $jsoncontent = array("id" => $row["id"],"name" => $row["name"], "description" => $row["description"], "photo" => $row["photo"],"latitude" => $row["latitude"], "longitude" => $row["longitude"], "link" => $row["link"]);
    echo json_encode($jsoncontent);// TODO change to response function
}

function getbicyclephoto($bicycleid)
{
    global $db;
    $result = $db->query("SELECT bikeNum,image_path FROM bikes WHERE bikes.bikeNum=" . $bicycleid);
    $row = $result->fetch_assoc();

    $result1 = $db->query("SELECT status FROM maintenance WHERE bike_id=" . $bicycleid);
    $row1 = $result1->fetch_assoc();
    $status_image_path = '';
    if($row1["status"]){
        switch ($row1["status"]){
            case 'Green':
                $status_image_path = 'images/green.png';
                break;
            case 'Yellow':
                $status_image_path = 'images/yellow.png';
                break;
            case 'Orange':
                $status_image_path = 'images/orange.png';
                break;
            case 'Red':
                $status_image_path = 'images/red.png';
                break;
        }
    }
    $jsoncontent = array("bikeNum" => $row["bikeNum"], "image_path" => $row["image_path"],"status_image_path" => $status_image_path);
    echo json_encode($jsoncontent);// TODO change to response function
}

function getbicyclephotobytype($bicycleid,$standtype)
{
    global $db;
    if($standtype == 'event_stand'){
        $result = $db->query("SELECT ev.id,ev.event_num,ev.image_path,ev.total_rides,COUNT(eu.event_id) as total_bikes FROM events ev LEFT JOIN event_users eu ON ev.id=eu.event_id WHERE ev.event_num=" . $bicycleid . " AND ev.is_deleted=0 AND ev.rsvp_date >= CURDATE()");
        $row = $result->fetch_assoc();

        $jsoncontent = array("id" => $row["event_num"], "image_path" => $row["image_path"],"total_rides" => $row["total_rides"],"total_bikes" => $row["total_bikes"]);
    }else {
        ($standtype == 'watercraft_stand') ? $bikeOrWatercraft='watercraft' : $bikeOrWatercraft= 'bike';

        $result = $db->query("SELECT bikeNum,image_path FROM bikes WHERE bikes.bike_num=" . $bicycleid . " AND type='$bikeOrWatercraft'");
        $row = $result->fetch_assoc();
        $bikeNum = $row["bikeNum"];
        $result1 = $db->query("SELECT status FROM maintenance WHERE bike_id=" . $bikeNum);
        $row1 = $result1->fetch_assoc();
        $status_image_path = '';
        if($row1["status"]){
            switch ($row1["status"]){
                case 'Green':
                    $status_image_path = 'images/green.png';
                    break;
                case 'Yellow':
                    $status_image_path = 'images/yellow.png';
                    break;
                case 'Orange':
                    $status_image_path = 'images/orange.png';
                    break;
                case 'Red':
                    $status_image_path = 'images/red.png';
                    break;
            }
        }
        $jsoncontent = array("bikeNum" => $bicycleid, "image_path" => $row["image_path"],"status_image_path" => $status_image_path);
    }

    echo json_encode($jsoncontent);// TODO change to response function
}


function editprofile($userid)
{
    global $db;
    $result = $db->query("SELECT users.userId,userName,mail,number,mailingAddress,physicalAddress,city,state,zipcode WHERE users.userId=" . $userid);
    $row = $result->fetch_assoc();
    $jsoncontent = array("userid" => $row["userId"], "username" => $row["userName"], "email" => $row["mail"], "phone" => $row["number"], "mailingaddress" => $row["mailingAddress"], "physicaladdress" => $row["physicalAddress"], "city" => $row["city"], "state" => $row["state"], "zipcode" => $row["zipcode"]);
    echo json_encode($jsoncontent);// TODO change to response function
}

function edituser($userid)
{
    global $db;
    $result = $db->query("SELECT users.userId,userName,age,gender,race,mail,number,mailingAddress,physicalAddress,city,state,zipcode,privileges,userLimit,credit FROM users LEFT JOIN limits ON users.userId=limits.userId LEFT JOIN credit ON users.userId=credit.userId WHERE users.userId=" . $userid);
    $row = $result->fetch_assoc();
    $jsoncontent = array("userid" => $row["userId"], "username" => $row["userName"], "email" => $row["mail"], "age" => $row["age"], "gender" => $row["gender"], "race" => $row["race"], "phone" => $row["number"], "mailingaddress" => $row["mailingAddress"], "physicaladdress" => $row["physicalAddress"], "city" => $row["city"], "state" => $row["state"], "zipcode" => $row["zipcode"], "privileges" => $row["privileges"], "limit" => $row["userLimit"], "credit" => $row["credit"]);
    echo json_encode($jsoncontent);// TODO change to response function
}
function check_subscription($userid)
{
    global $db;
    if($userid != 0){
        $result = $db->query("SELECT * FROM payment_subscription WHERE user_id=" . $userid . " AND is_active=1");
        $subscriptionList = [];
        while ($row = $result->fetch_assoc()) {
            $data = array("user_id" => $row["user_id"], "subscription_type" => $row["subscription_type"], "payment_info" => $row["payment_info"]);
            array_push($subscriptionList,$data);
        }
        echo json_encode($subscriptionList);
        //response($subscriptionList, 0, "", 0,200);
    }else{
        response("No data found", 0, "", 0,201);
    }
}

function stripe_unsubscription($userid,$subtype)
{
    global $db;
    if($userid != 0 && isset($subtype)){
        $result = $db->query("SELECT * FROM payment_subscription WHERE user_id=" . $userid . " AND subscription_type='$subtype' AND is_active=1");
        $row = $result->fetch_assoc();
        $payment_info = $row["payment_info"];
        $id = $row["id"];
        $payment_info = json_decode($payment_info,true);
        $subid = $payment_info['sub_id'];
        //Unsubscribe Stripe
        $subscription = \Stripe\Subscription::retrieve($subid);
        $subscription->cancel_at_period_end = true;
        $subscription_cancel = $subscription->save();
        if($subscription_cancel->cancel_at_period_end){
            //update db
            if(array_key_exists('unsubscription_request', $payment_info)){
                $payment_info['unsubscription_request'] = '1';
                $payment_info_merged = json_encode($payment_info);
            }else {
                $unsubscription_request = array('unsubscription_request'=>'1');
                $payment_info_merged = array_merge($payment_info, $unsubscription_request);
                $payment_info_merged = json_encode($payment_info_merged);
            }


            $result1 = $db->query("UPDATE payment_subscription SET payment_info='$payment_info_merged' WHERE id=" . $id);
            response("Successfully unsubscribed", 0, "", 0,201);
        }else {
            response("Unsubscription failed!", 1, "", 0,500);
        }

        //$jsoncontent = array("user_id" => $row["user_id"], "customer_id" => $row["customer_id"], "customer_email" => $row["customer_email"], "subscription_type" => $row["subscription_type"], "sub_id" => $subid, "created_date" => $row["created_date"], "expiration_date" => $row["expiration_date"]);
        //echo json_encode($jsoncontent);
    }else{
        response("Something went wrong", 1, "", 0,500);
    }
}


function savevideo($videoid, $filename, $thumbnail, $file)
{

    global $db;
    $response = null;
    $thumbnail = null;
    if (!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
        response(_('Video upload failed, please select video file.'), 1, "", 1);
    } else {
        //upload thumbnail
        if (file_exists($_FILES['thumbnail']['tmp_name']) && is_uploaded_file($_FILES['thumbnail']['tmp_name'])) {
            $thumbnail = uploadImage($_FILES['thumbnail'])[1];
        }
        $response = uploadVideo($_FILES['file']);
    }
    if (!empty($filename)) {
        $path = $response[1];
        $thumbnailPath = $response[2];
        $result = $db->query("INSERT INTO videos (fileName, videoPath,thumbnailPath) VALUES ('$filename','$path','$thumbnail');");
        response(_('New video added.'));
    } else {
        response(_('Video upload failed, complete filename.'), 1, "", 1);
    }
}

function addnewvideo($filename, $file, $thumbnail)
{
    global $db;
    $response = null;
    $thumbnail = null;
    $filename = strtoupper($filename);
    if (!file_exists($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        httpResponse('Video upload failed, please select video file.', 500, "", 1);
    } else {
        //upload thumbnail
        if (file_exists($_FILES['thumbnail']['tmp_name']) && is_uploaded_file($_FILES['thumbnail']['tmp_name'])) {
            $thumbnail = uploadImage($_FILES['thumbnail'])[1];
        }
        $response = uploadVideo($_FILES['file']);
    }
    if (!empty($filename) && count($response) > 1) {
        $path = $response[1];
        $size = $response[2];
        $result = $db->query("INSERT INTO videos (fileName, videoPath,thumbnailPath, size) VALUES ('$filename','$path','$thumbnail','$size');");
        httpResponse('New video added.', 201);
    } else {
        httpResponse('Something went wrong. Video upload failed.', 500);
    }
}

function addnewbicycle($currentstand, $file,$bike_type)
{
    global $db;
    $response = null;
    if (!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
        httpResponse('Image upload failed, please select bike image', 500);
    } else {
        $response = uploadImage($_FILES["file"]);
    }
    $path = $response[1];

    //Generate bike number
    $result_bike_num = $db->query("SELECT MAX(bike_num) AS max_bike_num FROM bikes WHERE type='$bike_type'");
    $row_bike_num = $result_bike_num->fetch_assoc();
    $max_bike_num = $row_bike_num["max_bike_num"];
    $bike_num = $max_bike_num+1;

    $code = generateRandomCode(4);
    $result = $db->query("INSERT INTO bikes (bike_num,currentStand,currentCode,image_path,type) VALUES ('$bike_num','$currentstand','$code','$path','$bike_type');");

    $bikeNum = $db->insertid();
    $userId = $_COOKIE['loguserid'];
    //Insert into maintenance table
    if($bikeNum && isset($userId)){
        $created_at = DATE('y-m-d h:i:s');
        $result1 = $db->query("INSERT INTO maintenance SET bike_id=$bikeNum, total_rental=0,created_at='$created_at', updated_by=$userId, status='Green'");
    }
    $message = "New $bike_type added.";
    httpResponse($message, 201);

}


function addnewevent($currentstand,$totalrides, $file, $event_description, $startdate, $enddate, $rsvpdate)
{
    global $db;
    $response = null;
    if (!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
        httpResponse('Image upload failed, please select bike image', 500);
    } else {
        $response = uploadImage($_FILES["file"]);
    }
    $path = $response[1];

    //Generate bike number
    $result_bike_num = $db->query("SELECT MAX(event_num) AS max_event_num FROM events");
    $row_bike_num = $result_bike_num->fetch_assoc();
    $max_bike_num = $row_bike_num["max_event_num"];
    $bike_num = $max_bike_num+1;

    $code = generateRandomCode(4);
    $result = $db->query("INSERT INTO events (event_num,current_stand,total_rides,image_path,event_description,start_date,end_date,rsvp_date) VALUES ('$bike_num','$currentstand','$totalrides','$path','$event_description','$startdate','$enddate','$rsvpdate');");

//    $bikeNum = $db->insertid();
//    $userId = $_COOKIE['loguserid'];
//    //Insert into maintenance table
//    if($bikeNum && isset($userId)){
//        $created_at = DATE('y-m-d h:i:s');
//        $result1 = $db->query("INSERT INTO maintenance SET bike_id=$bikeNum, total_rental=0,created_at='$created_at', updated_by=$userId, status='Green'");
//    }
    $message = "New event added.";
    httpResponse($message, 201);

}

function addnewplace($name,$description,$image,$latitude,$longitude,$link,$type){
    global $db;
    $response = null;
    if (!file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
        httpResponse('Image upload failed, please select '.$type.' image', 500);
    } else {
        $response = uploadImage($_FILES["image"]);
    }
    $path = $response[1];
    $result = $db->query("INSERT INTO places (name,description,photo,latitude,longitude,link,type) VALUES ('$name','$description','$path','$latitude','$longitude','$link','$type');");
    $message = "New ".$type." added.";
    httpResponse($message, 201);
}

function saveplace($id, $name, $description, $image, $latitude, $longitude, $link)
{
    global $db;
    $response = null;
    if (empty($name)) {
        httpResponse("Name cannot be empty.", 500);
        return 0;
    }
    if (empty($description)) {
        httpResponse("Description cannot be empty.", 500);
        return 0;
    }
    if (!file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
        $response = array("success", "notset");
    } else {
        $response = uploadImage($_FILES["image"]);
    }
    if (is_array($response)) {
        $path = $response[1];
        $result = null;
        if ($path == "notset") {
            $result = $db->query("UPDATE places SET name='$name',description='$description',latitude='$latitude',longitude='$longitude',link='$link' WHERE id=" . $id);
        } else {
            $result = $db->query("UPDATE places SET name='$name',description='$description',latitude='$latitude',longitude='$longitude',link='$link',photo='$path' WHERE id=" . $id);
        }
        httpResponse(_('Arrowhead ') . "(" . $id . ")" . $name . " " . _('updated') . ".", 201);

    } else {
        if ($response == "failed") {
            httpResponse("Image could not be uploaded", 500);
        } else {
            httpResponse('Image format not allowed', 500);
        }
    }

}


function addnewstand($standname, $description, $standtype, $standdescription, $longitude, $latitude, $file)
{
    global $db;
    $response = null;
    $standname = strtoupper($standname);
    $description = strtoupper($description);
    $standdescription = strtoupper($standdescription);
    if (!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
        httpResponse('Image upload failed, please select bike image', 500);
    } else {
        $response = uploadImage($_FILES["file"]);
    }
    if (!empty($standname) && !empty($description) && !empty($standdescription) && !empty($longitude) && !empty($latitude)) {
        $path = $response[1];
        //serviceTag = 0 : used in front end stand dropdown query
        //servoceTag default value updated to 0; Earliar it was Null

        $result = $db->query("INSERT INTO stands (standName,standAddress,type,standDescription,longitude,latitude,standPhoto) VALUES ('$standname','$description','$standtype','$standdescription','$longitude','$latitude','$path');");
        httpResponse('New stand added.', 201);
    } else {
        httpResponse('Operation failed, please complete all fields.', 500);
    }
}

function savestand($standid, $standname, $description, $standdescription, $type, $active, $longitude, $latitude, $file)
{
    global $db;
    $response = null;
    $standname = strtoupper($standname);
    $description = strtoupper($description);
    $standdescription = strtoupper($standdescription);
    if (empty($standname)) {
        httpResponse("Stand name cannot be empty.", 500);
        return 0;
    }
    if (empty($standdescription)) {
        httpResponse("Place name cannot be empty.", 500);
        return 0;
    }
    if (!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
        $response = array("success", "notset");
    } else {
        $response = uploadImage($_FILES["file"]);
    }
    if (is_array($response)) {
        $path = $response[1];
        $result = null;
        if ($path == "notset") {
            $result = $db->query("UPDATE stands SET standName='$standname',standAddress='$description',standDescription='$standdescription',type='$type', active='$active',longitude='$longitude',latitude='$latitude' WHERE standId=" . $standid);
        } else {
            $result = $db->query("UPDATE stands SET standName='$standname',standAddress='$description',standDescription='$standdescription',type='$type', active='$active',longitude='$longitude',latitude='$latitude',standPhoto='$path' WHERE standId=" . $standid);
        }
        httpResponse(_('Stand ') . " (" . $standid . ") " . $standname . " " . _('updated') . ".", 201);

    } else {
        if ($response == "failed") {
            httpResponse("Image could not be uploaded", 500);
        } else {
            httpResponse('Image format not allowed', 500);
        }
    }

}

function deletevideo($deleteid)
{
    global $db;
    $result = $db->query("DELETE FROM  videos  WHERE videoId=" . $deleteid);
    response(_('Video ') . $deleteid . (' deleted from system.'));
}

function deletebicycle($deleteid,$biketype="")
{
    global $db;
    if($biketype=='event'){
        $result = $db->query("UPDATE events SET is_active='0' WHERE id=" . $deleteid);
    }else {
        $result = $db->query("UPDATE bikes SET active='N' WHERE bikeNum=" . $deleteid);
    }
//    response(_('Bike ') . $deleteid . (' decomissioned.'));
    $bikeCap='Bike';
    if($biketype !=""){
        if($biketype == 'bike'){
            $bikeCap='Bike';
        }else if($biketype == 'watercraft'){
            $bikeCap='Watercraft';
        }else if($biketype == 'event'){
            $bikeCap='Event';
        }
    }
    httpResponse($bikeCap  . " " . $deleteid . " " . 'decomissioned.', 403);
}

function closeinquiry($inquiryid)
{
    global $db;
    $result = $db->query("UPDATE inquiries SET solved='Y' WHERE inquiryid=" . $inquiryid);
    response(_('Inquiry ') . $inquiryid . _(' closed.'));
}

function deletestand($deleteid)
{
    global $db;
    $result = $db->query("UPDATE stands SET active='N' WHERE standId=" . $deleteid);
    response(_('Stand ') . $deleteid . (' decomissioned.'));
}

function savebicycle($bicycleid, $currentstand, $file, $note, $status, $bike_num,$bike_type)
{
    global $db;
    $response = null;
    $note = strtoupper($note);
    if (!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
        $response = array("success", "notset");
    } else {
        $response = uploadImage($_FILES["file"]);
    }

    //Bike number should be unique
    $result_bike_num = $db->query("SELECT * FROM bikes WHERE bike_num=".$bike_num." AND bikeNum !=".$bicycleid ." AND type='$bike_type'");
    $is_unique = ($result_bike_num->num_rows == 0) ? 1 : 0;

    if (is_array($response) && ($is_unique)) {
        $path = $response[1];
        $result = null;
        if ($path == "notset") {
            if (!empty($note))
                $result = $db->query("UPDATE bikes SET bike_num=$bike_num, currentStand='$currentstand',note='$note',active='$status'  WHERE bikeNum=" . $bicycleid);
            else
                $result = $db->query("UPDATE bikes SET bike_num=$bike_num, currentStand='$currentstand',active='$status'  WHERE bikeNum=" . $bicycleid);
        } else {
            if (!empty($note))
                $result = $db->query("UPDATE bikes SET bike_num=$bike_num, currentStand='$currentstand',image_path='$path',note='$note',active='$status'  WHERE bikeNum=" . $bicycleid);
            else
                $result = $db->query("UPDATE bikes SET bike_num=$bike_num, currentStand='$currentstand',image_path='$path' ,active='$status'  WHERE bikeNum=" . $bicycleid);
        }
        $bike_typeCap = 'Bike';
        if($bike_type == 'bike')  $bike_typeCap = 'Bike';
        else if($bike_type == 'watercraft')  $bike_typeCap = 'Watercraft';
        httpResponse($bike_typeCap  . " " . $bicycleid . " " . 'updated', 201);
    } else {
        if ($response == "failed") {
            httpResponse('Image could not be uploaded', 500);
        } else if($is_unique == 0) {
            httpResponse('Duplicate bike number not allowed', 500);
        } else {
            httpResponse('Image format not allowed', 500);
        }
    }
}


function saveevent($bicycleid, $currentstand,$totalrides, $file, $note, $status, $bike_num,$bike_type,$startdate,$enddate,$rsvpdate)
{
    global $db;
    $response = null;
    $note = strtoupper($note);
    if (!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
        $response = array("success", "notset");
    } else {
        $response = uploadImage($_FILES["file"]);
    }

    //Bike number should be unique
    $result_bike_num = $db->query("SELECT * FROM events WHERE event_num=".$bike_num." AND id !=".$bicycleid);
    $is_unique = ($result_bike_num->num_rows == 0) ? 1 : 0;

    if (is_array($response) && ($is_unique)) {
        $path = $response[1];
        $result = null;
        if ($path == "notset") {
            if (!empty($note))
                $result = $db->query("UPDATE events SET event_num=$bike_num, current_stand='$currentstand',total_rides='$totalrides',event_description='$note',is_active='$status', start_date='$startdate', end_date='$enddate', rsvp_date='$rsvpdate' WHERE id=" . $bicycleid);
            else
                $result = $db->query("UPDATE events SET event_num=$bike_num, current_stand='$currentstand',total_rides='$totalrides',is_active='$status', start_date='$startdate', end_date='$enddate', rsvp_date='$rsvpdate' WHERE id=" . $bicycleid);
        } else {
            if (!empty($note))
                $result = $db->query("UPDATE events SET event_num=$bike_num, current_stand='$currentstand',total_rides='$totalrides',image_path='$path',event_description='$note',is_active='$status', start_date='$startdate', end_date='$enddate', rsvp_date='$rsvpdate' WHERE id=" . $bicycleid);
            else
                $result = $db->query("UPDATE events SET event_num=$bike_num, current_stand='$currentstand',total_rides='$totalrides',image_path='$path' ,is_active='$status', start_date='$startdate', end_date='$enddate', rsvp_date='$rsvpdate' WHERE id=" . $bicycleid);
        }

        httpResponse("Event " . $bicycleid . " " . 'updated', 201);
    } else {
        if ($response == "failed") {
            httpResponse('Image could not be uploaded', 500);
        } else if($is_unique == 0) {
            httpResponse('Duplicate bike number not allowed', 500);
        } else {
            httpResponse('Image format not allowed', 500);
        }
    }
}

function saveinquiry($userid, $phone, $email, $inquiry)
{
    global $db;
    $username = "Anonymous";
    $inquiry = $db->conn->real_escape_string($inquiry);
    $email = $db->conn->real_escape_string($email);
    $result = $db->query("INSERT INTO inquiries (userid,phone,email,inquiry,solved) VALUES ($userid,'$phone','$email','$inquiry','N')");
    if ($userid != 0) {
        $result2 = $db->query("SELECT userName,number,mail FROM users WHERE users.userId = " . $userid);
        $row = $result2->fetch_assoc();
        $username = $row["userName"];
        $phone = $row["number"];
        $email = $row["mail"];
    }
    $message = _('Inquiry by User : ') . $username . _(', Phone : ') . $phone . _(', Email : ') . $email . "\n";
    $message = $message . _('Inquiry description') . "\n";
    $message = $message . $inquiry . "\n\n";
    $message = $message . _('Please check admin console for more details');
    notifyAdmins($message, 1);
    response(_('Reported issue : </br><b>') . $inquiry . _('</b></br> send to helpdesk successfully.</br> We will get back to you, thank you.'));
}

function savehelp()
{
    global $db, $systemname;
    $inquiryId = $_REQUEST["inquiryid"];
    $answer = $db->conn->real_escape_string($_REQUEST["answer"]);
    $current_date = date('Y-m-d H:i:s');
    $result = $db->query("UPDATE inquiries SET answer='$answer', solved='Y', updated_at='$current_date' where inquiryid='$inquiryId'");
    $userResult = $db->query("SELECT help.userid, help.phone, help.email, help.inquiry, help.answer, u.mail, u.userName, u.number FROM inquiries help LEFT JOIN users u ON help.userid=u.userId WHERE help.inquiryid='$inquiryId'");
    $user = $userResult->fetch_assoc();
    if($user['userid'] == 0){
        $userEmail = $user['email'];
        $name_or_email = $user['email'];
        $phonenumber = $user['phone'];
    }else{
        $userEmail = $user['mail'];
        $name_or_email = $user['userName'];
        $phonenumber = $user['number'];
    }
    $text = $systemname." Q/A: plesae check your email or bikeshare account help section";
    sendSMS($phonenumber, $text);
    $subject = $systemname." Q/A";
    $emailMassage = '<html><body><div><h3>Hi ' . $name_or_email . ',</h3>
        <p>Admin has answered your question</p>
        <p><b>Question:</b> ' . $user["inquiry"] . '</p>
        <p><b>Answer:</b> ' . $user["answer"] . '</p>
        <p>Thanks<br>'.$systemname.'</p></div></body></html>';
    sendEmail($userEmail, $subject, $emailMassage);
    httpResponse("Answer replied successfully", 200);
}

function saveprofile($userid, $username, $email, $age, $gender, $race, $mailingaddress, $physicaladdress, $city, $state, $zipcode)
{
    global $db;
    $result = $db->query("UPDATE users SET username='$username',mail='$email',age='$age',gender='$gender',race='$race',mailingAddress='$mailingaddress',physicalAddress='$physicaladdress',city='$city',state='$state',zipcode='$zipcode' WHERE userId=" . $userid);
    response($username . ", " . _('your details have been updated') . ".");
}

function saveuser($userid, $username, $email, $mailingaddress, $physicaladdress, $city, $state, $zipcode, $phone, $privileges, $limit, $age, $gender, $race)
{
    $returnFlg = false;
    global $db;
    $result = $db->query("UPDATE users SET username='$username',mail='$email',mailingAddress='$mailingaddress',physicalAddress='$physicaladdress',city='$city',state='$state',zipcode='$zipcode',privileges='$privileges' ,age='$age',gender='$gender',race='$race'  WHERE userId=" . $userid);
    if ($result) {
        if ($phone) {
            $result = $db->query("UPDATE users SET number='$phone' WHERE userId=" . $userid);
            if ($result) {
                $result = $db->query("UPDATE limits SET userLimit='$limit' WHERE userId=" . $userid);
            }
        }
        $returnFlg = true;
    }
    if ($returnFlg) {
        httpResponse(_('Details of user') . " " . $username . " " . _('updated') . ".", 201);
    } else {
        httpResponse("Something went wrong", 500);
    }
}

function save_new_user($username, $email, $mailingaddress, $physicaladdress, $city, $state, $zipcode, $phone, $privileges, $limit, $age, $gender, $race)
{
    $returnFlg = false;
    global $db;
    try {
        $default_password = "123456";
        $query = "INSERT INTO users (username, mail,number,password, mailingAddress,physicalAddress,city,state,zipcode,privileges,age,gender,race)
                VALUES ('$username', '$email', '$phone',SHA2('$default_password',512),'$mailingaddress','$physicaladdress','$city','$state','$zipcode','$privileges','$age','$gender','$race')";
        $result = $db->query($query);
        if ($result) {
            $user_id = $db->insertid();
            $query_limit = "INSERT INTO limits (userLimit,userId) VALUES ('$limit','$user_id')";
            $db->query($query_limit);
            $returnFlg = true;
        }
        if ($returnFlg) {
            httpResponse(_('Details of user') . " " . $username . " " . _('added') . ".", 201);
        } else {
            httpResponse("Something went wrong", 500);
        }
    }catch (Exception $e){
        httpResponse($e->getMessage(), 500);
    }
}

function addcredit($userid, $creditmultiplier)
{
    global $db, $credit;
    $requiredcredit = $credit["min"] + $credit["rent"] + $credit["longrental"];
    $addcreditamount = $requiredcredit * $creditmultiplier;
    $result = $db->query("UPDATE credit SET credit=credit+" . $addcreditamount . " WHERE userId=" . $userid);
    $result = $db->query("INSERT INTO history SET userId=$userid,action='CREDITCHANGE',parameter='" . $addcreditamount . "|add+" . $addcreditamount . "'");
    $result = $db->query("SELECT userName FROM users WHERE users.userId=" . $userid);
    $row = $result->fetch_assoc();
    response(_('Added') . " " . $addcreditamount . $credit["currency"] . " " . _('credit for') . " " . $row["userName"] . ".");
}

function generatecoupons($multiplier)
{
    global $db, $credit;
    if (iscreditenabled() == FALSE) return; // if credit system disabled, exit
    $requiredcredit = $credit["min"] + $credit["rent"] + $credit["longrental"];
    $value = $requiredcredit * $multiplier;
    $codes = generatecodes(10, 6);
    foreach ($codes as $code) {
        $result = $db->query("INSERT IGNORE INTO coupons SET coupon='" . $code . "',value='" . $value . "',status='0'");
    }
    response(_('Generated 10 new') . ' ' . $value . ' ' . $credit["currency"] . ' ' . _('coupons') . '.', 0, array("coupons" => $codes));
}

function sellcoupon($couponid)
{
    global $db, $credit;
    if (iscreditenabled() == FALSE) return; // if credit system disabled, exit
    $result = $db->query("UPDATE coupons SET status='1' WHERE id='" . $couponid . "'");
    response('Coupon sold successfully',0,'',1,201);
}

function validatecoupon($userid, $coupon)
{
    global $db, $credit;
    if (iscreditenabled() == FALSE) return; // if credit system disabled, exit
    $result = $db->query("SELECT coupon,value FROM coupons WHERE coupon='" . $coupon . "' AND status<'2'");
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $value = $row["value"];
        $result = $db->query("UPDATE credit SET credit=credit+'" . $value . "' WHERE userId='" . $userid . "'");
        $result = $db->query("INSERT INTO history SET userId=$userid,action='CREDITCHANGE',parameter='" . $value . "|add+" . $value . "|" . $coupon . "'");
        $result = $db->query("UPDATE coupons SET status='2' WHERE coupon='" . $coupon . "'");
        response('+' . $value . ' ' . $credit["currency"] . '. ' . _('Coupon') . ' ' . $coupon . ' ' . _('has been redeemed') . '.');
    }
    response(_('Invalid coupon, try again.'), 1);
}

function resetpassword($number)
{
    global $db, $systemname, $systemrules, $systemURL;
    $result = $db->query("SELECT mail,userName FROM users WHERE number='$number'");
    if (!$result->num_rows) response(_('No such user found.'), 1);
    $row = $result->fetch_assoc();
    $email = $row["mail"];
    $username = $row["userName"];

    $subject = _('Password reset');

    mt_srand(crc32(microtime()));
    $password = substr(md5(mt_rand() . microtime() . $email), 0, 8);

    $result = $db->query("UPDATE users SET password=SHA2('$password',512) WHERE number='" . $number . "'");

    $names = preg_split("/[\s,]+/", $username);
    $firstname = $names[0];
    $message = _('Hello') . ' ' . $firstname . ",\n\n" .
        _('Your password has been reset successfully.') . "\n\n" .
        _('Your new password is:') . "\n" . $password;

    sendEmail($email, $subject, $message);
    response(_('Your password has been reset successfully.') . ' ' . _('Check your email.'));
}

function mapgetstandmarkers()
{
    global $db;
    $jsoncontent = array();
    $result = $db->query("SELECT standId,standName,standAddress,standPhoto,standDescription,longitude as lon, latitude as lat FROM stands where active='Y'");
    while ($row = $result->fetch_assoc()) {
        $jsoncontent[] = $row;
    }
    echo json_encode($jsoncontent); // TODO proper response function
}

function mapgetmarkers()
{
    global $db;
    $usereventssql = ' ' . excludeusereventssql('events.event_num');

    $jsoncontent = array();
    $stands = array();
    $result = $db->query("SELECT standId,count(bikeNum) AS bikecount,count(events.id) as eventcount,standAddress,standName,standPhoto,longitude AS lon, latitude AS lat, stands.type AS standtype FROM stands LEFT JOIN bikes on bikes.currentStand=stands.standId AND bikes.active='Y' LEFT JOIN events on events.current_stand=stands.standId AND events.is_deleted=0 AND events.is_active=1  AND events.rsvp_date >= CURDATE() $usereventssql WHERE stands.serviceTag=0 AND stands.active='Y'  GROUP BY standName ORDER BY standName");
    while ($row = $result->fetch_assoc()) {
        //check type
        $stands[] = $row;
    }
    $jsoncontent['stands'] = $stands;
    $places = array();
    $result = $db->query("SELECT id, name, description, latitude AS lat, longitude AS lon, link, type, photo FROM places");
    while ($row = $result->fetch_assoc()) {
        //check type
        $places[] = $row;
    }
    $jsoncontent['places'] = $places;
    echo json_encode($jsoncontent); // TODO proper response function
}

function mapgetlimit($userId)
{
    global $db;

    if (!isloggedin()) response("");
    $result = $db->query("SELECT count(*) as countRented FROM bikes where currentUser=$userId");
    $row = $result->fetch_assoc();
    $rented = $row["countRented"];

    $result = $db->query("SELECT userLimit FROM limits where userId=$userId");
    $row = $result->fetch_assoc();
    $limit = $row["userLimit"];

    $currentlimit = $limit - $rented;

    $usercredit = 0;
    $usercredit = getusercredit($userId);

    echo json_encode(array("limit" => $currentlimit, "rented" => $rented, "usercredit" => $usercredit));
}

function mapgeolocation($userid, $lat, $long)
{
    global $db;

    $result = $db->query("INSERT INTO geolocation SET userId='$userid',latitude='$lat',longitude='$long'");

    response("");

}

function resetPasswordSendLink($email)
{
    global $db, $systemURL, $systemname;
    $resultUser = $db->query("SELECT * FROM users where mail='$email'");
    if ($resultUser->num_rows == 1) {
        $user = $resultUser->fetch_assoc();
        $user_id = $user["userId"];
        $created_at = date("Y-m-d H:i:s");
        $updated_at = date("Y-m-d H:i:s");
        $expired_at = date('Y-m-d H:i:s', strtotime($created_at . ' + 1 days'));
        $resultRequest = $db->query("SELECT * FROM password_reset_request where user_id='$user_id' AND already_used=0 AND expired_at > '$created_at'");
        if ($resultRequest->num_rows > 0) {
            $request = $resultRequest->fetch_assoc();
            $request_id = $request["id"];
            $db->query("UPDATE password_reset_request SET expired_at='$expired_at', updated_at='$updated_at' WHERE id=$request_id");
            $url = $systemURL . "reset-password.php?q=" . $request["hash_code"];
        } else {
            $hash_code = uniqid("reset-");
            $db->query("INSERT INTO password_reset_request(hash_code, created_at, updated_at, expired_at, user_id) values('$hash_code','$created_at', '$updated_at', '$expired_at', $user_id)");
            $url = $systemURL . "reset-password.php?q=" . $hash_code;
        }
        $subject = $systemname . " reset password";
        $emailMassage = '<html><body><div><h3>Hi, ' . $user["userName"] . '</h3>
        <p>You recently requested to reset your password for your '.$systemname.' accout. Click the button bellow to reset it.</p>
        <a href="' . $url . '" style="font: bold 1em Arial;text-decoration: none;background-color: #006d8a;color: #f1f1f1;padding: 12px 14px 12px 14px;display: block;max-width: 200px;text-align: center;margin: 0 auto;border-radius: 4px;">Reset your password</a>
        <p>If you did not request a password reset, please ignore this email or reply to let us know. This password is only valid for next 24 hours.</p>
        <p>Thanks<br>'.$systemname.'</p></div></body></html>';
        sendEmail($email, $subject, $emailMassage);
        response("Reset password request sent successfully");
    } else {
        response("Email does not belong ta any account.");
    }
}

function resetPasswordMethod($newPassword, $hashKey)
{
    global $db;
    $queryResult = $db->query("SELECT id,user_id FROM password_reset_request where hash_code='$hashKey'");
    if ($queryResult->num_rows == 1) {
        $request = $queryResult->fetch_assoc();
        $user_id = $request["user_id"];
        $request_id = $request["id"];
        $db->query("UPDATE users SET password=SHA2('$newPassword',512) WHERE userId=$user_id");
        $db->query("UPDATE password_reset_request SET already_used=1 WHERE id='$request_id'");
        response("Password reset successfully.");
    } else {
        response("User not found.");
    }
}

function getuserhelp($userid)
{
    global $db;
    if($userid != 0){
        $result = $db->query("SELECT inquiry, answer, solved FROM inquiries WHERE userid='$userid' ORDER BY inquiryid DESC LIMIT 0 , 10");
        $helpList = [];
        while ($row = $result->fetch_assoc()) {
            $data = array('question' => $row['inquiry'], 'answer' => $row['answer'], 'solved' => $row['solved']);
            array_push($helpList,$data);
        }
        response($helpList, 0, "", 0,200);
    }else{
        response("No data found", 0, "", 0,201);
    }

}

function maintenance_setting($POST){
    global $db;
    if (!isset($POST['totalRent']) || !is_numeric($POST['totalRent'])) {
        httpResponse('Number of rentals must be numeric', 500, "", 1);
    } else {
        $totalRent = $POST['totalRent'];
        $userId = $_COOKIE['loguserid'];
        try{
            if(isset($userId) && is_numeric($userId) ){
                $total_rent = $db->query("SELECT * FROM setting WHERE setting_key='total_rent'");

                if($total_rent->num_rows > 0){
                    $result = $db->query("UPDATE setting SET setting_value=$totalRent, updated_by=$userId, updated_at=DATE('y-m-d h:i:s') WHERE setting_key='total_rent'");
                }else {
                    $result = $db->query("INSERT INTO setting SET setting_key='total_rent',setting_value=$totalRent, updated_by=$userId,created_at=DATE('y-m-d h:i:s') ");
                }
                httpResponse('Number of rentals successfully added', 201, "", 1);
            }
            else {
                httpResponse('Access denied!', 500, "", 1);
            }


        }catch (Exception $exception){
            httpResponse('Something went wrong!', 500, "", 1);
        }
    }
}

function get_maintenance_settings(){
    global $db;

    try{
        $total_rent = $db->query("SELECT * FROM setting WHERE setting_key='total_rent'");
        if($total_rent->num_rows > 0){
            $request = $total_rent->fetch_assoc();
            $setting_value = $request['setting_value'];
            response($setting_value, 0, "", 0,200);
        }else {
            httpResponse('Maintenance settings yet to set!', 201, "", 1);
        }
    }catch (Exception $exception){
        httpResponse('Something went wrong!', 500, "", 1);
    }

}

function changeUserPassword(){
    global $db, $systemname;
    $userid = $_POST['userid'];
    $password = $_POST['password'];
    $sendEmail = $_POST['send_email'];
    $db->query("UPDATE users SET password=SHA2('$password',512) WHERE userId=$userid");
    $subject = $systemname." reset password";
    if($sendEmail == 'true'){
        $resultUser = $db->query("SELECT * FROM users where userId='$userid'");
        if ($resultUser->num_rows == 1) {
            $user = $resultUser->fetch_assoc();
            $emailMassage = '<html><body><div><h3>Hi, ' . $user["userName"] . '</h3>
            <p>Your Password has been changed.</p>
            <p>Your new password is <b>'.$password.'</b></p>
            <p>Thanks<br>'.$systemname.'</p></div></body></html>';
            sendEmail($user["mail"], $subject, $emailMassage);
            response("Password Change successfully and send to user",0,"",0,200);
        }else{
            response("Something went wrong",1,"",0,500);
        }
    }else{
        response("Password Change successfully",0,"",0,200);
    }
}

//function notReturnInfo($bikeNum){
//    global $db;
//    $result=$db->query("SELECT currentUser,currentCode,currentStand FROM bikes WHERE bikeNum=$bikeNum");
//    $row=$result->fetch_assoc();
//    $currentCode=sprintf("%04d",$row["currentCode"]);
//    $newCode=sprintf("%04d",rand(100,9900)); //do not create a code with more than one leading zero or more than two leading 9s (kind of unusual/unsafe).
//    $message='<h3>'._('Bike').' '.$bikeNum.': <span class="label label-primary">'._('Open with code').' '.$currentCode.'.</span></h3>'._('Change code immediately to').' <span class="label label-default">'.$newCode.'</span><br />'._('(open, rotate metal part, set new code, rotate metal part back)').'.';
//    response($message);
//}

// TODO for admins: show bikes position on map depending on the user (allowed) geolocation, do not display user bikes without geoloc

?>