<?php 
require_once("../../conexao.php");

$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$id = $_POST['id'];
$antigo = $_POST['antigo'];
$created = date('d-m-Y H:i:s');




//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/salas/' .$nome_img;
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
	$res = $pdo->prepare("INSERT INTO salas SET nome = :nome, descricao = :descricao, foto = :foto, created = '$created'");

	$res->bindValue(":nome", $nome);
	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":foto", $imagem);
	$res->execute();
}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE salas SET nome = :nome,  descricao = :descricao, foto = :foto, modificado = '$created' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
		
		}else{
			$res = $pdo->prepare("UPDATE salas SET nome = :nome, descricao = :descricao, modificado = '$created' WHERE id = :id");

		}

	$res->bindValue(":nome", $nome);
	$res->bindValue(":descricao", $descricao);
	$res->bindValue(":id", $id);
	$res->execute();
	}



echo 'Salvo com Sucesso!';
?>