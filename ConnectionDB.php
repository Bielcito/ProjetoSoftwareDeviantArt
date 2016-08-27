<?php
    require_once('Credentials.php');
    
    class ConnectionDB
    {
        function ConnectionDB()
        {
            $this->dbname = Credentials::DB()['dbname'];
            $this->dbuser = Credentials::DB()['dbuser'];
            $this->dbpassword = Credentials::DB()['dbpassword'];
            $this->dbhost = Credentials::DB()['dbhost'];
        }
        
        public function Connect()
        {
            $this->dbconn = pg_connect("host=$this->dbhost dbname=$this->dbname user=$this->dbuser password=$this->dbpassword")
    	    or die('Não foi possível conectar: ' . pg_last_error());
        }
        
        public function Close()
        {
            pg_close($this->dbconn);
        }
        
        public function execFromArray($array)
        {
            $this->Connect();
            
            $arraysize = count($array);
            for($i = 0; $i < $arraysize; $i++)
            {
                if(!pg_query($this->dbconn, $array[$i]))
                {
                    echo 'Cannot execute '. $array[$i].'<br>';
                }
            }
            
            $this->Close();
        }
        
        public function exec($query)
        {
            $this->Connect();
            
            if(!pg_query($this->dbconn, $query))
            {
                echo 'Cannot execute'. $query.'<br>';
                echo pg_last_error;
            }
            
            $this->Close();
        }
        
        private $dbconn;
        private $dbname;
        private $dbuser;
        private $dbpassword;
        private $dbhost;
    }
?>