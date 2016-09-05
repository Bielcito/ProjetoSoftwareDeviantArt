<?php

    require_once('DeviantManager.php');
    require_once('ConnectionDB.php');
    require_once('FileParser.php');
    
    //Gerencia todo o sistema
    class Application
    {
        function Application()
        {
            $this->conDB = new ConnectionDB(); // inicia a conexão o PostgreSQL e realiza operações nele.
            $this->deviantManager = new DeviantManager($this->conDB); // chamadas de API ao deviantART.
            $this->fileparser = new FileParser(); // lê um arquivo de comandos do PostgreSQL e os divide.
            $this->criarBancodeDados();
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
        
        public function getDeviantManager()
        {
            return $this->deviantManager;
        }
        
        private $deviantManager;
        private $conDB;
        private $fileparser;
    }
?>