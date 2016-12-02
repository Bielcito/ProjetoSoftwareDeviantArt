<?php
	require_once('ConDB.php');
	require_once('DeviantManager.php');
	require_once('FileParser.php');
	require_once('LoginManager.php');
	require_once('Credentials.php');
	require_once('AccessToken.php');

	/**
	 * Classe principal do framework.
	 * @author Bielcito
	 *
	 */
	class DeviantARTSTATS {
		/**
		 * Construtor com as variáveis necessárias para funcionamento do framework.
		 */
		function __construct()
		{
			$this->conDB = new ConDB(); // inicia a conexão o PostgreSQL e realiza operações nele.
			$this->deviantManager = new DeviantManager($this->conDB); // chamadas de API ao deviantART.
			$this->fileparser = new FileParser('Categories.sql'); // lê um arquivo de comandos do PostgreSQL e os divide.
			$this->loginManager = new LoginManager($this->conDB); // gerencia os logins
			$this->access_token = new AccessToken();
			$this->access_token->verifyAccessToken();
			$this->checkDatabase();
		}
		
		/**
		 * Verifica se o banco de dados 'devartdb' existe, e se não existir, cria ele.
		 */
		function checkDatabase()
		{
			$query = "SELECT 1 FROM pg_database WHERE datname='".Credentials::DB()->dbname."'";
			$result = $this->conDB->exec($query);
			if(pg_fetch_object($result))
			{
				return;
			}
			else
			{
				$query = "CREATE DATABASE devartDB";
				$result = $this->conDB->exec($query);
			}
		}
		
		/**
		 * Cria o banco de dados a partir do arquivo passado por referência no início.
		 */
		public function criarBancoDeDados()
		{
			$this->conDB->execFromArray($this->fileparser->GetQueries());
		}
		
		public function getConDB() : ConDB
		{
			return $this->conDB;
		}
		
		public function getLoginManager() : LoginManager
		{
			return $this->loginManager;
		}
		
		public function getDeviantManager() : DeviantManager
		{
			return $this->deviantManager;
		}
		
		private $deviantManager;
		private $conDB;
		private $fileparser;
		private $loginManager;
		private $access_token;
	}
?>