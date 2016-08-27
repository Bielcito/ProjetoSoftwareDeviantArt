<?php
    require_once('SessionManager.php');
    require_once('Application.php');
    
    SessionManager::StartSession();
?>


<meta charset="UTF-8"> 
<html>
    <head>
        <title>
            
        </title>
    </head>
    
    <body>
        <?php
            $app = new Application();
            
            /*$etc = new DeviantQuery;
            
            $etc->verifyAccessToken();*/
            
            //echo (new DateTime('NOW'))->sub(new DateInterval('PT1H'))->format('d/m/Y H:i:s');
            SessionManager::echoSessionStatus();
        ?>
        
    </body>
</html>