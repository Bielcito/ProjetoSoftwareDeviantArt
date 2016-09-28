<?php
    require_once('Credentials.php');
    
    class ConDB
    {
        function ConDB()
        {
            $this->dbname = Credentials::DB()['dbname'];
            $this->dbuser = Credentials::DB()['dbuser'];
            $this->dbpassword = Credentials::DB()['dbpassword'];
            $this->dbhost = Credentials::DB()['dbhost'];
        }
        
        public function Connect()
        {

            $this->dbconn = pg_connect("host=$this->dbhost dbname=$this->dbname user=$this->dbuser password=$this->dbpassword");
            if(!$this->dbconn)
    	        throw new Exception('Exceção lançada: não foi possível conectar ao banco de dados<br>');
        }
        
        public function Close()
        {
            pg_close($this->dbconn);
        }
        
        public function execFromArray($array)
        {
            try
            {
                $this->Connect();
            }
            
            catch(Exception $e)
            {
                echo($e->getMessage());
            }
            
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