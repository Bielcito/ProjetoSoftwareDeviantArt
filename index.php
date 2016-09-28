<?php
    
    //Importa a página login.php para o index.php, assim ela abre na página inicial:

    //Inclui as classes importantes para serem usadas neste arquivo. Diferentemente do comando 'include', o 'require_once'
    //só inclui o arquivo se ele ainda não estiver incluido ainda.
    require_once('Application.php');
    $app = new Application();
    
    //Caso esteja escrito "?c=deleteAllDeviations" no fim da url, na barra de endereços do navegador
    //o banco de Dados deleta todos os deviations.
    if(isset($_GET['c']))
    {
        if($_GET['c'] == 'deleteAllDeviations')
        {
            echo 'APAGOU TUDO!!<br>';
            $app->deleteAllDeviations();
        }
    }
    
    if(isset($_GET['c']))
    {
        if($_GET['c'] == 'criarBancoDeDados')
        {
            echo 'CRIOU TUDO!!<br>';
            $app->criarBancoDeDados();
        }
    }
    
    //Executar as operações dentro do escopo do try para que caso haja alguma exceção, ela seja tratada.
    try
    {
        //CATEGORIAS!
        /*$qcat = $app->getDeviantManager()->categories('%2F');
        while(!$qcat)
        {
            throw new Exception("Deviant Art recusou o pedido inicial!<br>");
            sleep(1);
            $qcat = $app->getDeviantManager()->categories('%2F');
        }
        
        $app->getDeviantManager()->execCategories($qcat);*/
        
        
        //DADOS NEWEST!
        /*$var = "Magus Bride";
        $results = $app->getDeviantManager()->newest("", str_replace(' ', '%20', $var), "0", "24");
        var_dump($results);
        while(is_null($results))
        {
            sleep(1);
            $results = $app->getDeviantManager()->newest("", str_replace(' ', '%20', $var), "0", "24");
        }
        $app->getDeviantManager()->execDeviant($results->results);*/
        
        //DADOS TAG!
        /*$var = "WaterColor";
        $results = $app->getDeviantManager()->tag(str_replace(' ', '%20', $var), "0", "24");
        while(is_null($results))
        {
            sleep(1);
            $results = $app->getDeviantManager()->tag(str_replace(' ', '%20', $var), "0", "24");
        }
        
        while($results->has_more)
        {
            $app->getDeviantManager()->execDeviant($results->results);
            $results = $app->getDeviantManager()->tag(str_replace(' ', '%20', $var), $results->next_offset, "24");
            while($results == null)
            {
                sleep(1);
                $results = $app->getDeviantManager()->tag(str_replace(' ', '%20', $var), "0", "24");
            }
        }*/
        
        //ATUALIZAR TAG!
        //$app->getDeviantManager()->atualizarTags();
        
        
        
        
        //Verifica se enviaram uma variável chamada 'username' do tipo POST para esta página,
        //Se for verdadeiro, então tenta logar no banco com o usuário e senha descritos.
        if(SessionManager::isPost('username'))
        {
            $username = SessionManager::getPost('username');
            $password = SessionManager::getPost('password');
            $app->getLoginManager()->login($username, $password);
        }
        
        if(SessionManager::isChanged('username'))//Se o login já tiver sido feito:
        {
            //Inclui o arquivo 
            require_once('searchpage.php');
        }
        else
        {
            require_once('login.php');
        }
        
        //Para pesquisar no banco de dados, fazer da seguinte forma:
        /*$algumacoisa = 'Fantasy';
        $query = "SELECT categoryname, categorypath FROM category WHERE categoryname = '$algumacoisa'"; //String
        $aux = $app->getConDB()->exec($query);
        
        while($result = pg_fetch_object($aux)) //Enquanto ainda houver resultados disponíveis...
        {
            echo 'nome: '.$result->categoryname.'<br>';
            echo 'caminho: '.$result->categorypath.'<br><br>';
        }
        
        //Teste para verificar se um email está disponível:
        /*if($app->getLoginManager()->isEmailAvailable("shiha_hta_htinha@hotmail.com"))
        {
            echo "Email1 disponível!<br>";
        }
        if($app->getLoginManager()->isEmailAvailable("email"))
        {
            echo "Email2 disponível!<br>";
        }*/
        
        //Teste para verificar se um usuário está disponível:
        //if($app->getLoginManager()->isUserAvailable("OMestreDosMagos"))
        //{
        //    echo "Usuário disponível!<br>";
        //}
        
        //Exemplo que tenta logar um usuário:
        //$app->getLoginManager()->login("Fred", "123456");
        
        //Use isto para deslogar:
        //$app->getLoginManager()->logout();
        
        //Exemplo que pesquisa deviations pela API e os salva no banco de dados:
        
        //Atualiza as tags do banco de dados:
        //$app->getDeviantManager()->atualizarTags();
        
        //Exemplo que pesquisa tag de alguns deviantids e salva no banco de dados:
        /*$deviationids = [
            "45059129-D879-53FC-FE2B-311911C87310",
            "109D0786-280F-02E6-9C4F-4CF155B80864"
        ];
        $results = $app->getDeviantManager()->deviantInf($deviationids);
        $app->getDeviantManager()->execDeviantInf($results);*/
        
        //Exemplo que salva todas as categorias do deviantART no banco de dados:
    }
    
    catch(Exception $e)
    {
        echo $e->getMessage();
    }
    
    //Imprime estado atual da sessão:
    SessionManager::echoSessionStatus();
?>