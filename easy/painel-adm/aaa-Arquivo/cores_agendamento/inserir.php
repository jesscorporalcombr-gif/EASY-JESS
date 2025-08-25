<?php 
require_once("../../conexao.php");
@session_start();

@$id_usuario = $_SESSION['id_usuario'];
@$id = $_POST['id'];
@$created = date('d-m-Y H:i:s');

@$cor_confirmado = $_POST['cor_confirmado'];
@$cor_em_espera = $_POST['cor_em_espera'];
@$cor_em_andamento = $_POST['cor_em_andamento'];
@$cor_inativo = $_POST['cor_inativo'];
@$cor_faltou = $_POST['cor_faltou'];
@$cor_concluido = $_POST['cor_concluido'];
@$cor_cancelado = $_POST['cor_cancelado'];
@$cor_branco = $_POST['cor_branco'];
@$cor_pago = $_POST['cor_pago'];
@$cor_intervalo = $_POST['cor_intervalo'];
@$cor_bloqueado = $_POST['cor_bloqueado'];

//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['imagem']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/informacoes_do_estabelecimento/' .$nome_img;
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
	$res = $pdo->prepare("INSERT INTO cores_agendamento SET  cor_confirmado = '$cor_confirmado', cor_em_espera = '$cor_em_espera', cor_em_andamento = '$cor_em_andamento', cor_inativo = '$cor_inativo', cor_faltou = '$cor_faltou', cor_concluido = '$cor_concluido', cor_cancelado = '$cor_cancelado', cor_branco = '$cor_branco', cor_pago = '$cor_pago', cor_intervalo = '$cor_intervalo', cor_bloqueado = '$cor_bloqueado', usuario = '$id_usuario', created = '$created'");

	$res->bindValue(":foto", $imagem);
	$res->execute();

}else{

	if($imagem != 'sem-foto.jpg'){
		
		$res = $pdo->prepare("UPDATE cores_agendamento SET   cor_confirmado = '$cor_confirmado', cor_em_espera = '$cor_em_espera', cor_em_andamento = '$cor_em_andamento', cor_inativo = '$cor_inativo', cor_faltou = '$cor_faltou', cor_concluido = '$cor_concluido', cor_cancelado = '$cor_cancelado', cor_branco = '$cor_branco', cor_pago = '$cor_pago', modificado_por = '$id_usuario', cor_intervalo = '$cor_intervalo', cor_bloqueado = '$cor_bloqueado', modificado = '$created' WHERE id = :id");
		$res->bindValue(":foto", $imagem);
	}else{
		$res = $pdo->prepare("UPDATE cores_agendamento SET  cor_confirmado = '$cor_confirmado', cor_em_espera = '$cor_em_espera', cor_em_andamento = '$cor_em_andamento', cor_inativo = '$cor_inativo', cor_faltou = '$cor_faltou', cor_concluido = '$cor_concluido', cor_cancelado = '$cor_cancelado', cor_branco = '$cor_branco', cor_pago = '$cor_pago', modificado_por = '$id_usuario', cor_intervalo = '$cor_intervalo', cor_bloqueado = '$cor_bloqueado',  modificado = '$created' WHERE id = :id");
	}
	$res->bindValue(":id", $id);
	$res->execute();
}



echo 'Salvo com Sucesso!';
?>