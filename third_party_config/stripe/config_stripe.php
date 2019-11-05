<?php
#require_once('../../vendor/autoload.php');

//Test Mode

$stripe = [
    "secret_key"      => "sk_test_5qoINPbGen572c7Lrvwb73L4",
    "publishable_key" => "pk_test_ecmCHxXVY7W8EAYrfmmiFaV4",
    "endpoint_secret" => "whsec_t6wbciNrrmoT17FgyQgOxT1pbQXKZUJT",
    "monthly_plan_id" => "plan_EO9Ih2gbzNVk8T",
    "annually_plan_id" => "plan_EO9IuE9IR7nyBK"
];
\Stripe\Stripe::setApiKey($stripe['secret_key']);
?>