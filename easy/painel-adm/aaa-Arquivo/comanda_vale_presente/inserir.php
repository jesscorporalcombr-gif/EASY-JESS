<?php 
require_once("../../conexao.php");
@session_start();

$validade_vale_presente = $_POST['validade_vale_presente'];
$validade_vale_pre_venda = $_POST['validade_vale_pre_venda'];
$id = $_POST['id'];
$id_usuario = $_SESSION['id_usuario'];

$created = date('d-m-Y H:i:s');


//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/comanda_vale_presente/' .$nome_img;
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
	$res = $pdo->prepare("INSERT INTO comanda_vale_presente SET validade_vale_presente = :validade_vale_presente, validade_vale_pre_venda = :validade_vale_pre_venda, foto = :foto, created = '$created', usuario = '$id_usuario', modificado = '$created', modificado_por = '$id_usuario'");

	$res->bindValue(":validade_vale_presente", $validade_vale_presente);
	$res->bindValue(":validade_vale_pre_venda", $validade_vale_pre_venda);
	$res->bindValue(":foto", $imagem);
	$res->execute();
}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE comanda_vale_presente SET validade_vale_presente = :validade_vale_presente, validade_vale_pre_venda = :validade_vale_pre_venda, foto = :foto, created = '$created',  modificado = '$created', modificado_por = '$id_usuario' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
		
		}else{
			$res = $pdo->prepare("UPDATE comanda_vale_presente SET validade_vale_presente = :validade_vale_presente, validade_vale_pre_venda = :validade_vale_pre_venda, created = '$created', modificado = '$created', modificado_por = '$id_usuario' WHERE id = :id");

		}

	
	$res->bindValue(":validade_vale_presente", $validade_vale_presente);
	$res->bindValue(":validade_vale_pre_venda", $validade_vale_pre_venda);
	$res->bindValue(":id", $id);
	$res->execute();
	}



echo 'Salvo com Sucesso!';
?>