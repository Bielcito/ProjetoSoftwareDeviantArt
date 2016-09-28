<?php
    require_once('SessionManager.php');
    require_once('Credentials.php');
    require_once('ConDB.php');

    //Esta classe realiza as chamadas de API no site do deviantART e salva no banco de dados.
    class DeviantManager
    {
        function DeviantManager($conDB)
        {
            $this->client_id = Credentials::App()['CLIENT_ID'];
            $this->client_secret = Credentials::App()['CLIENT_SECRET'];
            $this->conDB = $conDB;
        }
        
        
        //crie $categorypath, $q, $offset, $limit isso em algum canto (maybe no application?) e passe como argumento no newest
        //$categorypath = categoria q vc quer pegar ex digitalart%2Fpaintings (existem várias no deviantart, tipo fanart, digitalart, painting etc etc)
        //todas as categorias aparecem quando vc dá submit em uma arte, mas não lembro de todas decoradas (e elas são obrigatórias btw, caso vc nunca tenha feito upload lá)
        //Biel: Você pode tentar depois colocar todas as categorias dentro do banco de dados? Tem uma tabela chamada categories lá, eu acho.
        //$q = não sei bem o q é, mas no exemplo tá robots, então deve ser o tema, tipo pokémon, anime, yaoi
        //$offset = a partir de qual qual submit ele deve olhar. Acredito q é dos mais recentes pros mais antigos, seta 0 mesmo e vê no q dá
        //$limit = "the pagination limit" o q caralhos isso significa
        
        public function newest($categorypath, $q, $offset, $limit)
        {
            $curl = curl_init();
            
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
            
            echo 'Requisition: <br>'.$url.'<br>';
            
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT x.y; rv:10.0) Gecko/20100101 Firefox/10.0',
                ));
                
            $resp = curl_exec($curl); //isso aqui tbm tem q colocar sempre
            
            if($resp)
            {
                $json = json_decode($resp);
                if(array_key_exists('error', $json))
                {
                    throw new Exception('<pre>Exceção lançada: <br>Error:'.$json->error.'<br>Error description: '.$json->error_description.'<br></pre>');
                }
            }
            else
            {
                throw new Exception('<pre>Exceção lançada: não foi possível executar o newest<br></pre>');
            }
            
            curl_close($curl);

            return $json;
        }
        
        public function atualizarTags()
        {
            $query = "SELECT deviationid FROM deviation";
            $aux = $this->conDB->exec($query);
            
            while($result = pg_fetch_object($aux)) // Enquanto houver resultados...
            {
                $deviationid = $result->deviationid;
                
                $results = $this->deviantInf($deviationid);
                var_dump($results);
                while(is_null($results))
                {
                    echo $results;
                    sleep(1);
                    $results = $this->deviantInf($deviationid);
                }
                $this->execDeviantInf($results);
            }
        }
        
        public function deviantInf($deviationid)
        {
            $curl = curl_init();
            
            $url = 'https://www.deviantart.com/api/v1/oauth2/deviation/metadata';
            $symbol = false;
            $number = 0;
            
            $this->checkurl($url, $symbol, $number);
            $url .= "deviationids%5B%5D=$deviationid";
            
            $this->checkurl($url, $symbol, $number);
            $url .= 'ext_submission=false';
            
            $this->checkurl($url, $symbol, $number);
            $url .= 'ext_camera=false';
            
            $this->checkurl($url, $symbol, $number);
            $url .= 'ext_stats=false';
            
            $this->checkurl($url, $symbol, $number);
            $url .= 'ext_collection=false';
            
            $this->checkurl($url, $symbol, $number);
            $url .= 'access_token='.$this->getAT();
            
            echo 'Requisition: <br>'.$url.'<br>';
            
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT x.y; rv:10.0) Gecko/20100101 Firefox/10.0',
                ));
                
            $resp = curl_exec($curl); //isso aqui tbm tem q colocar sempre
            
            if($resp)
            {
                $json = json_decode($resp);
                if(array_key_exists('error', $json))
                {
                    throw new Exception('<pre>Exceção lançada: <br>Error:'.$json->error.'<br>Error description: '.$json->error_description.'<br></pre>');
                }
            }
            else
            {
                throw new Exception('<pre>Exceção lançada: não foi possível executar o tag<br></pre>');
            }
            
            curl_close($curl);

            return $json;
        }
        
        public function execDeviantInf($s)
        {
            foreach($s->metadata as $instance)
            {
                $deviationid = $this->g($instance->deviationid);
                foreach($instance->tags as $instancetag)
                {
                    $tagname = $this->g($instancetag->tag_name);
                    $sponsored = $this->g($instancetag->sponsored);
                    $sponsor = $this->g($instancetag->sponsor);
                    
                    //Checa se existe:
                    $query = "SELECT tagname FROM tag WHERE tagname = $tagname";
                    $aux = $this->conDB->exec($query);
                    
                    if(!$result = pg_fetch_object($aux)) // Se não houver algum resultado:
                    {
                        // Pega o coddeviation do deviationID:
                        $query = "SELECT coddeviation FROM deviation WHERE deviationid = $deviationid";
                        $aux = $this->conDB->exec($query);
                        $coddeviation;
                        if(!$coddeviation = pg_fetch_object($aux)->coddeviation)
                        {
                            throw new Exception('<pre>Exceção lançada: Não foi possível achar o coddeviation do deviationid passado</pre><br>');
                        }
                        else
                        {
                            $query = "INSERT INTO tag VALUES(default, $coddeviation, $tagname, $sponsored, $sponsor)";
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
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT x.y; rv:10.0) Gecko/20100101 Firefox/10.0',
                ));
                
            $resp = curl_exec($curl); //isso aqui tbm tem q colocar sempre
            
            if($resp)
            {
                $json = json_decode($resp);
                if(array_key_exists('error', $json))
                {
                    throw new Exception('<pre>Exceção lançada: <br>Error:'.$json->error.'<br>Error description: '.$json->error_description.'<br></pre>');
                }
            }
            else
            {
                throw new Exception('<pre>Exceção lançada: não foi possível executar o tag<br></pre>');
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
            var_dump($s);
            foreach($s as $instance)
            {
                //Se já existe $instance no banco, então não continua:
                $deviationid = $this->g($instance->deviationid);
                $query = "SELECT coddeviation FROM deviation WHERE deviationid = $deviationid";
                $coddeviation;
                if( $coddeviation = pg_fetch_object($this->conDB->exec($query)) )
                {
                    return;
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
                $query = "INSERT INTO deviation (deviationid, printid, url, title, codcategory, isdownloadable, ismature, isfavourited, isdeleted, codauthor, codstats, publishedtime, isallowcomments, codcontent, codtag)
                SELECT
                    $deviationid, 
                    $printid, 
                    $url, 
                    $title, 
                    (SELECT codcategory FROM category WHERE categorypath = $categorypath),
                    $is_downloadable,
                    $is_mature,
                    $is_favourited,
                    $is_deleted,
                    (SELECT codauthor FROM author WHERE userid = $authoruserid LIMIT 1),
                    $codstats,
                    $published_time,
                    $allows_comments,
                    null,
                    (SELECT codtag FROM tag WHERE tagname = 'NoTag' LIMIT 1)
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
        
        public function categories($name)
        {
            $curl = curl_init();
            
            $url = 'https://www.deviantart.com/api/v1/oauth2/browse/categorytree?catpath='.$name.'&access_token='.$this->getAT();
            echo 'Requisition: <br>'.$url.'<br>';
            
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_USERAGENT => $_SERVER ['HTTP_USER_AGENT'],
                ));
                
            $resp = curl_exec($curl); //isso aqui tbm tem q colocar sempre
            
            //daqui pra baixo é meio padrão, mas olha lá na aba RESPONSE no site da API e vê o q retorna
            //tamo pegando o access_token aqui mas é pra ser outra coisa, pq isso aqui foi copiado do generateAT()
            if($resp)
            {
                $json = json_decode($resp);
            }
            else
            {
                die('Error: "' . curl_error($curl) . '" - Code: ' . curl_error($curl));
            }
            
            curl_close($curl);
            
            return $json;
        }
        
        public function execCategories($s)
        {
            var_dump($s);
            
            foreach($s->categories as $instance)
            {
                $catpath = $this->g($instance->catpath);
                $title = $this->g($instance->title);
                $sub = $instance->has_subcategory;
                
                $query = "INSERT INTO category VALUES(default, $title, $catpath)";
                $this->conDB->exec($query);
                
                if($sub)
                    {
                        $results = $this->categories($instance->catpath);
                        while(!$results)
                        {
                            throw new Exception("Deviant Art recusou o pedido em andamento!<br>");
                            sleep(1);
                            $results = $this->categories($instance->catpath);
                        }
                        $this->execCategories($results);
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
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT x.y; rv:10.0) Gecko/20100101 Firefox/10.0',
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
                throw new Exception('<pre>Exceção lançada: Não foi possível gerar a chave de acesso.<br></pre>');
                
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