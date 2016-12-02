<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Busca</title>
    <link rel="stylesheet" href="css/searchpage.css">
  </head>

  <body>
    
    <div style="text-align: right; padding-right: 30px; padding-top: 10px">
        <a href="logout.php">Logout</a> <a href="Lista.php">Minha Lista</a>
    </div>
    
    <div class="searchconteiner">
		<h1>Minha Lista</h1>
	    <h3>Escrever algo aqui ~~~~~~~~!!!!</h3>
    </div>
    
    <div>
    <?php

	include_once('ConDB.php');
	include_once('SessionManager.php');
	
	$conDB = new ConDB();
	
	$query = "SELECT DISTINCT deviation.coddeviation, deviation.title, deviation.url, stats.favourites, content.src, stats.comments FROM deviation
	LEFT JOIN tag on tag.coddeviation = deviation.coddeviation
	LEFT JOIN stats on deviation.codstats = stats.codstats
	LEFT JOIN content on deviation.codcontent = content.codcontent
	LEFT JOIN userdeviation on deviation.coddeviation = userdeviation.coddeviation
	LEFT JOIN userdata on userdata.username = userdeviation.username
	WHERE userdeviation.username = '".SessionManager::get('username')."'";
	
	$aux = $conDB->exec($query); // para executar o sql
	$line = 0;
	
	while($result = pg_fetch_object($aux)) //Enquanto ainda houver resultados disponíveis...
	{
		//Do something...
		//Para acessar algum campo, usar '$result->campo'.
		echo 'Title: '.$result->title.' ';
		echo '<button id=button'.$line.' onclick="buttonClick('.$result->coddeviation.', '.$line.')"> Remover da minha Lista </button><br>';
		echo 'Favourites: '.$result->favourites.' ';
		echo 'Comments: '.$result->comments.'<br>';
		echo "<img src=\"$result->src\"><br>";
		echo $result->url.'<br><br>';
		$line++;
	}

?>
    </div>
    
    
  </body>
</html>