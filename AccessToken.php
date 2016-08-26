<?php

    include 'Credentials.php';

    class AccessToken
    {
        function AccessToken()
        {
            /*echo (Credentials::getAppCred'CLIENT_ID']); // CONSERTAR ISSOOOO!
            $this->client_id = Credentials::getAppCred()->CLIENT_ID;
            $this->client_secret = Credentials::getAppCred()->CLIENT_SECRET;*/
            $this->client_id = '5100';
            $this->client_secret = '5a91c5a46dad4ad73278a570869b7791';
        }
        
        function getAT()
        {
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'https://www.deviantart.com/oauth2/token',
                CURLOPT_POST => 1,
                CURLOPT_USERAGENT => $_SERVER ['HTTP_USER_AGENT'],
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_CAINFO => __DIR__ . "/cacert.pem",
                CURLOPT_POSTFIELDS => array(
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret
                    )
                ));
        
            $resp = curl_exec($curl);
            
            if($resp)
            {
                $json = json_decode($resp);
                echo($json->access_token);
            }
            else
            {
                die('Error: "' . curl_error($curl) . '" - Code: ' . curl_error($curl));
            }
            
            curl_close($curl);
        }
        
        private $client_id;
        private $client_secret;
    }

?>