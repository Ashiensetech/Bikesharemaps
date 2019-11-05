<?php
require("config.php");
require("db.class.php");
require("common.php");
require_once('vendor/autoload.php');
require_once('third_party_config/stripe/config_stripe.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title><?= $systemname; ?><?php echo _('registration'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrapValidator.min.js"></script>
    <script type="text/javascript" src="js/translations.php"></script>
    <script type="text/javascript" src="js/register.js"></script>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/bootstrapValidator.min.css"/>
    <?php if (file_exists("analytics.php")) require("analytics.php"); ?>
</head>
<body>
<!-- Fixed navbar -->
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only"><?php echo _('Toggle navigation'); ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo $systemURL; ?>"><?php echo $systemname; ?></a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="<?php echo $systemURL; ?>">Map</a></li>
                <li class="active"><a href="<?php echo $systemURL; ?>register.php"><?php echo _('Registration'); ?></a>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
<br/>
<div class="container">

    <div class="page-header">
        <h1><?php echo _('Registration'); ?></h1>
        <div id="console"></div>
    </div>

    <?php if (issmssystemenabled() == TRUE): ?>
    <form class="container" id="step1">
        <h2><?php echo _('Step 1 - Confirm your phone number'); ?></h2>
        <div class="form-group">
            <label for="number" class="control-label"><?php echo _('Phone number:'); ?></label> <input
                    placeholder="5551234567" type="text" name="number" id="number" class="form-control"/>
        </div>
        <div class="alert alert-info"><?php echo _('You will receive SMS code to this phone number.'); ?></div>
        <button type="submit" id="validate"
                class="btn btn-primary"><?php echo _('Validate this phone number'); ?></button>
    </form>
    <form class="container" id="step2">
        <h2 id="step2title"><?php echo _('Step 2 - Create account'); ?></h2>
        <div class="form-group">
            <label for="smscode" class="control-label"><?php echo _('SMS code (received to your phone):'); ?></label>
            <input type="text" name="smscode" id="smscode" class="form-control"/></div>
        <?php else: ?>
        <form class="container" id="step2">
            <h2 id="step2title"><?php echo _('Step 1 - Create account'); ?></h2>
            <?php endif; ?>
            <div id="regonly">
                <div class="form-group">
                    <label for="fullname"><?php echo _('Fullname:'); ?></label> <input type="text" name="fullname"
                                                                                       id="fullname"
                                                                                       class="form-control"
                                                                                       placeholder="<?php echo _('Firstname Lastname'); ?>"/>
                </div>
                <div class="form-group">
                    <label for="useremail"><?php echo _('Email:'); ?></label> <input type="text" name="useremail"
                                                                                     id="useremail" class="form-control"
                                                                                     placeholder="email@domain.com"/>
                </div>
            </div>
            <div class="form-group">
                <label for="userage"><?php echo _('Age:'); ?></label>
                <select class="form-control" name="userage" id="userage">
                    <option value="18">Under 18</option>
                    <option value="18-29">18-29</option>
                    <option value="30-44">30-44</option>
                    <option value="45-64">45-64</option>
                    <option value="65+">65+</option>
                </select>
            </div>
            <div class="form-group">
                <label for="usergender"><?php echo _('Gender:'); ?></label>
                <select class="form-control" name="usergender" id="usergender">
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                    <option value="no-answer">Prefer not to Answer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="userrace"><?php echo _('Race:'); ?></label>
                <select class="form-control" name="userrace" id="userrace">
                    <option value="white">White</option>
                    <option value="hispanic-or-latino">Hispanic or Latino</option>
                    <option value="black-or-african-american">Black or African American</option>
                    <option value="native-american">Native American</option>
                    <option value="asian-or-pacific">Asian / Pacific Islander</option>
                    <option value="other">Other</option>
                    <option value="no-answer">Prefer not to Answer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="mailingaddress"><?php echo _('Address(Line 1):'); ?></label> <input type="text"
                                                                                                name="mailingaddress"
                                                                                                id="mailingaddress"
                                                                                                class="form-control"
                                                                                                placeholder="<?php echo _('(Mailing address) Street address, P.O Box, Company name, C/O'); ?>"/>
            </div>
            <div class="form-group">
                <label for="physicaladdress"><?php echo _('Address(Line 2):'); ?></label> <input type="text"
                                                                                                 name="physicaladdress"
                                                                                                 id="physicaladdress"
                                                                                                 class="form-control"
                                                                                                 placeholder="<?php echo _('(Physical address) Apartment, building, suite, building, floor, etc'); ?>"/>
            </div>
            <div class="form-group">
                <label for="city"><?php echo _('City/Town:'); ?></label> <input type="text" name="city" id="city"
                                                                                class="form-control"/>
            </div>
            <div class="form-group">
                <label for="state"><?php echo _('State:'); ?></label> <input type="text" name="state" id="state"
                                                                             class="form-control"/>
            </div>
            <div class="form-group">
                <label for="zipcode"><?php echo _('ZIP/Postal Code:'); ?></label> <input type="text" name="zipcode"
                                                                                         id="zipcode"
                                                                                         class="form-control"/>
            </div>
            <div class="form-group">
                <label for="password"><?php echo _('Password:'); ?></label> <input type="password" name="password"
                                                                                   id="password" class="form-control"/>
            </div>
            <div class="form-group">
                <label for="password2"><?php echo _('Password confirmation:'); ?></label> <input type="password"
                                                                                                 name="password2"
                                                                                                 id="password2"
                                                                                                 class="form-control"/>
            </div>
            <input type="hidden" name="validatednumber" id="validatednumber" value=""/>
            <input type="hidden" name="checkcode" id="checkcode" value=""/>
            <input type="hidden" name="existing" id="existing" value="0"/>

            <button type="submit" id="register" class="btn btn-primary"><?php echo _('Next'); ?></button>
        </form>

        <br/>

        <div class="row" id="steplast" style="display: none">
            <?php require("view/subscription/stripe/stripe_form.php"); ?>

        </div>

        <br>
        <br>
        <br>
        <div class="panel panel-default">
            <div class="panel-body">
                <i class="glyphicon glyphicon-copyright-mark"></i> <? echo date("Y"); ?> <a
                        href="<?php echo $systemURL; ?>"><?php echo $systemname; ?></a>
            </div>
            <div class="panel-footer"><strong><?php echo _('Privacy policy'); ?>
                    :</strong> <?php echo _('We will use your details for');
                echo $systemname, '-';
                echo _('related activities only'); ?>.
            </div>
        </div>

</div><!-- /.container -->
</body>
</html>