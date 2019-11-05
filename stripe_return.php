<?php
require("config.php");
require("db.class.php");
require("common.php");
require_once('vendor/autoload.php');
require_once('third_party_config/stripe/config_stripe.php');

function addSubHistory($payment_subid,$plan_check,$current_period_start,$current_period_end){
    global $db;
    if($payment_subid != 0){
        $result = $db->query("INSERT INTO subscription_list SET payment_subscription_id='$payment_subid',subscription_type='$plan_check',start_date='$current_period_start',end_date='$current_period_end'");
        return true;
    }
}

# Api : https://stripe.com/docs/api/customers/create
if (isset($_POST['stripeToken']) && isset($_POST['plan']) && isset($_POST['user_id'])) {
    try {
        $token = $_POST['stripeToken'];
        $email = $_POST['stripeEmail'];
        $plan_check = $_POST['plan'];
        $user_id = $_POST['user_id'];
//        $payment_info = serialize($_POST);

        $insert_subscription = 0;
        $update_subscription = 0;
        $insert_charge = 0;
        $update_charge = 0;
        $is_active = 0;
        $where_id = 0;

        if($plan_check == 'monthly' || $plan_check == 'annually'){
            $result = $db->query("SELECT * FROM payment_subscription WHERE customer_email='$email' AND subscription_type!='family_weekend' LIMIT 1");
            if ($result->num_rows > 0){
                //Existing customer. Get customer id.
                $row = $result->fetch_assoc();
                $customer_id = $row['customer_id'];
                $customer_email = $row['customer_email'];
                $is_active = $row['is_active'];
                $where_id = $row['id'];
                if($is_active){
                    $error = 'You are already subscribed. You have not been charged.';
                    throw new Exception($error);
                }
                $update_subscription = 1;
            }else if($result->num_rows == 0){
                //New customer. Create customer on stripe and get customer id.
                $customer = \Stripe\Customer::create([
                    'email' => $email,
                    'source' => $token,
                ]);
                $customer_id = $customer->id;
                $customer_email = $customer->email;
                $insert_subscription = 1;
            }
            //Get plan id from stripe dashboard
            ($plan_check == 'monthly') ? $plan_id = $stripe['monthly_plan_id'] : $plan_id = $stripe['annually_plan_id'];

            //Create subscription with customer id
            $subscription = \Stripe\Subscription::create([
                "customer" => $customer_id,
                "items" => [
                    [
                        "plan" => $plan_id,
                    ],
                ],
            ]);
            //Subscription Data
            $current_period_start = date('Y-m-d H:i:s', $subscription->current_period_start);
            $current_period_end = date('Y-m-d H:i:s', $subscription->current_period_end);
            $subscription_details = json_encode(array('sub_id'=>$subscription->id));

            if($insert_subscription) {
                //Insert subscription data in db
                $result = $db->query("INSERT INTO payment_subscription SET user_id='$user_id',customer_id='$customer_id',customer_email='$customer_email',subscription_type='$plan_check',payment_info='$subscription_details',is_active=1,created_date='$current_period_start',expiration_date='$current_period_end'");
                $payment_subid = $db->insertid();
                //$result1 = $db->query("UPDATE users SET status='active' WHERE userId='$user_id'");

            }else if ($update_subscription) {
                //Update existing row in db (type different)
                $result = $db->query("UPDATE payment_subscription SET subscription_type='$plan_check',payment_info='$subscription_details',is_active=1,created_date='$current_period_start',expiration_date='$current_period_end' WHERE id='$where_id'");
                $payment_subid = $where_id;
            }

            ($plan_check == 'monthly') ? $plan_message = 'Monthly' : $plan_message = 'Annual';
            $success= "Thanks! You've subscribed to the $plan_message plan.";
        } else if($plan_check == 'family_weekend'){
            $result = $db->query("SELECT * FROM payment_subscription WHERE customer_email='$email' AND subscription_type='family_weekend' LIMIT 1");
            if ($result->num_rows > 0){
                //Existing customer. Get customer id.
                $row = $result->fetch_assoc();
                $customer_id = $row['customer_id'];
                $customer_email = $row['customer_email'];
                $is_active = $row['is_active'];
                $where_id = $row['id'];
                if($is_active){
                    $error = 'You are already subscribed to Family Weekend. You have not been charged.';
                    throw new Exception($error);
                }
                $update_charge = 1;
            }else if($result->num_rows == 0){
                //New customer. Create customer on stripe and get customer id.
                $customer = \Stripe\Customer::create([
                    'email' => $email,
                    'source' => $token,
                ]);
                $customer_id = $customer->id;
                $customer_email = $customer->email;
                $insert_charge = 1;
            }
            //One time payment
            $charge = \Stripe\Charge::create([
                'customer' => $customer_id,
                'amount' => 2500,
                'currency' => 'usd',
            ]);

            //Subscription Data
            $current_period_start = date('Y-m-d H:i:s', $charge->created);
            $current_period_end = date('Y-m-d H:i:s', strtotime($current_period_start . " +48 hours") );
            $charge_details = json_encode(array('charge_id'=>$charge->id));

            if($insert_charge) {
                //Insert subscription data in db
                $result = $db->query("INSERT INTO payment_subscription SET user_id='$user_id',customer_id='$customer_id',customer_email='$customer_email',subscription_type='$plan_check',payment_info='$charge_details',is_active=1,created_date='$current_period_start',expiration_date='$current_period_end'");
                $payment_subid = $db->insertid();
                //$result1 = $db->query("UPDATE users SET status='active' WHERE userId='$user_id'");
            }else if ($update_charge) {
                //Update existing row in db
                $result = $db->query("UPDATE payment_subscription SET payment_info='$charge_details',is_active=1,created_date='$current_period_start',expiration_date='$current_period_end' WHERE id='$where_id'");
                $payment_subid = $where_id;
            }

            $success= "Thanks! You've subscribed to the Family Weekend plan.";
        }
        $success .= "Please check your Email.";

        if($insert_subscription || $update_subscription || $insert_charge || $update_charge) {
            addSubHistory($payment_subid,$plan_check,$current_period_start,$current_period_end);
        }
    }
    catch(Exception $e) {
        $error = $e->getMessage(). "\n";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title><?= $systemname; ?><?php echo _(' Subscription'); ?></title>
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
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
<br/>
<div class="container">

    <div class="page-header">
        <h1><?php echo _('Subscription'); ?></h1>

    </div>
    <div id="console">
        <?php
        if($success){
            echo "<p class='alert alert-success'>$success</p>";
        }else if($error){
            echo "<p class='alert alert-danger'>$error</p>";
        }
        ?>
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
<?php
//if (loggeduser()){
//    //echo "You logged";
//}else {
//    if($success){
//        header( "refresh:3;url=agree.php" );
//        exit;
//    }
//}

?>