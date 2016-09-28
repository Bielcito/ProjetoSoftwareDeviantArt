<?php
	require_once('SessionManager.php');
	SessionManager::StartSession();
?>

<!DOCTYPE html>
<!--
Copyright (c) 2016 by Tyler Fry (http://codepen.io/frytyler/pen/EGdtg)
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
-->

<html >
  <head>
  	
    <meta charset="UTF-8">
    <title>Login</title>
	<link rel="stylesheet" href="css/login.css">
	
  </head>

  <body>

    <div class="login">
	<h1>Login</h1>
    <form method="post">
    	<input type="text" name="username" placeholder="Usuário" required="required" />
        <input type="password" name="password" placeholder="Senha" required="required" />
        <button type="submit" class="btn btn-primary btn-block btn-large" formaction="">Entrar</button>
        <p><font color="white">Ainda não tem cadastro? Clique <a href="./cadastro.php">aqui</a>.</font>
    </form>
    </div>
    
        <!--script src="js/index.js"></script-->
  </body>
</html>
