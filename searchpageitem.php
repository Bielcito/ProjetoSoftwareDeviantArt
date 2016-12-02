<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>deviantARTSTATS - Busca por Itens</title>
    <link rel="stylesheet" href="css/searchpage.css">
    <script type="text/javascript" src="js/searchpage.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!--script type="text/javascript" src="js/cssrefresh.js"></script-->
  </head>

  <body>
    
    <div style="text-align: right; padding-right: 30px; padding-top: 10px">
        <a href="logout.php">Logout</a> <a href="Lista.php">Minha Lista</a>
    </div>
    
    <div class="searchconteiner">
		<h1>Busca por itens</h1>
	    <h3>Utilize as caixinhas para melhorar sua busca.</h3>
	    <form method="get" onsubmit="return checkCheckbox()">
	        <input id="searchbar" class="searchbar" type="text" name="searchedtag" placeholder="Busca" required/>
	        <table id="searchtableoptions" class="tableoptions">
	            <tr>
	                <td><input id="tag" type="checkbox" name="tag">Pesquisar por Tag</td>
	                <td><input type="radio" name="order" value="favourite">Ordenar por Favoritos</td>
	            </tr>
	            <tr>
	                <td><input id="title" type="checkbox" name="title">Pesquisar por Título</td> 
	                <td><input type="radio" name="order" value="order">Ordenar por Título</td> 
	            </tr>
	            <tr>
	                <td><input id="category" type="checkbox" name="category">Pesquisar por Categoria</td>
	                <td><input type="radio" name="order" value="comment" required>Ordenar por Comentários<br></td>
	            </tr>
	        </table> 
	        <button type="submit">Procurar</button>
	        <p id="speech" class="speech" style="display: none;">É necessário marcar pelo menos uma caixa!</p>
	    </form>
    </div>
    
    <div>
    <?php
    	include_once('Application.php');
    	
        $app = new Application();
        $conDB = $app->getConDB();
        
        function helper(&$text, $s, &$v)
        {
            if($s == false)
            {
                return;
            }
            else
            {
                if(!$v)
                {
                    $v = true;
                    $text .= $s;
                }
                else
                {
                    $text .= ' OR '.$s;
                }
            }
        }
        
        //Verifica se enviaram uma variável chamada 'searchedtag' do tipo POST para esta página,
        //Se for verdadeiro, então tenta procurar o resultado no banco de dados.
        if(SessionManager::isGet('searchedtag'))
        {
            $searchedtag = SessionManager::getGet('searchedtag');
            echo "<script> document.getElementById('searchbar').value = '$searchedtag'</script>";
            $tag = false;
            $title = false;
            $category = false;
            
            if(SessionManager::isGet('tag'))
            {
                echo "<script> document.getElementById('tag').checked = true </script>";
                $tag = true;
                $tagtext = "tagname='$searchedtag'";
            }
            if(SessionManager::isGet('title'))
            {
                echo "<script> document.getElementById('title').checked = true </script>";
                $title = true;
                $titletext = "title LIKE '%$searchedtag%'";
            }
            if(SessionManager::isGet('category'))
            {
                echo "<script> document.getElementById('category').checked = true </script>";
                $category = true;
                $categorytext = "category='$searchedtag'";
            }
            
            if(SessionManager::isGet('order'))
            {
                $order = SessionManager::getGet('order');
                echo 
                "<script> 
                var order = document.getElementsByName('order');
                for(var i = 0; i < order.length; i++)
                {
                    if(order[i].value == '$order')
                    {
                        order[i].checked = true;
                    }
                }
                </script>";
                if($order == 'favourite')
                {
                    $ordertext = "ORDER BY favourites DESC";
                }
                else if($order == 'comment')
                {
                    $ordertext = "ORDER BY comments DESC";
                }
                else
                {
                    $ordertext = "ORDER BY title ASC";
                }
            }
            
            if(!$tag && !$title && !$category || $searchedtag == "")
            {
                return;
            }
            
            $searchtext = "";
            $v = false;
            if($tag)
            {
                helper($searchtext, $tagtext, $v);
            }
            if($title)
            {
                helper($searchtext, $titletext, $v);
            }
            if($category)
            {
                helper($searchtext, $categorytext, $v);
            }
        
            $query = "SELECT DISTINCT deviation.coddeviation, deviation.title, deviation.url, stats.favourites, content.src, stats.comments FROM deviation 
            LEFT JOIN tag on tag.coddeviation = deviation.coddeviation 
            LEFT JOIN stats on deviation.codstats = stats.codstats 
            LEFT JOIN content on deviation.codcontent = content.codcontent
            WHERE $searchtext $ordertext";
            
            $aux = $conDB->exec($query); // para executar o sql
            $line = 0;
            
            while($result = pg_fetch_object($aux)) //Enquanto ainda houver resultados disponíveis...
            {
                //Do something...
                //Para acessar algum campo, usar '$result->campo'.
                echo 'Title: '.$result->title.' ';
                echo '<button id=button'.$line.' onclick="buttonClick('.$result->coddeviation.', '.$line.')"> Adicionar à Minha Lista </button><br>';
                echo 'Favourites: '.$result->favourites.' ';
                echo 'Comments: '.$result->comments.'<br>';
                echo "<img src=\"$result->src\"><br>";
                echo $result->url.'<br><br>';
                $line++;
            }
        }
    ?>
    </div>
    
    
  </body>
</html>
