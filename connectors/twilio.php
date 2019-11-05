<?php 
// Require the bundled autoload file - the path may need to change
// based on where you downloaded and unzipped the SDK
require __DIR__ . '/Twilio/Twilio/autoload.php';

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

// Your Account SID and Auth Token from twilio.com/console
$sid="AC3f13c0c196ad90d7ad38a10bae31e191";
$token="06d59a7bc6208c703431380bc41d17f5";
$twilioNumber="+18154728638";

class SMSConnector{
    function __construct(){
        $this->checkConfig();
    }
    function checkConfig(){
        global $sid, $token, $twilioNumber;
        if(!$sid OR !$token OR !$twilioNumber) exit('Please, configure Twilio SMS API gateway access in '.__FILE__.'!');
    }
    function Send($number,$text){
        global $sid, $token, $twilioNumber;
        $to="+".$number;
        $client = new Client($sid, $token);

        // Use the client to do fun stuff like send text messages!
        $client->messages->create(
        // the number you'd like to send the message to
        $to,
        array(
            // A Twilio phone number you purchased at twilio.com/console
            'from' => $twilioNumber,
            // the body of the text message you'd like to send
            'body' => $text
        )
        );
    }
    
}
?>