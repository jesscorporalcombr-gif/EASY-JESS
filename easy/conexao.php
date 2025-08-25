<?php 

require_once('config.php');
















date_default_timezone_set('America/Sao_Paulo');	
try {
	$pdo = new PDO("mysql:dbname=$banco;host=$servidor;charset=utf8", "$usuario", "$senha");
	//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
	echo 'Erro ao Conectar com o banco de dados! <p>' .$e;
}




 ?>