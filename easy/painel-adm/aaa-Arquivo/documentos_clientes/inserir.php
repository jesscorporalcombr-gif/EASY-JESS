<?php 
require_once("../../conexao.php");
@session_start();

$id_usuario = $_SESSION['id_usuario'];
$descricao = $_POST['descricao']; 
//$valor = $_POST['valor'];
$valor = 0;
$id = $_POST['id'];
$data = $_POST['data'];
$categoria = $_POST['categoria'];
$usuario_referente_id = $_POST['usuario_referente_id'];
//$usuario_referente_nome;

$created = date('d-m-Y H:i:s');


//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/documentos_clientes/' .$nome_img;
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
	$res = $pdo->prepare("INSERT INTO documentos_clientes SET  usuario_referente_id = :usuario_referente_id,data = curDate(), usuario = '$id_usuario', descricao = :descricao, valor = :valor, categoria = :categoria, arquivo = :foto, created = '$created'");

	$res->bindValue(":usuario_referente_id", $usuario_referente_id);
	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":valor", $valor);
	$res->bindValue(":categoria", $categoria);
	$res->bindValue(":foto", $imagem);
	$res->execute();

}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE documentos_clientes SET  usuario_referente_id = :usuario_referente_id, data = :data, usuario = '$id_usuario', descricao = :descricao, valor = :valor, categoria = :categoria, arquivo = :foto, modificado = '$created' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
	}else{
		$res = $pdo->prepare("UPDATE documentos_clientes SET  usuario_referente_id = :usuario_referente_id, data = :data, usuario = '$id_usuario', descricao = :descricao, valor = :valor, categoria = :categoria, modificado = '$created' WHERE id = :id");
	}

	
	$res->bindValue(":usuario_referente_id", $usuario_referente_id);
	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":valor", $valor);
	$res->bindValue(":categoria", $categoria);
	$res->bindValue(":id", $id);
	$res->bindValue(":data", $data);
	$res->execute();
}



echo 'Salvo com Sucesso!';
?>