<?php

    require_once('DeviantManager.php');
    require_once('ConDB.php');
    require_once('FileParser.php');
    require_once('LoginManager.php');
    
    //Gerencia todo o sistema
    class Application
    {
        function __construct()
        {
            $this->conDB = new ConDB(); // inicia a conexão o PostgreSQL e realiza operações nele.
            $this->deviantManager = new DeviantManager($this->conDB); // chamadas de API ao deviantART.
            $this->fileparser = new FileParser(); // lê um arquivo de comandos do PostgreSQL e os divide.
            $this->loginManager = new LoginManager($this->conDB); // gerencia os logins
            $this->deviantManager->verifyAccessToken();
            //$this->criarBancodeDados();
        }
        
        public function atualizarBancoDeDados()
        {
            //Deleta o banco de dados:
            $this->conDB->exec('drop schema public cascade');
            $this->conDB->exec('create schema public');
            
            //Recria o banco de dados:
            $this->criarBancodeDados();
        }
        
        public function criarBancoDeDados()
        {
            $this->conDB->execFromArray($this->fileparser->GetQueries());
        }
        
        public function getConDB()
        {
            return $this->conDB;
        }
        
        public function getLoginManager()
        {
            return $this->loginManager;
        }
        
        public function getDeviantManager()
        {
            return $this->deviantManager;
        }
        
        public function deleteAllDeviations()
        {
            $this->conDB->exec('DELETE FROM thumb WHERE codthumb > 0');
            $this->conDB->exec('DELETE FROM deviation WHERE coddeviation > 0');
            $this->conDB->exec('DELETE FROM content WHERE codcontent > 0');
            $this->conDB->exec('DELETE FROM stats WHERE codstats > 0');
            $this->conDB->exec('DELETE FROM author WHERE codauthor > 0');
            $this->conDB->exec('DELETE FROM preview WHERE codpreview > 0');
        }
        
        private $deviantManager;
        private $conDB;
        private $fileparser;
        private $loginManager;
    }
?>