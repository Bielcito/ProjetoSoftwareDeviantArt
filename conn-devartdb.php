<?php

class ConexionDB
{
    
}

// Dados da conexão:
$dbname = "devartdb";
$dbuser = "postgres";
$dbpassword = "1240";
$dbhost = "localhost";

// Conexão com o PostgreSQL:
$conn = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpassword")
	or die('Não foi possível conectar: ' . pg_last_error());
	
?>