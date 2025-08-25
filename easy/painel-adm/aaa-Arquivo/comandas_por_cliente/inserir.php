<?php 

// Essa classe tem os id multiplos. Clinte    cliente_nome


require_once("../../conexao.php");
@session_start();

$id_usuario = $_SESSION['id_usuario'];
$obs = $_POST['obs']; 
$valor = 0;
$valor_total = $_POST['valor_total'];
$status = $_POST['status'];
$produto = $_POST['produto'];
$id = $_POST['id'];
$id_form_pag = $_POST['id_form_pag'];
$qtd = $_POST['qtd'];
$situacao = $_POST['situacao'];

$data = date('d-m-Y H:i:s');
//$categoria = 'Foto';
//$categoria = $_POST['categoria'];
//$valor = $_POST['valor'];
//$usuario_referente_nome;

$created = date('d-m-Y H:i:s');

echo $id;

/*

//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/comandas_por_cliente/' .$nome_img;
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
	$res = $pdo->prepare("INSERT INTO orcamentos SET  id_form_pag = :id_form_pag, obs = :obs, usuario = '$id_usuario', valor_total = :valor_total, status = :status, produto = :produto, foto = :foto, criado = '$created'");

	$res->bindValue(":id_form_pag", $id_form_pag);
	$res->bindValue(":obs", $obs);
	$res->bindValue(":valor_total", $valor_total);
	$res->bindValue(":status", $status);
	$res->bindValue(":produto", $produto);
	$res->bindValue(":foto", $imagem);
	$res->execute();

}else{

	if($imagem != 'sem-foto.jpg'){
		$res = $pdo->prepare("UPDATE orcamentos SET  id_form_pag = :id_form_pag, obs = :obs, usuario = '$id_usuario', valor_total = :valor_total, status = :status, produto = :produto, foto = :foto, modificado = '$created', ultima_modificacao = '$id_usuario', qtd = '$qtd', situacao = '$situacao' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
	}else{
		$res = $pdo->prepare("UPDATE orcamentos SET  id_form_pag = :id_form_pag, obs = :obs, usuario = '$id_usuario', valor_total = :valor_total, status = :status, produto = :produto, foto = :foto, modificado = '$created', ultima_modificacao = '$id_usuario', qtd = '$qtd', situacao = '$situacao' WHERE id = :id");
	}

	$res->bindValue(":id_form_pag", $id_form_pag);
	$res->bindValue(":obs", $obs);
	$res->bindValue(":valor_total", $valor_total);
	$res->bindValue(":status", $status);
	$res->bindValue(":produto", $produto);
	$res->bindValue(":foto", $imagem);
	$res->bindValue(":id", $id);
	$res->execute();
}



echo 'Salvo com Sucesso!';
?>