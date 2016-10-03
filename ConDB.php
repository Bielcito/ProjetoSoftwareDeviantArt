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
            $this->isInTransaction = false;
        }
        
        public function Connect()
        {

            $this->dbconn = pg_connect("host=$this->dbhost dbname=$this->dbname user=$this->dbuser password=$this->dbpassword");
            if(!$this->dbconn)
    	        throw new Exception('Exceção lançada: não foi possível conectar ao banco de dados');
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

        public function begin()
        {
            $this->Connect();

            $query = "BEGIN";
            $result = pg_query($this->dbconn, $query);
            if(!$result)
            {
                echo 'Cannot execute:<br>'. '<pre>'.$query.'</pre>'.'<br>';
                echo '<pre>'.str_replace(["\r\n", "\r", "\n"], "<br/>", pg_last_error($this->dbconn)).'</pre>';
                $this->close();
            }
            else
            {
                $this->isInTransaction = true;
            }
        }

        //Usado apenas quando uma transação estiver ativa:
        public function exect($query)
        {
            if($this->isInTransaction)
            {
                $result = pg_query($this->dbconn, $query);
            
                if(!$result)
                {
                    echo 'Cannot execute:<br>'. '<pre>'.$query.'</pre>'.'<br>';
                    echo '<pre>'.str_replace(["\r\n", "\r", "\n"], "<br/>", pg_last_error($this->dbconn)).'</pre>';
                }

                return $result;
            }
            else
            {
                throw new Exception("Exceção lançada: Você não está em uma transação!");
            }
        }

        public function commit()
        {
            $query = "COMMIT";
            $result = pg_query($this->dbconn, $query);
            if(!$result)
            {
                echo 'Cannot execute:<br>'. '<pre>'.$query.'</pre>'.'<br>';
                echo '<pre>'.str_replace(["\r\n", "\r", "\n"], "<br/>", pg_last_error($this->dbconn)).'</pre>';
            }

            $this->close();
            $this->isInTransaction = false;
        }

        public function rollback()
        {
            $query = "ROLLBACK";
            $result = pg_query($this->dbconn, $query);
            if(!$result)
            {
                echo 'Cannot execute:<br>'. '<pre>'.$query.'</pre>'.'<br>';
                echo '<pre>'.str_replace(["\r\n", "\r", "\n"], "<br/>", pg_last_error($this->dbconn)).'</pre>';
            }

            $this->close();
            $this->isInTransaction = false;
        }
        
        public function exec($query)
        {
            if(!$this->isInTransaction)
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
            else
            {
                throw new Exception("O banco está em uma transação! Você não pode usar o comando exec.");
            }
        }
        
        private $dbconn;
        private $dbname;
        private $dbuser;
        private $dbpassword;
        private $dbhost;
        private $isInTransaction;
    }
?>