<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>deviantARTSTATS - Estatísticas</title>
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
		<h1>Estatísticas</h1>
	    <h3>Mostra algumas estatísticas do servidor em tempo real.</h3>
	    <form method="get" onsubmit="return checkCheckbox()">
	        <table id="searchtableoptions" class="tableoptions">
	            <tr>
	                <td><input type="radio" name="order" value="favourite">5 Resultados</td>
	            </tr>
	            <tr> 
	                <td><input type="radio" name="order" value="order">10 Resultados</td> 
	            </tr>
	            <tr>
	                <td><input type="radio" name="order" value="comment" required>50 resultados<br></td>
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
        if(SessionManager::isGet('order'))
        {
            $searchedtag = SessionManager::getGet('order');
            
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
                    $ordertext = "ORDER BY count(tagname) DESC LIMIT 5";
                }
                else if($order == 'order')
                {
                    $ordertext = "ORDER BY count(tagname) DESC LIMIT 10";
                }
                else
                {
                    $ordertext = "ORDER BY count(tagname) DESC LIMIT 50";
                }
            }
        
            $query = "SELECT tag.tagname, count(tag.tagname) as tagcount FROM deviation 
            LEFT JOIN tag on tag.coddeviation = deviation.coddeviation 
            GROUP BY tag.tagname
            $ordertext";
            
            $aux = $conDB->exec($query); // para executar o sql
            $line = 0;
            
            while($result = pg_fetch_object($aux)) //Enquanto ainda houver resultados disponíveis...
            {
                //Do something...
                //Para acessar algum campo, usar '$result->campo'.
                echo 'Tagname: '.$result->tagname.'<br>';
                echo 'Número de vezes que foi utilizada: '.$result->tagcount.'<br>';
                $line++;
            }
        }
    ?>
    </div>
    
    
  </body>
</html>
