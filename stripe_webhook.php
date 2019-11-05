<?php
require("config.php");
require("db.class.php");
require("common.php");
require_once('vendor/autoload.php');
require_once('third_party_config/stripe/config_stripe.php');

// Set your secret key: remember to change this to your live secret key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys
//\Stripe\Stripe::setApiKey("sk_test_eWQ3NpG6gbdcZ0GSfuP4ants");

// You can find your endpoint's secret in your webhook settings
$endpoint_secret = $stripe['endpoint_secret'];

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

function subscriptionEndpointSucceeded($payment_info_sub_id, $is_active, $created_date, $expiration_date,$email,$subscription_type){
    global $db, $systemname;

    //Insert into subscription_list
    $result1 = $db->query("SELECT * FROM payment_subscription WHERE customer_email='$email' AND subscription_type='$subscription_type'");
    $row = $result1->fetch_assoc();

    if($result1->num_rows) {
        $payment_subscription_id = $row['id'];
        $existing_payment_info = $row['payment_info'];

        $result2 = $db->query("INSERT INTO subscription_list SET payment_subscription_id = '$payment_subscription_id', subscription_type='$subscription_type',start_date='$created_date',end_date='$expiration_date'");

        $existing_payment_info = json_decode($existing_payment_info);
        $existing_payment_info->sub_id = $payment_info_sub_id;
        $payment_info = json_encode($existing_payment_info);

        //update payment_subscription
        $result = $db->query("UPDATE payment_subscription SET payment_info='$payment_info',is_active='$is_active',created_date='$created_date',expiration_date='$expiration_date' WHERE  customer_email='$email' AND subscription_type='$subscription_type'");
        $subject = $systemname ." ". $subscription_type . " subscription";
        $emailMassage = '<html><body><div><h3>Hi, ' . $email. '</h3>
        <p>You have successfully subscribe our '.$subscription_type.' Plan</p>
        <p>Thanks<br>'.$systemname.'</p></div></body></html>';
        sendEmail($email, $subject, $emailMassage);
    }
    return true;
}

function subscriptionEndpointFailed($is_active, $email, $subscription_type){
    global $db, $systemname;
    $result = $db->query("UPDATE payment_subscription SET is_active='$is_active' WHERE  customer_email='$email' AND subscription_type='$subscription_type'");
    $subject = $systemname ." ". $subscription_type . " subscription";
    $emailMassage = '<html><body><div><h3>Hi, ' . $email. '</h3>
        <p>You have canceled your '.$subscription_type.' Subscription.</p>
        <p>Thanks<br>'.$systemname.'</p></div></body></html>';
    sendEmail($email, $subject, $emailMassage);
    return true;
}

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
    );

    if(isset($event)){
        $obj = $event->data->object;

        //both same period start and end
//        $period_start = date('Y-m-d H:i:s', $obj->period_start);
//        $period_end = date('Y-m-d H:i:s', $obj->period_end);
        $payment_info_sub_id = $obj->subscription;

        ($event->type == "invoice.payment_succeeded") ?  $is_active = 1 : $is_active = 0;

        //subscription_type
        if($event->type == "customer.subscription.deleted"){
            $plan_interval = $obj->plan->interval; //year or month
        }else {
            $plan_interval = $obj->lines->data['0']->plan->interval; //year or month
        }
        $subscription_type = '';
        if($plan_interval == 'month'){
            $subscription_type = 'monthly';
        }else if ($plan_interval == 'year') {
            $subscription_type = 'annually';
        }
        //Testing data
//        $customer = \Stripe\Customer::retrieve('cus_EMJOEJBIhsZOmU');
//        $subscription2 = \Stripe\Subscription::retrieve('sub_ENlMVhGQUJekvy');
//        $email = 'vaaayzo@mailinator.net';

        //Real data
        $customer = \Stripe\Customer::retrieve($obj->customer);
        $subscription2 = \Stripe\Subscription::retrieve($obj->subscription);
        $email = $customer->email;


        $period_start = date('Y-m-d H:i:s', $subscription2->current_period_start);
        $period_end = date('Y-m-d H:i:s', $subscription2->current_period_end);
//        print_r($obj->subscription);
//        print_r(date('Y-m-d H:i:s', $subscription2->current_period_start));
//        print_r(date('Y-m-d H:i:s', $subscription2->current_period_end));
//        die("ss");
    }

    if (isset($event) && ( $event->type == "invoice.payment_failed" || $event->type == "customer.subscription.deleted" )) {

        // Sending your customers the amount in pennies is weird, so convert to dollars
        //$amount = sprintf('$%0.2f', $event->data->object->amount_due / 100.0);

        //Update Db
        subscriptionEndpointFailed($is_active, $email, $subscription_type);

    }
    else if(isset($event) && $event->type == "invoice.payment_succeeded"){
        //Update DB
        //If billing_reason == 'subscription_cycle' the webhook is for a subscription renewal.
        //If billing_reason == 'subscription_create' the webhook is for a brand new subscription.

        if($obj->billing_reason == 'subscription_cycle') {
            subscriptionEndpointSucceeded($payment_info_sub_id, $is_active, $period_start, $period_end,$email,$subscription_type);
        }
    }


} catch(\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400); // PHP 5.4 or greater
    exit();
} catch(\Stripe\Error\SignatureVerification $e) {
    // Invalid signature
    http_response_code(400); // PHP 5.4 or greater
    exit();
}

// Do something with $event

http_response_code(200); // PHP 5.4 or greater
?>
