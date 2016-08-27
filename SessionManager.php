<?php
    class SessionManager
    {
        static function StartSession()
        {
            if(session_status() != PHP_SESSION_ACTIVE)
            {
                session_start();
            }
            
            if (!isset($_SESSION['STARTED'])) 
            {
                $_SESSION['STARTED'] = new DateTime('NOW');
            }
            
            $_SESSION['LAST_ACTIVITY'] = new DateTime('NOW');
        }
        
        static function CloseSession()
        {
            if(session_status() != PHP_SESSION_ACTIVE)
            {
                session_start();
            }
            
            session_unset();
            session_destroy(); 
        }
        
        static function echoSessionStatus()
        {
            echo 'last activity: ';
            
            if(isset($_SESSION['LAST_ACTIVITY']))
            {
                echo $_SESSION['LAST_ACTIVITY']->format('d/m/y h:i:s');
            }
            else
            {
                echo 'didn\'t set';
            }
            echo '<br>';
            
            echo 'started: ';
            if(isset($_SESSION['STARTED']))
            {
                echo $_SESSION['STARTED']->format('d/m/y h:i:s');
            }
            else
            {
                echo 'didn\'t set';
            }
            echo '<br>';
            
            echo 'access token: ';
            if(isset($_SESSION['ACCESS_TOKEN']))
            {
                echo $_SESSION['ACCESS_TOKEN'];
            }
            else
            {
                echo 'didn\'t set';
            }
            echo '<br>';
        }
    }
?>