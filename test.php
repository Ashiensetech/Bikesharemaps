<?php 
    require("config.php");
    require("db.class.php");
    require('common.php');

    echo "Start of test...........</br>";


    // $to=array("18302794556");
    // $text="Welcome to bike share maps.";

    // $smsgateway=new SmsGateway("molekoreginald@gmail.com","Reginald_9");
    // $callback=$smsgateway->auth();
    // if($callback['response']==false){
    //     echo "Authentication failed";
    // }else{ 
    //     //get tokens (access token, refresh token)
    //     $accessToken=$callback['response']['payload']['access_token'];
    //     $refreshToken=$callback['response']['payload']['refresh_token'];
    //     //send message
    //     $res=$smsgateway->sendMessageToNumber($to,$text,"BIKESHARE",array('access_token'=>$accessToken,'refresh_token'=>$refreshToken));
    //     echo json_encode($res);
    // }
    $htmlmessage = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="https://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        </head>
        <body>
        <div>
                <p>'._('Hello').' '.$firstname.",\n\n".'</p>
                <p>'._('By clicking the following link you agree to the System rules:')."\n".'</p>
                <p>"<a href ="'.$systemURL."agree.php?key".$userKey.'">'.$systemURL."agree.php?key".$userKey.'</a></p>
        </div>
        </body>
        </html>';

    sendEmail("molekoreginald@gmail.com","BIKESHARE",$htmlmessage);
    
    echo "</br> End of test??";
?>