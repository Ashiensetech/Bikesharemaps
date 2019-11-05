<?php
require("common.php");

function response($message, $error = 0, $log = 1)
{
    global $db, $systemname, $systemURL;
    if ($log == 1 AND $message) {
        if (isset($_COOKIE["loguserid"])) {
            $userid = $db->conn->real_escape_string(trim($_COOKIE["loguserid"]));
        } else $userid = 0;
        $number = getphonenumber($userid);
        logresult($number, $message);
    }
    $db->conn->commit();
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>', $systemname, '</title>';
    echo '<base href="', $systemURL, '" />';
    echo '<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />';
    echo '<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css" />';
    if (file_exists("analytics.php")) require("analytics.php");
    echo '</head><body><div class="container">';
    if ($error) {
        echo '<div class="alert alert-danger" role="alert">', $message, '</div>';
    } else {
        echo '<div class="alert alert-success" role="alert">', $message, '</div>';
    }
    echo '</div></body></html>';
    exit;
}

function rent($userId, $bike, $force = FALSE)
{

    global $db, $forcestack, $watches, $credit;
    $stacktopbike = FALSE;
    $bikeNum = $bike;
    $requiredcredit = $credit["min"] + $credit["rent"] + $credit["longrental"];

    $creditcheck = checkrequiredcredit($userId);
    if ($creditcheck === FALSE) {
        response(_('You are below required credit') . " " . $requiredcredit . $credit["currency"] . ". " . _('Please, recharge your credit.'), ERROR);
    }
    checktoomany(0, $userId);

    $result = $db->query("SELECT count(*) as countRented FROM bikes where currentUser=$userId");
    $row = $result->fetch_assoc();
    $countRented = $row["countRented"];

    $result = $db->query("SELECT userLimit FROM limits where userId=$userId");
    $row = $result->fetch_assoc();
    $limit = $row["userLimit"];

    if ($countRented >= $limit) {
        if ($limit == 0) {
            response(_('You can not rent any bikes. Contact the admins to lift the ban.'), ERROR);
        } elseif ($limit == 1) {
            response(_('You can only rent') . " " . sprintf(ngettext('%d bike', '%d bikes', $limit), $limit) . " " . _('at once') . ".", ERROR);
        } else {
            response(_('You can only rent') . " " . sprintf(ngettext('%d bike', '%d bikes', $limit), $limit) . " " . _('at once and you have already rented') . " " . $limit . ".", ERROR);
        }
    }

    if ($forcestack OR $watches["stack"]) {
        $result = $db->query("SELECT currentStand FROM bikes WHERE bikeNum='$bike'");
        $row = $result->fetch_assoc();
        $standid = $row["currentStand"];
        $stacktopbike = checktopofstack($standid);
        if ($watches["stack"] AND $stacktopbike <> $bike) {
            $result = $db->query("SELECT standName FROM stands WHERE standId='$standid'");
            $row = $result->fetch_assoc();
            $stand = $row["standName"];
            $user = getusername($userId);
            notifyAdmins(_('Bike') . " " . $bike . " " . _('rented out of stack by') . " " . $user . ". " . $stacktopbike . " " . _('was on the top of the stack at') . " " . $stand . ".", ERROR);
        }
        if ($forcestack AND $stacktopbike <> $bike) {
            response(_('Bike') . " " . $bike . " " . _('is not rentable now, you have to rent bike') . " " . $stacktopbike . " " . _('from this stand') . ".", ERROR);
        }
    }

    $result = $db->query("SELECT currentUser,currentCode,currentStand FROM bikes WHERE bikeNum=$bikeNum");
    $row = $result->fetch_assoc();
    $currentCode = sprintf("%04d", $row["currentCode"]);
    $currentUser = $row["currentUser"];
    $bikeStand = $row["currentStand"];
    $result = $db->query("SELECT note FROM notes WHERE bikeNum='$bikeNum' ORDER BY time DESC");
    $note = "";
    while ($row = $result->fetch_assoc()) {
        $note .= $row["note"] . "; ";
    }
    $note = substr($note, 0, strlen($note) - 2); // remove last two chars - comma and space

    $newCode = sprintf("%04d", rand(100, 9900)); //do not create a code with more than one leading zero or more than two leading 9s (kind of unusual/unsafe).

    if ($currentUser == $userId) {
        response(_('You have already rented the bike') . ' ' . $bikeNum . '. ' . _('Code is') . ' <span class="label label-primary">' . $currentCode . '</span>. ' . _('Return bike by scanning QR code on a stand') . '.', ERROR);
        return;
    }
    if ($currentUser != 0) {
        response(_('Bike') . " " . $bikeNum . " " . _('is already rented') . ".", ERROR);
        return;
    }

    $message = '<h3>' . _('Bike') . ' ' . $bikeNum . ': <span class="label label-primary">' . _('Open with code') . ' ' . $currentCode . '.</span></h3>' . _('Change code immediately to') . ' <span class="label label-default">' . $newCode . '</span><br />' . _('(open, rotate metal part, set new code, rotate metal part back)') . '.';
    if ($note) {
        $message .= "<br />" . _('Reported issue:') . " <em>" . $note . "</em>";
    }

    $result = $db->query("UPDATE bikes SET currentUser=$userId,currentCode=$newCode,currentStand=NULL WHERE bikeNum=$bikeNum");
    $result = $db->query("INSERT INTO history SET userId=$userId,bikeNum=$bikeNum,action='RENT',parameter=$newCode");
    $result = $db->query("INSERT INTO activity_history SET user_id=$userId,bike_id=$bikeNum,action='rent',stand_id=$bikeStand");
    response($message);

}


