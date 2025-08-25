<?php 
require_once("../../conexao.php");

$id = $_POST['id'];

//BUSCAR A IMAGEM PARA EXCLUIR DA PASTA
$query_con = $pdo->query("SELECT * FROM termos_contratos_assinaturas WHERE id = '$id'");
$res_con = $query_con->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res_con);



//EXCLUIR A IMAGEM DA PASTA
$imagem = $res_con[0]['arquivo'];
if($imagem != 'sem-foto.jpg'){
	unlink('../../img/termos_contratos_assinaturas/'.$imagem);
}


$query_con = $pdo->query("DELETE from termos_contratos_assinaturas WHERE id = '$id'");


echo 'Excluído com Sucesso!';

 ?>