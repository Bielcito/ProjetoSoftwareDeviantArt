<?php
    ini_set('max_execution_time', 5000);
    header('Content-Type: text/html; charset=UTF-8');
    require_once('DeviantARTSTATS.php');
    
    $app = new DeviantARTSTATS();

	require_once 'A.php';
	
	/*$b = new B();
	var_dump($b::test());*/
?>