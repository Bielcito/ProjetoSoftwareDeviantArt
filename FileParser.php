<?php

class FileParser
{
    function FileParser()
    {
        $this->queries = $this->splitFileInQueries();
    }
    
    public function getQueries()
    {
        return $this->queries;
    }
    
    private function splitFileInQueries()
    {
        //Abre o arquivo:
        $file = fopen("Categories.sql", "r") or die("Unable to open file!");
        
        //Divide os comandos:
        $queries = explode(";;", fread($file, filesize("Categories.sql")));
        
        //Fecha o arquivo:
        fclose($file);
        
        return $queries;
    }

    private $queries;
}


?>