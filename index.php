<?php
    //Inclui as classes importantes para serem usadas neste arquivo. Diferentemente do comando 'include', o 'require_once'
    //só inclui o arquivo se ele ainda não estiver incluido ainda.
    require_once('SessionManager.php'); // Gerencia a sessão do php. Olha o arquivo 'README' para mais informações sobre sessão.
    require_once('Application.php'); // 
    
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
            $results = $app->getDeviantManager()->newest("digitalart/paintings","digimon","0","8")->results;
            
            $app->getDeviantManager()->execDeviant($results);
            
            SessionManager::echoSessionStatus();
        ?>
        
    </body>
</html>