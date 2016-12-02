<?php 

require_once('_ExecuteRequisition.php');

class execFetchDeviationID extends _ExecuteRequisition
{
	function __construct($coddeviation, $conDB)
	{
		$this->coddeviation = $coddeviation;
		$this->conDB = $conDB;
	}
	
	public function exec()
	{
		if(array_key_exists('content', $this->s))
		{
			$src = $this->g($this->s->content->src);
			$height = $this->g($this->s->content->height);
			$width = $this->g($this->s->content->width);
			$transparency = $this->g($this->s->content->transparency);
			$filesize = $this->g($this->s->content->filesize);
	
			$this->conDB->begin();
	
			//Insere o content:
			$query = "INSERT INTO content VALUES(default, $src, $filesize, $height, $width, $transparency) RETURNING codcontent";
			$aux = $this->conDB->exect($query);
			if(!$result = pg_fetch_object($aux))
			{
				throw new Exception("Exceção lançada: Não foi possível inserir o content.");
			}
	
			//Aponta o deviation com coddeviation igual a $coddeviation para o content:
			$query = "UPDATE deviation SET codcontent = $result->codcontent WHERE coddeviation = $this->coddeviation";
			$aux = $this->conDB->exect($query);
			$this->conDB->commit();
		}
		else
		{
			$query = "UPDATE deviation SET codcontent = 0 WHERE coddeviation = $this->coddeviation";
			$aux = $this->conDB->exec($query);
		}
	}
	
	private $coddeviation;
	private $conDB;
}

?>