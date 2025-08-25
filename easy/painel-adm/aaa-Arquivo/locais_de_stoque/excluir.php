<?php 
require_once("../../conexao.php");

$id = $_POST['id'];

//BUSCAR A IMAGEM PARA EXCLUIR DA PASTA
$query_con = $pdo->query("SELECT * FROM locais_de_stoque WHERE id = '$id'");
$res_con = $query_con->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res_con);




$query_con = $pdo->query("DELETE from locais_de_stoque WHERE id = '$id'");


echo 'Excluído com Sucesso!';

 ?>