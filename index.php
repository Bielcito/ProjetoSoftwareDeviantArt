<?php
    ini_set('max_execution_time', 5000);
    
    header('Content-Type: text/html; charset=UTF-8');

    require_once('Application.php');    //Inclui as classes importantes para serem usadas neste arquivo. Diferentemente do comando 'include', o 'require_once' só inclui o arquivo se ele ainda não estiver incluido ainda.
    $app = new Application(); // Aplicação, só existe uma dela por página.

    //Os três ifs a seguir foram muito utilizados em tempo de execução para poupar tempo e realizar testes rápidos no navegador:
    
    if(isset($_GET['c']))//Caso esteja escrito "?c=deleteAllDeviations" no fim da url na barra de endereços do navegador, o banco de Dados deleta todos os deviations, CUIDADO!
    {
        if($_GET['c'] == 'deleteAllDeviations')
        {
            echo 'APAGOU TUDO!!<br>';
            //$app->deleteAllDeviations();
        }
    }
    
    if(isset($_GET['c']))//O mesmo que o caso anterior, mas com outra frase. Neste caso, ele recria o banco de dados através do método criarBancoDeDados do Application.
    {
        if($_GET['c'] == 'criarBancoDeDados')
        {
            echo 'CRIOU TUDO!!<br>';
            $app->criarBancoDeDados();
        }
    }

    if(isset($_GET['c']))//Este aqui é para mostrar as informações do php na tela, serviu para saber principalmente a posição do arquivo de configuração "php.ini" onde tive que setar diversos plugins para o sistema. Como por exemplo, o curl_init() e pg_connect().
    {
        if($_GET['c'] == 'phpinfo')
        {
            phpinfo();
            die();
        }
    }
    
    //Executar as operações dentro do escopo do try para que caso haja alguma exceção, ela seja tratada.
    try
    {
        //$app->getDeviantManager()->atualizarContents();


        //Exemplo atualizando o content de um deviation passando um deviationid e o coddeviation referente a ele:
        //$result = $app->getDeviantManager()->fetchDeviationID("AAF1856E-80E0-44FC-D3FC-F1149112F463");
        //$app->getDeviantManager()->execFetchDeviationID($result, 1);

        //As seguintes instruções são exemplos de algumas formas de como se fazer inserções no banco de dados:

        /*$var = "Megaman";
        $results = $app->getDeviantManager()->newest("", str_replace(' ', '%20', $var), "24", "1");
        while(is_null($results))
        {
            sleep(1);
            $results = $app->getDeviantManager()->newest("", str_replace(' ', '%20', $var), "24", "1");
        }
        $app->getDeviantManager()->execDeviant($results->results);*/
        
        //--

        /*$var = "WaterColor";
        $results = $app->getDeviantManager()->tag(str_replace(' ', '%20', $var), "744", "24");
        while(is_null($results))
        {
            sleep(1);
            $results = $app->getDeviantManager()->tag(str_replace(' ', '%20', $var), "744", "24");
        }
        
        while($results->has_more)
        {
            $app->getDeviantManager()->execDeviant($results->results);
            $results = $app->getDeviantManager()->tag(str_replace(' ', '%20', $var), $results->next_offset, "24");
            while(is_null($results))
            {
                error_log("Repitiu no $results->next_offset :(");
                sleep(1);
                $results = $app->getDeviantManager()->tag(str_replace(' ', '%20', $var), $results->next_offset, "24");
            }
        }*/

        //--

        //$app->getDeviantManager()->atualizarTags();

        //--

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
        
        //Teste para verificar se um email está disponível para ser cadastrado:
        /*if($app->getLoginManager()->isEmailAvailable("shiha_hta_htinha@hotmail.com"))
        {
            echo "Email1 disponível!<br>";
        }
        if($app->getLoginManager()->isEmailAvailable("email"))
        {
            echo "Email2 disponível!<br>";
        }*/
        
        //Teste para verificar se um usuário está disponível para ser cadastrado:
        //if($app->getLoginManager()->isUserAvailable("OMestreDosMagos"))
        //{
        //    echo "Usuário disponível!<br>";
        //}
        
        //Exemplo que tenta logar um usuário:
        //$app->getLoginManager()->login("Fred", "123456");
        
        //Use isto para deslogar um usuário:
        //$app->getLoginManager()->logout();
    }
    
    catch(Exception $e)
    {
        //Trata qualquer exceção que acontecera dentro do método Try.
        echo $e->getMessage();
    }
    
    //Imprime estado atual da sessão, desabilite caso não queira mostrar a sua senha na tela inicial:
    SessionManager::echoSessionStatus();
?>