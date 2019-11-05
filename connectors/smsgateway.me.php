<?php
/*** https://smsgateway.me
Create callback at: https://smsgateway.me/admin/callbacks/index
Event: Received
Method: HTTP
Action: https://example.com/receive.php (replace example.com with your website URL)
Secret: secretstring (e.g. your password)
***/

$gatewayemail="molekoreginald@gmail.com";
$gatewaypassword="M0unta1n_9";
$gatewaysecret="secret 123"; // your "Secret" from callback

require('smsGateway.me.class.php');

class SMSConnector
   {

   function __construct()
      {
      $this->CheckConfig();
      if (isset($_POST["message"])) $this->message=$_POST["message"];
      if (isset($_POST["contact"])) $this->number=$_POST["contact"]["Number"];
      if (isset($_POST["id"])) $this->uuid=$_POST["id"];
      if (isset($_POST["received_at"])) $this->time=date("Y-m-d H:i:s",$_POST["received_at"]);
      $this->ipaddress=$_SERVER['REMOTE_ADDR'];
      }

   function CheckConfig()
      {
      global $gatewayemail,$gatewaypassword,$gatewaysecret;
      if (DEBUG===TRUE) return;
      if (!$gatewayemail OR !$gatewaypassword OR !$gatewaysecret) exit('Please, configure SMS API gateway access in '.__FILE__.'!');
      // when SMS received, check if secret matches or exit, if does not:
      if (isset($_POST["secret"]) AND isset($_POST["id"]) AND $_POST["secret"]<>$gatewaysecret) exit;
      }

   function Text()
      {
      return $this->message;
      }

   function ProcessedText()
      {
      return strtoupper($this->message);
      }

   function Number()
      {
      return $this->number;
      }

   function UUID()
      {
      return $this->uuid;
      }

   function Time()
      {
      return $this->time;
      }

   function IPAddress()
      {
      return $this->ipaddress;
      }

    // confirm SMS received to API
   function Respond()
      {
      if (DEBUG===TRUE) return;
      }

   // send SMS message via API
   // returns status code{200 if OK}
   function Send($number,$text)
    {
      global $gatewayemail,$gatewaypassword;
        $statusCode=0;
        $smsgateway=new SmsGateway($gatewayemail,$gatewaypassword);
        $callback=$smsgateway->auth();
        if($callback['response']==false)
        {
            $statusCode=0;
        }
        else
        { 
           //error_log(json_encode($callback));
            //get tokens (access token, refresh token)
            $accessToken=$callback['response']['payload']['access_token'];
            $refreshToken=$callback['response']['payload']['refresh_token'];
            //send message
            $result=$smsgateway->sendMessageToNumber(array($number),$text,"BIKESHARE",array('access_token'=>$accessToken,'refresh_token'=>$refreshToken));
            //error_log(json_encode($result));
            $statusCode=intval($result['response']['status_code']);
        }
        return $statusCode;
      }
   }

?>