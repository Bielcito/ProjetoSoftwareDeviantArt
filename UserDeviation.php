<?php
	include('SessionManager.php');
	include('ConDB.php');

	if(SessionManager::isPost('coddeviation'))
	{
		$coddeviation = SessionManager::getPost('coddeviation');
		$db = new ConDB();
		$query = "INSERT INTO userdeviation VALUES ('".SessionManager::get('username').
		"', ".$coddeviation.")";
		$results = $db->exec($query);
		if($results != null)
		{
			echo "true";
		}
	}
	else
	{
		echo "false";
	}
?>