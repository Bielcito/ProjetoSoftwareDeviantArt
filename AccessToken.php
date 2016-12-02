<?php 
/**
 * Provê gerenciamento da chave de acesso usada para fazer as requisições no deviantART.
 * @author Bielcito
 *
 */
class AccessToken
{
	function __construct()
	{
		new Credentials(); // Permite a utilização das credenciais nesta classe.
		$this->client_id = Credentials::App()->client_id;
		$this->client_secret = Credentials::App()->client_secret;
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
}

?>