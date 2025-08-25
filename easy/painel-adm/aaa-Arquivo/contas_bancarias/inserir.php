<?php 
require_once("../../conexao.php");
@session_start();

$id_usuario = $_SESSION['id_usuario'];

$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$saldo_inicial = $_POST['saldo_inicial'];
$data_saldo_inicial = $_POST['data_saldo_inicial'];
$gerente = $_POST['gerente'];
$telefone = $_POST['telefone'];
$banco = $_POST['banco'];
$tipo_de_conta = $_POST['tipo_de_conta'];
$agencia = $_POST['agencia'];
$conta = $_POST['conta'];
$digito = $_POST['digito'];
$nome_favorecido = $_POST['nome_favorecido'];
$documento_favorecido = $_POST['documento_favorecido'];
$id = $_POST['id'];

$created = date('d-m-Y H:i:s');


//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/contas_bancarias/' .$nome_img;
if (@$_FILES['imagem']['name'] == ""){
  $imagem = "sem-foto.jpg";
}else{
    $imagem = $nome_img;
}

$imagem_temp = @$_FILES['imagem']['tmp_name']; 
$ext = pathinfo($imagem, PATHINFO_EXTENSION);   
if($ext == 'JPG' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'JPEG' or $ext == 'png' or $ext == 'PNG' or $ext == 'gif' or $ext == 'GIF' or $ext == 'pdf' or $ext == 'PDF' or $ext == 'mp4' or $ext == 'MP4' or $ext == 'mp3' or $ext == 'MP3' or $ext == 'txt' or $ext == 'TXT' or $ext == 'docx' or $ext == 'DOCX' or $ext == 'doc' or $ext == 'DOC' or $ext == 'xlsx' or $ext == 'XLSX' or $ext == 'pptx' or $ext == 'PPTX'){ 
move_uploaded_file($imagem_temp, $caminho);
}else{
	echo 'Extensão de Imagem não permitida!';
	exit();
}



if($id == ""){
	$res = $pdo->prepare("INSERT INTO contas_bancarias SET nome = :nome, descricao = :descricao, saldo_inicial = :saldo_inicial, data_saldo_inicial = :data_saldo_inicial, gerente = :gerente, telefone = :telefone, banco = :banco, tipo_de_conta = :tipo_de_conta, agencia = :agencia, conta = :conta, digito = :digito, nome_favorecido = :nome_favorecido, documento_favorecido = :documento_favorecido, foto = :foto, created = '$created', usuario = '$id_usuario'");

	$res->bindValue(":nome", $nome);
	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":saldo_inicial", $saldo_inicial);
	$res->bindValue(":data_saldo_inicial", $data_saldo_inicial);
	$res->bindValue(":gerente", $gerente);
	$res->bindValue(":telefone", $telefone);
	$res->bindValue(":banco", $banco);
	$res->bindValue(":tipo_de_conta", $tipo_de_conta);
	$res->bindValue(":agencia", $agencia);
	$res->bindValue(":conta", $digito);
	$res->bindValue(":digito", $banco);
	$res->bindValue(":nome_favorecido", $nome_favorecido);
	$res->bindValue(":documento_favorecido", $documento_favorecido);
	$res->bindValue(":foto", $imagem);
	$res->execute();
}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE contas_bancarias SET nome = :nome, descricao = :descricao, saldo_inicial = :saldo_inicial, data_saldo_inicial = :data_saldo_inicial, gerente = :gerente, telefone = :telefone, banco = :banco, tipo_de_conta = :tipo_de_conta, agencia = :agencia, conta = :conta, digito = :digito, nome_favorecido = :nome_favorecido, documento_favorecido = :documento_favorecido, foto = :foto, modificado = '$created', modificado_por = '$id_usuario' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
		
		}else{
			$res = $pdo->prepare("UPDATE contas_bancarias SET nome = :nome, descricao = :descricao, saldo_inicial = :saldo_inicial, data_saldo_inicial = :data_saldo_inicial, gerente = :gerente, telefone = :telefone, banco = :banco, tipo_de_conta = :tipo_de_conta, agencia = :agencia, conta = :conta, digito = :digito, nome_favorecido = :nome_favorecido, documento_favorecido = :documento_favorecido, modificado = '$created', modificado_por = '$id_usuario' WHERE id = :id");

		}

	
	$res->bindValue(":nome", $nome);
	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":saldo_inicial", $saldo_inicial);
	$res->bindValue(":data_saldo_inicial", $data_saldo_inicial);
	$res->bindValue(":gerente", $gerente);
	$res->bindValue(":telefone", $telefone);
	$res->bindValue(":banco", $banco);
	$res->bindValue(":tipo_de_conta", $tipo_de_conta);
	$res->bindValue(":agencia", $agencia);
	$res->bindValue(":conta", $digito);
	$res->bindValue(":digito", $banco);
	$res->bindValue(":nome_favorecido", $nome_favorecido);
	$res->bindValue(":documento_favorecido", $documento_favorecido);
	$res->bindValue(":id", $id);
	$res->execute();
	}



echo 'Salvo com Sucesso!';
?>