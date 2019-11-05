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
    <script type="text/javascript" src="js/viewportDetect.js"></script>
    <script type="text/javascript" src="js/translations.php"></script>
    <script type="text/javascript" src="js/logout.js"></script>
    <script type="text/javascript" src="js/toastr.min.js"></script>
    <script type="text/javascript" src="js/jquery-confirm.js"></script>

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
    <link rel="stylesheet" type="text/css" href="css/map.css" />
    <link rel="stylesheet" type="text/css"
          href="css/toastr.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/jquery-confirm.css"/>
    <style>
        body{
            padding-top:50px;
        }
        .navbar-inverse{
            background: #006d8a !important;
            border-bottom: 2px solid #03cde3;
        }
        .navbar-inverse .navbar-brand{
            color:#fff;
        }
        .navbar-inverse .navbar-nav>li>a {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only"><?php echo _('Toggle navigation'); ?></span>
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="<?php echo $systemURL; ?>"><?php echo $systemname; ?></a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="<?php echo $systemURL; ?>"><?php echo _('Map'); ?></a></li>
                    <?php if (isloggedin()): ?>
                        <li><a class="user-logout" style="cursor:pointer;" id="logout"><?php echo _('Log out'); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>
<div class="container">

    <div class="page-header">
    <img class="pull-left" src="<?php echo $systemlogo;?>" width="40" height="40" style="margin-right:7px;"><h4 style="margin-top:0px;font-size:35px;"><?php echo _(" ").$systemname; ?></h4>
    </div>
<?php if(isloggedin()): ?>
<input type="hidden" name="userid" id="userid"  value="<?php echo $_COOKIE['loguserid']; ?>" />
<?php endif; ?>