function returnbike($userId, $stand)
{

    global $db, $connectors;
    $stand = strtoupper($stand);

    $result = $db->query("SELECT bikeNum FROM bikes WHERE currentUser=$userId ORDER BY bikeNum");
    $bikenumber = $result->num_rows;

    if ($bikenumber == 0) {
        response(_('You have no rented bikes currently.'), ERROR);
    } elseif ($bikenumber > 1) {
        $message = _('You have') . ' ' . $bikenumber . ' ' . _('rented bikes currently. QR code return can be used only when 1 bike is rented. Please, use web');
        if ($connectors["sms"]) $message .= _(' or SMS');
        $message .= _(' to return the bikes.');
        response($message, ERROR);
    } else {
        $result = $db->query("SELECT bikeNum,currentCode FROM bikes WHERE currentUser=$userId");
        $row = $result->fetch_assoc();
        $currentCode = sprintf("%04d", $row["currentCode"]);
        $bikeNum = $row["bikeNum"];

        $result = $db->query("SELECT standId FROM stands where standName='$stand'");
        $row = $result->fetch_assoc();
        $standId = $row["standId"];

        $result = $db->query("UPDATE bikes SET currentUser=NULL,currentStand=$standId WHERE bikeNum=$bikeNum and currentUser=$userId");

        $message = '<h3>' . _('Bike') . ' ' . $bikeNum . ': <span class="label label-primary">' . _('Lock with code') . ' ' . $currentCode . '.</span></h3>';
        $message .= '<br />' . _('Please') . ', <strong>' . _('rotate the lockpad to') . ' <span class="label label-default">0000</span></strong> ' . _('when leaving') . '.';

        $creditchange = changecreditendrental($bikeNum, $userId);
        $last_rented = $db->query("SELECT activity_time FROM `activity_history` WHERE user_id=$userId and bike_id=$bikeNum and action IN('rent') ORDER BY id DESC LIMIT 1");
        $rented_bike = $last_rented->fetch_assoc();
        $current_date = date('Y-m-d H:i:s');
        $diff = strtotime($current_date) - strtotime($rented_bike['activity_time']);
        $rental_time = abs(floor($diff / 60));
        if (iscreditenabled() AND $creditchange) $message .= '<br />' . _('Credit change') . ': -' . $creditchange . getcreditcurrency() . '.';
        $result = $db->query("INSERT INTO history SET userId=$userId,bikeNum=$bikeNum,action='RETURN',parameter=$standId");
        $result = $db->query("INSERT INTO activity_history SET user_id=$userId,bike_id=$bikeNum,action='return',stand_id=$standId,rental_time=$rental_time");
        response($message);
    }

}

function unrecognizedqrcode($userId)
{
    global $db;
    response("<h3>" . _('Unrecognized QR code action. Try scanning the code again or report this to the system admins.') . "</h3>", ERROR);
}

?>