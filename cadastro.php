<?php
	require_once('SessionManager.php');
	SessionManager::StartSession();
?>

<?php
	header('Content-Type: text/html; charset=UTF-8');

	require_once('SessionManager.php');
	require_once('Application.php');
	
	$app = new Application();

	if(SessionManager::isPost('username'))
    {
        $username = SessionManager::getPost('username');
        $password = SessionManager::getPost('password');
        $email = SessionManager::getPost('email');
        
        if($app->getLoginManager()->isUserAvailable($username) && $app->getLoginManager()->isEmailAvailable($email))
        {
        	if($app->getLoginManager()->createUser($username, $password, $email))
        	{
        		header('Location: .');
        		die();
        	}
        }
        else
        {
        	echo 'Usuário/Email já existente!';
        }
    }
?>

<!DOCTYPE html>
<!--
Copyright (c) 2016 by Tyler Fry (http://codepen.io/frytyler/pen/EGdtg)
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
-->

<meta charset="UTF-8">
<html >
 	 <head>
	    <title>Cadastro</title>
	    <link rel="stylesheet" href="css/cadastro.css">
  	</head>

  	<body>
	
	<!--Necessárias mensagens de erro, e tal-->
	<div class="login">
	<h1>Cadastre-se no ARTSTATS</h1>
    <form method="post">
    	<input type="text" name="username" placeholder="Nome de Usuário" required="required" />
        <input id="password" type="password" name="password" placeholder="Senha" required="required" />
        <input id="password2" type="password" placeholder="Confirme sua senha" required="required" />
        <input id="email" type="email" name="email" placeholder="Email" required="required" />
        <input id="email2" type="email" placeholder="Confirme seu email" required="required" />
        <button type="submit" class="btn btn-primary btn-block btn-large" formaction="">Criar Conta</button>
    </form>
    </div>
        
    <script>
    
    	var password = document.getElementById("password");
    	var password2 = document.getElementById("password2");
    	var email = document.getElementById("email");
    	var email2 = document.getElementById("email2");

		function validatePassword()
		{
			if(password.value != password2.value) 
			{
			    password2.setCustomValidity("As senhas não conferem!");
			} 
			else 
			{
		    	password2.setCustomValidity('');
			}
		}
		
		function validateEmail()
		{
			if(email.value != email2.value) 
			{
			    email2.setCustomValidity("Os e-mails não conferem!");
			} 
			else 
			{
		    	email2.setCustomValidity('');
			}
		}

		password.onchange = validatePassword;
		password2.onkeyup = validatePassword;
		email.onchange = validateEmail;
		email2.onkeyup = validateEmail;
		
    </script>
  	</body>
</html>
