<?php

    class Credentials
    {
        static function App()
        {
            return array(
               'CLIENT_ID' => '5100',
               'CLIENT_SECRET' => '5a91c5a46dad4ad73278a570869b7791'
            );
        }
        
        static function DB()
        {
            return array(
                'dbname' => 'devartdb',
                'dbuser' => 'postgres',
                'dbpassword' => '1240',
                'dbhost' => 'localhosT'
            );
        }
    }

?>