<?php 
require_once("../../conexao.php");
@session_start();

@$nome = $_POST['nome'];
@$id = $_POST['id'];

@$id_produto = $_POST['id_produto'];   
@$valor = $_POST['valor'];  
@$data_inicio = $_POST['data_inicio']; 
@$data_final = $_POST['data_final'];   
@$usuario = $_SESSION['id_usuario'];    
@$ativo = $_POST['ativo'];
@$created = date('d-m-Y H:i:s');


//echo $id_produto;

//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/promocoes/' .$nome_img;
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
	$res = $pdo->prepare("INSERT INTO promocoes SET nome = :nome, id_produto = :id_produto, valor = :valor, data_inicio = :data_inicio, data_final = :data_final, foto = :foto, usuario = :usuario, ativo = :ativo, created = '$created'");


	$res->bindValue(":nome", $nome);
	$res->bindValue(":id_produto", $id_produto);
	$res->bindValue(":valor", $valor);
	$res->bindValue(":data_inicio", $data_inicio);
	$res->bindValue(":data_final", $data_final);
	$res->bindValue(":foto", $imagem);
	$res->bindValue(":usuario", $usuario);
	$res->bindValue(":ativo", $ativo);
	$res->execute();

	/*
	$res2 = $pdo->prepare("UPDATE produtos SET em_promocao = '$ativo' WHERE id = '$id_produto'");
	$res2->execute();
	*/

}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE promocoes SET nome = :nome, id_produto = :id_produto, valor = :valor, data_inicio = :data_inicio, data_final = :data_final, foto = :foto, usuario = :usuario, ativo = :ativo, modificado = '$created', modificado_por = '$usuario' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
		
		}else{
			$res = $pdo->prepare("UPDATE promocoes SET nome = :nome, id_produto = :id_produto, valor = :valor, data_inicio = :data_inicio, data_final = :data_final, usuario = :usuario, ativo = :ativo, modificado = '$created', modificado_por = '$usuario' WHERE id = :id");

		}

	$res->bindValue(":nome", $nome);
	$res->bindValue(":id_produto", $id_produto);
	$res->bindValue(":valor", $valor);
	$res->bindValue(":data_inicio", $data_inicio);
	$res->bindValue(":data_final", $data_final);
	$res->bindValue(":usuario", $usuario);
	$res->bindValue(":ativo", $ativo);
	$res->bindValue(":id", $id);
	$res->execute();
	}



echo 'Salvo com Sucesso!';
?>