<?php

abstract class _FileParser
{
    function __construct(string $path)
    {
    	$this->path = $path;
    	
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
    
    public abstract function splitFileInQueries() : string;

    private $queries;
    private $path;
}


?>