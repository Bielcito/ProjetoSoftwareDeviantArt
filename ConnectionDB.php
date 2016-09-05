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
                    echo 'Cannot execute:<br>'. '<pre>'.$array[$i].'</pre>'.'<br>';
                    echo '<pre>'.str_replace(["\r\n", "\r", "\n"], "<br/>", pg_last_error($this->dbconn)).'</pre>';
                }
            }
            
            $this->Close();
        }
        
        public function exec($query)
        {
            $this->Connect();
            
            $result = pg_query($this->dbconn, $query);
            
            if(!$result)
            {
                echo 'Cannot execute:<br>'. '<pre>'.$query.'</pre>'.'<br>';
                echo '<pre>'.str_replace(["\r\n", "\r", "\n"], "<br/>", pg_last_error($this->dbconn)).'</pre>';
            }
            
            $this->Close();
            
            return $result;
        }
        
        private $dbconn;
        private $dbname;
        private $dbuser;
        private $dbpassword;
        private $dbhost;
    }
?>