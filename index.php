
<?php
require("config.php");
require("db.class.php");
require("actions-web.php");
include_once("analytics.php");
$db=new Database($dbserver,$dbuser,$dbpassword,$dbname);
$db->connect();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="icon" href="<?php echo $titlelogo;?>" type="image/gif" sizes="16x16">
<title><?= $systemname; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="js/bootbox.min.js"></script>
<script type="text/javascript" src="js/viewportDetect.js"></script>
<script type="text/javascript" src="js/leaflet.js"></script>
<script type="text/javascript" src="js/L.Control.Sidebar.js"></script>
<script type="text/javascript" src="js/translations.php"></script>
<script type="text/javascript" src="js/logout.js"></script>
<script type="text/javascript" src="js/functions.js"></script>
<?php
if (isset($geojson))
   {
   foreach($geojson as $url)
      {
      echo '<link rel="points" type="application/json" href="',$url,'">'."\n";
      }
   }
?>
<?php if (date("m-d")=="04-01") echo '<script type="text/javascript" src="https://maps.stamen.com/js/tile.stamen.js?v1.3.0"></script>'; ?>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrapValidator.min.css" />
<link rel="stylesheet" type="text/css" href="css/leaflet.css" />
<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="css/L.Control.Sidebar.css" />
<link rel="stylesheet" type="text/css" href="css/map.css" />
<script>
var maplat=<?php echo $systemlat; ?>;
var maplon=<?php echo $systemlong; ?>;
var mapzoom=<?php echo $systemzoom; ?>;
var standselected=0;
<?php
if (isloggedin())
   {
   echo 'var loggedin=1;',"\n";
   echo 'var priv=',getprivileges($_COOKIE["loguserid"]),";\n";
   }
else
   {
   echo 'var loggedin=0;',"\n";
   echo 'var priv=0;',"\n";
   }
if (iscreditenabled())
   {
   echo 'var creditsystem=1;',"\n";
   }
else
   {
   echo 'var creditsystem=0;',"\n";
   }
if (issmssystemenabled()==TRUE)
   {
   echo 'var sms=1;',"\n";
   }
else
   {
   echo 'var sms=0;',"\n";
   }
?>
</script>
<?php if (file_exists("analytics.php")) require("analytics.php"); ?>
</head>
<body>
<div id="map">
</div>

<div id="sidebar"><div id="overlay"></div>
<div class="row">
   <div>
   <ul class="list-inline list-cstm-top">
      <li><a href="donate.php" class="btn btn-success btn-xs btn-top-plus"  target="_blank"> <span class="glyphicon glyphicon-plus"></span> <?php echo _('Donate'); ?></a></li>
      <li><a href="help.php" class="" target="_blank"><span class="glyphicon glyphicon-question-sign"></span> <?php echo _('Help'); ?></a></li>

<?php
if (isloggedin() AND getprivileges($_COOKIE["loguserid"])>0) echo '<li><a href="admin.php"><span class="glyphicon glyphicon-cog"></span> ',_('Admin'),'</a></li>';
if (isloggedin())
   {
   echo '<li><a href="profile.php" class="" target="_blank"><span class="glyphicon glyphicon-user"></span> <span>',getusername($_COOKIE["loguserid"]),'</span></a>';
//   if (iscreditenabled()) echo ' (<span id="usercredit" title="',_('Remaining credit'),'">',getusercredit($_COOKIE["loguserid"]),'</span> ',getcreditcurrency(),' <button type="button" class="btn btn-success btn-xs btn-top-plus" id="opencredit" title="',_('Add credit'),'"><span class="glyphicon glyphicon-plus"></span></button>)<span id="couponblock"><br /><span class="form-inline"><input type="text" class="form-control input-sm" id="coupon" placeholder="XXXXXX" /><button type="button" class="btn btn-primary btn-sm" id="validatecoupon" title="',_('Confirm coupon'),'"><span class="glyphicon glyphicon-plus"></span></button></span></span></li>';
//   echo '<li><a href="command.php?action=logout" id="logout"><span class="glyphicon glyphicon-log-out"></span> ',_('Log out'),'</a></li>';
   echo '<li><a class="user-logout" id="logout" style="cursor: pointer;"><span class="glyphicon glyphicon-log-out"></span> ',_('Log out'),'</a></li>';
   }
?>
   </ul>
   </div>
   <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
   </div>
</div>
<div class="row">
   <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
   <table class="table">
   <tr>
    <td><img class="pull-left" src="<?php echo $systemlogo;?>" width="40" height="40"></td> <td><h4 style="margin-top:0px;font-size:35px;"><?php echo _(" ").$systemname; ?></h4></td>
   </tr>
   </table>
   </div>
   <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
   </div>
</div>

<?php if (!isloggedin()): ?>
<div id="loginform">
<h1>Log in</h1>
<?php
if (isset($_GET["error"]) AND $_GET["error"]==1) echo '<div class="alert alert-danger" role="alert"><h3>',_('User / phone number or password incorrect! Please, try again.'),'</h3></div>';
elseif (isset($_GET["error"]) AND $_GET["error"]==2) echo '<div class="alert alert-danger" role="alert"><h3>',_('Session timed out! Please, log in again.'),'</h3></div>';
?>
      <form method="POST" action="command.php?action=login">
      <div class="row"><div class="col-lg-12">
            <label for="number" class="control-label"><?php if (issmssystemenabled()==TRUE) echo _('Phone number:'); else echo _('User number:'); ?></label> <input type="text" name="number" id="number" class="form-control" />
       </div></div>
       <div class="row"><div class="col-lg-12">
            <label for="password"><?php echo _('Password:'); ?> <small id="passwordresetblock"></small></label> <input type="password" name="password" id="password" class="form-control" />
       </div></div><br />
       <div class="row"><div class="col-lg-12">
         <button type="submit" id="register" class="btn btn-lg btn-block btn-primary"><?php echo _('Log in'); ?></button>
        <a href="forget-password.php" id="resetpassword"><?php echo _('Forgotten? Reset password'); ?></a>
       </div>
       </div>
         </form>
         <h3 class="center">OR</h3>
         <form action="register.php">
         <div class="row"><div class="col-lg-12">
         <button type="submit" id="register" class="btn btn-lg btn-block btn-primary"><?php echo _('Register'); ?></button>
         </form>
       </div>
