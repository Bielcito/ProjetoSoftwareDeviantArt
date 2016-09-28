<?php
    require_once('SessionManager.php');
    SessionManager::CloseSession();
    
    header('Location: .');
    die();
?>