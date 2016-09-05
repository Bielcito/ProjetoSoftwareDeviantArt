<?php
    require_once('SessionManager.php');
    SessionManager::StartSession();
?>

<?php
    require_once('Credentials.php');

    //Esta classe realiza as chamadas de API no site do deviantART e retorna o seu resultado.
    class DeviantManager
    {
        function DeviantManager($conDB)
        {
            $this->client_id = Credentials::App()['CLIENT_ID'];
            $this->client_secret = Credentials::App()['CLIENT_SECRET'];
            $this->connDB = $conDB;
        }
        
        
        //crie $category_path, $q, $offset, $limit isso em algum canto (maybe no application?) e passe como argumento no newest
        //$category_path = categoria q vc quer pegar ex digitalart%2Fpaintings (existem várias no deviantart, tipo fanart, digitalart, painting etc etc)
        //todas as categorias aparecem quando vc dá submit em uma arte, mas não lembro de todas decoradas (e elas são obrigatórias btw, caso vc nunca tenha feito upload lá)
        //Biel: Você pode tentar depois colocar todas as categorias dentro do banco de dados? Tem uma tabela chamada categories lá, eu acho.
        //$q = não sei bem o q é, mas no exemplo tá robots, então deve ser o tema, tipo pokémon, anime, yaoi
        //$offset = a partir de qual qual submit ele deve olhar. Acredito q é dos mais recentes pros mais antigos, seta 0 mesmo e vê no q dá
        //$limit = "the pagination limit" o q caralhos isso significa
        
        public function newest($category_path, $q, $offset, $limit)
        {
            $this->verifyAccessToken();
            
            $curl = curl_init();
            
            $url = 'https://www.deviantart.com/api/v1/oauth2/browse/newest'.'?category_path='.$category_path.'&q='.$q.'&offset='.$offset.'&limit='.$limit.'&access_token='.$this->AT;
            echo $url.'<br>';
            
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
                return "'".$var."'";
            }
            //Se for uma integer:
            else if(is_int($var))
            {
                return $var;
            }
        }
        
        public function execDeviant($s)
        {
            //Variáveis pegas do deviant $s:
            var_dump($s[0]);
            $deviationid = $this->g($s[0]->deviationid);
            $printid = $this->g($s[0]->printid);
            $url = $this->g($s[0]->url);
            $title = $this->g($s[0]->title);
            $category = $this->g($s[0]->category);
            $category_path = $this->g($s[0]->category_path);
            $is_favourited = $this->g($s[0]->is_favourited);
            $is_deleted = $this->g($s[0]->is_deleted);
            $authoruserid = $this->g($s[0]->author->userid);
            $authorusername = $this->g($s[0]->author->username);
            $authorusericon = $this->g($s[0]->author->usericon);
            $authortype = $this->g($s[0]->author->type);
            $statscomments = $this->g($s[0]->stats->comments);
            $statsfavourites = $this->g($s[0]->stats->favourites);
            $published_time = $s[0]->published_time;
            $allows_comments = $this->g($s[0]->allows_comments);
            $preview = $s[0]->preview;
            $contentsrc = $this->g($s[0]->content->src);
            $contentheight = $s[0]->content->height;
            $contentwidth = $s[0]->content->width;
            $contenttransparency = $this->g($s[0]->content->transparency);
            $contentfilesize = $s[0]->content->filesize;
            $thumbs = $s[0]->thumbs;
            $is_mature = $this->g($s[0]->is_mature);
            $is_downloadable = $this->g($s[0]->is_downloadable);
            
            //Inserindo Author se ele não existir:
            $query = "INSERT INTO author (userid, username, usericon, type) 
            SELECT $authoruserid, $authorusername, $authorusericon, $authortype 
            WHERE NOT EXISTS (SELECT userid FROM author WHERE userid = $authoruserid)";
            $codauthor = $this->connDB->exec($query);
            
            //Inserindo Stats retornando o codigo:
            $query = "INSERT INTO stats VALUES(default, $statscomments, $statsfavourites) RETURNING codstats";
            $codstats = pg_fetch_object($this->connDB->exec($query))->codstats;
            
            //Inserindo Content retornando o codigo:
            $query = "INSERT INTO content 
            VALUES(default, $contentsrc, $contentfilesize, $contentheight, $contentwidth, $contenttransparency) 
            RETURNING codcontent";
            $codcontent = pg_fetch_object($this->connDB->exec($query))->codcontent;
            
            //Salvando no banco:
            $query = "INSERT INTO deviation (deviationid, printid, url, title, codcategory, isdownloadable, ismature, isfavourited, isdeleted, codauthor, codstats, publishedtime, isallowcomments, codcontent)
            SELECT
                $deviationid, 
                $printid, 
                $url, 
                $title, 
                (SELECT codcategory FROM category WHERE categoryname = $category),
                $is_downloadable,
                $is_mature,
                $is_favourited,
                $is_deleted,
                (SELECT codauthor FROM author WHERE userid = $authoruserid),
                $codstats,
                $published_time,
                $allows_comments,
                $codcontent
            WHERE NOT EXISTS (SELECT coddeviation FROM deviation WHERE deviationid = $deviationid)";
            
            $this->connDB->exec($query);
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
                $this->generateAT() or die('Não foi possível gerar uma chave de acesso!');
                SessionManager::updateAccessToken($this->AT);
            }
            
            //Verifica se já passou do tempo limite, e se passou, gera uma nova chave, salva ela na classe e também na sessão.
            $interval = SessionManager::getAccessTokenTime()->diff(new DateTime('NOW'));
            $diff = (new DateTime())->setTimeStamp(0)->add($interval)->getTimeStamp();
            echo 'diff: '.$diff.'<br>';
            
            if($diff > 3600)
            {
                $this->generateAT();
                SessionManager::updateAccessToken($this->AT);
            }
        }
        
        public function getAT()
        {
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
                return true;
            }
            else
            {
                die('Error: "' . curl_error($curl) . '" - Code: ' . curl_error($curl));
                return false;
            }
            
            curl_close($curl);
        }
        
        private $client_id;
        private $client_secret;
        private $connDB;
        private $AT;
    }



?>