</div>
<?php endif; ?>

<?php if (isloggedin()): ?>
<div id="standname">
  <select id="stands" class="form-control" style="margin-bottom:15px;"></select>
  <span id="standcount"></span>
</div>
<?php endif; ?>
<div id="standinfo"></div>
<div id="standphoto"></div>
<div id="standbikes"></div>
<div class="row">
   <div class="col-lg-12">
   <div id="console">
   </div>
   </div>
</div>

<div id="bike" class="row" style="margin:0px;margin-top:5px;">
<p class="bike-name-new"></p>
<img class="bike-status-pic" src="" alt="" style="float: right;margin-top: -25px;">
<div id="eventTotalRides" style="float: right;text-align: right;font-weight: bold;margin-bottom: 20px;">
    <div class="total-rides">

    </div>

    <div class="total-bikes">

    </div>
</div>
  <table class="table table-bordered">
    <tbody>
      <tr>
        <td style="text-align:center;"><img class="bikepic" src="" width="150"/></td>
      </tr>
      <tr>
      <td style="vertical-align:middle;padding:0px;border:0px;">
          <div id="standactions">
              <button class="btn btn-primary btn-large col-lg-12" type="button" id="rent" data-biketype="bike" title="<?php echo _('Choose bike/watercraft number and rent bicycle/watercraft. You will receive a code to unlock the bike and the new code to set.'); ?>"><span class="glyphicon glyphicon-log-out"></span> <?php echo _('Rent'); ?> <span class="bikenumber badge badge-info"></span></button>
              <button class="btn btn-primary btn-large col-lg-12" type="button" id="rentevent" title="<?php echo _('Choose event number.'); ?>"><span class="glyphicon glyphicon-log-out"></span> <?php echo _('RSVP to Event'); ?> <span class="bikenumber badge badge-info"></span></button>
            </div>

            <div id="rentedbikes" style="padding:5px 0px;"></div>
            <div id="rentedevents" style="padding:5px 0px;"></div>
        </td>
      </tr>
    </tbody>
  </table>
</div>


<div class="row">

</div>
<div class="row"><div class="col-lg-12">
<br /></div></div>

<div class="row">
   <div class="">
   <div class="col-lg-12">

        <select class="form-control" id="notetext">
               <option value>--Select problem--</option>
               <option value="Password not working">Password not working</option>
               <option value="Bike is broken">Bike is broken</option>
               <option value="Something is wrong">Something is wrong</option>
        </select>
       <br>
<!--       <textarea type="text" name="notetext" id="notetext" class="form-control" placeholder="--><?php //echo _('Describe problem'); ?><!--"></textarea>-->
   </div>
   </div>
</div>
<div class="row no-margin">
   <div class="btn-group bicycleactions return-container" style="width:100%;">
   <div class="col-lg-12">
   <button type="button" class="btn btn-primary" id="return" title="<?php echo _('Return this bicycle to the selected stand.'); ?>"><span class="glyphicon glyphicon-log-in"></span> <?php echo _('Return bicycle'); ?> <span class="bikenumber badge badge-info"></span></button><br class="display-br display-br-hide" /><span class="display-br-hide">(<?php echo _('and'); ?></span> <a href="#" id="note" class="display-br-hide" title="<?php echo _('Use this link to open a text field to write in any issues with the bicycle you are returning (flat tire, chain stuck etc.).'); ?>"><?php echo _('report problem'); ?> <span class="glyphicon glyphicon-exclamation-sign"></span></a><span class="display-br-hide">)</span>
   <button type="button" class="btn btn-primary" id="returnwatercraft" title="<?php echo _('Return this watercraft to the selected stand.'); ?>"><span class="glyphicon glyphicon-log-in"></span> <?php echo _('Return watercraft'); ?> <span class="bikenumber badge badge-info"></span></button><br class="display-br display-br-hide-watercraft" /><span class="display-br-hide-watercraft">(<?php echo _('and'); ?></span> <a href="#" id="notewatercraft" class="display-br-hide-watercraft" title="<?php echo _('Use this link to open a text field to write in any issues with the watercraft you are returning.'); ?>"><?php echo _('report problem'); ?> <span class="glyphicon glyphicon-exclamation-sign"></span></a><span class="display-br-hide-watercraft">)</span>
   <button type="button" class="btn btn-primary" id="returnevent" style="display: none;" title="<?php echo _('Remove yourself from this event'); ?>">
       <span class="glyphicon glyphicon-log-in"></span> <?php echo _('Cancel RSVP'); ?>
       <span class="bikenumber badge badge-info"></span>
   </button>
   </div></div>
</div>

  <?php if (isloggedin()): ?>
  <div class="row">
      <div class="watch-container">
      <a href="user_video.php" class="btn" target="_blank"><span class="glyphicon glyphicon-film"></span>
          Watch Tutorials</a>
      </div>
  </div>
  <?php endif; ?>


</div>
</body>
</html>
