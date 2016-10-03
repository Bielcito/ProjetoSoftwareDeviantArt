<!DOCTYPE html>
<!--
Copyright (c) 2016 by Tyler Fry (http://codepen.io/frytyler/pen/EGdtg)
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
-->

<html>
  <head>
    <meta charset="UTF-8">
    <title>Busca</title>
    <link rel="stylesheet" href="css/searchpage.css">
    <script type="text/javascript" src="js/cssrefresh.js"></script>
  </head>

  <body>
    <div>
	<h1>Busca</h1>
    <h3>Os resultados mais favoritados pelos usuários serão mostrados primeiro.</h3>
    <form method="get">
    	<input class="searchbar" type="text" name="searchedtag" placeholder="Busca" formaction=""/>
        <input type="checkbox" name="tag">Pesquisar por Tag
        <input type="checkbox" name="tag">Pesquisar por Título
        <input type="checkbox" name="tag">Pesquisar por Categoria<br>
        <input type="checkbox" name="tag">Ordenar por Favoritos
        <input type="checkbox" name="tag">Pesquisar por Ordem Alfabética
        <input type="checkbox" name="tag">Pesquisar por Número de Comentários<br>
        <button>Procurar</button>
    </form>
    </div>
    
    <div>
    <?php
        $app = new Application();
        $conDB = $app->getConDB();
        
        //Verifica se enviaram uma variável chamada 'searchedtag' do tipo POST para esta página,
        //Se for verdadeiro, então tenta procurar o resultado no banco de dados.
        if(SessionManager::isGet('searchedtag'))
        {
            $searchedtag = SessionManager::getGet('searchedtag');
        
            $query = "SELECT deviation.title, deviation.url, stats.favourites, content.src FROM deviation 
            LEFT JOIN tag on tag.coddeviation = deviation.coddeviation 
            LEFT JOIN stats on deviation.codstats = stats.codstats 
            LEFT JOIN content on deviation.codcontent = content.codcontent
            WHERE tagname='$searchedtag' ORDER BY favourites DESC";
            $aux = $conDB->exec($query); // para executar o sql
            while($result = pg_fetch_object($aux)) //Enquanto ainda houver resultados disponíveis...
            {
                //Do something...
                //Para acessar algum campo, usar '$result->campo'.
                echo 'Title: '.$result->title.'<br>';
                echo 'Favourites: '.$result->favourites.'<br>';
                echo "<img src=\"$result->src\"><br>";
                echo $result->url.'<br><br>';
            }
        }
    ?>
    </div>
    
    
  </body>
</html>
