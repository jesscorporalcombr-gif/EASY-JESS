<?php 
require_once("../../conexao.php");
@session_start();

$id_usuario = $_SESSION['id_usuario'];

$descricao = $_POST['descricao']; 
$local = $_POST['local'];

$id = $_POST['id'];

$created = date('d-m-Y H:i:s');
$imagem = 'sem-foto.jpg';


if($id == ""){
	$res = $pdo->prepare("INSERT INTO locais_de_stoque SET   data = '$created', usuario = '$id_usuario', descricao = :descricao, local = :local, created = '$created'");

	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":local", $local);
	$res->execute();

}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE locais_de_stoque SET  modificado_por = '$id_usuario', data = '$created', usuario = '$id_usuario', descricao = :descricao, local = :local, modificado = '$created' WHERE id = :id");
		$res->bindValue(":foto", $imagem); /* mudar o sistema de bindvalue*/

	}else{
		$res = $pdo->prepare("UPDATE locais_de_stoque SET  modificado_por = '$id_usuario', data = '$created', usuario = '$id_usuario', descricao = :descricao, local = :local, modificado = '$created' WHERE id = :id");
	}

	

	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":local", $local);
	$res->bindValue(":id", $id);
	$res->execute();
}



echo 'Salvo com Sucesso!';
?>