<?php
require("config.php");
require("db.class.php");
require("common.php");
require_once('vendor/autoload.php');
require_once('third_party_config/stripe/config_stripe.php');
//require_once('vendor/stripe/stripe-php/init.php');
// set up messaging
//echo $_COOKIE['loguserid'];die();
$error = '';
$success = '';

function addDonationInfo($charge, $customer_email){
    global $db;
    if($charge){
        $charge_id = $charge->id;
        $customer_email = $customer_email;
        $amount = $charge->amount; // in cents
        $amount = number_format(($amount /100), 0, '.', ' ');
        $balance_transaction = $charge->balance_transaction;
        $result = $db->query("INSERT INTO donation SET charge_id='$charge_id',customer_email='$customer_email',amount='$amount',balance_transaction='$balance_transaction'");
        return true;
    }
}

if ($_POST) {
//    echo $token;die();
    // Set your secret key: remember to change this to your live secret key in production
    // See your keys here https://dashboard.stripe.com/account
//    \Stripe\Stripe::setApiKey($stripe['secret_key']);
    // Get the credit card and customer interaction details submitted by the form
    $token = $_POST['stripeToken'];
    $custemail = $_POST['stripeEmail'];
    $donation = $_POST['donationAmt'];
    // Create the customer
    $customer = \Stripe\Customer::create(array(
        "source" => $token,
        "description" => $custemail,
        "email" => $custemail
    ));
    // Create the charge on Stripe's servers - this will charge the user's card
    try {
        $charge = \Stripe\Charge::create(array(
            "amount" => $donation,
            "currency" => "usd",
            "customer" => $customer->id,
            "receipt_email" => $custemail,
            "description" => "Online Donation - $custemail"
        ));
        addDonationInfo($charge,$custemail);
        $success = 'Your payment was successful.';

    } catch(\Stripe\Error\Card $e) {
        // The card has been declined from some reason
        $error = $e->getMessage();
    }
    // send back messaging json
    $arr = array(
        'success'=>$success,
        'error'=>$error
    );
    echo json_encode($arr);
    return;
}
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
    <style>
        .donate-process,
        .donate-thanks,
        .donate-alert {
            font-size: 1.2em;
            -webkit-transition: all .3s ease-out;
            -moz-transition: all .3s ease-out;
            -o-transition: all .3s ease-out;
            transition: all .3s ease-out;
            visibility: hidden;
            opacity: 0;
            height: 0;
            display: block;
        }
        .donate-process.show,
        .donate-thanks.show,
        .donate-alert.show {
            opacity: 1;
            height: auto;
            visibility: visible;
            padding: 1em;
        }
        .donate-alert.show {
            background: #f6cfcf;
        }
        .donate-thanks.show {
            background: #39d1b4;
            color: #fff;
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
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo $systemURL; ?>"><?php echo $systemname; ?></a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="<?php echo $systemURL; ?>">Map</a></li>
                <li><a href="<?php echo $systemURL; ?>register.php"><?php echo _('Registration'); ?></a>
                <li class="active"><a href="<?php echo $systemURL; ?>donate.php"><?php echo _('Donation'); ?></a>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
<br/>
<div class="container">

    <div class="page-header">
        <h1><?php echo _('Donation'); ?></h1>
        <div id="console"></div>
    </div>

    <div class="container">
        <div class="row">
            <?php require("view/donate/donate.php"); ?>
        </div>
    </div>

    <div class="panel panel-default" style="margin-top: 50px">
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
    <input id="stripe_pk" type="hidden" value="<?php echo $stripe['publishable_key'];?>">
    <script src="https://checkout.stripe.com/checkout.js"></script>
    <script src="js/donate.js"></script>


</div><!-- /.container -->
</body>
</html>