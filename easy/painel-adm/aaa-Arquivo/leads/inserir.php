<?php 

require_once("../../conexao.php");
@session_start();

$data = date('d/m/Y');

$nome = $_POST['nome'];
$email = $_POST['email'];
$whatsapp = $_POST['whatsapp'];
$origem = $_POST['origem'];
@$id_protocolo = $_POST['id_protocolo'];
@$qualificacao_do_lead = $_POST['qualificacao_do_lead'];
$agendou = $_POST['agendou'];
$fechou = $_POST['fechou'];

$data_do_fechamento = $_POST['data_do_fechamento'];

$valor = $_POST['valor'];
$observacoes = $_POST['observacoes'];
@$interesse = $_POST['interesse'];
$compareceu = $_POST['compareceu'];

$data_l = $_POST['data_l'];


$nome_usu = $_POST['nome_usu'];
$nome_usu_l = $_POST['nome_usu_l'];
$status = $_POST['status'];  


$id = $_POST['id'];

$antigo = $_POST['antigo'];
$antigo2 = $_POST['antigo2'];


if($id == ""){
	$res = $pdo->prepare("INSERT INTO leads SET nome = :nome, email = :email, data = :data, whatsapp = :whatsapp, origem = :origem, id_protocolo = :id_protocolo, qualificacao_do_lead = :qualificacao_do_lead, agendou = :agendou, fechou = :fechou, data_do_fechamento = :data_do_fechamento, valor = :valor, observacoes = :observacoes, interesse = :interesse, compareceu = :compareceu, data_l = :data_l, nome_usu = :nome_usu, status = :status ");

	$res->bindValue(":nome", $nome);
	$res->bindValue(":email", $email);
	$res->bindValue(":data", $data);
	$res->bindValue(":whatsapp", $whatsapp);
	$res->bindValue(":origem", $origem);
	$res->bindValue(":id_protocolo", $id_protocolo);
	$res->bindValue(":qualificacao_do_lead", $qualificacao_do_lead);
	$res->bindValue(":agendou", $agendou);
	$res->bindValue(":fechou", $fechou);
	$res->bindValue(":data_do_fechamento", $data_do_fechamento);
	$res->bindValue(":valor", $valor);
	$res->bindValue(":observacoes", $observacoes);
	$res->bindValue(":interesse", $interesse);
	$res->bindValue(":compareceu", $compareceu);
	$res->bindValue(":data_l", $data_l);
	
	$res->bindValue(":nome_usu", $nome_usu);
	$res->bindValue(":status", $status);

	$res->execute();

}else{
	$res = $pdo->prepare("UPDATE leads SET nome = :nome, email = :email, modificado = :data, whatsapp = :whatsapp, origem = :origem, id_protocolo = :id_protocolo, qualificacao_do_lead = :qualificacao_do_lead, agendou = :agendou, fechou = :fechou, data_do_fechamento = :data_do_fechamento, valor = :valor, observacoes = :observacoes, interesse = :interesse, compareceu = :compareceu, data_l = :data_l, modificado_por = :nome_usu_l, status = :status WHERE id = :id");

	$res->bindValue(":nome", $nome);
	$res->bindValue(":email", $email);
	$res->bindValue(":data", $data);
	$res->bindValue(":whatsapp", $whatsapp);
	$res->bindValue(":origem", $origem);
	$res->bindValue(":id_protocolo", $id_protocolo);
	$res->bindValue(":qualificacao_do_lead", $qualificacao_do_lead);
	$res->bindValue(":agendou", $agendou);
	$res->bindValue(":fechou", $fechou);
	$res->bindValue(":data_do_fechamento", $data_do_fechamento);
	$res->bindValue(":valor", $valor);
	$res->bindValue(":observacoes", $observacoes);
	$res->bindValue(":interesse", $interesse);
	$res->bindValue(":compareceu", $compareceu);
	$res->bindValue(":data_l", $data_l);
	$res->bindValue(":id", $id);
	
	$res->bindValue(":nome_usu_l", $nome_usu_l);
	$res->bindValue(":status", $status);
	
	$res->execute();

}

echo 'Salvo com Sucesso!';
?>