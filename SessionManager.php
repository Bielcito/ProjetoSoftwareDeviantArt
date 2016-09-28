<?php
    class SessionManager
    {
        static function StartSession()
        {
            $status = session_status();
            if($status == PHP_SESSION_NONE)
            {
                //There is no active session
                session_start();
            }
            else if($status == PHP_SESSION_DISABLED)
            {
                //Sessions are not available
            }
            
            if (!isset($_SESSION['STARTED'])) 
            {
                $_SESSION['STARTED'] = new DateTime('NOW');
            }
            
            $_SESSION['LAST_ACTIVITY'] = new DateTime('NOW');
        }
        
        static function isPost($var)
        {
            if(session_status() != PHP_SESSION_ACTIVE)
            {
                session_start();
            }
            
            return isset($_POST[$var]);
        }
        
        static function isGet($var)
        {
            return isset($_GET[$var]);
        }
        
        static function getGet($var)
        {
            return $_GET[$var];
        }
        
        static function getPost($var)
        {
            return $_POST[$var];
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
        
        static function echoSessionStatus()
        {
            if(session_status() != PHP_SESSION_ACTIVE)
            {
                session_start();
            }
            
            echo '<pre>';
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
            
            echo 'username: ';
            if(isset($_SESSION['username']))
            {
                echo $_SESSION['username'];
            }
            else
            {
                echo 'didn\'t set';
            }
            echo '<br>';
            
            echo 'password: ';
            if(isset($_SESSION['password']))
            {
                echo $_SESSION['password'];
            }
            else
            {
                echo 'didn\'t set';
            }
            echo '<br>';
            echo '</pre>';
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
        
        static function set($var, $valor)
        {
            if(session_status() != PHP_SESSION_ACTIVE)
            {
                session_start();
            }
            
            $_SESSION[$var] = $valor;
        }
        
        static function remove($var)
        {
            if(session_status() != PHP_SESSION_ACTIVE)
            {
                session_start();
            }
            
            unset($_SESSION[$var]);
        }
    }
?>