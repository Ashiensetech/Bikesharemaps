<?php
if (!isset($_GET['id']) || !(is_numeric($_GET['id']))){
    header("Location: admin.php");
}

require("config.php");
require("db.class.php");
require('actions-web.php');

$db = new Database($dbserver, $dbuser, $dbpassword, $dbname);
$db->connect();


checksession();
if (getprivileges($_COOKIE["loguserid"]) <= 0) exit(_('You need admin privileges to access this page.'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $systemname; ?><?php echo _('administration'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/dataTables.responsive.min.js"></script>

    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrapValidator.min.js"></script>
    <script type="text/javascript" src="js/translations.php"></script>
    <script type="text/javascript" src="js/jquery-confirm.js"></script>
    <script type="text/javascript" src="js/logout.js"></script>
    <script type="text/javascript" src="js/admin.js"></script>

    <script src="js/jquery.fancybox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script type="application/javascript" src="js/daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="js/toastr.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrapValidator.min.css"/>

    <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="css/responsive.bootstrap.min.css"/>

    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/jquery-confirm.css"/>
    <link rel="stylesheet" type="text/css" href="css/map.css"/>
    <link rel="stylesheet" href="css/jquery.fancybox.min.css"/>
    <link rel="stylesheet" href="css/custom.min.css"/>
    <link href="js/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css"
          href="css/toastr.min.css"/>
    <script type="application/javascript"
            src="js/Chart.js/Chart.bundle.min.js"></script>
    <script type="application/javascript" src="js/Chart.js/Chart.min.js"></script>
    <?php if (file_exists("analytics.php")) require("analytics.php"); ?>
    <script>
        <?php
        if (iscreditenabled()) {
            echo 'var creditenabled=1;', "\n";
            echo 'var creditcurrency="', $credit["currency"], '"', ";\n";
            $requiredcredit = $credit["min"] + $credit["rent"] + $credit["longrental"];
        } else {
            echo 'var creditenabled=0;', "\n";
        }
        ?>
    </script>
    <style>
        .navbar-inverse {
            background: #006d8a !important;
            border-bottom: 2px solid #03cde3;
        }

        .navbar-inverse .navbar-brand {
            color: #fff;
        }

        .navbar-inverse .navbar-nav > li > a {
            color: #fff;
        }
        table.standdis tbody td,th {
            width: 2%; !important;
        }

    </style>
</head>
<body>

<!-- Fixed navbar -->
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
                <li class="active"><a href="<?php echo $systemURL; ?>admin.php"><?php echo _('Admin'); ?></a></li>
                <?php if (isloggedin()): ?>
                    <li><a class="user-logout" style="cursor: pointer;" id="logout"><?php echo _('Log out'); ?></a></li>
                <?php endif; ?>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
<br/>
<div class="container">

    <div class="page-header">
        <h1><?php echo _('Administration'); ?></h1>
    </div>

    <?php
    if (isloggedin()):
        ?>
        <div role="tabpanel" id="menu-tabs">

            <!-- Nav tabs -->
            <ul class="nav nav-tabs nav-tab-style" role="tablist">
                <li role="presentation" class="active"><a href="#eventusers" aria-controls="eventusers" role="tab"
                                                          data-toggle="tab"><span class="fa fa-user"
                                                                                  aria-hidden="true"></span> <?php echo _('Event users'); ?>
                    </a></li>


            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="eventusers">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="eventusers_id" value="<?php echo $_GET['id'];?>">
                        </div>
                        <div class="col-lg-12">
                            <br>
                            <button type="button" id="refresh_eventusers" class="btn btn-primary"
                                    title="Refresh event user list."><span
                                        class="glyphicon glyphicon-repeat"></span></button>
                            <button type="button" id="eventnewmessage" class="btn btn-primary"
                                    title="<?php echo _('Broadcast message'); ?>"><span
                                        class="glyphicon glyphicon-chat"></span> <?php echo _('Broadcast message'); ?>
                            </button>

                        </div>
                        <div class="col-lg-12">
                            <form class="" id="eventbroadcast">
                                <div><h3><?php echo _('Broadcast message') ?></h3></div>
                                <div class="form-group"><label for="eventmessage"><?php echo _('Message:'); ?></label>
                                    <textarea type="text" name="eventmessage" id="eventmessage" class="form-control"></textarea></div>
                                <button type="button" id="eventbroadcastsend" class="btn btn-primary"><?php echo _('Send'); ?></button>
                            </form>
                        </div>
                    </div>
                    <div class=" table-responsive1 dtable-cl">
                        <table id="eventusers-list" class="display" >
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Event Number</th>
                                <th>User Id</th>
                                <th>User Name</th>
                                <th>RSVP Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                </div>

            </div>

        </div>

    <?php endif; ?>

    <br/>
    <div class="panel panel-default">
        <div class="panel-body">
            <i class="glyphicon glyphicon-copyright-mark"></i> <? echo date("Y"); ?> <a
                    href="<?php echo $systemURL; ?>"><?php echo $systemname; ?></a>
        </div>
        <div class="panel-footer">
            <strong><?php echo _('Privacy policy:'); ?></strong> <?php echo _('We will use your details for ');
            echo $systemname, ' - ';
            echo _('related activities only'); ?>.
            <script type="text/javascript" src="js/admin_init.js"></script>
            <script type="text/javascript" src="js/dataTableList/eventuserList.js"></script>
        </div>
    </div>
    <!-- Start Modal -->
    <?php

    include_once "view/eventuser/broadcast.php";
    ?>
    <!-- End Modal -->


</div><!-- /.container -->

</body>
</html>
