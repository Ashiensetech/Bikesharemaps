<?php
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
    <title><?php echo $systemname; ?><?php echo _(' Administration'); ?></title>
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
            <a class="navbar-brand brname" href="<?php echo $systemURL; ?>"><?php echo $systemname; ?></a>
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
                <li role="presentation" class="active"><a href="#fleet" aria-controls="fleet" role="tab"
                                                          data-toggle="tab"><span class="glyphicon glyphicon-lock"
                                                                                  aria-hidden="true"></span> <?php echo _('Fleet'); ?>
                    </a></li>
                <li role="presentation"><a href="#stands" aria-controls="stands" role="tab" data-toggle="tab"><span
                                class="glyphicon glyphicon-map-marker"
                                aria-hidden="true"></span> <?php echo _('Stands'); ?></a></li>
                <li role="presentation"><a href="#bikes" aria-controls="bikes" role="tab" data-toggle="tab"><span
                                aria-hidden="true"></span> <i class="fa fa-bicycle"></i>&nbsp;<?php echo _('Bikes'); ?>
                    </a></li>
                <li role="presentation"><a href="#watercrafts" aria-controls="watercrafts" role="tab" data-toggle="tab"><span
                                aria-hidden="true"></span> <i class="fa fa-ship"></i>&nbsp;<?php echo _('Watercraft'); ?>
                    </a></li>
                <li role="presentation"><a href="#events" aria-controls="events" role="tab" data-toggle="tab"><span
                                aria-hidden="true"></span> <i class="fa fa-calendar-check-o"></i>&nbsp;<?php echo _('Event Location'); ?>
                    </a></li>
                <li role="presentation"><a href="#lodging" aria-controls="lodging" role="tab" data-toggle="tab"><span
                                aria-hidden="true"></span> <i class="fa fa-bed"></i>&nbsp;<?php echo _('Lodging'); ?>
                    </a></li>
                <li role="presentation"><a href="#shopping" aria-controls="shopping" role="tab" data-toggle="tab"><span
                                aria-hidden="true"></span> <i class="fa fa-shopping-basket"></i>&nbsp;<?php echo _('Shopping'); ?>
                    </a></li>
                <li role="presentation"><a href="#adventure" aria-controls="adventure" role="tab" data-toggle="tab"><span
                                aria-hidden="true"></span> <i class="fa fa-plane"></i>&nbsp;<?php echo _('Adventure'); ?>
                    </a></li>
                <li role="presentation"><a href="#food-dining" aria-controls="food-dining" role="tab" data-toggle="tab"><span
                                aria-hidden="true"></span> <i class="fa fa-cc-diners-club"></i>&nbsp;<?php echo _('Food/Dining'); ?>
                    </a></li>
                <li role="presentation"><a href="#grocery-fuel" aria-controls="grocery-fuel" role="tab" data-toggle="tab"><span
                                aria-hidden="true"></span> <i class="fa fa-shopping-cart"></i>&nbsp;<?php echo _('Grocery/Fuel'); ?>
                    </a></li>
                <li role="presentation"><a href="#services" aria-controls="services" role="tab" data-toggle="tab"><span
                                aria-hidden="true"></span> <i class="fa fa-bell"></i>&nbsp;<?php echo _('Services'); ?>
                    </a></li>
                <li role="presentation"><a href="#culture" aria-controls="culture" role="tab" data-toggle="tab"><span
                                aria-hidden="true"></span> <i class="fa"></i>&nbsp;<?php echo _('Culture'); ?>
                    </a></li>
                <li role="presentation"><a href="#users" aria-controls="users" role="tab" data-toggle="tab"><span
                                class="glyphicon glyphicon-user" aria-hidden="true"></span> <?php echo _('Users'); ?>
                    </a></li>
                <li role="presentation"><a href="#videos" aria-controls="videos" role="tab" data-toggle="tab"><span
                                class="glyphicon glyphicon-film" aria-hidden="true"></span> <?php echo _('Videos'); ?>
                    </a></li>
                <li role="presentation"><a href="#inquiries" aria-controls="inquiries" role="tab"
                                           data-toggle="tab"><span class="glyphicon glyphicon-question-sign"
                                                                   aria-hidden="true"></span> <?php echo _('Inquiries'); ?>
                    </a></li>
                <?php
                if (iscreditenabled()):
                    ?>
                    <li role="presentation"><a href="#credit" aria-controls="credit" role="tab" data-toggle="tab"><span
                                    class="glyphicon glyphicon-usd"
                                    aria-hidden="true"></span> <?php echo _('Credit system'); ?></a></li>
                <?php endif; ?>
                <li role="presentation"><a href="#maintenance" aria-controls="maintenance" role="tab" data-toggle="tab"><span
                                class="fa fa-wrench" aria-hidden="true"></span> <?php echo _('Maintenance'); ?>
                    </a></li>
                <li role="presentation"><a href="#reports" aria-controls="reports" role="tab" data-toggle="tab"><span
                                class="glyphicon glyphicon-stats" aria-hidden="true"></span> <?php echo _('Reports'); ?>
                    </a></li>

            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="fleet">
                    <div class="row">
                        <div class="col-lg-12 fleet-btn-wrap">
                            <input type="text" name="adminparam" id="adminparam" class="form-control"
                                   style="margin: 20px 0px;">
                            <button class="btn btn-default" type="button" id="where"
                                    title="<?php echo _('Display the bike stand location or name of person using it.'); ?>">
                                <span class="glyphicon glyphicon-screenshot"></span> <?php echo _('Where is?'); ?>
                            </button>
                            <button type="button" id="revert" class="btn btn-default"
                                    title="<?php echo _('Be careful! Revert accidentaly rented bike in case of mistake or misread bike number.'); ?>">
                                <span class="glyphicon glyphicon-fast-backward"></span> <?php echo _('Revert'); ?>
                            </button>
                            <button type="button" id="last" class="btn btn-default"
                                    title="<?php echo _('Display network usage (blank) or history of bike usage (number entered).'); ?>">
                                <span class="glyphicon glyphicon-stats"></span> <?php echo _('Last usage'); ?></button>

                            <div id="fleetconsole"></div>
                        </div>
                    </div>

                </div>
                <div role="tabpanel" class="tab-pane" id="stands">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <button type="button" id="refresh_stands" class="btn btn-primary"
                                    title="Refresh stand list."><span
                                        class="glyphicon glyphicon-repeat"></span></button>
                            <button type="button" data-toggle="modal" class="btn btn-primary"
                                    data-target=".add-stand-modal" title="Add new stand."><span
                                        class="glyphicon glyphicon-plus"></span> <?php echo _('Add stand'); ?></button>
                        </div>
                    </div>
                    <div class="row dtable table-responsive dtable-cl">
                        <table id="stands-list" class="display standdis" style="width:100%">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Image</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th width="5%">Latitude</th>
                                <th width="5%">Longitude</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    </br>
                    <!--<div id="standsconsole"></div>-->
                </div>

                <div role="tabpanel" class="tab-pane" id="bikes">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <button type="button" id="refresh_bikes" class="btn btn-primary"
                                    title="Refresh Bikes list."><span
                                        class="glyphicon glyphicon-repeat"></span></button>
                            <button type="button" data-toggle="modal" data-target=".add-bike-modal"
                                    onclick="getStandsByType('bike_stand')"
                                    class="btn btn-primary" title="Add new bike."><span
                                        class="glyphicon glyphicon-plus"></span> <?php echo _('Add bike'); ?></button>

                        </div>
                    </div>
                    <div class=" table-responsive dtable-cl">
                        <table id="bikes-list" class="display" style="width:100%">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Bike No.</th>
                                <th>Current User</th>
                                <th>Current Stand</th>
                                <th>Current Code</th>
                                <th>Note</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                </div>

                <div role="tabpanel" class="tab-pane" id="watercrafts">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <button type="button" id="refresh_watercrafts" class="btn btn-primary"
                                    title="Refresh Watercrafts list."><span
                                        class="glyphicon glyphicon-repeat"></span></button>
                            <button type="button" data-toggle="modal" data-target=".add-watercraft-modal"
                                    onclick="getStandsByType('watercraft_stand')"
                                    class="btn btn-primary" title="Add new bike."><span
                                        class="glyphicon glyphicon-plus"></span> <?php echo _('Add watercraft'); ?></button>

                        </div>
                    </div>
                    <div class=" table-responsive dtable-cl">
                        <table id="watercrafts-list" class="display" style="width:100%">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Watercraft No.</th>
                                <th>Current User</th>
                                <th>Current Stand</th>
                                <th>Current Code</th>
                                <th>Note</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                </div>

                <div role="tabpanel" class="tab-pane" id="events">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <button type="button" id="refresh_events" class="btn btn-primary"
                                    title="Refresh Events list."><span
                                        class="glyphicon glyphicon-repeat"></span></button>
                            <button type="button" data-toggle="modal" data-target=".add-event-modal"
                                    onclick="getStandsByType('event_stand')"
                                    class="btn btn-primary" title="Add new event."><span
                                        class="glyphicon glyphicon-plus"></span> <?php echo _('Add event'); ?></button>

                        </div>
                    </div>
                    <div class=" table-responsive dtable-cl">
                        <table id="events-list" class="display" style="width:100%">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Event No.</th>
                                <th>Event Location</th>
                                <th>Image</th>
                                <th>Total Bikes</th>
                                <th>Total Users</th>
                                <th>RSVP Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                </div>

                <div role="tabpanel" class="tab-pane" id="lodging">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <button type="button" id="refresh_lodging" class="btn btn-primary"
                                    title="Refresh Lodging list."><span
                                        class="glyphicon glyphicon-repeat"></span></button>
                            <button class="btn btn-primary" id="add-lodging-details" title="Add Details."><span
                                        class="glyphicon glyphicon-plus"></span> <?php echo _('Add Details'); ?></button>

                        </div>
                    </div>
                    <div class=" table-responsive dtable-cl">
                        <table id="lodging-list" class="display" style="width:100%">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Lodging Name</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="shopping">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <button type="button" id="refresh_shopping" class="btn btn-primary"
                                    title="Refresh Shopping list."><span
                                        class="glyphicon glyphicon-repeat"></span></button>
                            <button class="btn btn-primary" id="add-shopping-details" title="Add Details."><span
                                        class="glyphicon glyphicon-plus"></span> <?php echo _('Add Details'); ?></button>

                        </div>
                    </div>
                    <div class=" table-responsive dtable-cl">
                        <table id="shopping-list" class="display" style="width:100%">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="adventure">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <button type="button" id="refresh_adventure" class="btn btn-primary"
                                    title="Refresh Adventure list."><span
                                        class="glyphicon glyphicon-repeat"></span></button>
                            <button class="btn btn-primary" id="add-adventure-details" title="Add Details."><span
                                        class="glyphicon glyphicon-plus"></span> <?php echo _('Add Details'); ?></button>

                        </div>
                    </div>
                    <div class=" table-responsive dtable-cl">
                        <table id="adventure-list" class="display" style="width:100%">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="food-dining">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <button type="button" id="refresh_food_dining" class="btn btn-primary"
                                    title="Refresh Food/Dining list."><span
                                        class="glyphicon glyphicon-repeat"></span></button>
                            <button class="btn btn-primary" id="add-food-dining-details" title="Add Details."><span
                                        class="glyphicon glyphicon-plus"></span> <?php echo _('Add Details'); ?></button>

                        </div>
                    </div>
                    <div class=" table-responsive dtable-cl">
                        <table id="food-dining-list" class="display" style="width:100%">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="grocery-fuel">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <button type="button" id="refresh_grocery_fuel" class="btn btn-primary"
                                    title="Refresh Grocery/Fuel list."><span
                                        class="glyphicon glyphicon-repeat"></span></button>
                            <button class="btn btn-primary" id="add-grocery-fuel-details" title="Add Details."><span
                                        class="glyphicon glyphicon-plus"></span> <?php echo _('Add Details'); ?></button>

                        </div>
                    </div>
                    <div class=" table-responsive dtable-cl">
                        <table id="grocery-fuel-list" class="display" style="width:100%">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="services">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <button type="button" id="refresh_services" class="btn btn-primary"
                                    title="Refresh Services list."><span
                                        class="glyphicon glyphicon-repeat"></span></button>
                            <button class="btn btn-primary" id="add-services-details" title="Add Details."><span
                                        class="glyphicon glyphicon-plus"></span> <?php echo _('Add Details'); ?></button>

                        </div>
                    </div>
                    <div class=" table-responsive dtable-cl">
                        <table id="services-list" class="display" style="width:100%">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="culture">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <button type="button" id="refresh_culture" class="btn btn-primary"
                                    title="Refresh Culture list."><span
                                        class="glyphicon glyphicon-repeat"></span></button>
                            <button class="btn btn-primary" id="add-culture-details" title="Add Details."><span
                                        class="glyphicon glyphicon-plus"></span> <?php echo _('Add Details'); ?></button>

                        </div>
                    </div>
                    <div class=" table-responsive dtable-cl">
                        <table id="culture-list" class="display" style="width:100%">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <?php
                if (iscreditenabled()):
                    ?>
                    <div role="tabpanel" class="tab-pane" id="credit">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="credit-btn-wrap">
                                    </br>
                                    <button type="button" id="refresh_coupon" class="btn btn-primary"
                                            title="Refresh Coupon list."><span
                                                class="glyphicon glyphicon-repeat"></span></button>
                                    <button type="button" id="listcoupons" class="btn btn-default"
                                            title="<?php echo _('Display existing coupons.'); ?>"><span
                                                class="glyphicon glyphicon-list-alt"></span> <?php echo _('List coupons'); ?>
                                    </button>
                                    <button type="button" id="generatecoupons1" class="btn btn-success"
                                            title="<?php echo _('Generate new coupons.'); ?>"><span
                                                class="glyphicon glyphicon-plus"></span> <?php echo _('Generate');
                                        echo ' ', $credit["currency"], $requiredcredit, ' ';
                                        echo _('coupons'); ?></button>
                                    <button type="button" id="generatecoupons2" class="btn btn-success"
                                            title="<?php echo _('Generate new coupons.'); ?>"><span
                                                class="glyphicon glyphicon-plus"></span> <?php echo _('Generate');
                                        echo ' ', $credit["currency"], $requiredcredit * 5, ' ';
                                        echo _('coupons'); ?></button>
                                    <button type="button" id="generatecoupons3" class="btn btn-success"
                                            title="<?php echo _('Generate new coupons.'); ?>"><span
                                                class="glyphicon glyphicon-plus"></span> <?php echo _('Generate');
                                        echo ' ', $credit["currency"], $requiredcredit * 10, ' ';
                                        echo _('coupons'); ?></button>
                                </div>
                                </br>
                                <div class="row  table-responsive dtable-cl">
                                    <div id=""></div>
                                    <table id="coupon-list" class="display" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Coupon</th>
                                            <th>Value</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div role="tabpanel" class="tab-pane" id="users">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <button type="button" id="refresh_users" class="btn btn-primary"
                                    title="<?php echo _('Refresh list of users.'); ?>"><span
                                        class="glyphicon glyphicon-repeat"></span></button>
                            <button type="button" id="newmessage" class="btn btn-primary"
                                    title="<?php echo _('Broadcast message'); ?>"><span
                                        class="glyphicon glyphicon-chat"></span> <?php echo _('Broadcast message'); ?>
                            </button>

                            <button type="button" data-toggle="modal" data-target=".add-user-modal"
                                    class="btn btn-primary" title="Add new user."><span
                                        class="glyphicon glyphicon-plus"></span> <?php echo _('Add User'); ?></button>
                        </div>
                    </div>
                    <form class="container" id="broadcast">
                        <div><h3><?php echo _('Broadcast message') ?></h3></div>
                        <div class="form-group"><label for="message"><?php echo _('Message:'); ?></label>
                            <textarea type="text" name="message" id="message" class="form-control"></textarea></div>
                        <button type="button" id="send" class="btn btn-primary"><?php echo _('Send'); ?></button>
                    </form>

                    <!--                    <div id="userconsole"></div>
                    -->
                    <div id="user_message"></div>
                    <div class="row  table-responsive dtable-cl">
                        <div id=""></div>
                        <table id="user-list" class="display" style="width:100%">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>User Code</th>
                                <th>Privilege</th>
                                <th>Limit</th>
                                <th>Credit</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="videos">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <button type="button" id="videolistRefresh" class="btn btn-primary" title=""><span
                                        class="glyphicon glyphicon-repeat"></span></button>
                            <button type="button"
                                    data-toggle="modal" data-target=".add-video-modal" class="btn btn-primary"
                                    title="Add Video"><span
                                        class="glyphicon glyphicon-film"></span> <?php echo _('Add Video'); ?></button>
                        </div>
                    </div>
                    <div class="progress">
                        <p>Uploading...</p>
                    </div>
                    <div id="row" class=" table-responsive dtable-cl">
                        <table id="video-list" class="display" style="width:100%">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Video</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- <div id="videoconsole">

                     </div>-->

                </div>

                <div role="tabpanel" class="tab-pane" id="inquiries">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <div class="report-tab">
                                <button type="button" id="refresh_inquiries" class="btn btn-primary refresh-btn"
                                        title="<?php echo _('Reload inquiries.'); ?>"><span
                                            class="glyphicon glyphicon-repeat"></span> <?php ?></button>

                                <button class="report-tablinks" id="inquiries-data" title="<?php echo _('Show Inquiry list.'); ?>" onclick="openStat(event, 'inquiry_list_tab')"><span
                                            class="glyphicon glyphicon-question-sign"></span> <?php echo _('Inquiries'); ?></button>
                                <button class="report-tablinks" id="helps-data" title="<?php echo _('Show help list.'); ?>" onclick="openStat(event, 'help_list_tab')"><span
                                            class="glyphicon glyphicon-info-sign"></span> <?php echo _('Helps'); ?></button>
                            </div>
                            <?php include('./view/inquiry/inquiry.php'); ?>
                            <?php include('./view/inquiry/help.php'); ?>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="maintenance">
                    <div class="row">
                        <div class="col-lg-12">
                            <br>
                            <div class="report-tab">
                                <button type="button" id="refresh_maintenance" class="btn btn-primary refresh-btn"
                                        title="<?php echo _('Reload inquiries.'); ?>"><span
                                            class="glyphicon glyphicon-repeat"></span> <?php ?></button>
                                <button class="report-tablinks" id="maintenance_settings" title="<?php echo _('Maintenance settings.'); ?>" onclick="openStat(event, 'maintenance_settings_tab')"><span
                                            class="fa fa-cog"></span> <?php echo _('Settings'); ?></button>
                                <button class="report-tablinks" id="maintenance_list" title="<?php echo _('Show maintenance list.'); ?>" onclick="openStat(event, 'bike_maintenance_list_tab')"><span
                                            class="fa fa-wrench"></span> <?php echo _('Bike Maintenance'); ?></button>
                                <button class="report-tablinks" id="maintenance_doc" title="<?php echo _('Maintenance documentation.'); ?>" onclick="openStat(event, 'bike_maintenance_doc_tab')"><span
                                            class="fa fa-file"></span> <?php echo _('Bike Documentation'); ?></button>
                                <button class="report-tablinks" id="maintenance_list" title="<?php echo _('Show maintenance list.'); ?>" onclick="openStat(event, 'watercraft_maintenance_list_tab')"><span
                                            class="fa fa-wrench"></span> <?php echo _('Watercraft Maintenance'); ?></button>
                                <button class="report-tablinks" id="maintenance_doc" title="<?php echo _('Maintenance documentation.'); ?>" onclick="openStat(event, 'watercraft_maintenance_doc_tab')"><span
                                            class="fa fa-file"></span> <?php echo _('Watercraft Documentation'); ?></button>
                            </div>
                            <?php include('./view/maintenance/maintenanceSettings.php'); ?>
                            <?php include('./view/maintenance/bikemaintenanceList.php'); ?>
                            <?php include('./view/maintenance/bikemaintenanceDoc.php'); ?>
                            <?php include('./view/maintenance/watercraftmaintenanceDoc.php'); ?>
                            <?php include('./view/maintenance/watercraftmaintenanceList.php'); ?>
                        </div>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="reports">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="report-tab">
                                <button class="report-tablinks" id="daily_stats" title="<?php echo _('Show usage stats by day.'); ?>" onclick="openStat(event, 'daily_stats_report')"><span
                                            class="glyphicon glyphicon-road"></span> <?php echo _('Daily stats'); ?></button>
                                <button class="report-tablinks" id="user_stats" title="<?php echo _('Show user stats.'); ?>" onclick="openStat(event, 'user_stats_report')"><span
                                            class="glyphicon glyphicon-road"></span> <?php echo _('User stats'); ?></button>
                                <button class="report-tablinks" id="weather_stats" title="<?php echo _('Show weather stats.'); ?>" onclick="openStat(event, 'weather_stats_report')"><span
                                            class="glyphicon glyphicon-road"></span> <?php echo _('Weather stats'); ?></button>
                            </div>

                            <?php include('./view/report/reports.php'); ?>
                        </div>
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
            <script type="text/javascript" src="js/dataTableList/standList.js"></script>
            <script type="text/javascript" src="js/dataTableList/bikeList.js"></script>
            <script type="text/javascript" src="js/dataTableList/watercraftList.js"></script>
            <script type="text/javascript" src="js/dataTableList/eventList.js"></script>
            <script type="text/javascript" src="js/dataTableList/usersList.js"></script>
            <script type="text/javascript" src="js/dataTableList/videoList.js"></script>
            <script type="text/javascript" src="js/dataTableList/inquiryList.js"></script>
            <script type="text/javascript" src="js/dataTableList/couponList.js"></script>
            <script type="text/javascript" src="js/maintenanceSetting.js"></script>
