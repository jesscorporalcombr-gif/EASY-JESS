<?php 
require_once("../../conexao.php");
@session_start();

$id_usuario = $_SESSION['id_usuario'];

$bandeira = $_POST['bandeira'];
$conta_bancaria = $_POST['conta_bancaria'];
$taxa_cartao_de_credito = $_POST['taxa_cartao_de_credito'];
$prazo = $_POST['prazo'];

$taxa_antecipacao = $_POST['taxa_antecipacao'];
$parcelas = $_POST['parcelas'];

$id = $_POST['id'];

$created = date('d-m-Y H:i:s');

/*
echo $bandeira;
echo $conta_bancaria;
echo $taxa_cartao_de_credito;
echo $prazo;
*/


//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/bandeira_cartao_credito/' .$nome_img;
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
	$res = $pdo->prepare("INSERT INTO bandeira_cartao_credito SET parcelas = :parcelas, taxa_antecipacao = :taxa_antecipacao, prazo = :prazo, taxa_cartao_de_credito = :taxa_cartao_de_credito, conta_bancaria = :conta_bancaria, bandeira = :bandeira, foto = :foto, created = '$created', usuario = '$id_usuario'");

	$res->bindValue(":parcelas", $parcelas);
	$res->bindValue(":taxa_antecipacao", $taxa_antecipacao);
	$res->bindValue(":prazo", $prazo);
	$res->bindValue(":taxa_cartao_de_credito", $taxa_cartao_de_credito);
	$res->bindValue(":conta_bancaria", $conta_bancaria);
	$res->bindValue(":bandeira", $bandeira);
	$res->bindValue(":foto", $imagem);
	$res->execute();

}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE bandeira_cartao_credito SET parcelas = :parcelas, taxa_antecipacao = :taxa_antecipacao, prazo = :prazo, taxa_cartao_de_credito = :taxa_cartao_de_credito, conta_bancaria = :conta_bancaria, bandeira = :bandeira, foto = :foto, modificado = '$created', modificado_por = '$id_usuario' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
		
		}else{
			$res = $pdo->prepare("UPDATE bandeira_cartao_credito SET parcelas = :parcelas, taxa_antecipacao = :taxa_antecipacao, prazo = :prazo, taxa_cartao_de_credito = :taxa_cartao_de_credito, conta_bancaria = :conta_bancaria, bandeira = :bandeira, modificado = '$created', modificado_por = '$id_usuario' WHERE id = :id");

		}

	$res->bindValue(":parcelas", $parcelas);
	$res->bindValue(":taxa_antecipacao", $taxa_antecipacao);
	$res->bindValue(":prazo", $prazo);
	$res->bindValue(":taxa_cartao_de_credito", $taxa_cartao_de_credito);
	$res->bindValue(":conta_bancaria", $conta_bancaria);
	$res->bindValue(":bandeira", $bandeira);
	$res->bindValue(":id", $id);
	$res->execute();
	}



echo 'Salvo com Sucesso!';
?>