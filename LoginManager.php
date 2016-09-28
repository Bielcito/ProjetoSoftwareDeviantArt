<?php

require_once('SessionManager.php');

//Classe que gerencia o login do usuário no deviantART, 
//já gerenciando tudo certinho no SessionManager e salvando as informações no banco de dados também.
class LoginManager
{
    function LoginManager($conDB)
    {
        $this->conDB = $conDB;
    }
    
    public function verifyPostVariables()
    {
        if(SessionManager::isChanged('username') && SessionManager::isChanged('password'))
        {
            return true;
        }
        else 
        {
            return false;
        }
    }
    
    //Checa se o usuário já foi usado:
    public function isUserAvailable($username)
    {
        $query = "SELECT username FROM userdata WHERE username = '$username'";
        
        $aux = $this->conDB->exec($query);
        $result = pg_fetch_object($aux);
        
        if(!$result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    //Checa se o email já foi usado:
    public function isEmailAvailable($email)
    {
        $query = "SELECT email FROM userdata WHERE email = '$email'";
        
        $aux = $this->conDB->exec($query);
        $result = pg_fetch_object($aux);
        
        if(!$result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    //Cria um usuário:
    public function createUser($username, $password, $email)
    {
        $query = "INSERT INTO userdata VALUES('$username', '$password', '$email', 'client')";
        $aux = $this->conDB->exec($query);
        
        if($aux)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    //Faz o login:
    public function login($username, $password)
    {
        //Verifica se o login existe e se a senha está correta:
        $query = "SELECT username, password FROM userdata WHERE username = '$username'";
        $aux = $this->conDB->exec($query);
        $result = pg_fetch_object($aux);
        
        if($result && $result->password == $password)
        {
            SessionManager::set("username", $username);
            SessionManager::set("password", $password);
        }
        else
        {
            throw new Exception("<pre>Exceção lançada: Variáveis de sessão do login não conferem com o banco de dados!</pre>");
        }
    }
    
    public function logout()
    {
        SessionManager::remove("username");
        SessionManager::remove("password");
    }
    
    private $conDB;
}

?>