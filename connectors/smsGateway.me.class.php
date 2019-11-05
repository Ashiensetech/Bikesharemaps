<?php

    class SmsGateway {

        static $baseUrl = "https://sms.solutions4mobiles.com";
        static $senderID = "BIKESHARE";

        function __construct($email,$password) {
            $this->email = $email;
            $this->password = $password;
        }

        function sendMessageToNumber($to=[], $message, $from="BIKESHARE", $tokens=[]) {
            $query = array(['to'=>$to,'from' =>$from,'message'=>$message]);
            return $this->makeJsonRequest('/apis/sms/mt/v2/send','POST',$query,$tokens);
        }

        function auth($options=[]){
            $query=array_merge(['type'=>'access_token'],$options);
            return $this->makeAuthRequest('/apis/auth','POST',$query);
        }

        private function makeJsonRequest ($url, $method, $fields=[],$tokens=[]) {

            $url = smsGateway::$baseUrl.$url;
            $fieldsJson=json_encode($fields);

            $ch = curl_init($url);
            if($method == 'POST')
            {
                curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fieldsJson);
            }
            else
            {
                curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fieldsJson);
            }
            
            $authorization="Authorization: Bearer ".$tokens['access_token'];;
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',$authorization));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_HEADER , false);  // we want headers
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec ($ch);

            $return['response'] = json_decode($result,true);

            if($return['response'] == false)
                $return['response'] = $result;

            $return['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close ($ch);

            return $return;
        }
        private function makeAuthRequest ($url, $method, $fields=[]) {

            $fields['username'] = $this->email;
            $fields['password'] = $this->password;

            $url = smsGateway::$baseUrl.$url;

            //$fieldsString = http_build_query($fields);
            $fieldsJson=json_encode($fields);

            $ch = curl_init($url);
            if($method == 'POST')
            {
                curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fieldsJson);
            }
            else
            {
                curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fieldsJson);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_HEADER , false);  // we want headers
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec ($ch);

            $return['response'] = json_decode($result,true);

            if($return['response'] == false)
                $return['response'] = $result;

            $return['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close ($ch);

            return $return;
        }
        private function makeRequest ($url, $method, $fields=[]) {

            $fields['username'] = $this->email;
            $fields['password'] = $this->password;

            $url = smsGateway::$baseUrl.$url;

            $fieldsString = http_build_query($fields);


            $ch = curl_init();

            if($method == 'POST')
            {
                curl_setopt($ch,CURLOPT_POST, count($fields));
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fieldsString);
            }
            else
            {
                $url .= '?'.$fieldsString;
            }

            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_HEADER , false);  // we want headers
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec ($ch);

            $return['response'] = json_decode($result,true);

            if($return['response'] == false)
                $return['response'] = $result;

            $return['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close ($ch);

            return $return;
        }

    }


?>