<?php 
require_once("../../conexao.php");
@session_start();

$id_usuario = $_SESSION['id_usuario'];
$descricao = $_POST['descricao']; 
$id = $_POST['id'];
$data = $_POST['data'];
$usuario_referente_id = $_POST['usuario_referente_id'];
//$usuario_referente_nome;

$created = date('d-m-Y H:i:s');


//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/galeria_resultados/' .$nome_img;
if (@$_FILES['imagem']['name'] == ""){
  $imagem = "sem-foto.jpg";
}else{
    $imagem = $nome_img;
}

$imagem_temp = @$_FILES['imagem']['tmp_name']; 
$ext = pathinfo($imagem, PATHINFO_EXTENSION);   
if($ext == 'JPG' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'JPEG' or $ext == 'png' or $ext == 'PNG' or $ext == 'gif' or $ext == 'GIF'){ 
move_uploaded_file($imagem_temp, $caminho);
}else{
	echo 'Extensão de Imagem não permitida!';
	exit();
}



if($id == ""){
	$res = $pdo->prepare("INSERT INTO galeria_resultados SET  usuario_referente_id = :usuario_referente_id,data = curDate(), usuario = '$id_usuario', descricao = :descricao, arquivo = :foto, created = '$created'");

	$res->bindValue(":usuario_referente_id", $usuario_referente_id);
	$res->bindValue(":descricao", $descricao);

	$res->bindValue(":foto", $imagem);
	$res->execute();

}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE galeria_resultados SET  usuario_referente_id = :usuario_referente_id, data = :data, usuario = '$id_usuario', descricao = :descricao, arquivo = :foto, modificado = '$created' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
	}else{
		$res = $pdo->prepare("UPDATE galeria_resultados SET  usuario_referente_id = :usuario_referente_id, data = :data, usuario = '$id_usuario', descricao = :descricao, modificado = '$created' WHERE id = :id");
	}

	
	$res->bindValue(":usuario_referente_id", $usuario_referente_id);
	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":id", $id);
	$res->bindValue(":data", $data);
	$res->execute();
}



echo 'Salvo com Sucesso!';
?>