<?php 

require_once("../../conexao.php");
@session_start();

$data = date('d/m/Y');

$nome = $_POST['nome'];
$interesse = $_POST['interesse'];

$id_usuario = $_SESSION['id_usuario'];
$created = date('d-m-Y H:i:s');


$id = $_POST['id'];

$antigo = $_POST['antigo'];
$antigo2 = $_POST['antigo2'];



if($id == ""){
	$res = $pdo->prepare("INSERT INTO avisos SET nome = :nome, interesse = :interesse, data = curDate(), created = '$created', criado ='$id_usuario'");

	$res->bindValue(":nome", $nome);
	$res->bindValue(":interesse", $interesse);
	

	$res->execute();

}else{
	$res = $pdo->prepare("UPDATE avisos SET nome = :nome, interesse = :interesse, modificado_por ='$id_usuario' WHERE id = :id");

	$res->bindValue(":nome", $nome);
	$res->bindValue(":interesse", $interesse);
	$res->bindValue(":id", $id);	
	
	$res->execute();

}

echo 'Salvo com Sucesso!';
?>