<?php
    require_once('SessionManager.php');
    require_once('Credentials.php');
    require_once('ConDB.php');

    //Esta classe realiza as chamadas de API no site do deviantART e salva no banco de dados.
    class DeviantManager
    {
        function __construct($conDB) //Construtor
        {
            //Credenciais salvas em variáveis privadas nesta classe, que são necessárias para fazer a requisição da chave de acesso.
            //A chave de acesso é utilizada para fazer requisições no deviantART, e dura por apenas 3600 segundos.
            $this->client_id = Credentials::App()['CLIENT_ID'];
            $this->client_secret = Credentials::App()['CLIENT_SECRET'];

            //Para fazer qualquer leitura ou modificação no banco de dados:
            $this->conDB = $conDB;
        }
        
        // Esta função faz uma busca por deviations e retorna uma variável do tipo JSON, que deverá ser tratada em outro método.
            // $categorypath = Busca por categoria.
            // $q = Busca por título.
            // $offset = A partir de qual ponto o primeiro resultado será retornado. EX: Com 5000 resultados totais e com offset = 150, serão retornados apenas os resultados que vão de 150 até 174 (24 resultados).
            // $limit = limite de resultados para esta requisição. A API disse que era 150, mas impede que você peça mais que 24 resultados, não sei por que.*/
        public function newest($categorypath, $q, $offset, $limit)
        {
            // Inicializa a variável curl para realizar a requisição.
            $curl = curl_init();

            if(!$curl)// Tratando o erro.
            {
                return false;
            }
            
            // Url para a qual será feita a requisição, as linhas seguintes é chamado o método checkurl, que basicamente auxilia na criação da url completa, com as variáveis do tipo GET organizadas.
            $url = 'https://www.deviantart.com/api/v1/oauth2/browse/newest';
            $symbol = false;
            $number = 0;
            
            if($categorypath != "")
            {
                $this->checkurl($url, $symbol, $number);
                $url .= 'category_path='.$categorypath;
            }
            if($q != "")
            {
                $this->checkurl($url, $symbol, $number);
                $url .= 'q='.$q;
            }
            if($offset != "")
            {
                $this->checkurl($url, $symbol, $number);
                $url .= 'offset='.$offset;
            }
            if($limit != "")
            {
                $this->checkurl($url, $symbol, $number);
                $url .= 'limit='.$limit;
            }
            
            $this->checkurl($url, $symbol, $number);
            $url .= 'access_token='.$this->getAT();
            
            echo 'Requisition: <br>'.$url.'<br>'; // url final sobre a qual será feita a requisição.
            
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1, // indica que você quer uma resposta do servidor, indicando se a requisição deu certo ou não (true ou false)
                CURLOPT_URL => $url, // url da requisição
                CURLOPT_USERAGENT => $_SERVER ['HTTP_USER_AGENT'], // dá informação sobre o seu sistema operacional e seu navegador, é necessário para que o site não bloqueie sua requisição.
                CURLOPT_SSL_VERIFYPEER => TRUE, // Para uso do https, o deviantART exije isso.
                CURLOPT_CAINFO => __DIR__ . "/cacert.pem" // Certificado para o https, em vários testes, a API do deviantART recusava a requisição indicando erro de autorização. Isso resolveu o problema.
                ));
                
            $resp = curl_exec($curl); //isso aqui tbm tem q colocar sempre

            if(curl_error($curl))
            {
                error_log(curl_error($curl), 0);
                return false;
            }
            
            if($resp) // Caso haja uma resposta...
            {
                $json = json_decode($resp);

                if(json_last_error() != JSON_ERROR_NONE) // Caso a conversão da resposta para JSON dê algum problema...
                {
                    error_log("Error on json_decode(resp)", 0);
                    return false;
                }
            }
            else
            {
                error_log("Error on resp", 0);
                var_dump($resp);
                return false;
            }

            curl_close($curl); // É sempre recomendável fechar as variáveis $curl após utilizá-las.
            
            return $json;
        }

        //Faz a requisição das informações do deviation que possui id igual a $deviationid, retorna uma variável do tipo JSON.
        public function fetchDeviationID($deviationid)
        {
            $curl = curl_init();

            if(!$curl)
            {
                return false;
            }
            
            $url = 'https://www.deviantart.com/api/v1/oauth2/deviation/'.$deviationid.'?access_token='.$this->getAT();
            
            echo 'Requisition: <br>'.$url.'<br>';
            
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1, 
                CURLOPT_URL => $url, 
                CURLOPT_USERAGENT => $_SERVER ['HTTP_USER_AGENT'],
                CURLOPT_SSL_VERIFYPEER => TRUE,
                CURLOPT_CAINFO => __DIR__ . "/cacert.pem" 
                ));
                
            $resp = curl_exec($curl); //isso aqui tbm tem q colocar sempre

            if(curl_error($curl))
            {
                error_log(curl_error($curl), 0);
                return false;
            }
            
            if($resp) // Caso haja uma resposta...
            {
                $json = json_decode($resp);

                if(json_last_error() != JSON_ERROR_NONE) // Caso a conversão da resposta para JSON dê algum problema...
                {
                    error_log("Error on json_decode(resp)", 0);
                    sleep(180);
                    return false;
                }
            }
            else
            {
                error_log("Error on resp", 0);
                var_dump($resp);
                return false;
            }

            curl_close($curl); // É sempre recomendável fechar as variáveis $curl após utilizá-las.
            
            return $json;
        }

        //Recebe uma variável $s do tipo JSON e $coddeviation do tipo string, e salva o codcontent do JSON no banco de dados, no deviation com coddeviation igual a $coddeviation:
        public function execFetchDeviationID($s, $coddeviation)
        {
            var_dump($s);
            $src; $height; $width; $transparency; $filesize;

            if(array_key_exists('content', $s))
            {
                $src = $this->g($s->content->src);
                $height = $this->g($s->content->height);
                $width = $this->g($s->content->width);
                $transparency = $this->g($s->content->transparency);
                $filesize = $this->g($s->content->filesize);

                $this->conDB->begin();

                //Insere o content:
                $query = "INSERT INTO content VALUES(default, $src, $filesize, $height, $width, $transparency) RETURNING codcontent";
                $aux = $this->conDB->exect($query);
                if(!$result = pg_fetch_object($aux))
                {
                    throw new Exception("Exceção lançada: Não foi possível inserir o content.");
                }

                //Aponta o deviation com coddeviation igual a $coddeviation para o content:
                $query = "UPDATE deviation SET codcontent = $result->codcontent WHERE coddeviation = $coddeviation";
                $aux = $this->conDB->exect($query);
                $this->conDB->commit();
            }
            else
            {
                $query = "UPDATE deviation SET codcontent = 0 WHERE coddeviation = $coddeviation";
                $aux = $this->conDB->exec($query);
            }
        }
        
        //Para cada deviation que existir no banco pega a identificação deles e cria um array com tamanho e passa eles para o método deviantInf(). São passadas 50 identificações por vez, que é o limite da API.
        public function atualizarTags()
        {
            //Pega o valor a partir do qual irá fazer a pesquisa das tags:
            $query = "SELECT coddeviation FROM tag ORDER BY coddeviation DESC LIMIT 1";
            $aux = $this->conDB->exec($query);

            if(!$result = pg_fetch_object($aux))
            {
                $valor = 0;
            }
            else
            {
                $valor = $result->coddeviation;
            }

            //Seleciona todos os deviations do banco ordenados por seu código, caso dê algum problema na execução, será possível continuar de onde parou.
            $query = "SELECT deviationid FROM deviation WHERE coddeviation >= $valor ORDER BY coddeviation";
            $aux = $this->conDB->exec($query);

            $contador = 0;
            $offset = 0;
            $deviationids; // Array que vai armazenar todas as identificações dos deviations.
            while(true)
            {
                if($result = pg_fetch_object($aux))//Se existir um próximo resultado, atribua a $result, até no máximo 50.
                {
                    $deviationids[] = $result->deviationid;
                    $contador++;//aumenta em 1

                    if($contador == $offset + 50)//Quando o contador for 50, salva tudo no banco
                    {
                        $results = $this->deviantInf($deviationids);
                        $i = 0;
                        while(is_null($results))
                        {
                            error_log("Result is null", 0);
                            sleep(1);
                            $results = $this->deviantInf($deviationids);
                            if($i++ == 50)
                            {
                                break;
                            }
                        }

                        $this->execDeviantInf($results);

                        //Agora precisa contar de 50 até 100:
                        $offset = $contador;
                        unset($deviationids);
                        $deviationids = array();
                    }
                }
                else//quando não tiver mais resultados, pega o que foi preenchido até o momento, e salva no banco também.
                {
                    $results = $this->deviantInf($deviationids);
                    $i = 0;
                    while(is_null($results))
                    {
                        error_log("Result is null", 0);
                        sleep(1);
                        $results = $this->deviantInf($deviationids);
                        if($i++ == 50)
                        {
                            break;
                        }
                    }

                    $this->execDeviantInf($results);

                    break;
                }
            }
        }

        //APAGAR DEPOIS!
        public function atualizarContents()
        {
            $query = "SELECT deviation.deviationid, deviation.coddeviation FROM deviation WHERE deviation.codcontent IS NULL ORDER BY coddeviation";
            $aux = $this->conDB->exec($query);

            while($result = pg_fetch_object($aux))//Para cada deviation no banco...
            {
                //Faz a requisição do JSON pedindo o dados deste deviation.
                $json = $this->fetchDeviationID($result->deviationid);

                while($json == null)
                {
                    error_log("json is null", 0);
                    sleep(1);
                    $json = $this->fetchDeviationID($result->deviationid);
                }

                //Trata o JSON e salva o content do deviation no banco de dados.
                $this->execFetchDeviationID($json, $result->coddeviation);
            }
        }
        
        //Pega as 50 identificações recebidas na variável $deviationids e faz uma requisição à API do deviantART, como os métodos de requisição são muito parecidos, não comentarei muito eles daqui para baixo.
        public function deviantInf($deviationids)
        {
            $curl = curl_init();

            if(!$curl)
            {
                return false;
            }
            
            // A url gerada para esta requisição contém a identificação de 50 deviants! Isso gera uma string de, geralmente, 2500 caracteres.
            // Um fato curioso é que os internet Explorers mais antigos são os únicos que não iriam conseguir tratar esta url. Eles possuem limitação de 2083 caracteres.
            // Retorna uma variável do tipo JSON, que deverá ser tratada em execDeviantInf().
            $url = 'https://www.deviantart.com/api/v1/oauth2/deviation/metadata';
            $symbol = 0;
            $number = 0;
            foreach($deviationids as $instance)
            {
                $this->checkurl($url, $symbol, $number);
                $url .= "deviationids%5B%5D=$instance";
                if($number==50)
                {
                    break;
                }
            }
            
            $this->checkurl($url, $symbol, $number);
            $url .= 'access_token='.$this->getAT();
            
            echo  'Requisition: <br>'.$url.'<br>';
            
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_USERAGENT => $_SERVER ['HTTP_USER_AGENT'],
                CURLOPT_SSL_VERIFYPEER => TRUE,
                CURLOPT_CAINFO => __DIR__ . "/cacert.pem"
                ));
                
            $resp = curl_exec($curl);

            if(curl_error($curl))
            {
                error_log("curl_error: ".curl_error($curl), 0);
                return false;
            }

            if($resp)
            {
                $json = json_decode($resp);

                if(json_last_error() != JSON_ERROR_NONE)
                {
                    error_log("Error on json_decode(resp)", 0);
                    return false;
                }
            }
            else
            {
                error_log("Error on resp", 0);
                var_dump($resp);
                return false;
            }

            curl_close($curl);
            
            return $json;
        }
        
        // Para o JSON recebido, salva-o no banco de dados:
        public function execDeviantInf($s)
        {
            foreach($s->metadata as $instance)
            {
                var_dump($instance);
                $deviationid = $this->g($instance->deviationid);
                foreach($instance->tags as $instancetag)
                {
                    var_dump($instancetag);
                    $tagname = $this->g($instancetag->tag_name);
                    $sponsored = $this->g($instancetag->sponsored);
                    $sponsor = $this->g($instancetag->sponsor);

                    // Pega o coddeviation do deviationID:
                    $query = "SELECT coddeviation FROM deviation WHERE deviationid = $deviationid";
                    $aux = $this->conDB->exec($query);
                    $coddeviation;
                    if(!$coddeviation = pg_fetch_object($aux)->coddeviation)
                    {
                        throw new Exception('Exceção lançada: Não foi possível achar o coddeviation do deviationid passado');
                    }
                    else
                    {
                        //Checa se existe:
                        $query = "SELECT tagname, coddeviation FROM tag WHERE tagname = $tagname AND coddeviation = $coddeviation";
                        $aux = $this->conDB->exec($query);
                        
                        if(!$result = pg_fetch_object($aux)) // Se não houver algum resultado:
                        {
                            $query = "INSERT INTO tag VALUES(default, $tagname, $sponsored, $sponsor, $coddeviation)";
                            $result = $this->conDB->exec($query);
                        }
                    }
                }
            }
        }
        
        //Adiciona os símbolos de forma correta para os campos opcionais:
        public function checkurl(&$url, &$symbol, &$number)
        {
            if(!$symbol)
            {
                $url .= '?';
                $symbol = true;
            }
            if($number > 0)
            {
                $url .= '&';
            }
            
            $number++;
            
            return $url;
        }
            
        
        public function tag($tag, $offset, $limit)
        {
            $curl = curl_init();

            if(!$curl)
            {
                return false;
            }
            
            $url = 'https://www.deviantart.com/api/v1/oauth2/browse/tags';
            $symbol = false;
            $number = 0;
            
            if($tag != "")
            {
                $this->checkurl($url, $symbol, $number);
                $url .= 'tag='.$tag;
            }
            if($offset != "")
            {
                $this->checkurl($url, $symbol, $number);
                $url .= 'offset='.$offset;
            }
            if($limit != "")
            {
                $this->checkurl($url, $symbol, $number);
                $url .= 'limit='.$limit;
            }
            
            $this->checkurl($url, $symbol, $number);
            $url .= 'access_token='.$this->getAT();
            
            echo 'Requisition: <br>'.$url.'<br>';
            
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_USERAGENT => $_SERVER ['HTTP_USER_AGENT'],
                CURLOPT_SSL_VERIFYPEER => TRUE,
                CURLOPT_CAINFO => __DIR__ . "/cacert.pem"
                ));
                
            $resp = curl_exec($curl); //isso aqui tbm tem q colocar sempre

            if(curl_error($curl))
            {
                error_log(curl_error($curl), 0);
                return false;
            }
            
            if($resp)
            {
                $json = json_decode($resp);

                if(json_last_error() != JSON_ERROR_NONE)
                {
                    error_log("Error on json_decode(resp)", 0);
                    return false;
                }
            }
            else
            {
                error_log("Error on resp", 0);
                var_dump($resp);
                return false;
            }

            curl_close($curl);
            
            return $json;
        }
        
        //Essa função eu criei para facilitar na hora de salvar coisas no banco de dados, 
        //ela pega as variáveis retornadas pelo curl, e automaticamente as transforma para o formato desejado do banco de dados:
        private function g($var)
        {
            //Se for uma variável nula:
            if(is_null($var))
            {
                return "'null'";
            }
            //Se for uma variável booleana:
            else if(is_bool($var))
            {
                if($var)
                {
                    return 'true';
                }
                else
                {
                    return 'false';
                }
            }
            //Se for uma string:
            else if(is_string($var))
            {
                return "'".htmlspecialchars($var, ENT_QUOTES)."'";
            }
            //Se for uma integer:
            else if(is_int($var))
            {
                return $var;
            }
        }
        
        public function execDeviant($s)
        {
            foreach($s as $instance)
            {
                //Se já existe $instance no banco, então não continua:
                $deviationid = $this->g($instance->deviationid);
                $query = "SELECT coddeviation FROM deviation WHERE deviationid = $deviationid";
                $coddeviation;
                if( $coddeviation = pg_fetch_object($this->conDB->exec($query)) )
                {
                    continue;
                }
                
                //Variáveis pegas do deviant $s:
                $printid = $this->g($instance->printid);
                $url = $this->g($instance->url);
                $title = $this->g($instance->title);
                $categoryname = $this->g($instance->category);
                $categorypath = $this->g($instance->category_path);
                $is_favourited = $this->g($instance->is_favourited);
                $is_deleted = $this->g($instance->is_deleted);
                $authoruserid = $this->g($instance->author->userid);
                $authorusername = $this->g($instance->author->username);
                $authorusericon = $this->g($instance->author->usericon);
                $authortype = $this->g($instance->author->type);
                $statscomments = $this->g($instance->stats->comments);
                $statsfavourites = $this->g($instance->stats->favourites);
                $published_time = $instance->published_time;
                $allows_comments = $this->g($instance->allows_comments);
                
                //Checa se preview existe:
                $preview;
                $previewexist = false;
                if(array_key_exists('preview', $instance))
                {
                    $previewexist = true;
                    
                    $preview = $instance->preview;
                }
                
                //Verifica se content existe:
                $contentsrc; $contentheight; $contentwidth; $contenttransparency; $contentfilesize;
                $contentexist = false;
                if(array_key_exists('content', $instance))
                {
                    $contentexist = true;
                    
                    $contentsrc = $this->g($instance->content->src);
                    $contentheight = $instance->content->height;
                    $contentwidth = $instance->content->width;
                    $contenttransparency = $this->g($instance->content->transparency);
                    $contentfilesize = $instance->content->filesize;
                }
                $thumbs = $instance->thumbs;
                $is_mature = $this->g($instance->is_mature);
                $is_downloadable = $this->g($instance->is_downloadable);


                
                //Inserindo Author se ele não existir:
                $query = "INSERT INTO author (userid, username, usericon, type) 
                SELECT $authoruserid, $authorusername, $authorusericon, $authortype 
                WHERE NOT EXISTS (SELECT userid FROM author WHERE userid = $authoruserid)";
                $this->conDB->exec($query);
                
                //Inserindo Stats retornando o codigo:
                $query = "INSERT INTO stats VALUES(default, $statscomments, $statsfavourites) RETURNING codstats";
                $codstats = pg_fetch_object($this->conDB->exec($query))->codstats;
                
                //Caso exista Content, insere e retorna o codigo:
                if($contentexist == true)
                {
                    $query = "INSERT INTO content 
                    VALUES(default, $contentsrc, $contentfilesize, $contentheight, $contentwidth, $contenttransparency) 
                    RETURNING codcontent";
                    $codcontent = pg_fetch_object($this->conDB->exec($query))->codcontent;
                }
                
                //Salvando no banco:
                $query = "INSERT INTO deviation (deviationid, printid, url, title, codcategory, isdownloadable, ismature, isfavourited, isdeleted, codauthor, codstats, publishedtime, isallowcomments, codcontent)
                SELECT
                    $deviationid, 
                    $printid, 
                    $url, 
                    $title, 
                    ".$this->getCodCategory($instance->category, $instance->category_path).",
                    $is_downloadable,
                    $is_mature,
                    $is_favourited,
                    $is_deleted,
                    (SELECT codauthor FROM author WHERE userid = $authoruserid LIMIT 1),
                    $codstats,
                    $published_time,
                    $allows_comments,
                    $codcontent
                WHERE NOT EXISTS (SELECT coddeviation FROM deviation WHERE deviationid = $deviationid) RETURNING coddeviation";
    
                $coddeviation = pg_fetch_object($this->conDB->exec($query))->coddeviation;
                
                //Inserindo Thumbs e apontando eles para o deviation:
                foreach($thumbs as $thumb)
                {
                    $thumbsrc = $this->g($thumb->src);
                    $thumbheight = $this->g($thumb->height);
                    $thumbwidth = $this->g($thumb->width);
                    $thumbistransparency = $this->g($thumb->transparency);
                    $query = "INSERT INTO thumb 
                    VALUES(default, $coddeviation, $thumbsrc, $thumbheight, $thumbwidth, $thumbistransparency)";
                    $this->conDB->exec($query);
                }
            }
        }

        public function getCodCategory($categoryname, $categorypath)
        {
            $query = "SELECT codcategory FROM category WHERE categorypath = '$categorypath'";
            $aux = $this->conDB->exec($query);
            if($result = pg_fetch_object($aux)) // caso a categoria exista no banco...
            {
                return $result->codcategory;
            }
            else
            {
                $query = "INSERT INTO category VALUES (default, '$categoryname', '$categorypath') RETURNING codcategory";
                $aux = $this->conDB->exec($query);
                if($result = pg_fetch_object($aux))
                {
                    return $result->codcategory;
                }
            }
        }
        
        public function verifyAccessToken()
        {
            //Se houver uma ACCESS_TOKEN na sessão, salva ela na classe.
            if(SessionManager::isChanged('ACCESS_TOKEN'))
            {
                $this->AT = SessionManager::get('ACCESS_TOKEN');
            }
            //Caso contrário, gera uma, salva na classe e também na sessão.
            else
            {
                try
                {
                    $this->generateAT();
                }
                
                catch(Exception $e)
                {
                    echo($e->getMessage());
                }
                
                SessionManager::updateAccessToken($this->AT);
            }
            
            //Verifica se já passou do tempo limite, e se passou, gera uma nova chave, salva ela na classe e também na sessão.
            $interval = SessionManager::get('ACCESS_TOKEN_TIME')->diff(new DateTime('NOW'));
            $diff = (new DateTime())->setTimeStamp(0)->add($interval)->getTimeStamp();
            
            if($diff > 3600)
            {
                $this->generateAT();
                SessionManager::updateAccessToken($this->AT);
            }
        }
        
        public function getAT()
        {
            $this->verifyAccessToken();
            return $this->AT;
        }
        
        private function generateAT()
        {
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'https://www.deviantart.com/oauth2/token',
                CURLOPT_POST => 1,
                CURLOPT_USERAGENT => $_SERVER ['HTTP_USER_AGENT'],
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_CAINFO => __DIR__ . "/cacert.pem",
                CURLOPT_POSTFIELDS => array(
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret
                    )
                ));
        
            $resp = curl_exec($curl);
            
            if($resp)
            {
                $json = json_decode($resp);
                $this->AT = $json->access_token;
                
                curl_close($curl);
                return true;
            }
            else
            {
                throw new Exception('Exceção lançada: Não foi possível gerar a chave de acesso.');
                
                curl_close($curl);
                return false;
            }
        }
        
        private $client_id;
        private $client_secret;
        private $conDB;
        private $AT;
    }
?>