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
        
        static function UpdateAccessToken($AT)
        {
            if(session_status() != PHP_SESSION_ACTIVE)
            {
                session_start();
            }
            
            $_SESSION['ACCESS_TOKEN_TIME'] = new DateTime('NOW');
            $_SESSION['ACCESS_TOKEN'] = $AT;
        }
        
        static function getStarted()
        {
            if(session_status() != PHP_SESSION_ACTIVE)
            {
                session_start();
            }
            
            if(isset($_SESSION['STARTED']))
            {
                return $_SESSION['STARTED'];
            }
        }
        
        static function getLastActivity()
        {
            if(session_status() != PHP_SESSION_ACTIVE)
            {
                session_start();
            }
            
            if(isset($_SESSION['LAST_ACTIVITY']))
            {
                return $_SESSION['LAST_ACTIVITY'];
            }
        }
        
        static function getAccessTokenTime()
        {
            if(session_status() != PHP_SESSION_ACTIVE)
            {
                session_start();
            }
            
            if(isset($_SESSION['ACCESS_TOKEN_TIME']))
            {
                return $_SESSION['ACCESS_TOKEN_TIME'];
            }
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
            
            echo 'access token time: ';
            if(isset($_SESSION['ACCESS_TOKEN_TIME']))
            {
                echo $_SESSION['ACCESS_TOKEN_TIME']->format('d/m/y h:i:s');
            }
            else
            {
                echo 'didn\'t set';
            }
            echo '<br>';
        }
        
        static function isChanged($var)
        {
            if(session_status() != PHP_SESSION_ACTIVE)
            {
                session_start();
            }
            
            if(isset($_SESSION[$var]))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        
        static function get($var)
        {
            if(session_status() != PHP_SESSION_ACTIVE)
            {
                session_start();
            }
            
            return $_SESSION[$var];
        }
    }
?>