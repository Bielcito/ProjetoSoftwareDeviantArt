	<?php 

	//Abre o arquivo:
	$file = fopen("Categories.sql", "r");
	if(!$file)
		throw new Exception("Exceção lançada: não foi possível abrir o arquivo!");

		//Divide os comandos:
		$queries = explode(";;", fread($file, filesize("Categories.sql")));

		//Fecha o arquivo:
		fclose($file);

		return $queries;

	?>