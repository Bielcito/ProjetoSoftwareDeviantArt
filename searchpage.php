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

    
  </head>

  <body>
    <div>
	<h1>Busca de tags</h1>
    <form method="get">
    	<input type="text" name="searchedtag" placeholder="Busca" formaction=""/>
        <button>Procurar</button>
    </form>
    </div>
    
    <div>
    <?php
        $app = new Application();
        $conDB = $app->getConDB();
        
        //Verifica se enviaram uma variável chamada 'searchedtag' do tipo POST para esta página,
        //Se for verdadeiro, então tenta logar no banco com o usuário e senha descritos.
        if(SessionManager::isGet('searchedtag'))
        {
            $searchedtag = SessionManager::getGet('searchedtag');
        
            $query = "SELECT title, url FROM tag LEFT JOIN deviation on deviation.codtag = tag.codtag WHERE tagname='$searchedtag'";
            $aux = $conDB->exec($query); // para executar o sql
            while($result = pg_fetch_object($aux)) //Enquanto ainda houver resultados disponíveis...
            {
                //Do something...
                //Para acessar algum campo, usar '$result->campo'.
                echo $result->title.'<br>';
                echo $result->url.'<br><br>';
            }
        }
        
        
        
    ?>
    </div>
    
    
  </body>
</html>
