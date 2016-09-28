<?php

class FileParser
{
    function FileParser()
    {
        try
        {
            $this->queries = $this->splitFileInQueries();
        }
        
        catch(Exception $e)
        {
            echo($e->getMessage());
        }
    }
    
    public function getQueries()
    {
        return $this->queries;
    }
    
    private function splitFileInQueries()
    {
        //Abre o arquivo:
        $file = fopen("Categories.sql", "r");
        if(!$file)
            throw new Exception("Exceção lançada: não foi possível abrir o arquivo!<br>");
        
        //Divide os comandos:
        $queries = explode(";;", fread($file, filesize("Categories.sql")));
        
        //Fecha o arquivo:
        fclose($file);
        
        return $queries;
    }

    private $queries;
}


?>