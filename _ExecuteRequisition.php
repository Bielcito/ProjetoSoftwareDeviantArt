<?php

abstract class _ExecuteRequisition
{
	function __construct(string $s)
	{
		$this->s = $s;
	}
	
	public abstract function exec();
	
	private function g($var)
	{
		//Se for uma variável nula:
		if(is_null($var))
		{
			return "'null'";
		}
		//Se for uma variável booleana:
		else if(is_bool($var))
		{
			if($var)
			{
				return 'true';
			}
			else
			{
				return 'false';
			}
		}
		//Se for uma string:
		else if(is_string($var))
		{
			return "'".htmlspecialchars($var, ENT_QUOTES)."'";
		}
		//Se for uma integer:
		else if(is_int($var))
		{
			return $var;
		}
	}
	
	private $json;
}

?>