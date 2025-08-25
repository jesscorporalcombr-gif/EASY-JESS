<?php 
require_once("../../conexao.php");
@session_start();

$id_usuario = $_SESSION['id_usuario'];
@$descricao = $_POST['descricao'];  //Observação
@$valor = $_POST['valor'];

@$cliente = $_POST['cliente'];
@$tel_cliente = $_POST['tel_cliente'];
@$profissional = $_POST['profissional'];
@$sala = $_POST['sala'];
@$inicio = $_POST['inicio'];
@$fim = $_POST['fim'];

@$equipamento = $_POST['equipamento'];
@$produtos = $_POST['produtos'];
@$procedimento = $_POST['procedimento']; //serviços

@$status = $_POST['status'];
@$situacao = $_POST['situacao'];

$id = $_POST['id'];
$data = $_POST['data'];

$created = date('d-m-Y H:i:s');

/*
 echo $id_usuario.'//ide usua/////'. $descricao.'//descrição/////'. $data  .'//data////'. $valor.'//valor/////'. $cliente.'//cliente/////'. $tel_cliente.'//tel cliente/////'. $profissional.'//profissi/////'. $sala.'//sala/////'. $inicio.'//inicio/////'. $fim.'//fim/////'. $equipamento.'//equipamen/////'. $produtos.'//produto/////'. $procedimento.'//procedim/////'. $status.'status/////';
 */


//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/agendar_conectado/' .$nome_img;
if (@$_FILES['imagem']['name'] == ""){
  $imagem = "sem-foto.jpg";
}else{
    $imagem = $nome_img;
}

$imagem_temp = @$_FILES['imagem']['tmp_name']; 
$ext = pathinfo($imagem, PATHINFO_EXTENSION);   
if($ext == 'JPG' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'JPEG' or $ext == 'png' or $ext == 'PNG' or $ext == 'gif' or $ext == 'GIF' or $ext == 'pdf' or $ext == 'PDF' or $ext == 'mp4' or $ext == 'MP4' or $ext == 'mp3' or $ext == 'MP3' or $ext == 'txt' or $ext == 'TXT' or $ext == 'docx' or $ext == 'DOCX' or $ext == 'doc' or $ext == 'DOC'){ 
move_uploaded_file($imagem_temp, $caminho);
}else{
	echo 'Extensão de Imagem não permitida!';
	exit();
}



if($id == ""){
	$res = $pdo->prepare("INSERT INTO agendar_conectado SET  data = curDate(), usuario = '$id_usuario', descricao = :descricao, valor = :valor, arquivo = :foto, inicio = :inicio, fim = :fim, sala = :sala, equipamento = :equipamento, produtos = :produtos, procedimento = :procedimento, status = :status, cliente = :cliente, tel_cliente = :tel_cliente, profissional = :profissional, situacao = :situacao, created = '$created'");

	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":valor", $valor);
	$res->bindValue(":foto", $imagem);
	$res->bindValue(":inicio", $inicio);
	$res->bindValue(":fim", $fim);
	$res->bindValue(":sala", $sala);
	$res->bindValue(":equipamento", $equipamento);
	$res->bindValue(":produtos", $produtos);
	$res->bindValue(":procedimento", $procedimento);
	$res->bindValue(":status", $status);
	$res->bindValue(":cliente", $cliente);
	$res->bindValue(":tel_cliente", $tel_cliente);
	$res->bindValue(":profissional", $profissional);
	$res->bindValue(":situacao", $situacao);
	$res->execute();

}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE agendar_conectado SET data = :data, usuario = '$id_usuario', descricao = :descricao, valor = :valor, arquivo = :foto, inicio = :inicio, fim = :fim, sala = :sala, equipamento = :equipamento, produtos = :produtos, procedimento = :procedimento, status = :status, cliente = :cliente, tel_cliente = :tel_cliente, profissional = :profissional, situacao = :situacao, modificado = '$created' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
	}else{
		$res = $pdo->prepare("UPDATE agendar_conectado SET  data = :data, usuario = '$id_usuario', descricao = :descricao, valor = :valor,  inicio = :inicio, fim = :fim, sala = :sala, equipamento = :equipamento, produtos = :produtos, procedimento = :procedimento, status = :status, cliente = :cliente, tel_cliente = :tel_cliente, profissional = :profissional, situacao = :situacao, modificado = '$created' WHERE id = :id");
	}

	
	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":valor", $valor);
	$res->bindValue(":id", $id);
	$res->bindValue(":data", $data);
	$res->bindValue(":inicio", $inicio);
	$res->bindValue(":fim", $fim);
	$res->bindValue(":sala", $sala);
	$res->bindValue(":equipamento", $equipamento);
	$res->bindValue(":produtos", $produtos);
	$res->bindValue(":procedimento", $procedimento);
	$res->bindValue(":status", $status);
	$res->bindValue(":cliente", $cliente);
	$res->bindValue(":tel_cliente", $tel_cliente);
	$res->bindValue(":profissional", $profissional);
	$res->bindValue(":situacao", $situacao);
	$res->execute();
}

echo 'Salvo com Sucesso!';
?>
