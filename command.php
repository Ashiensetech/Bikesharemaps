<?php
require("config.php");
require("db.class.php");
require('actions-web.php');

$db = new Database($dbserver, $dbuser, $dbpassword, $dbname);
$db->connect();

if (isset($_COOKIE["loguserid"])) $userid = $_COOKIE["loguserid"];
else $userid = 0;
if (isset($_COOKIE["logsession"])) $session = $_COOKIE["logsession"];
$action = "";
if (isset($_GET["action"])) $action = trim($_GET["action"]);


switch ($action) {
    case "smscode":
        $number = trim($_GET["number"]);
        smscode($number);
        break;
    case "register":
        $number = trim($_GET["validatednumber"]);
        $smscode = trim($_GET["smscode"]);
        $checkcode = trim($_GET["checkcode"]);
        $fullname = trim($_GET["fullname"]);
        $useremail = trim($_GET["useremail"]);
        $userage = trim($_GET["userage"]);
        $usergender = trim($_GET["usergender"]);
        $userrace = trim($_GET["userrace"]);
        $password = trim($_GET["password"]);
        $password2 = trim($_GET["password2"]);
        $existing = trim($_GET["existing"]);
        $mailingaddress = trim($_GET["mailingaddress"]);
        $physicaladdress = trim($_GET["physicaladdress"]);
        $city = trim($_GET["city"]);
        $state = trim($_GET["state"]);
        $status = trim($_GET["status"]);
        $status = 'active'; //no more pending as we use smscode
        $zipcode = trim($_GET["zipcode"]);
        register($number, $smscode, $checkcode, $fullname, $useremail, $userage, $usergender, $userrace, $password, $password2, $existing, $mailingaddress, $physicaladdress, $city, $state, $status, $zipcode);
        break;
//    case "register_paypal_return":
//        $paypal_amt = trim($_GET["paypal_amt"]);
//        $paypal_cc = trim($_GET["paypal_cc"]);
//        $paypal_cm = trim($_GET["paypal_cm"]);
//        $paypal_item_name = trim($_GET["paypal_item_name"]);
//        $paypal_item_number = trim($_GET["paypal_item_number"]);
//        $paypal_st = trim($_GET["paypal_st"]);
//        $paypal_tx = trim($_GET["paypal_tx"]);
//        $paypal_info = trim($_GET["paypal_info"]);
//        register_paypal_return($paypal_amt, $paypal_cc, $paypal_cm, $paypal_item_name, $paypal_item_number, $paypal_st, $paypal_tx,$paypal_info);
//        break;
    case "login":
        $number = trim($_POST["number"]);
        $password = trim($_POST["password"]);
        login($number, $password);
        break;
    case "logout":
        logout();
        break;
    case "resetpassword":
        resetpassword($_GET["number"]);
        break;
    case "list":
        $stand = trim($_GET["stand"]);
        listbikes($stand);
        break;
    case "listbytype":
        $stand = trim($_GET["stand"]);
        $standtype = trim($_GET["standtype"]);
        listbikesbytype($stand, $standtype);
        break;
    case "rent":
        logrequest($userid, $action);
        checksession();
        $bikeno = trim($_GET["bikeno"]);
//        checkbikeno($bikeno);
        checkeditablebikeno($bikeno);
        rent($userid, $bikeno);
       break;

    case "rentbytype":
        logrequest($userid, $action);
        checksession();
        $bikeno = trim($_GET["bikeno"]);
        $biketype = trim($_GET["biketype"]);
//        checkbikeno($bikeno);
        checkeditablebikeno($bikeno);
        rentbytype($userid, $bikeno,$biketype);
        break;

    case "rentevent":
        logrequest($userid, $action);
        checksession();
        $bikeno = trim($_GET["bikeno"]);
//        checkbikeno($bikeno);
        checkeditableeventno($bikeno);
        rentevent($userid, $bikeno);
        break;
//   case "notreturn":
//      checksession();
//      $bikeno=trim($_GET["bikeno"]);
//      checkbikeno($bikeno);
//      notReturnInfo($bikeno);
//      break;
    case "return":
        logrequest($userid, $action);
        checksession();
        $bikeno = trim($_GET["bikeno"]);
        $stand = trim($_GET["stand"]);
        $note = "";
        if (isset($_GET["note"])) $note = trim($_GET["note"]);
//        checkbikeno($bikeno);
        checkeditablebikeno($bikeno);
        checkstandname($stand);
        returnBike($userid, $bikeno, $stand, $note);
        break;
    case "returnbytype":
        logrequest($userid, $action);
        checksession();
        $bikeno = trim($_GET["bikeno"]);
        $stand = trim($_GET["stand"]);
        $biketype = trim($_GET["biketype"]);
        $note = "";
        if (isset($_GET["note"])) $note = trim($_GET["note"]);
//        checkbikeno($bikeno);
        checkeditablebikeno($bikeno);
        checkstandname($stand);
        returnBikeByType($userid, $bikeno, $stand, $note,$biketype);
        break;
    case "returnevent":
        logrequest($userid, $action);
        checksession();
        $bikeno = trim($_GET["bikeno"]);
        $stand = trim($_GET["stand"]);
        $note = "";
        if (isset($_GET["note"])) $note = trim($_GET["note"]);
//        checkbikeno($bikeno);
        checkeditableeventno($bikeno);
        checkstandname($stand);
        returnEvent($userid, $bikeno, $stand, $note);
        break;
    case "validatecoupon":
        logrequest($userid, $action);
        checksession();
        $coupon = trim($_GET["coupon"]);
        validatecoupon($userid, $coupon);
        break;
    case "forcerent":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        $bikeno = trim($_GET["bikeno"]);
//        checkbikeno($bikeno);
        checkeditablebikeno($bikeno);
        rent($userid, $bikeno, TRUE);
        break;
    case "forcereturn":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        $bikeno = trim($_GET["bikeno"]);
        $stand = trim($_GET["stand"]);
        $note = "";
        if (isset($_GET["note"])) $note = trim($_GET["note"]);
//        checkbikeno($bikeno);
        checkeditablebikeno($bikeno);
        checkstandname($stand);
        returnBike($userid, $bikeno, $stand, $note, TRUE);
        break;
    case "where":
        logrequest($userid, $action);
        checksession();
        $bikeno = trim($_GET["bikeno"]);
        checkbikeno($bikeno);
        where($userid, $bikeno);
        break;
    case "removenote":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        checkbikeno($bikeno);
        removenote($userid, $bikeno);
        break;
    case "revert":
        logrequest($userid, $action);
        checksession();
        $bikeno = trim($_GET["bikeno"]);
        checkprivileges($userid);
        checkbikeno($bikeno);
        revert($userid, $bikeno);
        break;
    case "last":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        if ($_GET["bikeno"]) {
            $bikeno = trim($_GET["bikeno"]);
            checkbikeno($bikeno);
            last($userid, $bikeno);
        } else last($userid);
        break;
    case "stands":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        liststands();
        break;
    case "standsmin":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        liststandsmin();
        break;
    case "standsminbytype":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        liststandsminbytype($_GET['stand_type']);
        break;
    case "standsminbytypewithselected":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        standsminbytypewithselected($_GET['stand_type'],$_GET['bikeOrEventId']);
        break;
    case "braintree":
        braintree();
        break;
    case "userlist":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        getuserlist();
        break;
    case "videolist":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        getvideolist();
        break;
    case "inquirylist":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        getinquirylist();
        break;
    case "userstats":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        getuserstats();
        break;
    case "usagestats":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        getusagestats();
        break;
    case "edituser":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        edituser($_GET["edituserid"]);
        break;
    case "editinquiry":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        editinquiry($_GET["inquiryid"]);
        break;
    case "edithelp":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        edithelp($_REQUEST["edithelpid"]);
        break;
    case "editprofile":
        logrequest($userid, $action);
        checksession();
        edituser($_GET["edituserid"]);
        break;
    case "check_subscription":
        checksession();
        check_subscription($_GET["userid"]);
        break;
    case "stripe_unsubscription":
        checksession();
        stripe_unsubscription($_GET["userid"], $_GET["subtype"]);
        break;
    case "editvideo":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        editvideo($_GET["editvideoid"]);
        break;
    case "editbicycle":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        editbicycle($_GET["editbicycleid"]);
        break;
    case "editevent":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        editevent($_GET["editeventid"]);
        break;
    case "editplace":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        editplace($_GET["editplaceid"]);
        break;
    case "getbicyclephoto":
        logrequest($userid, $action);
        checksession();
        getbicyclephoto($_GET["bicycleid"]);
        break;
    case "getbicyclephotobytype":
        logrequest($userid, $action);
        checksession();
        getbicyclephotobytype($_GET["bicycleid"],$_GET["standtype"]);
        break;
    case "getsystemurl":
        getsystemurl();
        break;
    case "editstand":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        editstand($_GET["editstandid"]);
        break;
    case "saveuser":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        saveuser($_GET["edituserid"], $_GET["username"], $_GET["email"], $_GET["mailingaddress"], $_GET["physicaladdress"], $_GET["city"], $_GET["state"], $_GET["zipcode"], $_GET["phone"], $_GET["privileges"], $_GET["limit"], $_GET["age"], $_GET["gender"], $_GET["race"]);
        break;
    case "save_new_user":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        save_new_user($_GET["username"], $_GET["email"], $_GET["mailingaddress"], $_GET["physicaladdress"], $_GET["city"], $_GET["state"], $_GET["zipcode"], $_GET["phone"], $_GET["privileges"], $_GET["limit"], $_GET["age"], $_GET["gender"], $_GET["race"]);
        break;
    case "saveprofile":
        logrequest($userid, $action);
        checksession();
        saveprofile($_REQUEST["edituserid"], $_REQUEST["username"], $_REQUEST["email"], $_REQUEST["age"], $_REQUEST["gender"], $_REQUEST["race"], $_REQUEST["mailingaddress"], $_REQUEST["physicaladdress"], $_REQUEST["city"], $_REQUEST["state"], $_REQUEST["zipcode"]);
        break;
    case "saveinquiry":
        saveinquiry($_REQUEST["userid"], $_REQUEST["phone"], $_REQUEST["email"], $_REQUEST["inquiry"]);
        break;
    case "savehelp":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        savehelp();
        break;
    case "message":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        message($_GET["message"]);
        break;
    case "eventmessage":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        eventmessage($_GET["message"],$_GET["eventid"]);
        break;
    case "savebicycle":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        savebicycle($_POST["editbicycleid"], $_POST["currentstand"], $_POST["file"], $_POST["note"], $_POST['bike_status'] , $_POST['bike_no'], $_POST['bike_type']);
        break;
    case "saveevent":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        saveevent($_POST["editeventid"], $_POST["currentstand"],$_POST["total_rides"], $_POST["file"], $_POST["event_description"], $_POST['is_active'], $_POST['event_num'], $_POST['bike_type'], $_POST['startdate'], $_POST['enddate'], $_POST['rsvpdate']);
        break;
    case "savestand":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        savestand($_POST["editstandid"], $_POST["standname"], $_POST["description"], $_POST["standdescription"], $_POST["type"], $_POST["active"], $_POST["longitude"], $_POST["latitude"], $_POST["file"]);
        break;
    case "addnewstand":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        addnewstand($_POST["standname"], $_POST["description"], $_POST["standtype"], $_POST["standdescription"], $_POST["longitude"], $_POST["latitude"], $_POST["file"]);
        break;
    case "addnewbicycle":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        addnewbicycle($_POST["currentstand"], $_POST["file"],'bike');
        break;
    case "addnewwatercraft":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        addnewbicycle($_POST["currentstand"], $_POST["file"],'watercraft');
        break;
    case "addnewevent":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        addnewevent($_POST["currentstand"],$_POST['totalrides'], $_POST["file"],$_POST["event_description"],$_POST["startdate"],$_POST["enddate"],$_POST["rsvpdate"]);
        break;
    case "addnewplace":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        addnewplace($_POST["name"],$_POST['description'], $_POST["image"],$_POST["latitude"],$_POST["longitude"],$_POST["link"],$_POST["type"]);
        break;
    case "saveplace":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        saveplace($_POST["editplaceid"],$_POST["name"], $_POST["description"], $_POST["image"], $_POST["latitude"], $_POST["longitude"], $_POST["link"]);
        break;
    case "addnewvideo":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        addnewvideo($_POST["filename"], $_FILES["file"], $_FILES["thumbnail"]);
        break;
    case "deletebicycle":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        deletebicycle($_GET["deleteid"]);
        break;
    case "deletebicycleByType":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        deletebicycle($_GET["deleteid"],$_GET["biketype"]);
        break;
    case "closeinquiry":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        closeinquiry($_GET["inquiryid"]);
        break;
    case "deletestand":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        deletestand($_GET["deleteid"]);
        break;
    case "deletevideo":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        deletevideo($_GET["deleteid"]);
        break;
    case "addcredit":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        addcredit($_GET["edituserid"], $_GET["creditmultiplier"]);
        break;
    case "trips":
        logrequest($userid, $action);
        checksession();
        checkprivileges($userid);
        if ($_GET["bikeno"]) {
            $bikeno = trim($_GET["bikeno"]);
            checkbikeno($bikeno);
            trips($userid, $bikeno);
        } else trips($userid);
        break;
    case "userbikes":
        userbikes($userid);
        break;
    case "userevents":
        userevents($userid);
        break;
    case "generatecoupons":
        logrequest($userid, $action);
        checksession();
        generatecoupons($_GET["multiplier"]);
        break;
    case "sellcoupon":
        logrequest($userid, $action);
        checksession();
        sellcoupon($_REQUEST["couponid"]);
        break;
    case "map:standmarkers":
        mapgetstandmarkers();
        break;
    case "map:markers":
        mapgetmarkers();
        break;
    case "map:status":
        mapgetlimit($userid);
        break;
    case "map:geolocation":
        $lat = floatval(trim($_GET["lat"]));
        $long = floatval(trim($_GET["long"]));
        mapgeolocation($userid, $lat, $long);
        break;
    case "reset-password-send-link":
        $userEmail = trim($_POST["email"]);
        resetPasswordSendLink($userEmail);
        break;
    case "reset-password-form-submit":
        $newPassword = trim($_POST["password"]);
        $hashKey = trim($_POST["hashKey"]);
        resetPasswordMethod($newPassword, $hashKey);
        break;
    case "getuserhelp":
        getuserhelp($userid);
        break;
    case "maintenance-settings":
        maintenance_setting($_POST);
        break;
    case "get-maintenance-settings":
        get_maintenance_settings();
        break;
    case "change-user-password":
        checksession();
        isAdmin($userid);
        changeUserPassword();
        break;
}

?>