<!--            <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/sorting/enum.js"></script>-->
            <script type="text/javascript" src="js/dataTableList/maintenanceList.js"></script>
            <script type="text/javascript" src="js/dataTableList/placeList.js"></script>
        </div>
    </div>
    <!-- Start Modal -->
    <?php

    include_once "view/user/modal/addUser.php";
    include_once "view/user/modal/editUser.php";
    include_once "view/user/modal/changePassword.php";
    include_once "view/common/modal/delete.php";
    include_once "view/stand/modal/createStand.php";
    include_once "view/stand/modal/editStand.php";
    include_once "view/bike/modal/createBike.php";
    include_once "view/bike/modal/editBike.php";
    include_once "view/watercraft/modal/createWatercraft.php";
    include_once "view/watercraft/modal/editWatercraft.php";
    include_once "view/event/modal/createEvent.php";
    include_once "view/event/modal/editEvent.php";
    include_once "view/video/modal/createVideo.php";
    include_once "view/inquiry/modal/helpModal.php";
    include_once "view/place/modal/createPlace.php";
    include_once "view/place/modal/editplace.php";
    ?>
    <!-- End Modal -->


</div><!-- /.container -->

<script>
    $('body').on('hide.bs.modal', '.video-modal', function () {
        var video = $(this).find('video')[0];
        video.currentTime = 0;
        video.pause();
    })
</script>
</body>
</html>
