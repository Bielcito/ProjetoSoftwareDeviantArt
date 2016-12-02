<?php

class _Credentials
{
	function set(string $client_id, string $client_secret, string $dbhost, string $dbname, string $dbuser, string $dbpassword)
	{
		self::$client_id = $client_id;
		self::$client_secret = $client_secret;
		self::$dbhost = $dbhost;
		self::$dbname = $dbname;
		self::$dbuser = $dbuser;
		self::$dbpassword = $dbpassword;
	}
	
	function test($client_id, $client_secret, $dbhost, $dbname, $dbuser, $dbpassword)
	{
		self::set($client_id, $client_secret, $dbhost, $dbname, $dbuser, $dbpassword);
	}
	
	static function App()
	{
		return (object)array(
				'client_id' => self::$client_id,//'5100',
				'client_secret' => self::$client_secret//'5a91c5a46dad4ad73278a570869b7791'
		);
	}

	static function DB()
	{
		return(object)array(
				'dbhost' => self::$dbhost,//'localhost',
				'dbname' => self::$dbname,//'devartdb',
				'dbuser' => self::$dbuser,//'postgres',
				'dbpassword' => self::$dbpassword//'th3f0das'
		);
	}
	
	public static $client_id;
	public static $client_secret;
	
	public static $dbhost;
	public static $dbname;
	public static $dbuser;
	public static $dbpassword;
}

?>