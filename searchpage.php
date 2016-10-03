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
    <!--script type="text/javascript" src="js/cssrefresh.js"></script-->
  </head>

  <body>
    <div>
	<h1>Busca</h1>
    <h3>Os resultados mais favoritados pelos usuários serão mostrados primeiro.</h3>
    <form method="get">
        <input id="searchbar" class="searchbar" type="text" name="searchedtag" placeholder="Busca" formaction=""/>
        <table class="tableoptions">
            <tr>
                <td><input id="tag" type="checkbox" name="tag">Pesquisar por Tag</td>
                <td><input type="radio" name="order" value="favourite">Ordenar por Favoritos</td>
            </tr>
            <tr>
                <td><input id="title" type="checkbox" name="title">Pesquisar por Título</td> 
                <td><input type="radio" name="order" value="order">Ordernar por Ordem Alfabética</td> 
            </tr>
            <tr>
                <td><input id="category" type="checkbox" name="category">Pesquisar por Categoria</td>
                <td><input type="radio" name="order" value="comment">Ordenar por Comentários<br></td>
            </tr>
        </table> 
        <button>Procurar</button>
    </form>
    </div>
    
    <div>
    <?php
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
                    $text .= ' AND '.$s;
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
                    $ordertext = "";
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
        
            $query = "SELECT DISTINCT deviation.title, deviation.url, stats.favourites, content.src, stats.comments FROM deviation 
            LEFT JOIN tag on tag.coddeviation = deviation.coddeviation 
            LEFT JOIN stats on deviation.codstats = stats.codstats 
            LEFT JOIN content on deviation.codcontent = content.codcontent
            WHERE $searchtext $ordertext";
            
            $aux = $conDB->exec($query); // para executar o sql
            while($result = pg_fetch_object($aux)) //Enquanto ainda houver resultados disponíveis...
            {
                //Do something...
                //Para acessar algum campo, usar '$result->campo'.
                echo 'Title: '.$result->title.'<br>';
                echo 'Favourites: '.$result->favourites.' ';
                echo 'Comments: '.$result->comments.'<br>';
                echo "<img src=\"$result->src\"><br>";
                echo $result->url.'<br><br>';
            }
        }
    ?>
    </div>
    
    
  </body>
</html>
