<?php

require_once('_Credentials.php');

class Credentials extends _Credentials
{
	function __construct()
	{
		parent::set('5100', '5a91c5a46dad4ad73278a570869b7791', 'localhost', 'devartdb', 'postgres', 'th3f0das');
	}
	
	static function App()
	{
		return parent::App();
	}
	
	static function DB()
	{
		return parent::DB();
	}
}

?>